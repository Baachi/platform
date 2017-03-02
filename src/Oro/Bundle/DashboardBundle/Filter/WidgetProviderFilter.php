<?php

namespace Oro\Bundle\DashboardBundle\Filter;

use Doctrine\ORM\QueryBuilder;

use Oro\Component\DoctrineUtils\ORM\QueryUtils;
use Oro\Bundle\UserBundle\Dashboard\OwnerHelper;
use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;
use Oro\Bundle\DashboardBundle\Model\WidgetOptionBag;

class WidgetProviderFilter
{
    /** @var AclHelper */
    protected $aclHelper;

    /** @var OwnerHelper */
    protected $ownerHelper;

    public function __construct(AclHelper $aclHelper, OwnerHelper $ownerHelper)
    {
        $this->aclHelper   = $aclHelper;
        $this->ownerHelper = $ownerHelper;
    }

    public function filter(QueryBuilder $queryBuilder, WidgetOptionBag $widgetOptions)
    {
        $this->processOwners($queryBuilder, $widgetOptions);

        return $this->applyAcl($queryBuilder);
    }

    public function getOwnerIds(WidgetOptionBag $widgetOptions)
    {
        return $this->ownerHelper->getOwnerIds($widgetOptions);
    }

    protected function processOwners(QueryBuilder $queryBuilder, WidgetOptionBag $widgetOptions)
    {
        $owners = $this->getOwnerIds($widgetOptions);
        $alias = QueryUtils::getSingleRootAlias($queryBuilder, false);
        if ($owners) {
            // check if options are for opportunity_by_status
            QueryUtils::applyOptimizedIn($queryBuilder, $alias.'.owner', $owners);
        }
    }

    public function applyAcl(QueryBuilder $queryBuilder)
    {
        return $this->aclHelper->apply($queryBuilder);
    }
}
