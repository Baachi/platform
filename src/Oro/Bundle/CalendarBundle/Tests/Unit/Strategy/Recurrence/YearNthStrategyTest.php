<?php

namespace Oro\Bundle\CalendarBundle\Tests\Unit\Model\Recurrence;

use Oro\Bundle\CalendarBundle\Entity\Recurrence;
use Oro\Bundle\CalendarBundle\Strategy\Recurrence\Helper\StrategyHelper;
use Oro\Bundle\CalendarBundle\Strategy\Recurrence\YearNthStrategy;

class YearNthStrategyTest extends \PHPUnit_Framework_TestCase
{
    /** @var YearNthStrategy  */
    protected $strategy;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $validator;

    protected function setUp()
    {
        $this->validator = $this->getMockBuilder('Symfony\Component\Validator\Validator\ValidatorInterface')
            ->getMock();
        $helper = new StrategyHelper($this->validator);
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Translation\TranslatorInterface */
        $translator = $this->getMock('Symfony\Component\Translation\TranslatorInterface');
        $translator->expects($this->any())
            ->method('transChoice')
            ->will(
                $this->returnCallback(
                    function ($id, $count, array $parameters = []) {
                        return $id;
                    }
                )
            );
        $translator->expects($this->any())
            ->method('trans')
            ->will(
                $this->returnCallback(
                    function ($id) {
                        return $id;
                    }
                )
            );
        $dateTimeFormatter = $this->getMockBuilder('Oro\Bundle\LocaleBundle\Formatter\DateTimeFormatter')
            ->disableOriginalConstructor()
            ->getMock();
        $this->strategy = new YearNthStrategy($helper, $translator, $dateTimeFormatter);
    }

    public function testGetName()
    {
        $this->assertEquals($this->strategy->getName(), 'recurrence_yearnth');
    }

    public function testSupports()
    {
        $recurrence = new Recurrence();
        $recurrence->setRecurrenceType(Recurrence::TYPE_YEAR_N_TH);
        $this->assertTrue($this->strategy->supports($recurrence));

        $recurrence->setRecurrenceType('Test');
        $this->assertFalse($this->strategy->supports($recurrence));
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
        $recurrence->setRecurrenceType(Recurrence::TYPE_YEAR_N_TH)
            ->setInstance($params['instance'])
            ->setInterval($params['interval'])
            ->setMonthOfYear($params['monthOfYear'])
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
     * @param $recurrenceData
     * @param $expected
     *
     * @dataProvider recurrencePatternsDataProvider
     */
    public function testGetRecurrencePattern($recurrenceData, $expected)
    {
        $recurrence = new Recurrence();
        $recurrence->setRecurrenceType(Recurrence::TYPE_YEAR_N_TH)
            ->setInterval($recurrenceData['interval'])
            ->setInstance($recurrenceData['instance'])
            ->setDayOfWeek($recurrenceData['dayOfWeek'])
            ->setMonthOfYear($recurrenceData['monthOfYear'])
            ->setStartTime(new \DateTime($recurrenceData['startTime']))
            ->setEndTime(new \DateTime($recurrenceData['endTime']))
            ->setOccurrences($recurrenceData['occurrences']);

        $this->assertEquals($expected, $this->strategy->getRecurrencePattern($recurrence));
    }

    /**
     * @param $recurrenceData
     * @param $expected
     *
     * @dataProvider recurrenceLastOccurrenceDataProvider
     */
    public function testGetCalculatedEndTime($recurrenceData, $expected)
    {
        $recurrence = new Recurrence();
        $recurrence->setRecurrenceType(Recurrence::TYPE_YEAR_N_TH)
            ->setInterval($recurrenceData['interval'])
            ->setInstance($recurrenceData['instance'])
            ->setDayOfWeek($recurrenceData['dayOfWeek'])
            ->setMonthOfYear($recurrenceData['monthOfYear'])
            ->setStartTime(new \DateTime($recurrenceData['startTime']))
            ->setOccurrences($recurrenceData['occurrences']);

        if (!empty($recurrenceData['endTime'])) {
            $recurrence->setEndTime(new \DateTime($recurrenceData['endTime']));
        }

        $this->assertEquals($expected, $this->strategy->getCalculatedEndTime($recurrence));
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
                    'instance' => Recurrence::INSTANCE_FIRST,
                    'interval' => 12, // a number of months, which is a multiple of 12
                    'monthOfYear' => 4,
                    'daysOfWeek' => [
                        'monday',
                    ],
                    'occurrences' => null,
                    'start' => '2015-03-28',
                    'end' => '2016-04-01',
                    'startTime' => '2016-04-25',
                    'endTime' => '2020-06-10',
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
                    'instance' => Recurrence::INSTANCE_FIRST,
                    'interval' => 12, // a number of months, which is a multiple of 12
                    'monthOfYear' => 4,
                    'daysOfWeek' => [
                        'monday',
                    ],
                    'occurrences' => null,
                    'start' => '2015-03-01',
                    'end' => '2018-03-01',
                    'startTime' => '2016-04-25',
                    'endTime' => '2020-03-01',
                ],
                'expected' => [
                    '2017-04-03',
                ],
            ],
            /**
             * |-----|
             *   |-|
             */
            'start < startTime < endTime < end' => [
                'params' => [
                    'instance' => Recurrence::INSTANCE_FIRST,
                    'interval' => 12, // a number of months, which is a multiple of 12
                    'monthOfYear' => 4,
                    'daysOfWeek' => [
                        'monday',
                    ],
                    'occurrences' => null,
                    'start' => '2015-03-01',
                    'end' => '2020-03-01',
                    'startTime' => '2016-04-25',
                    'endTime' => '2018-03-01',
                ],
                'expected' => [
                    '2017-04-03',
                ],
            ],
            /**
             *     |-----|
             * |-----|
             */
            'startTime < start < endTime < end' => [
                'params' => [
                    'instance' => Recurrence::INSTANCE_FIRST,
                    'interval' => 12, // a number of months, which is a multiple of 12
                    'monthOfYear' => 4,
                    'daysOfWeek' => [
                        'monday',
                    ],
                    'occurrences' => null,
                    'start' => '2017-03-01',
                    'end' => '2019-08-01',
                    'startTime' => '2016-04-25',
                    'endTime' => '2018-08-01',
                ],
                'expected' => [
                    '2017-04-03',
                    '2018-04-02',
                ],
            ],
            /**
             *         |-----|
             * |-----|
             */
            'startTime < endTime < start < end' => [
                'params' => [
                    'instance' => Recurrence::INSTANCE_FIRST,
                    'interval' => 12, // a number of months, which is a multiple of 12
                    'monthOfYear' => 4,
                    'daysOfWeek' => [
                        'monday',
                    ],
                    'occurrences' => null,
                    'start' => '2022-03-28',
                    'end' => '2022-05-01',
                    'startTime' => '2016-04-25',
                    'endTime' => '2020-06-10',
                ],
                'expected' => [
                ],
            ],

