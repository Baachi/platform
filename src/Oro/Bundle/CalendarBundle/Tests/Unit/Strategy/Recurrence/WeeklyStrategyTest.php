<?php

namespace Oro\Bundle\CalendarBundle\Tests\Unit\Model\Recurrence;

use Oro\Bundle\CalendarBundle\Entity\Recurrence;
use Oro\Bundle\CalendarBundle\Strategy\Recurrence\Helper\StrategyHelper;
use Oro\Bundle\CalendarBundle\Strategy\Recurrence\WeeklyStrategy;

class WeeklyStrategyTest extends \PHPUnit_Framework_TestCase
{
    /** @var WeeklyStrategy  */
    protected $strategy;

    protected function setUp()
    {
        $helper = new StrategyHelper();
        $this->strategy = new WeeklyStrategy($helper);
    }

    public function testGetName()
    {
        $this->assertEquals($this->strategy->getName(), 'recurrence_weekly');
    }

    public function testSupports()
    {
        $recurrence = new Recurrence();
        $recurrence->setRecurrenceType(Recurrence::TYPE_WEEKLY);
        $this->assertTrue($this->strategy->supports($recurrence));

        $recurrence->setRecurrenceType('Test');
        $this->assertFalse($this->strategy->supports($recurrence));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetOccurrencesWithWrongIntervalValue()
    {
        $recurrence = new Recurrence();
        $recurrence->setDayOfWeek([
            'sunday',
            'monday',
        ]);
        $recurrence->setInterval(-1.5);
        $this->strategy->getOccurrences(
            $recurrence,
            new \DateTime(),
            new \DateTime()
        );
    }

    /**
     * @param array $params
     * @param array $expected
     *
     * @dataProvider propertiesDataProvider
     */
    public function testGetOccurrences(array $params, array $expected)
    {
        // @TODO move method body to abstract test class
        $expected = array_map(
            function ($date) {
                return new \DateTime($date);
            }, $expected
        );
        $recurrence = new Recurrence();
        $recurrence->setRecurrenceType(Recurrence::TYPE_WEEKLY)
            ->setInterval($params['interval'])
            ->setDayOfWeek($params['daysOfWeek'])
            ->setStartTime(new \DateTime($params['startTime']))
            ->setEndTime(new \DateTime($params['endTime']));
        if ($params['occurrences']) {
            $recurrence->setOccurrences($params['occurrences']);
        }
        $result = $this->strategy->getOccurrences(
            $recurrence,
            new \DateTime($params['start']),
            new \DateTime($params['end'])
        );
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function propertiesDataProvider()
    {
        return [
            /**
             * |-----|
             *         |-----|
             */
            'start < end < startTime < endTime' => [
                'params' => [
                    'daysOfWeek' => [
                        'sunday',
                        'monday',
                    ],
                    'interval' => 2,
                    'occurrences' => null,
                    'start' => '2016-03-28',
                    'end' => '2016-04-18',
                    'startTime' => '2016-04-25',
                    'endTime' => '2016-06-10',
                ],
                'expected' => [
                ],
            ],
            /**
             * |-----|
             *   |-----|
             */
            'start < startTime < end < endTime' => [
                'params' => [
                    'daysOfWeek' => [
                        'sunday',
                        'monday',
                    ],
                    'interval' => 2,
                    'occurrences' => null,
                    'start' => '2016-03-28',
                    'end' => '2016-05-01',
                    'startTime' => '2016-04-25',
                    'endTime' => '2016-06-10',
                ],
                'expected' => [
                    '2016-04-25',
                ],
            ],
            /**
             * |-----|
             *   |-|
             */
            'start < startTime < endTime < end' => [
                'params' => [
                    'daysOfWeek' => [
                        'sunday',
                        'monday',
                    ],
                    'interval' => 2,
                    'occurrences' => null,
                    'start' => '2016-03-28',
                    'end' => '2016-07-20',
                    'startTime' => '2016-04-25',
                    'endTime' => '2016-06-13',
                ],
                'expected' => [
                    '2016-04-25',
                    '2016-05-08',
                    '2016-05-09',
                    '2016-05-22',
                    '2016-05-23',
                    '2016-06-05',
                    '2016-06-06',
                ],
            ],
            /**
             *     |-----|
             * |-----|
             */
            'startTime < start < endTime < end after x occurrences' => [
                'params' => [
                    'daysOfWeek' => [
                        'sunday',
                        'monday',
                    ],
                    'interval' => 2,
                    'occurrences' => 4,
                    'start' => '2016-05-01',
                    'end' => '2016-07-03',
                    'startTime' => '2016-04-25',
                    'endTime' => '2016-06-10',
                ],
                'expected' => [
                    '2016-05-08',
                    '2016-05-09',
                    '2016-05-22',
                ],
            ],
            /**
             *         |-----|
             * |-----|
             */
            'startTime < endTime < start < end' => [
                'params' => [
                    'daysOfWeek' => [
                        'sunday',
                        'monday',
                    ],
                    'interval' => 2,
                    'occurrences' => null,
                    'start' => '2016-06-12',
                    'end' => '2016-07-20',
                    'startTime' => '2016-04-25',
                    'endTime' => '2016-06-10',
                ],
                'expected' => [
                ],
            ],

            'start = end = startTime = endTime' => [
                'params' => [
                    'daysOfWeek' => [
                        'sunday',
                        'monday',
                    ],
                    'interval' => 2,
                    'occurrences' => null,
                    'start' => '2016-04-25',
                    'end' => '2016-04-25',
                    'startTime' => '2016-04-25',
                    'endTime' => '2016-04-25',
                ],
                'expected' => [
                    '2016-04-25',
                ],
            ],
            'start = end = (startTime - 1 day) = (endTime - 1 day)' => [
                'params' => [
                    'daysOfWeek' => [
                        'sunday',
                        'monday',
                    ],
                    'interval' => 2,
                    'occurrences' => null,
                    'start' => '2016-04-25',
                    'end' => '2016-04-25',
                    'startTime' => '2016-04-24',
                    'endTime' => '2016-04-24',
                ],
                'expected' => [
                ],
            ],
            'startTime = endTime = (start + interval) = (end + interval)' => [
                'params' => [
                    'daysOfWeek' => [
                        'sunday',
                        'monday',
                    ],
                    'interval' => 2,
                    'occurrences' => null,
                    'start' => '2016-04-25',
                    'end' => '2016-04-25',
                    'startTime' => '2016-05-08',
                    'endTime' => '2016-0-08',
                ],
                'expected' => [
                ],
            ],
            'startTime < start < end < endTime' => [
                'params' => [
                    'daysOfWeek' => [
                        'sunday',
                        'monday',
                    ],
                    'interval' => 2,
                    'occurrences' => null,
                    'start' => '2016-05-01',
                    'end' => '2016-05-27',
                    'startTime' => '2016-04-25',
                    'endTime' => '2016-06-10',
                ],
                'expected' => [
                    '2016-05-08',
                    '2016-05-09',
                    '2016-05-22',
                    '2016-05-23',
                ],
            ],
            'startTime < start < end < endTime after x occurrences' => [
                'params' => [
                    'daysOfWeek' => [
                        'sunday',
                        'monday',
                    ],
                    'interval' => 2,
                    'occurrences' => 4,
                    'start' => '2016-05-01',
                    'end' => '2016-05-27',
                    'startTime' => '2016-04-25',
                    'endTime' => '2016-06-10',
                ],
                'expected' => [
                    '2016-05-08',
                    '2016-05-09',
                    '2016-05-22',
                ],
            ],
            'no endTime' => [
                'params' => [
                    'daysOfWeek' => [
                        'sunday',
                        'monday',
                    ],
                    'interval' => 2,
                    'occurrences' => 4,
                    'start' => '2016-05-01',
                    'end' => '2016-07-03',
                    'startTime' => '2016-04-25',
                    'endTime' => '9999-12-31',
                ],
                'expected' => [
                    '2016-05-08',
                    '2016-05-09',
                    '2016-05-22',
                ],
            ],
        ];
    }
}
