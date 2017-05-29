UPGRADE FROM 2.2 to 2.3
=======================

PhpUtils component
------------------
- Removed deprecated class `Oro\Component\PhpUtils\QueryUtil`. Use `Oro\Component\DoctrineUtils\ORM\QueryBuilderUtil` instead

DoctrineUtils component
-----------------------
- Class `Oro\Component\DoctrineUtils\ORM\QueryUtils` was marked as deprecated. Its methods were moved to 4 classes:
    - `Oro\Component\DoctrineUtils\ORM\QueryUtil`
    - `Oro\Component\DoctrineUtils\ORM\QueryBuilderUtil`
    - `Oro\Component\DoctrineUtils\ORM\ResultSetMappingUtil`
    - `Oro\Component\DoctrineUtils\ORM\DqlUtil`

DataGridBundle
--------------
- Removed class `Oro\Bundle\DataGridBundle\DependencyInjection\CompilerPass\ActionProvidersPass`
- Removed class `Oro\Bundle\DataGridBundle\DependencyInjection\CompilerPass\MassActionsPass`
- Class `Oro\Bundle\DataGridBundle\Extension\FieldAcl\FieldAclExtension`
    - removed constant `OWNER_FIELD_PLACEHOLDER`
    - removed constant `ORGANIZARION_FIELD_PLACEHOLDER`
    - removed property `$ownershipMetadataProvider`
    - removed property `$entityClassResolver`
    - removed property `$configProvider`
    - removed property `$queryAliases`
    - changed constructor signature from `__construct(OwnershipMetadataProvider $ownershipMetadataProvider, EntityClassResolver $entityClassResolver, AuthorizationCheckerInterface $authorizationChecker, ConfigProvider $configProvider)` to `__construct(AuthorizationCheckerInterface $authorizationChecker, ConfigManager $configManager, OwnershipQueryHelper $ownershipQueryHelper)`
    - removed method `collectEntityAliases`
    - removed method `addIdentitySelectsToQuery`
    - removed method `tryToGetAliasFromSelectPart`
- Removed service `oro_datagrid.extension.action.abstract`
- Added class `Oro\Bundle\DataGridBundle\Extension\Action\ActionFactory`
- Added class `Oro\Bundle\DataGridBundle\Extension\Action\ActionMetadataFactory`
- Class `Oro\Bundle\DataGridBundle\Extension\Action\ActionExtension`
    - removed constant `ACTION_TYPE_KEY`
    - removed property `$container`
    - removed property `$translator`
    - removed property `$actions`
    - removed property `$excludeParams`
    - changed constructor signature from `__construct(ContainerInterface $container, SecurityFacade $securityFacade, TranslatorInterface $translator)` to `__construct(ActionFactory $actionFactory, ActionMetadataFactory $actionMetadataFactory, SecurityFacade $securityFacade, OwnershipQueryHelper $ownershipQueryHelper)`
    - removed method `registerAction`. Use `Oro\Bundle\DataGridBundle\Extension\Action\ActionFactory::registerAction` instead
    - removed method `getApplicableActionProviders`
    - removed method `getActionObject`
    - removed method `create`
    - removed method `isResourceGranted`
- Added class `Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionFactory`
- Added class `Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionMetadataFactory`
- Class `Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionExtension`
    - removed inheritance from `Oro\Bundle\DataGridBundle\Extension\Action\ActionExtension`
    - removed property `$actions`
    - changed constructor signature from `__construct(ContainerInterface $container, SecurityFacade $securityFacade, TranslatorInterface $translator)` to `__construct(MassActionFactory $actionFactory, MassActionMetadataFactory $actionMetadataFactory, SecurityFacade $securityFacade)`
    - removed method `registerAction`. Use `Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionFactory::registerAction` instead

MigrationBundle
---------------
- Added event `oro_migration.data_fixtures.pre_load` that is raised before data fixtures are loaded
- Added event `oro_migration.data_fixtures.post_load` that is raised after data fixtures are loaded

SecurityBundle
--------------
- Class `Oro\Bundle\SecurityBundle\Acl\Domain\DomainObjectReference`
    - made `organizationId` optional
- Added class `Oro\Bundle\SecurityBundle\Owner\OwnershipQueryHelper`

SearchBundle
------------
- Class `Oro\Bundle\SearchBundle\EventListener\ReindexDemoDataListener` was replaced with `Oro\Bundle\SearchBundle\EventListener\ReindexDemoDataFixturesListener`
- Service `oro_search.event_listener.reindex_demo_data` was replaced with `oro_search.migration.demo_data_fixtures_listener.reindex`

TestFrameworkBundle
-------------------
- Class `TestListener` namespace added, use `Oro\Bundle\TestFrameworkBundle\Test\TestListener` instead