            'start < startTime < end < endTime with no result' => [
                'params' => [
                    'instance' => Recurrence::INSTANCE_FIRST,
                    'interval' => 12, // a number of months, which is a multiple of 12
                    'monthOfYear' => 4,
                    'daysOfWeek' => [
                        'monday',
                    ],
                    'occurrences' => null,
                    'start' => '2016-03-28',
                    'end' => '2016-05-01',
                    'startTime' => '2016-04-25',
                    'endTime' => '2020-06-10',
                ],
                'expected' => [
                ],
            ],
            'startTime < start < end < endTime with X occurrences' => [
                'params' => [
                    'instance' => Recurrence::INSTANCE_LAST,
                    'interval' => 12, // a number of months, which is a multiple of 12
                    'monthOfYear' => 4,
                    'daysOfWeek' => [
                        'monday',
                    ],
                    'occurrences' => 2,
                    'start' => '2018-03-28',
                    'end' => '2022-05-01',
                    'startTime' => '2016-04-25',
                    'endTime' => '2022-12-31',
                ],
                'expected' => [
                ],
            ],
            'startTime < start < end < endTime with Y occurrences' => [
                'params' => [
                    'instance' => Recurrence::INSTANCE_LAST,
                    'interval' => 12, // a number of months, which is a multiple of 12
                    'monthOfYear' => 4,
                    'daysOfWeek' => [
                        'monday',
                    ],
                    'occurrences' => 2,
                    'start' => '2017-03-28',
                    'end' => '2017-05-01',
                    'startTime' => '2016-04-25',
                    'endTime' => '2022-12-31',
                ],
                'expected' => [
                    '2017-04-24',
                ],
            ],
            'start < startTime < end < endTime with last instance' => [
                'params' => [
                    'instance' => Recurrence::INSTANCE_LAST,
                    'interval' => 12, // a number of months, which is a multiple of 12
                    'monthOfYear' => 4,
                    'daysOfWeek' => [
                        'monday',
                    ],
                    'occurrences' => null,
                    'start' => '2016-03-28',
                    'end' => '2016-05-01',
                    'startTime' => '2016-04-25',
                    'endTime' => '2020-06-10',
                ],
                'expected' => [
                    '2016-04-25',
                ],
            ],
            'startTime < start < end < endTime' => [
                'params' => [
                    'instance' => Recurrence::INSTANCE_FIRST,
                    'interval' => 12, // a number of months, which is a multiple of 12
                    'monthOfYear' => 4,
                    'daysOfWeek' => [
                        'monday',
                    ],
                    'occurrences' => null,
                    'start' => '2017-03-28',
                    'end' => '2017-05-01',
                    'startTime' => '2016-04-25',
                    'endTime' => '2020-06-10',
                ],
                'expected' => [
                    '2017-04-03',
                ],
            ],
            'with_weekend_day' => [
                'params' => [
                    'instance' => Recurrence::INSTANCE_FOURTH,
                    'interval' => 12, // a number of months, which is a multiple of 12
                    'monthOfYear' => 4,
                    'daysOfWeek' => [
                        'sunday',
                        'saturday'
                    ],
                    'occurrences' => null,
                    'start' => '2017-03-28',
                    'end' => '2018-05-01',
                    'startTime' => '2016-04-25',
                    'endTime' => '2020-06-10',
                ],
                'expected' => [
                    '2017-04-09',
                    '2018-04-14',
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function recurrencePatternsDataProvider()
    {
        return [
            'without_occurrences_and_end_date' => [
                'params' => [
                    'interval' => 2,
                    'instance' => 3,
                    'dayOfWeek' => ['saturday'],
                    'monthOfYear' => 6,
                    'startTime' => '2016-04-28',
                    'endTime' => Recurrence::MAX_END_DATE,
                    'occurrences' => null,
                ],
                'expected' => 'oro.calendar.recurrence.patterns.yearnth'
            ],
            'with_occurrences' => [
                'params' => [
                    'interval' => 2,
                    'instance' => 3,
                    'dayOfWeek' => ['saturday'],
                    'monthOfYear' => 6,
                    'startTime' => '2016-04-28',
                    'endTime' => Recurrence::MAX_END_DATE,
                    'occurrences' => 3,
                ],
                'expected' => 'oro.calendar.recurrence.patterns.yearnthoro.calendar.recurrence.patterns.occurrences'
            ],
            'with_end_date' => [
                'params' => [
                    'interval' => 2,
                    'instance' => 3,
                    'dayOfWeek' => ['saturday'],
                    'monthOfYear' => 6,
                    'startTime' => '2016-04-28',
                    'endTime' => '2016-06-10',
                    'occurrences' => null,
                ],
                'expected' => 'oro.calendar.recurrence.patterns.yearnthoro.calendar.recurrence.patterns.end_date'
            ]
        ];
    }

    /**
     * @return array
     */
    public function recurrenceLastOccurrenceDataProvider()
    {
        return [
            'without_end_date' => [
                'params' => [
                    'interval' => 12,
                    'instance' => 3,
                    'dayOfWeek' => ['saturday'],
                    'monthOfYear' => 6,
                    'startTime' => '2016-04-28',
                    'endTime' => null,
                    'occurrences' => null,
                ],
                'expected' => new \DateTime(Recurrence::MAX_END_DATE)
            ],
            'with_end_date' => [
                'params' => [
                    'interval' => 12,
                    'instance' => 3,
                    'dayOfWeek' => ['saturday'],
                    'monthOfYear' => 6,
                    'startTime' => '2016-04-28',
                    'endTime' => '2016-05-12',
                    'occurrences' => null,
                ],
                'expected' => new \DateTime('2016-05-12')
            ],
            'with_occurrences' => [
                'params' => [
                    'interval' => 24,
                    'instance' => 2,
                    'dayOfWeek' => ['monday'],
                    'monthOfYear' => 6,
                    'startTime' => '2016-04-28',
                    'endTime' => null,
                    'occurrences' => 3,
                ],
                'expected' => new \DateTime('2020-06-08')
            ],
            'with_occurrences_1' => [
                'params' => [
                    'interval' => 24,
                    'instance' => 3,
                    'dayOfWeek' => ['sunday'],
                    'monthOfYear' => 3,
                    'startTime' => '2016-04-28',
                    'endTime' => null,
                    'occurrences' => 3,
                ],
                'expected' => new \DateTime('2022-03-20')
            ],
            'with_occurrences_2' => [
                'params' => [
                    'interval' => 24,
                    'instance' => 3,
                    'dayOfWeek' => ['saturday', 'sunday'],
                    'monthOfYear' => 6,
                    'startTime' => '2016-04-28',
                    'endTime' => null,
                    'occurrences' => 3,
                ],
                'expected' => new \DateTime('2020-06-13')
            ]
        ];
    }
}
