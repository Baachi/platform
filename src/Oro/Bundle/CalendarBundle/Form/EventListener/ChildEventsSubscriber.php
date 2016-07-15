<?php

namespace Oro\Bundle\CalendarBundle\Form\EventListener;

use Doctrine\Common\Persistence\ManagerRegistry;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Oro\Bundle\CalendarBundle\Entity\Attendee;
use Oro\Bundle\CalendarBundle\Entity\CalendarEvent;
use Oro\Bundle\CalendarBundle\Entity\Repository\CalendarRepository;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Oro\Bundle\SecurityBundle\SecurityFacade;

class ChildEventsSubscriber implements EventSubscriberInterface
{
    /** @var ManagerRegistry */
    protected $registry;

    /** @var SecurityFacade */
    protected $securityFacade;

    /** @var array */
    protected $editableFieldsForRecurrence = [
        'title',
        'description',
        'contexts',
    ];

    /**
     * @param ManagerRegistry $registry
     * @param SecurityFacade  $securityFacade
     */
    public function __construct(
        ManagerRegistry $registry,
        SecurityFacade $securityFacade
    ) {
        $this->registry       = $registry;
        $this->securityFacade = $securityFacade;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT  => 'preSubmit',
            FormEvents::POST_SUBMIT => 'postSubmit', // synchronize child events
        ];
    }

    /**
     * We check if there is wrong value in attendee type and set it to null
     *
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();

        if (!empty($data['attendees']) && is_array($data['attendees'])) {
            $attendees = &$data['attendees'];

            foreach ($attendees as &$attendee) {
                $type = array_key_exists('type', $attendee) ? $attendee['type'] : null;

                if ($this->shouldTypeBeChecked($type)) {
                    $attendee['type'] = $this->getTypeEnum($type);
                }
            }

            $event->setData($data);
        }
    }

    /**
     * - creates/removes calendar events based on attendee changes
     * - makes sure displayName is not empty
     * - sets default attendee status
     * - updates duplicated values of child events
     * (It would be better to have separate entity for common data. Could be e.g. CalendarEventInfo)
     *
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event)
    {
        /** @var CalendarEvent $calendarEvent */
        $calendarEvent = $event->getForm()->getData();
        $this->updateCalendarEvents($calendarEvent);
        $this->updateAttendeeDisplayNames($calendarEvent);
        if (!$calendarEvent) {
            return;
        }

        $this->setDefaultAttendeeStatus($calendarEvent->getRelatedAttendee(), CalendarEvent::STATUS_ACCEPTED);
        foreach ($calendarEvent->getChildEvents() as $childEvent) {
            $childEvent
                ->setTitle($calendarEvent->getTitle())
                ->setDescription($calendarEvent->getDescription())
                ->setStart($calendarEvent->getStart())
                ->setEnd($calendarEvent->getEnd())
                ->setAllDay($calendarEvent->getAllDay());

            if ($calendarEvent->getRecurringEvent() && $childEvent->getCalendar()) {
                $childEvent
                    ->setRecurringEvent(
                        $calendarEvent
                            ->getRecurringEvent()
                            ->getChildEventByCalendar($childEvent->getCalendar())
                    )
                    ->setOriginalStart($calendarEvent->getOriginalStart());
            }
        }

        foreach ($calendarEvent->getChildAttendees() as $attendee) {
            $this->setDefaultAttendeeStatus($attendee);
        }
    }

    /**
     * Creates/removes calendar events based on attendee changes
     *
     * @param CalendarEvent $calendarEvent
     */
    protected function updateCalendarEvents(CalendarEvent $calendarEvent)
    {
        $attendeesByUserId = [];
        if ($calendarEvent->getRecurringEvent() && $calendarEvent->isCancelled()) {
            $attendees = $calendarEvent->getRecurringEvent()->getAttendees();
        } else {
            $attendees = $calendarEvent->getAttendees();
        }

        foreach ($attendees as $attendee) {
            if (!$attendee->getUser()) {
                continue;
            }

            $attendeesByUserId[$attendee->getUser()->getId()] = $attendee;
        }
        $currentUserIds = array_keys($attendeesByUserId);

        $calendarEventOwnerIds = [];
        $calendar              = $calendarEvent->getCalendar();
        if ($calendar && $calendar->getOwner()) {
            $owner = $calendar->getOwner();
            if (isset($attendeesByUserId[$owner->getId()])) {
                $calendarEvent->setRelatedAttendee($attendeesByUserId[$owner->getId()]);
            }
            $calendarEventOwnerIds[] = $calendar->getOwner()->getId();
        }
        $events = $calendarEvent->getChildEvents();
        foreach ($events as $event) {
            $calendar = $event->getCalendar();
            if (!$calendar) {
                continue;
            }

            $owner = $calendar->getOwner();
            if (!$owner) {
                continue;
            }

            $ownerId = $owner->getId();
            if (!in_array($ownerId, $currentUserIds)) {
                $calendarEvent->removeChildEvent($event);

                continue;
            }

            $calendarEventOwnerIds[] = $ownerId;
        }

        $this->createChildEvent(
            $calendarEvent,
            array_diff($currentUserIds, $calendarEventOwnerIds),
            $attendeesByUserId
        );
    }

    /**
     * Makes sure displayName is not empty
     *
     * @param CalendarEvent $parent
     */
    protected function updateAttendeeDisplayNames(CalendarEvent $parent)
    {
        foreach ($parent->getAttendees() as $attendee) {
            if ($attendee->getDisplayName()) {
                continue;
            }

            $attendee->setDisplayName($attendee->getEmail());
        }
    }

    /**
     * @param Attendee|null $attendee
     * @param string        $status
     */
    protected function setDefaultAttendeeStatus(Attendee $attendee = null, $status = CalendarEvent::STATUS_NONE)
    {
        if (!$attendee || $attendee->getStatus()) {
            return;
        }

        $statusEnum = $this->registry
            ->getRepository(ExtendHelper::buildEnumValueClassName(Attendee::STATUS_ENUM_CODE))
            ->find($status);
        $attendee->setStatus($statusEnum);
    }

    /**
     * @param CalendarEvent $parent
     * @param array         $missingEventUserIds
     * @param array         $attendeesByUserId
     */
    protected function createChildEvent(CalendarEvent $parent, array $missingEventUserIds, array $attendeesByUserId)
    {
        if ($missingEventUserIds) {
            /** @var CalendarRepository $calendarRepository */
            $calendarRepository = $this->registry->getRepository('OroCalendarBundle:Calendar');
            $organizationId     = $this->securityFacade->getOrganizationId();

            $calendars = $calendarRepository->findDefaultCalendars($missingEventUserIds, $organizationId);
            foreach ($calendars as $calendar) {
                $event = new CalendarEvent();
                $event->setCalendar($calendar);
                $parent->addChildEvent($event);
                if ($calendar->getOwner() && isset($attendeesByUserId[$calendar->getOwner()->getId()])) {
                    $event->setRelatedAttendee($attendeesByUserId[$calendar->getOwner()->getId()]);
                }
            }
        }
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    protected function shouldTypeBeChecked($type)
    {
        return
            null !== $type
            && !in_array($type, [Attendee::TYPE_OPTIONAL, Attendee::TYPE_REQUIRED, Attendee::TYPE_ORGANIZER]);
    }

    /**
     * @param $type
     *
     * @return object
     */
    protected function getTypeEnum($type)
    {
        return $this->registry
            ->getRepository(ExtendHelper::buildEnumValueClassName(Attendee::TYPE_ENUM_CODE))
            ->find($type);
    }
}
