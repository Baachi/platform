<?php

namespace Oro\Bundle\CalendarBundle\Tests\Unit\Model\Recurrence;

use Oro\Bundle\CalendarBundle\Entity\Recurrence;
use Oro\Bundle\CalendarBundle\Strategy\Recurrence\MonthNthStrategy;
use Oro\Bundle\CalendarBundle\Strategy\Recurrence\Helper\StrategyHelper;

class MonthNthStrategyTest extends \PHPUnit_Framework_TestCase
{
    /** @var MonthNthStrategy  */
    protected $strategy;

    protected function setUp()
    {
        $helper = new StrategyHelper();
        /** @var \PHPUnit_Framework_MockObject_MockObject|TranslatorInterface */
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

        $this->strategy = new MonthNthStrategy($helper, $translator, $dateTimeFormatter);
    }

    public function testGetName()
    {
        $this->assertEquals($this->strategy->getName(), 'recurrence_monthnth');
    }

    public function testSupports()
    {
        $recurrence = new Recurrence();
        $recurrence->setRecurrenceType(Recurrence::TYPE_MONTH_N_TH);
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
        $recurrence->setRecurrenceType(Recurrence::TYPE_MONTH_N_TH)
            ->setInterval($params['interval'])
            ->setDayOfWeek($params['daysOfWeek'])
            ->setInstance($params['instance'])
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
        $recurrence->setRecurrenceType(Recurrence::TYPE_MONTH_N_TH)
            ->setInterval($recurrenceData['interval'])
            ->setInstance($recurrenceData['instance'])
            ->setDayOfWeek($recurrenceData['dayOfWeek'])
            ->setStartTime(new \DateTime($recurrenceData['startTime']))
            ->setEndTime(new \DateTime($recurrenceData['endTime']))
            ->setOccurrences($recurrenceData['occurrences']);

        $this->assertEquals($expected, $this->strategy->getRecurrencePattern($recurrence));
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
                        'monday',
                    ],
                    'instance' => Recurrence::INSTANCE_FIRST,
                    'interval' => 2,
                    'occurrences' => null,
                    'start' => '2016-02-01',
                    'end' => '2016-04-01',
                    'startTime' => '2016-04-25',
                    'endTime' => '2016-08-01',
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
                        'monday',
                    ],
                    'instance' => Recurrence::INSTANCE_FIRST,
                    'interval' => 2,
                    'occurrences' => null,
                    'start' => '2016-03-28',
                    'end' => '2016-05-01',
                    'startTime' => '2016-04-25',
                    'endTime' => '2016-06-10',
                ],
                'expected' => [
                ],
            ],
            /**
             * |-----|
             *   |-|
             */
            'start < startTime < endTime < end' => [
                'params' => [
                    'daysOfWeek' => [
                        'monday',
                    ],
                    'instance' => Recurrence::INSTANCE_FIRST,
                    'interval' => 2,
                    'occurrences' => null,
                    'start' => '2016-04-01',
                    'end' => '2016-09-01',
                    'startTime' => '2016-04-25',
                    'endTime' => '2016-08-01',
                ],
                'expected' => [
                    '2016-06-06',
                    '2016-08-01',
                ],
            ],
            /**
             *     |-----|
             * |-----|
             */
            'startTime < start < endTime < end' => [
                'params' => [
                    'daysOfWeek' => [
                        'monday',
                    ],
                    'instance' => Recurrence::INSTANCE_FIRST,
                    'interval' => 2,
                    'occurrences' => null,
                    'start' => '2016-05-30',
                    'end' => '2016-07-03',
                    'startTime' => '2016-04-25',
                    'endTime' => '2016-06-10',
                ],
                'expected' => [
                    '2016-06-06',
                ],
            ],
            /**
             *         |-----|
             * |-----|
             */
            'startTime < endTime < start < end' => [
                'params' => [
                    'daysOfWeek' => [
                        'monday',
                    ],
                    'instance' => Recurrence::INSTANCE_FIRST,
                    'interval' => 2,
                    'occurrences' => null,
                    'start' => '2016-09-01',
                    'end' => '2016-11-01',
                    'startTime' => '2016-04-25',
                    'endTime' => '2016-08-01',
                ],
                'expected' => [
                ],
            ],

            'start < startTime < end < endTime with X instance' => [
                'params' => [
                    'daysOfWeek' => [
                        'monday',
                    ],
                    'instance' => Recurrence::INSTANCE_LAST,
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
            'start < startTime < end < endTime with X occurrence' => [
                'params' => [
                    'daysOfWeek' => [
                        'monday',
                    ],
                    'instance' => Recurrence::INSTANCE_LAST,
                    'interval' => 2,
                    'occurrences' => 2,
                    'start' => '2016-07-25',
                    'end' => '2016-09-04',
                    'startTime' => '2016-04-25',
                    'endTime' => '2016-12-31',
                ],
                'expected' => [
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
                    'dayOfWeek' => ['sunday'],
                    'startTime' => '2016-04-28',
                    'endTime' => Recurrence::MAX_END_DATE,
                    'occurrences' => null,
                ],
                'expected' => 'oro.calendar.recurrence.patterns.monthnth'
            ],
            'with_occurrences' => [
                'params' => [
                    'interval' => 2,
                    'instance' => 3,
                    'dayOfWeek' => ['sunday'],
                    'startTime' => '2016-04-28',
                    'endTime' => Recurrence::MAX_END_DATE,
                    'occurrences' => 3,
                ],
                'expected' => 'oro.calendar.recurrence.patterns.monthnthoro.calendar.recurrence.patterns.occurrences'
            ],
            'with_end_date' => [
                'params' => [
                    'interval' => 2,
                    'instance' => 3,
                    'dayOfWeek' => ['sunday'],
                    'startTime' => '2016-04-28',
                    'endTime' => '2016-06-10',
                    'occurrences' => null,
                ],
                'expected' => 'oro.calendar.recurrence.patterns.monthnthoro.calendar.recurrence.patterns.end_date'
            ]
        ];
    }
}
