UPGRADE FROM 2.0 to 2.1
========================

ActionBundle
------------
- `Oro\Bundle\ActionBundle\Condition\RouteExists` deprecated because of:
    - work with `RouteCollection` is performance consuming
    - it was used to check bundle presence, which could be done with `service_exists`

ActivityListBundle
------------------
- Class `Oro\Bundle\ActivityListBundle\Filter`
    - construction signature was changed now it takes next arguments:
        - `FormFactoryInterface` $factory,
        - `FilterUtility` $util,
        - `ActivityAssociationHelper` $activityAssociationHelper,
        - `ActivityListChainProvider` $activityListChainProvider,
        - `ActivityListFilterHelper` $activityListFilterHelper,
        - `EntityRoutingHelper` $entityRoutingHelper,
        - `ServiceLink` $queryDesignerManagerLink,
        - `ServiceLink` $datagridHelperLink

AddressBundle
-------------
- Class `Oro\Bundle\AddressBundle\Twig\PhoneExtension`
    - construction signature was changed now it takes next arguments:
        - `ServiceLink` $providerLink

DashboardBundle
---------------
- Class `Oro\Bundle\DashboardBundle\Twig\DashboardExtension`
    - construction signature was changed now it takes next arguments:
        - `ServiceLink` $converterLink,
        - `ServiceLink` $managerLink,
        - `EntityProvider` $entityProvider

DataAuditBundle
---------------
- Class `Oro\Bundle\DataAuditBundle\Filter\AuditFilter`
    - construction signature was changed now it takes next arguments:
        - `FormFactoryInterface` $factory,
        - `FilterUtility` $util,
        - `ServiceLink` $queryDesignerManagerLink

EmailBundle
-----------
- Added `Oro\Bundle\EmailBundle\Sync\EmailSynchronizerInterface` and implemented it in `Oro\Bundle\EmailBundle\Sync\AbstractEmailSynchronizer`
- Class `Oro\Bundle\EmailBundle\Twig\EmailExtension`
    - construction signature was changed now it takes next arguments:
        - `EmailHolderHelper` $emailHolderHelper,
        - `EmailAddressHelper` $emailAddressHelper,
        - `EmailAttachmentManager` $emailAttachmentManager,
        - `EntityManager` $em,
        - `MailboxProcessStorage` $mailboxProcessStorage,
        - `SecurityFacade` $securityFacade,
        - `ServiceLink` $relatedEmailsProviderLink
- `Oro/Bundle/EmailBundle/Migrations/Data/ORM/EnableEmailFeature` removed, feature enabled by default

EntityBundle
------------
- Class `Oro\Bundle\EntityBundle\Twig\EntityFallbackExtension`
    - construction signature was changed now it takes next arguments:
        - `ServiceLink` $fallbackResolverLink
- Added class `Oro\Bundle\EntityBundle\ORM\DiscriminatorMapListener' that should be used for entities with single table inheritance.
  Example:
```yml
oro_acme.my_entity.discriminator_map_listener:
    class: 'Oro\Bundle\EntityBundle\ORM\DiscriminatorMapListener'
    public: false
    calls:
        - [ addClass, ['oro_acme_entity', '%oro_acme.entity.acme_entity.class%'] ]
    tags:
        - { name: doctrine.event_listener, event: loadClassMetadata }
