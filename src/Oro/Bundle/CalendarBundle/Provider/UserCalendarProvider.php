<?php

namespace Oro\Bundle\CalendarBundle\Provider;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

use Oro\Bundle\CalendarBundle\Entity;
use Oro\Bundle\CalendarBundle\Entity\Repository\CalendarEventRepository;
use Oro\Bundle\CalendarBundle\Model\Recurrence;
use Oro\Bundle\CalendarBundle\Model\Recurrence\StrategyInterface;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\EntityBundle\Provider\EntityNameResolver;
use Oro\Component\PropertyAccess\PropertyAccessor;

class UserCalendarProvider extends AbstractCalendarProvider
{
    /** @var EntityNameResolver */
    protected $entityNameResolver;

    /** @var AbstractCalendarEventNormalizer */
    protected $calendarEventNormalizer;

    /** @var StrategyInterface  */
    protected $recurrenceModel;

    /** @var PropertyAccessor */
    protected $propertyAccessor;

    /**
     * UserCalendarProvider constructor.
     *
     * @param DoctrineHelper $doctrineHelper
     * @param EntityNameResolver $entityNameResolver
     * @param AbstractCalendarEventNormalizer $calendarEventNormalizer
     * @param Recurrence $recurrenceModel
     */
    public function __construct(
        DoctrineHelper $doctrineHelper,
        EntityNameResolver $entityNameResolver,
        AbstractCalendarEventNormalizer $calendarEventNormalizer,
        Recurrence $recurrenceModel
    ) {
        parent::__construct($doctrineHelper);
        $this->entityNameResolver      = $entityNameResolver;
        $this->calendarEventNormalizer = $calendarEventNormalizer;
        $this->recurrenceModel         = $recurrenceModel;
    }

