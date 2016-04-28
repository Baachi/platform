<?php

namespace Oro\Bundle\CalendarBundle\Model\Recurrence;

use Oro\Bundle\CalendarBundle\Entity\Recurrence;
use Oro\Bundle\CalendarBundle\Tools\Recurrence\NthStrategyHelper;

class YearNthStrategy extends AbstractStrategy implements StrategyInterface
{
    /** @var NthStrategyHelper */
    protected $strategyHelper;

    /**
     * {@inheritdoc}
     */
    public function getOccurrences(Recurrence $recurrence, \DateTime $start, \DateTime $end)
    {
        $result = [];
        $startTime = $recurrence->getStartTime();
        $dayOfWeek = $recurrence->getDayOfWeek();
        $monthOfYear = $recurrence->getMonthOfYear();
        $instance = $recurrence->getInstance();
        $occurrenceDate = $this->getNextOccurrence(0, $dayOfWeek, $monthOfYear, $instance, $startTime);

        if ($occurrenceDate < $recurrence->getStartTime()) {
            $occurrenceDate = $this->getNextOccurrence(
                $recurrence->getInterval(),
                $dayOfWeek,
                $monthOfYear,
                $instance,
                $occurrenceDate
            );
        }

        $interval = $recurrence->getInterval();
        $fromStartInterval = 1;

        if ($start > $occurrenceDate) {
            $dateInterval = $start->diff($occurrenceDate);
            $fromStartInterval = (int)$dateInterval->format('%y') * 12 + (int)$dateInterval->format('m');
            $fromStartInterval = floor($fromStartInterval / $interval);
            $occurrenceDate = $this->getNextOccurrence(
                $fromStartInterval++ * $interval,
                $dayOfWeek,
                $monthOfYear,
                $instance,
                $occurrenceDate
            );
        }

        $occurrences = $recurrence->getOccurrences();
        while ($occurrenceDate <= $recurrence->getEndTime()
            && $occurrenceDate <= $end
            && ($occurrences === null || $fromStartInterval <= $occurrences)
        ) {
            if ($occurrenceDate >= $start) {
                $result[] = $occurrenceDate;
            }
            $fromStartInterval++;
            $occurrenceDate = $this->getNextOccurrence($interval, $dayOfWeek, $monthOfYear, $instance, $occurrenceDate);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Recurrence $recurrence)
    {
        return $recurrence->getRecurrenceType() === Recurrence::TYPE_YEAR_N_TH;
    }

    /**
     * {@inheritdoc}
     */
    public function getRecurrencePattern(Recurrence $recurrence)
    {
        $interval = (int)($recurrence->getInterval() / 12);
        $instanceValue = $this->strategyHelper->getInstanceRelativeValue($recurrence->getInstance());
        $instance = $this->translator->trans('oro.calendar.recurrence.instances.' . $instanceValue);
        $day = $this->strategyHelper->getDayOfWeekRelativeValue($recurrence->getDayOfWeek());
        $day = $this->translator->trans('oro.calendar.recurrence.days.' . $day);
        $currentDate = new \DateTime();
        $currentDate->setDate($currentDate->format('Y'), $recurrence->getMonthOfYear(), $currentDate->format('d'));
        $month = $this->dateTimeFormatter->format($currentDate, null, \IntlDateFormatter::NONE, null, null, 'MMM');

        return $this->getFullRecurrencePattern(
            $recurrence,
            'oro.calendar.recurrence.patterns.yearnth',
            $interval,
            ['%count%' => $interval, '%day%' => $day, '%instance%' => strtolower($instance), '%month%' => $month]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'recurrence_yearnth';
    }

    /**
     * Returns occurrence date according to last occurrence date and recurrence rules.
     *
     * @param $interval
     * @param $dayOfWeek
     * @param $monthOfYear
     * @param $instance
     * @param \DateTime $date
     *
     * @return \DateTime
     */
    protected function getNextOccurrence($interval, $dayOfWeek, $monthOfYear, $instance, \DateTime $date)
    {
        $occurrenceDate = new \DateTime("+{$interval} month {$date->format('c')}");

        $instanceRelativeValue = $this->strategyHelper->getInstanceRelativeValue($instance);
        $month = date('M', mktime(0, 0, 0, $monthOfYear));
        $year = $occurrenceDate->format('Y');
        $nextDays = [];
        foreach ($dayOfWeek as $day) {
            $nextDays[] = new \DateTime("{$instanceRelativeValue} {$day} of {$month} {$year}");
        }

        return $instance === Recurrence::INSTANCE_LAST ? max($nextDays) : min($nextDays);
    }

    /**
     * Sets strategy helper.
     *
     * @param NthStrategyHelper $helper
     *
     * @return self
     */
    public function setHelper(NthStrategyHelper $helper)
    {
        $this->strategyHelper = $helper;

        return $this;
    }
}
