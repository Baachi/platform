<?php

namespace Oro\Bundle\FormBundle\Tests\Unit\Form\Type;

use Oro\Bundle\FormBundle\Form\Type\OroDateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Test\TypeTestCase;

class OroDateTimeTypeTest extends TypeTestCase
{
    public function testGetParent()
    {
        $type = new OroDateTimeType();
        $this->assertEquals(DateTimeType::class, $type->getParent());
    }

    public function testConfigureOptions()
    {
        $expectedOptions = array(
            'model_timezone'   => 'UTC',
            'view_timezone'    => 'UTC',
            'format'           => DateTimeType::HTML5_FORMAT,
            'widget'           => 'single_text',
            'placeholder'      => 'oro.form.click_here_to_select',
            'years'            => [],
        );

        $form = $this->factory->create(OroDateTimeType::class);
        $form->submit((new \DateTime()));

        $options = $form->getConfig()->getOptions();
        foreach ($expectedOptions as $name => $expectedValue) {
            $this->assertArrayHasKey($name, $options);
            $this->assertEquals($expectedValue, $options[$name]);
        }
    }

    /**
     * @dataProvider optionsDataProvider
     * @param array  $options
     * @param array $expectedKeys
     * @param array  $expectedValues
     */
    public function testFinishView($options, $expectedKeys, $expectedValues)
    {
        $form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()->getMock();

        $view = new FormView();
        $type = new OroDateTimeType();
        $type->finishView($view, $form, $options);
        foreach ($expectedKeys as $key => $expectedKey) {
            $this->assertArrayHasKey($expectedKey, $view->vars);
            $this->assertEquals($expectedValues[$key], $view->vars[$expectedKey]);
        }
    }

    public function optionsDataProvider()
    {
        return array(
            array(
                array('placeholder' => 'some.placeholder', 'minDate' => '-120y', 'maxDate' => '0'),
                array('attr', 'minDate', 'maxDate'),
                array(
                    ['placeholder' => 'some.placeholder'],
                    '-120y',
                    '0'
                ),
            ),
            array(
                array('years' => [2001, 2002, 2003], 'minDate' => '-120y', 'maxDate' => '0'),
                array('years', 'minDate', 'maxDate'),
                array('2001:2003', '-120y', '0')
            ),
        );
    }

    /**
     * @dataProvider valuesDataProvider
     * @param string  $value
     * @param \DateTime $expectedValue
     */
    public function testSubmitValidData($value, $expectedValue)
    {
        $form = $this->factory->create(OroDateTimeType::class);
        $form->submit($value);
        $this->assertDateTimeEquals($expectedValue, $form->getData());
    }

    public function valuesDataProvider()
    {
        return array(
            array(
                '2002-10-02T15:00:00+00:00',
                new \DateTime('2002-10-02T15:00:00+00:00')
            ),
            array(
                '2002-10-02T15:00:00Z',
                new \DateTime('2002-10-02T15:00:00Z')
            ),
            array(
                '2002-10-02T15:00:00.05Z',
                new \DateTime('2002-10-02T15:00:00.05Z')
            )
        );
    }
}