    /**
     * {@inheritdoc}
     */
    public function getCalendarDefaultValues($organizationId, $userId, $calendarId, array $calendarIds)
    {
        if (empty($calendarIds)) {
            return [];
        }

        $qb = $this->doctrineHelper->getEntityRepository('OroCalendarBundle:Calendar')
            ->createQueryBuilder('o')
            ->select('o, owner')
            ->innerJoin('o.owner', 'owner');
        $qb->where($qb->expr()->in('o.id', $calendarIds));

        $result = [];

        /** @var Entity\Calendar[] $calendars */
        $calendars = $qb->getQuery()->getResult();
        foreach ($calendars as $calendar) {
            $resultItem = [
                'calendarName' => $this->buildCalendarName($calendar),
                'userId'       => $calendar->getOwner()->getId()
            ];
            // prohibit to remove the current calendar from the list of connected calendars
            if ($calendar->getId() === $calendarId) {
                $resultItem['removable'] = false;
                $resultItem['canAddEvent']    = true;
                $resultItem['canEditEvent']   = true;
                $resultItem['canDeleteEvent'] = true;
            }
            $result[$calendar->getId()] = $resultItem;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getCalendarEvents(
        $organizationId,
        $userId,
        $calendarId,
        $start,
        $end,
        $connections,
        $extraFields = []
    ) {
        /** @var CalendarEventRepository $repo */
        $repo        = $this->doctrineHelper->getEntityRepository('OroCalendarBundle:CalendarEvent');
        $extraFields = $this->filterSupportedFields($extraFields, 'Oro\Bundle\CalendarBundle\Entity\CalendarEvent');
        $qb          = $repo->getUserEventListByTimeIntervalQueryBuilder($start, $end, [], $extraFields);

        $visibleIds = [];
        foreach ($connections as $id => $visible) {
            if ($visible) {
                $visibleIds[] = $id;
            }
        }
        if ($visibleIds) {
            $qb
                ->andWhere('c.id IN (:visibleIds)')
                ->setParameter('visibleIds', $visibleIds);
        } else {
            $qb
                ->andWhere('1 = 0');
        }

        $this->addRecurrencesConditions($qb, $start, $end);

        $items = $this->calendarEventNormalizer->getCalendarEvents($calendarId, $qb->getQuery());
        $items = $this->getExpandedRecurrences($items, $start, $end);

        return $items;
    }

    /**
     * @param Entity\Calendar $calendar
     *
     * @return string
     */
    protected function buildCalendarName(Entity\Calendar $calendar)
    {
        return $calendar->getName() ?: $this->entityNameResolver->getName($calendar->getOwner());
    }

    /**
     * Returns transformed and expanded list with respected recurring events based on unprocessed events in $rawItems
     * and date range.
     *
     * @param array $rawItems
     * @param \DateTime $start
     * @param \DateTime $end
     *
     * @return array
     */
    protected function getExpandedRecurrences(array $rawItems, \DateTime $start, \DateTime $end)
    {
        $regularEvents = $this->filterRegularEvents($rawItems);
        $recurringExceptionEvents = $this->filterRecurringExceptionEvents($rawItems);
        $recurringOccurrenceEvents = $this->filterRecurringOccurrenceEvents($rawItems, $start, $end);

        return $this->mergeRegularAndRecurringEvents(
            $regularEvents,
            $recurringOccurrenceEvents,
            $recurringExceptionEvents
        );
    }

    /**
     * Returns list of all regular and not recurring events. Filters processed events from $items.
     *
     * @param array $items
     *
     * @return array
     */
    protected function filterRegularEvents(array &$items)
    {
        $events = [];
        foreach ($items as $index => $item) {
            if (empty($item[Recurrence::STRING_KEY]) && empty($item['recurringEventId'])) {
                $events[] = $item;
                unset($items[$index]);
            }
        }

        return $events;
    }

    /**
     * Returns list of all events which represent exception of recurring event. Filters processed events from $items.
     *
     * @param array $items
     *
     * @return array
     */
    protected function filterRecurringExceptionEvents(array &$items)
    {
        $exceptions = [];
        foreach ($items as $index => $item) {
            if (empty($item[Recurrence::STRING_KEY]) &&
                !empty($item['recurringEventId']) &&
                !empty($item['originalStart'])
            ) {
                unset($items[$index]);
                $exceptions[] = $item;
            }
        }

        return $exceptions;
    }

    /**
     * For each recurring event creates records representing events of recurring occurrence for [$start, $end] range.
     * Returns merged list of all such events. Filters processed events from $items.
     *
     * @param array $items
     * @param \DateTime $start
     * @param \DateTime $end
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \Symfony\Component\PropertyAccess\Exception\InvalidPropertyPathException
     * @throws \Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException
     */
    protected function filterRecurringOccurrenceEvents(array &$items, \DateTime $start, \DateTime $end)
    {
        $key = Recurrence::STRING_KEY;
        $propertyAccessor = $this->getPropertyAccessor();
        $occurrences = [];
        $dateFields = ['startTime', 'endTime', 'calculatedEndTime'];
        foreach ($items as $index => $item) {
            if (!empty($item[$key]) && empty($item['recurringEventId'])) {
                unset($items[$index]);
                $recurrence = new Entity\Recurrence();
                foreach ($item[$key] as $field => $value) {
                    $value = in_array($field, $dateFields, true) && $value !== null ? new \DateTime($value) : $value;
                    if ($field !== 'id') {
                        $propertyAccessor->setValue($recurrence, $field, $value);
                    }
                }
                unset($item[$key]['calculatedEndTime']);
                $occurrenceDates = $this->recurrenceModel->getOccurrences($recurrence, $start, $end);
                foreach ($occurrenceDates as $occurrenceDate) {
                    $newItem = $item;
                    $newItem['recurrencePattern'] = $this->recurrenceModel->getTextValue($recurrence);
                    $newItem['start'] = $occurrenceDate->format('c');
                    $endDate = new \DateTime($newItem['end']);
                    $endDate->setDate(
                        $occurrenceDate->format('Y'),
                        $occurrenceDate->format('m'),
                        $occurrenceDate->format('d')
                    );
                    $newItem['end'] = $endDate->format('c');
                    $occurrences[] = $newItem;
                }
            }
        }

        return $occurrences;
    }

    /**
     * Merges all previously filtered events.
     *
     * Result will contain:
     * $regularEvents + ($recurringOccurrenceEvents - $recurringExceptionEvents) + $recurringExceptionEvents
     *
     * @param array $regularEvents
     * @param array $recurringOccurrenceEvents
     * @param array $recurringExceptionEvents
     *
     * @return array
     */
    protected function mergeRegularAndRecurringEvents(
        array $regularEvents,
        array $recurringOccurrenceEvents,
        array $recurringExceptionEvents
    ) {
        $recurringEvents = [];

        foreach ($recurringOccurrenceEvents as $occurrence) {
            $exceptionFound = false;
            foreach ($recurringExceptionEvents as $key => $exception) {
                if ((int)$exception['recurringEventId'] === (int)$occurrence['id'] &&
                    new \DateTime($exception['originalStart']) == new \DateTime($occurrence['start'])
                ) {
                    $exceptionFound = true;
                    if (empty($exception['isCancelled'])) {
                        $recurringEvents[] = $exception;
                    }
                    unset($recurringExceptionEvents[$key]);
                }
            }

            if (!$exceptionFound) {
                $recurringEvents[] = $occurrence;
            }
        }

        return array_merge($regularEvents, $recurringEvents, $recurringExceptionEvents);
    }

    /**
     * Adds conditions for getting recurrence events that could be out of filtering dates.
     *
     * @param QueryBuilder $queryBuilder
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     *
     * @return self
     */
    protected function addRecurrencesConditions(QueryBuilder $queryBuilder, $startDate, $endDate)
    {
        //add condition that recurrence dates and filter dates are crossing
        $expr = $queryBuilder->expr();
        $queryBuilder->orWhere(
            $expr->andX(
                $expr->lte('r.startTime', ':endDate'),
                $expr->gte('r.calculatedEndTime', ':startDate')
            )
        )
        ->orWhere(
            $expr->andX(
                $expr->isNotNull('e.originalStart'),
                $expr->lte('e.originalStart', ':endDate'),
                $expr->gte('e.originalStart', ':startDate')
            )
        );
        $queryBuilder->setParameter('startDate', $startDate);
        $queryBuilder->setParameter('endDate', $endDate);

        return $this;
    }

    /**
     * @return PropertyAccessor
     */
    protected function getPropertyAccessor()
    {
        if (null === $this->propertyAccessor) {
            $this->propertyAccessor = new PropertyAccessor();
        }

        return $this->propertyAccessor;
    }
}
