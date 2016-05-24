<?php

namespace Oro\Bundle\CalendarBundle\Tests\Unit\Model\Recurrence;

use Symfony\Component\Translation\TranslatorInterface;

use Oro\Bundle\CalendarBundle\Entity;
use Oro\Bundle\CalendarBundle\Model\Recurrence;
use Oro\Bundle\CalendarBundle\Model\Recurrence\DailyStrategy;

class DailyStrategyTest extends AbstractTestStrategy
{
    /** @var DailyStrategy */
    protected $strategy;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $validator;

    protected function setUp()
    {
        $this->validator = $this->getMockBuilder('Symfony\Component\Validator\Validator\ValidatorInterface')
            ->getMock();
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

        $this->strategy = new DailyStrategy($translator, $dateTimeFormatter);
    }

    public function testGetName()
    {
        $this->assertEquals($this->strategy->getName(), 'recurrence_daily');
    }

    public function testSupports()
    {
        $recurrence = new Entity\Recurrence();
        $recurrence->setRecurrenceType(Recurrence::TYPE_DAILY);
        $this->assertTrue($this->strategy->supports($recurrence));

        $recurrence->setRecurrenceType('Test');
        $this->assertFalse($this->strategy->supports($recurrence));
    }

    /**
     * @param $recurrenceData
     * @param $expected
     *
     * @dataProvider recurrencePatternsDataProvider
     */
    public function testGetTextValue($recurrenceData, $expected)
    {
        $recurrence = new Entity\Recurrence();
        $recurrence->setRecurrenceType(Recurrence::TYPE_DAILY)
            ->setInterval($recurrenceData['interval'])
            ->setStartTime(new \DateTime($recurrenceData['startTime']))
            ->setEndTime($recurrenceData['endTime'] === null ? null : new \DateTime($recurrenceData['endTime']))
            ->setOccurrences($recurrenceData['occurrences']);

        $this->assertEquals($expected, $this->strategy->getTextValue($recurrence));
    }

