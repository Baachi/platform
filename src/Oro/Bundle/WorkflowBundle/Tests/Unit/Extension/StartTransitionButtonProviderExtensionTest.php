<?php

namespace Oro\Bundle\WorkflowBundle\Tests\Unit\Extension;

use Doctrine\Common\Collections\ArrayCollection;

use Oro\Bundle\ActionBundle\Exception\UnsupportedButtonException;
use Oro\Bundle\ActionBundle\Button\ButtonContext;
use Oro\Bundle\ActionBundle\Button\ButtonInterface;
use Oro\Bundle\ActionBundle\Button\ButtonSearchContext;
use Oro\Bundle\ActionBundle\Provider\RouteProviderInterface;
use Oro\Bundle\ActionBundle\Tests\Unit\Stub\StubButton;
use Oro\Bundle\WorkflowBundle\Button\StartTransitionButton;
use Oro\Bundle\WorkflowBundle\Entity\WorkflowDefinition;
use Oro\Bundle\WorkflowBundle\Extension\StartTransitionButtonProviderExtension;
use Oro\Bundle\WorkflowBundle\Model\Transition;
use Oro\Bundle\WorkflowBundle\Model\TransitionManager;
use Oro\Bundle\WorkflowBundle\Model\Workflow;
use Oro\Bundle\WorkflowBundle\Model\WorkflowRegistry;

class StartTransitionButtonProviderExtensionTest extends \PHPUnit_Framework_TestCase
{
    const ENTITY_CLASS = 'entity1';
    const ROUTE_NAME = 'route1';
    const DATAGRID = 'datagrid1';

    /** @var WorkflowRegistry|\PHPUnit_Framework_MockObject_MockObject */
    protected $workflowRegistry;

