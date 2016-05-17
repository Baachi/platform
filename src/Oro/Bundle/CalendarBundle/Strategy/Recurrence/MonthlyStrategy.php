<?php

namespace Oro\Bundle\CalendarBundle\Strategy\Recurrence;

use Oro\Bundle\CalendarBundle\Entity\Recurrence;

class MonthlyStrategy extends AbstractStrategy implements StrategyInterface
{
    /**
     * {@inheritdoc}
     */
    public function getOccurrences(Recurrence $recurrence, \DateTime $start, \DateTime $end)
    {
        $this->strategyHelper->validateRecurrence($recurrence);
        $result = [];
        $occurrenceDate = $this->getFirstOccurrence($recurrence);
        $interval = $recurrence->getInterval();
        $fromStartInterval = 1;

        if ($start > $occurrenceDate) {
            $dateInterval = $start->diff($occurrenceDate);
            $fromStartInterval = (int)$dateInterval->format('%y') * 12 + (int)$dateInterval->format('m');
            $fromStartInterval = floor($fromStartInterval / $interval);
            $occurrenceDate = $this->getNextOccurrence($fromStartInterval++ * $interval, $occurrenceDate);
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
            $occurrenceDate = $this->getNextOccurrence($interval, $occurrenceDate);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Recurrence $recurrence)
    {
        return $recurrence->getRecurrenceType() === Recurrence::TYPE_MONTHLY;
    }

    /**
     * {@inheritdoc}
     */
    public function getRecurrencePattern(Recurrence $recurrence)
    {
        $interval = $recurrence->getInterval();

        return $this->getFullRecurrencePattern(
            $recurrence,
            'oro.calendar.recurrence.patterns.monthly',
            $interval,
            ['%count%' => $interval, '%day%' => $recurrence->getDayOfMonth()]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'recurrence_monthly';
    }

    /**
     * Returns occurrence date according to last occurrence date and recurrence interval.
     *
     * @param integer $interval A number of months.
     * @param \DateTime $date
     *
     * @return \DateTime
     */
    protected function getNextOccurrence($interval, \DateTime $date)
    {
        return new \DateTime("+{$interval} month {$date->format('c')}");
    }

    /**
     * Returns first occurrence according to recurrence rules.
     *
     * @param Recurrence $recurrence
     *
     * @return \DateTime
     */
    protected function getFirstOccurrence(Recurrence $recurrence)
    {
        $dayOfMonth = $recurrence->getDayOfMonth();
        $occurrenceDate = clone $recurrence->getStartTime();
        $occurrenceDate->setDate($occurrenceDate->format('Y'), $occurrenceDate->format('m'), $dayOfMonth);

        if ($occurrenceDate->format('d') < $recurrence->getStartTime()->format('d')) {
            $occurrenceDate = $this->getNextOccurrence($recurrence->getInterval(), $occurrenceDate);
        }

        return $occurrenceDate;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastOccurrence(Recurrence $recurrence)
    {
        $occurrenceDate = $this->getFirstOccurrence($recurrence);

        return $this->getNextOccurrence(
            ($recurrence->getOccurrences() - 1) * $recurrence->getInterval(),
            $occurrenceDate
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getValidationErrorMessage(Recurrence $recurrence)
    {
        if (empty($recurrence->getDayOfMonth())) {
            return "Parameter 'dayOfMonth' can't be empty for Monthly recurrence pattern.";
        }

        return null;
    }
}