    /**
     * @param $recurrenceData
     * @param $expected
     *
     * @dataProvider recurrenceLastOccurrenceDataProvider
     */
    public function testGetCalculatedEndTime($recurrenceData, $expected)
    {
        $recurrence = new Entity\Recurrence();
        $recurrence->setRecurrenceType(Recurrence::TYPE_DAILY)
            ->setInterval($recurrenceData['interval'])
            ->setStartTime(new \DateTime($recurrenceData['startTime']))
            ->setOccurrences($recurrenceData['occurrences']);

        if (!empty($recurrenceData['endTime'])) {
            $recurrence->setEndTime(new \DateTime($recurrenceData['endTime']));
        }

        $this->assertEquals($expected, $this->strategy->getCalculatedEndTime($recurrence));
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
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
                    'interval' => 5,
                    'occurrences' => null,
                    'start' => '2016-03-28',
                    'end' => '2016-04-17',
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
                    'interval' => 5,
                    'occurrences' => null,
                    'start' => '2016-03-28',
                    'end' => '2016-05-01',
                    'startTime' => '2016-04-25',
                    'endTime' => '2016-06-10',
                ],
                'expected' => [
                    '2016-04-25',
                    '2016-04-30',
                ],
            ],
            /**
             * |-----|
             *   |-|
             */
            'start < startTime < endTime < end' => [
                'params' => [
                    'interval' => 5,
                    'occurrences' => null,
                    'start' => '2016-05-30',
                    'end' => '2016-07-03',
                    'startTime' => '2016-04-25',
                    'endTime' => '2016-06-10',
                ],
                'expected' => [
                    '2016-05-30',
                    '2016-06-04',
                    '2016-06-09',
                ],
            ],
            /**
             *     |-----|
             * |-----|
             */
            'startTime < start < endTime < end' => [
                'params' => [
                    'interval' => 5,
                    'occurrences' => null,
                    'start' => '2016-04-30',
                    'end' => '2016-05-30',
                    'startTime' => '2016-04-25',
                    'endTime' => '2016-05-10',
                ],
                'expected' => [
                    '2016-04-30',
                    '2016-05-05',
                    '2016-05-10',
                ],
            ],
            /**
             *         |-----|
             * |-----|
             */
            'startTime < endTime < start < end' => [
                'params' => [
                    'interval' => 5,
                    'occurrences' => null,
                    'start' => '2016-06-11',
                    'end' => '2016-07-03',
                    'startTime' => '2016-04-25',
                    'endTime' => '2016-06-10',
                ],
                'expected' => [
                ],
            ],

            'start = end = startTime = endTime' => [
                'params' => [
                    'interval' => 5,
                    'occurrences' => null,
                    'start' => '2016-04-24',
                    'end' => '2016-04-24',
                    'startTime' => '2016-04-24',
                    'endTime' => '2016-04-24',
                ],
                'expected' => [
                    '2016-04-24',
                ],
            ],
            'start = end = (startTime + 1 day) = (endTime + 1 day)' => [
                'params' => [
                    'interval' => 5,
                    'occurrences' => null,
                    'start' => '2016-04-24',
                    'end' => '2016-04-24',
                    'startTime' => '2016-04-25',
                    'endTime' => '2016-04-25',
                ],
                'expected' => [
                ],
            ],
            'startTime = endTime = (start + interval days) = (end + interval days)' => [
                'params' => [
                    'interval' => 5,
                    'occurrences' => null,
                    'start' => '2016-04-30',
                    'end' => '2016-04-30',
                    'startTime' => '2016-04-25',
                    'endTime' => '2016-04-25',
                ],
                'expected' => [
                ],
            ],
            'start < startTime < endTime < end after x occurrences' => [
                'params' => [
                    'interval' => 5,
                    'occurrences' => 8,
                    'start' => '2016-05-30',
                    'end' => '2016-07-03',
                    'startTime' => '2016-04-25',
                    'endTime' => '2016-06-10',
                ],
                'expected' => [
                    '2016-05-30',
                ],
            ],
            'start < startTime < endTime < end after y occurrences' => [
                'params' => [
                    'interval' => 5,
                    'occurrences' => 7,
                    'start' => '2016-05-30',
                    'end' => '2016-07-03',
                    'startTime' => '2016-04-25',
                    'endTime' => '2016-06-10',
                ],
                'expected' => [
                ],
            ],
            'no endTime' => [
                'params' => [
                    'interval' => 5,
                    'occurrences' => 8,
                    'start' => '2016-05-30',
                    'end' => '2016-07-03',
                    'startTime' => '2016-04-25',
                    'endTime' => '9999-12-31',
                ],
                'expected' => [
                    '2016-05-30',
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
                    'startTime' => '2016-04-28',
                    'endTime' => null,
                    'occurrences' => null,
                ],
                'expected' => 'oro.calendar.recurrence.patterns.daily'
            ],
            'with_occurrences' => [
                'params' => [
                    'interval' => 2,
                    'startTime' => '2016-04-28',
                    'endTime' => null,
                    'occurrences' => 3,
                ],
                'expected' => 'oro.calendar.recurrence.patterns.dailyoro.calendar.recurrence.patterns.occurrences'
            ],
            'with_end_date' => [
                'params' => [
                    'interval' => 2,
                    'startTime' => '2016-04-28',
                    'endTime' => '2016-06-10',
                    'occurrences' => null,
                ],
                'expected' => 'oro.calendar.recurrence.patterns.dailyoro.calendar.recurrence.patterns.end_date'
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
                    'interval' => 2,
                    'startTime' => '2016-04-28',
                    'endTime' => null,
                    'occurrences' => null,
                ],
                'expected' => new \DateTime(Recurrence::MAX_END_DATE)
            ],
            'with_end_date' => [
                'params' => [
                    'interval' => 2,
                    'startTime' => '2016-04-28',
                    'endTime' => '2016-05-12',
                    'occurrences' => null,
                ],
                'expected' => new \DateTime('2016-05-12')
            ],
            'with_occurrences' => [
                'params' => [
                    'interval' => 2,
                    'startTime' => '2016-04-28',
                    'endTime' => null,
                    'occurrences' => 5,
                ],
                'expected' => new \DateTime('2016-05-06')
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getType()
    {
        return Recurrence::TYPE_DAILY;
    }
}
