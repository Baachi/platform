<?php

namespace Oro\Bundle\ConfigBundle\Tests\Behat\Context;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Oro\Bundle\TestFrameworkBundle\Behat\Context\OroFeatureContext;
use Oro\Bundle\TestFrameworkBundle\Behat\Element\OroPageObjectAware;
use Oro\Bundle\TestFrameworkBundle\Behat\Fixtures\FixtureLoaderAwareInterface;
use Oro\Bundle\TestFrameworkBundle\Behat\Fixtures\FixtureLoaderDictionary;
use Oro\Bundle\TestFrameworkBundle\Tests\Behat\Context\OroMainContext;
use Oro\Bundle\TestFrameworkBundle\Tests\Behat\Context\PageObjectDictionary;

class FeatureContext extends OroFeatureContext implements
    KernelAwareContext,
    FixtureLoaderAwareInterface,
    OroPageObjectAware
{
    use KernelDictionary, FixtureLoaderDictionary, PageObjectDictionary;

    /**
     * @var OroMainContext
     */
    private $oroMainContext;

    /**
     * @BeforeScenario
     */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();
        $this->oroMainContext = $environment->getContext(OroMainContext::class);
    }

    /**
     * Alias for setting parameters on configuration forms
     * Example:   When I set configuration to:
     *              | Minimal password length      | 10   |
     *              | Require a number             | true |
     *
     * @Then /^(?:|I )set configuration to:$/
     */
    public function iSetConfiguration(TableNode $table)
    {
        $this->oroMainContext->iFillFormWith($table, 'SystemConfigForm');
    }

}
