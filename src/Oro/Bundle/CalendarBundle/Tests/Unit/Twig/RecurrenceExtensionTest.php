<?php

namespace Oro\Bundle\CalendarBundle\Tests\Unit\Twig;

use Oro\Bundle\CalendarBundle\Entity;
use Oro\Bundle\CalendarBundle\Model\Recurrence;
use Oro\Bundle\CalendarBundle\Twig\RecurrenceExtension;

class RecurrenceExtensionTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Translation\TranslatorInterface */
    protected $translator;

    /** @var RecurrenceExtension */
    protected $extension;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $validator;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $strategy;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $recurrenceModel;

    protected function setUp()
    {
        $this->translator = $this->getMockBuilder('Symfony\Component\Translation\TranslatorInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->validator = $this->getMockBuilder('Symfony\Component\Validator\Validator\ValidatorInterface')
            ->getMock();
        $this->strategy = $this->getMockBuilder('Oro\Bundle\CalendarBundle\Model\Recurrence\StrategyInterface')
            ->getMock();
        $this->recurrenceModel = new Recurrence($this->validator, $this->strategy);
        $this->extension = new RecurrenceExtension($this->translator, $this->recurrenceModel);
    }

    public function testGetName()
    {
        $this->assertEquals('oro_recurrence', $this->extension->getName());
    }

    public function testGetRecurrenceTextValue()
    {
        $this->strategy->expects($this->once())
            ->method('getTextValue')
            ->willReturn('test_pattern');
        $this->assertEquals('test_pattern', $this->extension->getRecurrenceTextValue(new Entity\Recurrence()));
    }
    
    public function testGetRecurrenceAttributesTextValueWithNA()
    {
        $this->translator->expects($this->once())
            ->method('trans')
            ->willReturn('N/A');
        $this->assertEquals('N/A', $this->extension->getRecurrenceAttributesTextValue(null, []));
    }

    public function testGetRecurrenceAttributesTextValue()
    {
        $this->strategy->expects($this->once())
            ->method('getTextValue')
            ->willReturn('test_pattern');
        $this->assertEquals(
            'test_pattern',
            $this->extension->getRecurrenceAttributesTextValue(
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
