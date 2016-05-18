<?php

namespace Oro\Bundle\CalendarBundle\Tests\Unit\Twig;

use Oro\Bundle\CalendarBundle\Entity;
use Oro\Bundle\CalendarBundle\Model\Recurrence;
use Oro\Bundle\CalendarBundle\Twig\RecurrenceExtension;

class RecurrenceExtensionTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|\Oro\Bundle\CalendarBundle\Model\Recurrence\DelegateStrategy */
    protected $delegateStrategy;

    /** @var \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Translation\TranslatorInterface */
    protected $translator;

    /** @var RecurrenceExtension */
    protected $extension;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $validator;

    protected function setUp()
    {
        $this->delegateStrategy = $this->getMockBuilder('Oro\Bundle\CalendarBundle\Model\Recurrence\DelegateStrategy')
            ->disableOriginalConstructor()
            ->getMock();
        $this->translator = $this->getMockBuilder('Symfony\Component\Translation\TranslatorInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->validator = $this->getMockBuilder('Symfony\Component\Validator\Validator\ValidatorInterface')
            ->getMock();
        $model = new Recurrence($this->validator);
        $this->extension = new RecurrenceExtension($this->delegateStrategy, $this->translator, $model);
    }

    public function testGetName()
    {
        $this->assertEquals('oro_recurrence', $this->extension->getName());
    }

    public function testGetRecurrencePattern()
    {
        $this->delegateStrategy->expects($this->once())
            ->method('getRecurrencePattern')
            ->willReturn('test_pattern');
        $this->assertEquals('test_pattern', $this->extension->getRecurrencePattern(new Entity\Recurrence()));
    }
    
    public function testGetRecurrencePatternByAttributesWithNA()
    {
        $this->translator->expects($this->once())
            ->method('trans')
            ->willReturn('N/A');
        $this->assertEquals('N/A', $this->extension->getRecurrencePatternByAttributes(null, []));
    }

    public function testGetRecurrencePatternByAttributes()
    {
        $this->delegateStrategy->expects($this->once())
            ->method('getRecurrencePattern')
            ->willReturn('test_pattern');
        $this->assertEquals(
            'test_pattern',
            $this->extension->getRecurrencePatternByAttributes(
                1,
                [
                    'recurrence_type' => 'daily',
                    'interval' => 1,
                    'start_time' => date(DATE_RFC3339),
                    'end_time' => date(DATE_RFC3339),
                    'occurrences' => 2,
                ]
            )
        );
    }
}
