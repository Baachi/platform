<?php

namespace Oro\Bundle\EmailBundle\Tests\Unit\Form\Type;

use Oro\Bundle\EmailBundle\Form\Type\EmailTemplateType;

class EmailTemplateTypeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var EmailTemplateType
     */
    protected $type;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $localeSettings;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $configManager;

    protected function setUp()
    {
        $this->localeSettings = $this->getMockBuilder('Oro\Bundle\LocaleBundle\Model\LocaleSettings')
            ->disableOriginalConstructor()->getMock();

        $this->configManager = $this->getMockBuilder('Oro\Bundle\ConfigBundle\Config\ConfigManager')
            ->disableOriginalConstructor()->getMock();

        $this->type = new EmailTemplateType(
            $this->configManager,
            $this->localeSettings
        );
    }

    protected function tearDown()
    {
        unset($this->type);
    }

    public function testConfigureOptions()
    {
        $resolver = $this->createMock('Symfony\Component\OptionsResolver\OptionsResolver');
        $resolver->expects($this->once())
            ->method('setDefaults')
            ->with($this->isType('array'));

        $this->type->configureOptions($resolver);
    }

    public function testBuildForm()
    {
        $builder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $builder->expects($this->exactly(6))
            ->method('add');

        $this->configManager->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap([
                ['oro_locale.languages', false, false, null, ['en', 'fr_FR']],
                ['oro_email.sanitize_html', false, false, null, true]
            ]));

        $this->type->buildForm($builder, array());
    }
}
