<?php

namespace Oro\Bundle\ActionBundle\Extension;

use Doctrine\Common\Collections\Collection;

use Oro\Bundle\ActionBundle\Button\ButtonContext;
use Oro\Bundle\ActionBundle\Button\ButtonInterface;
use Oro\Bundle\ActionBundle\Button\ButtonSearchContext;
use Oro\Bundle\ActionBundle\Button\OperationButton;
use Oro\Bundle\ActionBundle\Exception\UnsupportedButtonException;
use Oro\Bundle\ActionBundle\Helper\ContextHelper;
use Oro\Bundle\ActionBundle\Model\ActionData;
use Oro\Bundle\ActionBundle\Model\Criteria\OperationFindCriteria;
use Oro\Bundle\ActionBundle\Model\Operation;
use Oro\Bundle\ActionBundle\Model\OperationRegistry;
use Oro\Bundle\ActionBundle\Provider\RouteProviderInterface;

class OperationButtonProviderExtension implements ButtonProviderExtensionInterface
{
    /** @var OperationRegistry */
    protected $operationRegistry;

    /** @var ContextHelper */
    protected $contextHelper;

    /** @var RouteProviderInterface */
    protected $routeProvider;

    /** @var ButtonContext */
    private $baseButtonContext;

    /**
     * @param OperationRegistry $operationRegistry
     * @param ContextHelper $contextHelper
     * @param RouteProviderInterface $routeProvider
     */
    public function __construct(
        OperationRegistry $operationRegistry,
        ContextHelper $contextHelper,
        RouteProviderInterface $routeProvider
    ) {
        $this->operationRegistry = $operationRegistry;
        $this->contextHelper = $contextHelper;
        $this->routeProvider = $routeProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function find(ButtonSearchContext $buttonSearchContext)
    {
        $operations = $this->getOperations($buttonSearchContext);
        $actionData = $this->getActionData($buttonSearchContext);
        $result = [];

        foreach ($operations as $operation) {
            $result[] = new OperationButton(
                $operation,
                $this->generateButtonContext($operation, $buttonSearchContext),
                clone $actionData
            );
        }

        $this->baseButtonContext = null;

        return $result;
    }

    /**
     * {@inheritdoc}
     * @param OperationButton $button
     */
    public function isAvailable(
        ButtonInterface $button,
        ButtonSearchContext $buttonSearchContext,
        Collection $errors = null
    ) {
        if (!$this->supports($button)) {
            throw new UnsupportedButtonException(
                sprintf(
                    'Button %s is not supported by %s. Can not determine availability.',
                    get_class($button),
                    get_class($this)
                )
            );
        }

        return $this->supports($button) &&
            $button->getOperation()->isAvailable($this->getActionData($buttonSearchContext));
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ButtonInterface $button)
    {
        return $button instanceof OperationButton;
    }

    /**
     * @param Operation $operation
     * @param ButtonSearchContext $searchContext
     *
     * @return ButtonContext
     */
    protected function generateButtonContext(Operation $operation, ButtonSearchContext $searchContext)
    {
        if (!$this->baseButtonContext) {
            $this->baseButtonContext = new ButtonContext();
            $this->baseButtonContext->setUnavailableHidden(true)
                ->setDatagridName($searchContext->getDatagrid())
                ->setEntity($searchContext->getEntityClass(), $searchContext->getEntityId())
                ->setRouteName($searchContext->getRouteName())
                ->setGroup($searchContext->getGroup())
                ->setExecutionRoute($this->routeProvider->getExecutionRoute());
        }

        $context = clone $this->baseButtonContext;
        $context->setEnabled($operation->isEnabled());

        if ($operation->hasForm()) {
            $context->setFormDialogRoute($this->routeProvider->getFormDialogRoute());
            $context->setFormPageRoute($this->routeProvider->getFormPageRoute());
        }

        return $context;
    }

    /**
     * @param ButtonSearchContext $buttonSearchContext
     *
     * @return array|Operation[]
     */
    protected function getOperations(ButtonSearchContext $buttonSearchContext)
    {
        return $this->operationRegistry->find(
            OperationFindCriteria::createFromButtonSearchContext($buttonSearchContext)
        );
    }

    /**
     * @param ButtonSearchContext $searchContext
     *
     * @return ActionData
     */
    protected function getActionData(ButtonSearchContext $searchContext)
    {
        return $this->contextHelper->getActionData([
            ContextHelper::ENTITY_ID_PARAM => $searchContext->getEntityId(),
            ContextHelper::ENTITY_CLASS_PARAM => $searchContext->getEntityClass(),
            ContextHelper::DATAGRID_PARAM => $searchContext->getDatagrid(),
            ContextHelper::FROM_URL_PARAM => $searchContext->getReferrer(),
            ContextHelper::ROUTE_PARAM => $searchContext->getRouteName(),
        ]);
    }
}
