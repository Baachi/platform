<?php

namespace Oro\Bundle\CalendarBundle\Model;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use Oro\Bundle\CalendarBundle\Entity;

class Recurrence
{
    const STRING_KEY = 'recurrence';
    const MAX_END_DATE = '9000-01-01T00:00:01+00:00';

    const TYPE_DAILY = 'daily';
    const TYPE_WEEKLY = 'weekly';
    const TYPE_MONTHLY = 'monthly';
    const TYPE_MONTH_N_TH = 'monthnth';
    const TYPE_YEARLY = 'yearly';
    const TYPE_YEAR_N_TH = 'yearnth';

    const INSTANCE_FIRST = 1;
    const INSTANCE_SECOND = 2;
    const INSTANCE_THIRD = 3;
    const INSTANCE_FOURTH = 4;
    const INSTANCE_LAST = 5;

    const DAY_SUNDAY = 'sunday';
    const DAY_MONDAY = 'monday';
    const DAY_TUESDAY = 'tuesday';
    const DAY_WEDNESDAY = 'wednesday';
    const DAY_THURSDAY = 'thursday';
    const DAY_FRIDAY = 'friday';
    const DAY_SATURDAY = 'saturday';

    /** @var ValidatorInterface */
    protected $validator;

    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /** @var array */
    protected static $instanceRelativeValues = [
        self::INSTANCE_FIRST => 'first',
        self::INSTANCE_SECOND => 'second',
        self::INSTANCE_THIRD => 'third',
        self::INSTANCE_FOURTH => 'fourth',
        self::INSTANCE_LAST => 'last',
    ];

    /** @var array */
    protected static $weekdays = [
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
    ];

    /** @var array */
    protected static $weekends = [
        'saturday',
        'sunday',
    ];

    /**
     * Returns recurrence instance relative value by its key.
     *
     * @param $key
     *
     * @return null|string
     */
    public function getInstanceRelativeValue($key)
    {
        return empty(self::$instanceRelativeValues[$key]) ? null : self::$instanceRelativeValues[$key];
    }

    /**
     * Validates recurrence entity according to its validation rules.
     *
     * @param Entity\Recurrence $recurrence
     *
     * @return self
     *
     * @throws \RuntimeException
     */
    public function validateRecurrence(Entity\Recurrence $recurrence)
    {
        $errors = $this->validator->validate($recurrence);

        if (count($errors) > 0) {
            $errorMessages = [];
            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }

            throw new \RuntimeException('Recurrence is invalid: ' . json_encode($errorMessages));
        }

        return $this;
    }

    /**
     * Returns relative value for dayOfWeek of recurrence entity.
     *
     * @param array $dayOfWeek
     *
     * @return string
     */
    public function getDayOfWeekRelativeValue($dayOfWeek)
    {
        sort($dayOfWeek);
        sort(self::$weekends);
        if (self::$weekends == $dayOfWeek) {
            return 'weekend';
        }

        sort(self::$weekdays);
        if (self::$weekdays == $dayOfWeek) {
            return 'weekday';
        }

        if (count($dayOfWeek) == 7) {
            return 'day';
        }

        //returns first element
        return reset($dayOfWeek);
    }

    /**
     * Returns the list of possible values for recurrenceType.
     *
     * @return array
     */
    public static function getRecurrenceTypesValues()
    {
        return array_keys(self::getRecurrenceTypes());
    }

    /**
     * Returns the list of possible values for dayOfWeek.
     *
     * @return array
     */
    public static function getDaysOfWeekValues()
    {
        return array_keys(self::getDaysOfWeek());
    }

    /**
     * Returns the list of possible values(with labels) for recurrenceType.
     *
     * @return array
     */
    public static function getRecurrenceTypes()
    {
        return [
            self::TYPE_DAILY => 'oro.calendar.recurrence.types.daily',
            self::TYPE_WEEKLY => 'oro.calendar.recurrence.types.weekly',
            self::TYPE_MONTHLY => 'oro.calendar.recurrence.types.monthly',
            self::TYPE_MONTH_N_TH => 'oro.calendar.recurrence.types.monthnth',
            self::TYPE_YEARLY => 'oro.calendar.recurrence.types.yearly',
            self::TYPE_YEAR_N_TH => 'oro.calendar.recurrence.types.yearnth',
        ];
    }

    /**
     * Returns the list of possible values(with labels) for instance.
     *
     * @return array
     */
    public static function getInstances()
    {
        return [
            self::INSTANCE_FIRST => 'oro.calendar.recurrence.instances.first',
            self::INSTANCE_SECOND => 'oro.calendar.recurrence.instances.second',
            self::INSTANCE_THIRD => 'oro.calendar.recurrence.instances.third',
            self::INSTANCE_FOURTH => 'oro.calendar.recurrence.instances.fourth',
            self::INSTANCE_LAST => 'oro.calendar.recurrence.instances.last',
        ];
    }

    /**
     * Returns the list of possible values(with labels) for dayOfWeek.
     *
     * @return array
     */
    public static function getDaysOfWeek()
    {
        return [
            self::DAY_SUNDAY => 'oro.calendar.recurrence.days.sunday',
            self::DAY_MONDAY => 'oro.calendar.recurrence.days.monday',
            self::DAY_TUESDAY => 'oro.calendar.recurrence.days.tuesday',
            self::DAY_WEDNESDAY => 'oro.calendar.recurrence.days.wednesday',
            self::DAY_THURSDAY => 'oro.calendar.recurrence.days.thursday',
            self::DAY_FRIDAY => 'oro.calendar.recurrence.days.friday',
            self::DAY_SATURDAY => 'oro.calendar.recurrence.days.saturday',
        ];
    }
}
