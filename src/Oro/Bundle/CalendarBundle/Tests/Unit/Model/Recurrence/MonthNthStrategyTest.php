<?php

namespace Oro\Bundle\CalendarBundle\Tests\Unit\Model\Recurrence;

use Oro\Bundle\CalendarBundle\Entity\Recurrence;
use Oro\Bundle\CalendarBundle\Model\Recurrence\MonthNthStrategy;
use Oro\Bundle\CalendarBundle\Tools\Recurrence\NthStrategyHelper;

class MonthNthStrategyTest extends \PHPUnit_Framework_TestCase
{
    /** @var MonthNthStrategy  */
    protected $strategy;

    protected function setUp()
    {
        $helper = new NthStrategyHelper();
        $this->strategy = new MonthNthStrategy($helper);
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

    public function testGetOccurrences()
    {
        $recurrence = new Recurrence();
        $recurrence->setRecurrenceType(Recurrence::TYPE_MONTH_N_TH)
            ->setInterval(2)
            ->setDayOfWeek(['monday'])
            ->setInstance(Recurrence::INSTANCE_FIRST)
            ->setStartTime(new \DateTime('2016-04-25'))
            ->setEndTime(new \DateTime('2016-06-10'));

        $result = $this->strategy->getOccurrences(
            $recurrence,
            new \DateTime('2016-03-28'),
            new \DateTime('2016-05-01')
        );

        $this->assertEquals([], $result);

        $result = $this->strategy->getOccurrences(
            $recurrence,
            new \DateTime('2016-05-30'),
            new \DateTime('2016-07-03')
        );

        $this->assertEquals([new \DateTime('2016-06-06')], $result);

        $recurrence->setInstance(Recurrence::INSTANCE_LAST);
        $result = $this->strategy->getOccurrences(
            $recurrence,
            new \DateTime('2016-03-28'),
            new \DateTime('2016-05-01')
        );
        $this->assertEquals([new \DateTime('2016-04-25')], $result);

        $recurrence->setOccurrences(2);
        $recurrence->setEndTime(new \DateTime('2016-12-31'));
        $result = $this->strategy->getOccurrences(
            $recurrence,
            new \DateTime('2016-07-25'),
            new \DateTime('2016-09-04')
        );
        $this->assertEquals([], $result);
    }
}
