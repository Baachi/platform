<?php

namespace Oro\Bundle\CalendarBundle\Model\Recurrence;

use Oro\Bundle\CalendarBundle\Entity\Recurrence;
use Oro\Bundle\CalendarBundle\Tools\Recurrence\NthStrategyHelper;

class MonthNthStrategy implements StrategyInterface
{
    /** @var NthStrategyHelper */
    protected $strategyHelper;

    /**
     * MonthNthStrategy constructor.
     *
     * @param NthStrategyHelper $strategyHelper
     */
    public function __construct(NthStrategyHelper $strategyHelper)
    {
        $this->strategyHelper = $strategyHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getOccurrences(Recurrence $recurrence, \DateTime $start, \DateTime $end)
    {
        $result = [];
        $startTime = $recurrence->getStartTime();
        $dayOfWeek = $recurrence->getDayOfWeek();
        $instance = $recurrence->getInstance();
        $occurrenceDate = $this->getNextOccurrence(0, $dayOfWeek, $instance, $startTime);

        if ($occurrenceDate < $recurrence->getStartTime()) {
            $occurrenceDate = $this->getNextOccurrence(
                $recurrence->getInterval(),
                $dayOfWeek,
                $instance,
                $occurrenceDate
            );
        }

        $interval = $recurrence->getInterval();
        $fromStartInterval = 1;

        if ($start > $occurrenceDate) {
            $dateInterval = $start->diff($occurrenceDate);
            $fromStartInterval = intval($dateInterval->format('%y')) * 12 + intval($dateInterval->format('m'));
            $fromStartInterval = floor($fromStartInterval / $interval);
            $occurrenceDate = $this->getNextOccurrence(
                $fromStartInterval * $interval,
                $dayOfWeek,
                $instance,
                $occurrenceDate
            );
        }

        $occurrences = $recurrence->getOccurrences();
        while ($occurrenceDate <= $recurrence->getEndTime()
            && $occurrenceDate <= $end
            && (is_null($occurrences) || $fromStartInterval <= $occurrences)
        ) {
            $result[] = $occurrenceDate;
            $fromStartInterval++;
            $occurrenceDate = $this->getNextOccurrence($interval, $dayOfWeek, $instance, $occurrenceDate);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Recurrence $recurrence)
    {
        return $recurrence->getRecurrenceType() === Recurrence::TYPE_MONTH_N_TH;
    }

    /**
     * {@inheritdoc}
     */
    public function getRecurrencePattern(Recurrence $recurrence)
    {
        return 'monthnth';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'recurrence_monthnth';
    }

    /**
     * Returns occurrence date according to last occurrence date and recurrence rules.
     *
     * @param integer $interval
     * @param array $dayOfWeek
     * @param integer $instance
     * @param \DateTime $date
     *
     * @return \DateTime
     */
    protected function getNextOccurrence($interval, $dayOfWeek, $instance, \DateTime $date)
    {
        $occurrenceDate = new \DateTime("+{$interval} month {$date->format('c')}");

        $instanceRelativeValue = $this->strategyHelper->getInstanceRelativeValue($instance);
        $month = $occurrenceDate->format('M');
        $year = $occurrenceDate->format('Y');
        $nextDays = [];
        foreach ($dayOfWeek as $day) {
            $nextDays[] = new \DateTime("{$instanceRelativeValue} {$day} of {$month} {$year}");
        }

        return $instance == Recurrence::INSTANCE_LAST ? max ($nextDays) : min($nextDays);
    }
}