    /** @var RouteProviderInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $routeProvider;

    /** @var StartTransitionButtonProviderExtension */
    protected $extension;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->workflowRegistry = $this->getMockBuilder(WorkflowRegistry::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->routeProvider = $this->createMock(RouteProviderInterface::class);

        $this->extension = new StartTransitionButtonProviderExtension($this->workflowRegistry, $this->routeProvider);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->workflowRegistry, $this->routeProvider, $this->extension);
    }

    /**
     * @dataProvider findDataProvider
     *
     * @param bool $expected
     * @param string|null $entityClass
     * @param string|null $routeName
     * @param string|null $datagrid
     */
    public function testFind($expected, $entityClass = null, $routeName = null, $datagrid = null)
    {
        $transition = new Transition();
        $transition->setName('transition1')
            ->setInitEntities($entityClass ? [$entityClass] : [])
            ->setInitRoutes($routeName ? [$routeName] : [])
            ->setInitDatagrids($datagrid ? [$datagrid] : []);

        $transitionManager = $this->createMock(TransitionManager::class);
        $transitionManager->expects($this->once())
            ->method('getStartTransitions')
            ->willReturn(new ArrayCollection([$transition]));

        $workflow = $this->getWorkflow($transitionManager);

        $this->workflowRegistry->expects($this->once())->method('getActiveWorkflows')->willReturn([$workflow]);

        if ($expected) {
            $buttonContext = (new ButtonContext())
                ->setEntity($entityClass)
                ->setRouteName($routeName)
                ->setDatagridName($datagrid);
            $buttons = [new StartTransitionButton($transition, $workflow, $buttonContext)];
        } else {
            $buttons = [];
        }

        $this->assertEquals(
            $buttons,
            $this->extension->find(
                (new ButtonSearchContext())->setEntity($entityClass)->setRouteName($routeName)->setDatagrid($datagrid)
            )
        );
    }

    /**
     * @return array
     */
    public function findDataProvider()
    {
        return [
            'entity' => [
                'expected' => true,
                'entityClass' => self::ENTITY_CLASS,
            ],
            'route' => [
                'expected' => true,
                'entityClass' => null,
                'routeName' => self::ROUTE_NAME,
            ],
            'datagrid' => [
                'expected' => true,
                'entityClass' => null,
                'routeName' => null,
                'datagrid' => self::DATAGRID,
            ],
            'not matched' => [
                'expected' => false,
                'entityClass' => 'test_entity',
            ],
        ];
    }

    public function testFindWithGroupAtContext()
    {
        $this->workflowRegistry->expects($this->never())->method('getActiveWorkflows');
        $this->assertEquals(
            [],
            $this->extension->find((new ButtonSearchContext())->setGroup('test_group'))
        );
    }

    /**
     * @dataProvider isAvailableDataProvider
     *
     * @param ButtonInterface $button
     * @param bool $expected
     */
    public function testIsAvailable(ButtonInterface $button, $expected)
    {
        $this->assertEquals($expected, $this->extension->isAvailable($button, new ButtonSearchContext()));
    }

    /**
     * @return array
     */
    public function isAvailableDataProvider()
    {
        return [
            'available' => [
                'button' => $this->createTransitionButton(true),
                'expected' => true
            ],
            'not available' => [
                'button' => $this->createTransitionButton(false),
                'expected' => false
            ]
        ];
    }

    public function testIsAvailableException()
    {
        $stubButton = new StubButton();

        $this->expectException(UnsupportedButtonException::class);
        $this->expectExceptionMessage(
            'Button Oro\Bundle\ActionBundle\Tests\Unit\Stub\StubButton is not supported by ' .
            'Oro\Bundle\WorkflowBundle\Extension\StartTransitionButtonProviderExtension. Can not determine availability'
        );

        $this->extension->isAvailable($stubButton, new ButtonSearchContext());
    }

    public function testSupports()
    {
        // for start transition
        $this->assertTrue($this->extension->supports($this->createTransitionButton()));
        // for notstart transition
        $this->assertFalse($this->extension->supports($this->createTransitionButton(false, false)));

        /** @var ButtonInterface|\PHPUnit_Framework_MockObject_MockObject $notTransitionButton */
        $notTransitionButton = $this->createMock(ButtonInterface::class);
        // for not supported button
        $this->assertFalse($this->extension->supports($notTransitionButton));
    }

    /**
     * @param TransitionManager|\PHPUnit_Framework_MockObject_MockObject $transitionManager
     * @return Workflow|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getWorkflow(TransitionManager $transitionManager)
    {
        $workflow = $this->getMockBuilder(Workflow::class)->disableOriginalConstructor()->getMock();

        $definition = $this->createMock(WorkflowDefinition::class);
        $definition->expects($this->any())->method('getRelatedEntity')->willReturn(self::ENTITY_CLASS);

        $workflow->expects($this->any())
            ->method('getInitEntities')
            ->willReturn([self::ENTITY_CLASS => ['transition1', 'transition2']]);

        $workflow->expects($this->any())
            ->method('getInitRoutes')
            ->willReturn([self::ROUTE_NAME => ['transition1', 'transition2']]);

        $workflow->expects($this->any())
            ->method('getInitDatagrids')
            ->willReturn([self::DATAGRID => ['transition1', 'transition2']]);

        $workflow->expects($this->any())->method('getDefinition')->willReturn($definition);
        $workflow->expects($this->any())->method('getTransitionManager')->willReturn($transitionManager);

        return $workflow;
    }

    /**
     * @param string $entityClass
     * @return ButtonContext
     */
    protected function getButtonContext($entityClass)
    {
        $context = new ButtonContext();
        $context->setEntity($entityClass)
            ->setEnabled(true)
            ->setUnavailableHidden(false);

        return $context;
    }

    /**
     * @param array $transitions
     * @return TransitionManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getTransitionManager(array $transitions)
    {
        $manager = $this->createMock(TransitionManager::class);
        $manager->expects($this->any())
            ->method('getStartTransitions')
            ->willReturn(new ArrayCollection($transitions));

        return $manager;
    }

    /**
     * @param string $name
     * @return Transition
     */
    protected function getTransition($name)
    {
        $transition = new Transition();
        $transition->setName($name);

        return $transition;
    }

    /**
     * @param bool $isAvailable
     * @param bool $isStart
     *
     * @return StartTransitionButton
     */
    private function createTransitionButton($isAvailable = false, $isStart = true)
    {
        /** @var Transition|\PHPUnit_Framework_MockObject_MockObject $transition */
        $transition = $this->createMock(Transition::class);
        $transition->expects($this->any())->method('isAvailable')->willReturn($isAvailable);
        $transition->expects($this->any())->method('isStart')->willReturn($isStart);
        $transitionManager = $this->createMock(TransitionManager::class);

        $workflow = $this->getWorkflow($transitionManager);

        return new StartTransitionButton($transition, $workflow, new ButtonContext());
    }
}