```

EntityConfigBundle
------------------
- Class `Oro\Bundle\EntityConfigBundle\Config\ConfigManager`
    - removed property `protected $providers`
    - removed property `protected $propertyConfigs`
    - removed method `public function addProvider(ConfigProvider $provider)` in favor of `public function setProviderBag(ConfigProviderBag $providerBag)`
- Class `Oro\Bundle\EntityConfigBundle\Provider\ConfigProvider`
    - removed property `protected $propertyConfig`
    - construction signature was changed. The parameter `array $config` was replaced with `PropertyConfigBag $configBag`

EntityPaginationBundle
----------------------
- Class `Oro\Bundle\EntityPaginationBundle\Storage\StorageDataCollector`
    - construction signature was changed now it takes next arguments:
        - `ServiceLink` $dataGridManagerLink,
        - `DoctrineHelper` $doctrineHelper,
        - `AclHelper` $aclHelper,
        - `EntityPaginationStorage` $storage,
        - `EntityPaginationManager` $paginationManager

DataGridBundle
--------------
- Class `Oro\Bundle\DataGridBundle\Engine\Orm\PdoMysql\GroupConcat` was removed. Use `GroupConcat` from package `oro/doctrine-extensions` instead.
- Class `Oro\Bundle\DataGridBundle\Twig\DataGridExtension`
    - construction signature was changed now it takes next arguments:
        - `ServiceLink` $managerLink,
        - `NameStrategyInterface` $nameStrategy,
        - `RouterInterface` $router,
        - `SecurityFacade` $securityFacade,
        - `DatagridRouteHelper` $datagridRouteHelper,
        - `RequestStack` $requestStack,
        - `LoggerInterface` $logger = null
- Added abstract entity class `Oro\Bundle\DataGridBundle\Entity\AbstractGridView`
    - entity `Oro\Bundle\DataGridBundle\Entity\GridView` extends from it
- Added abstract entity class `Oro\Bundle\DataGridBundle\Entity\AbstractGridViewUser`
    - entity `Oro\Bundle\DataGridBundle\Entity\GridViewUser` extends from it
- Class `Oro\Bundle\DataGridBundle\Controller\Api\Rest\GridViewController`
    - added argument `Request $request` for methods:
        - `public function postAction(Request $request)`
        - `public function putAction(Request $request, $id)`
        - `protected function checkCreatePublicAccess(Request $request)`
    - changed type hint of first argument of method `checkEditPublicAccess()` from `GridView $gridView` to `AbstractGridView $gridView`
- Changed type hint for first argument of `Oro\Bundle\DataGridBundle\Entity\Manager\GridViewApiEntityManager::setDefaultGridView()` from `User $user` to `AbstractUser $user`
- Class `Oro\Bundle\DataGridBundle\Entity\Manager\GridViewManager`
    - changed type hint for:
        - first argument of method `public funtion setDefaultGridView()` from `User $user` to `AbstractUser $user`
        - second argument of method `protected function isViewDefault()` from `User $user` to `AbstractUser $user`
        - first argument of method `public funtion getAllGridViews()` from `User $user` to `AbstractUser $user`
        - first argument of method `public funtion getDefaultView()` from `User $user` to `AbstractUser $user`
- Class `Oro\Bundle\DataGridBundle\Entity\Repository\GridViewRepository`
    - changed type hint for third argument of method `public funtion findDefaultGridViews()` from `GridView $gridView` to `AbstractGridView $gridView`
- Class `Oro\Bundle\DataGridBundle\Entity\Repository\GridViewUserRepository`
    - added method `findByGridViewAndUser(AbstractGridView $view, UserInterface $user)`
- Class `Oro\Bundle\DataGridBundle\Form\Handler\GridViewApiHandler`
    - changed type hint for:
        - first argument of method `protected funtion onSuccess()` from `GridView $entity` to `AbstractGridView $entity`
        - first argument of method `protected funtion setDefaultGridView()` from `GridView $entity` to `AbstractGridView $entity`
        - first argument of method `protected funtion fixFilters()` from `GridView $entity` to `AbstractGridView $entity`

EntityConfigBundle
------------------
- Added parameter `ConfigDatabaseChecker $databaseChecker` to the constructor of `Oro\Bundle\EntityConfigBundle\Config\ConfigModelManager`

ImapBundle
----------
- Updated `Oro\Bundle\ImapBundle\Async\SyncEmailMessageProcessor::__construct()` signature to use `Oro\Bundle\EmailBundle\Sync\EmailSynchronizerInterface`.

LayoutBundle
------------
- Class `Oro\Bundle\LayoutBundle\DependencyInjection\CompilerOverrideServiceCompilerPass` was removed

LocaleBundle
------------
- Class `Oro\Bundle\LocaleBundle\Formatter\AddressFormatter`
    - construction signature was changed now it takes next arguments:
        - `LocaleSettings` $localeSettings,
        - `NameFormatter` $nameFormatter,
        - `PropertyAccessor` $propertyAccessor

SearchBundle
------------
- `DbalStorer` is deprecated. If you need its functionality, please compose your class with `DBALPersistenceDriverTrait`
- Deprecated services and classes:
    - `oro_search.search.engine.storer`
    - `Oro\Bundle\SearchBundle\Engine\Orm\DbalStorer`
- `entityManager` instead of `em` should be used in `BaseDriver` children
- `OrmIndexer` should be decoupled from `DbalStorer` dependency
- Interface `Oro\Bundle\SearchBundle\Engine\EngineV2Interface` marked as deprecated - please, use
`Oro\Bundle\SearchBundle\Engine\EngineInterface` instead
- Return value types in `Oro\Bundle\SearchBundle\Query\SearchQueryInterface` and
`Oro\Bundle\SearchBundle\Query\AbstractSearchQuery` were fixed to support fluent interface
`Oro\Bundle\SearchBundle\Engine\Orm` `setDrivers` method and `$drivers` and injected directly to `Oro\Bundle\SearchBundle\Entity\Repository\SearchIndexRepository`
`Oro\Bundle\SearchBundle\Engine\OrmIndexer` `setDrivers` method and `$drivers` and injected directly to `Oro\Bundle\SearchBundle\Entity\Repository\SearchIndexRepository`

SecurityBundle
--------------
- Service overriding in compiler pass was replaced by service decoration for next services:
    - `sensio_framework_extra.converter.doctrine.orm`
    - `security.acl.dbal.provider`
    - `security.acl.cache.doctrine`
    - `security.acl.voter.basic_permissions`
- Next container parameters were removed:
    - `oro_security.acl.voter.class`
- `Oro\Bundle\SecurityBundle\Owner\AbstractOwnerTreeProvider`:
    - removed implementation of `Symfony\Component\DependencyInjection\ContainerAwareInterface`
    - removed method `public function setContainer(ContainerInterface $container = null)`
    - removed method `protected function getContainer()`
    - changed the visibility of `$tree` property from `protected` to `private`
    - removed method `public function getCache()`
    - removed method `protected function getTreeData()`
    - removed method `protected function getOwnershipMetadataProvider()`
    - removed method `protected function checkDatabase()`
    - removed method `getManagerForClass($className)`
- `Oro\Bundle\SecurityBundle\Owner\OwnerTreeProvider`:
    - removed constant `CACHE_KEY`
    - removed property `protected $em`
    - removed method `public function getCache()`
    - changed the signature of the constructor.
      Old signature: `__construct(EntityManager $em, CacheProvider $cache)`.
      New signature:
        ```
        __construct(
            ManagerRegistry $doctrine,
            DatabaseChecker $databaseChecker,
            CacheProvider $cache,
            MetadataProviderInterface $ownershipMetadataProvider,
            TokenStorageInterface $tokenStorage
        )
        ```
- `Oro\Bundle\SecurityBundle\Form\Extension\AclProtectedFieldTypeExtension`:
    - removed parameter `EntityClassResolver $entityClassResolver` from the constructor
    - removed property `protected $entityClassResolver`

TranslationBundle
-----------------
- Added parameter `ConfigDatabaseChecker $databaseChecker` to the constructor of `Oro\Bundle\TranslationBundle\Translation\OrmTranslationLoader`

UIBundle
--------
- Class `Oro\Bundle\UIBundle\Twig\FormatExtension`
    - construction signature was changed now it takes next arguments:
        - `ServiceLink` $formatterManagerLink

UserBundle
----------
- Class `Oro\Bundle\UserBundle\Security\ImpersonationAuthenticator`
    - replaced parameter `EntityManager $em` with `ManagerRegistry $doctrine` in the constructor
    - removed property `protected $em`

WorkflowBundle
--------------
- `Oro\Bundle\WorkflowBundle\Validator\WorkflowValidationLoader`:
    - replaced parameter `ServiceLink $emLink` with `ConfigDatabaseChecker $databaseChecker` in the constructor
    - removed property `protected $emLink`
    - removed property `protected $dbCheck`
    - removed property `protected $requiredTables`
    - removed method `protected function checkDatabase()`
    - removed method `protected function getEntityManager()`
    

TestFrameworkBundle
-------------------
- `@dbIsolation annotation removed, applied as defult behavior`
- `@dbReindex annotation removed, use \Oro\Bundle\SearchBundle\Tests\Functional\SearchExtensionTrait::clearIndexTextTable`
- `Oro/Bundle/TestFrameworkBundle/Test/Client`:
    - removed property `$pdoConnection`
    - removed property `$kernel`
    - removed property `$hasPerformedRequest`
    - removed property `$loadedFixtures`
    - removed method `reboot`
    - removed method `doRequest`
- `Oro/Bundle/TestFrameworkBundle/Test/WebTestCase`:
    - removed property `$dbIsolation`
    - removed property `$dbReindex`
    - removed method `getDbIsolationSetting`
    - removed method `getDbReindexSetting`
    - removed method `getDbReindexSetting`
    - renamed method `setUpBeforeClass` to `beforeClass`
    - renamed method `tearDownAfterClass` to `afterClass`
