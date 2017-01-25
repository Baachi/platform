UPGRADE FROM 2.0 to 2.1
========================

##ActionBundle
- Added aware interface `Oro\Bundle\ActionBundle\Provider\ApplicationProviderAwareInterface` and trait `ApplicationProviderAwareTrait`

##EntityConfigBundle
- Added query `Oro\Bundle\EntityConfigBundle\Migration\RemoveTableQuery` to remove entity config during migration

##SecurityBundle
- Service overriding in compiler pass was replaced by service decoration for next services:
    - `sensio_framework_extra.converter.doctrine.orm`
    - `security.acl.dbal.provider`
    - `security.acl.cache.doctrine`
    - `security.acl.voter.basic_permissions`
- Next container parameters were removed:
    - `oro_security.acl.voter.class`

##LayoutBundle
- Class `Oro\Bundle\LayoutBundle\DependencyInjection\CompilerOverrideServiceCompilerPass` was removed

##TranslationBundle
- Added query `Oro\Bundle\TranslationBundle\Migration\DeleteTranslationKeysQuery` to delete custom translation keys during migration

##WorkflowBundle
- Changes in `Oro\Bundle\WorkflowBundle\Entity\WorkflowDefinition`:
  * added `applications` entity field
  * added methods `getApplications()`, `setApplications(array $applications)`
- Added new node `applications` to `Oro\Bundle\WorkflowBundle\Configuration\WorkflowConfiguration`
- Added `Oro\Bundle\WorkflowBundle\Model\WorkflowManagerRegistry` to manage system or default WorkflowManagers.
System WorkflowManager should use only for internal needs and haven't any filters except filters by features
- Changed fourth constructor argument of `Oro\Bundle\WorkflowBundle\Datagrid\WorkflowStepColumnListener` from `Oro\Bundle\WorkflowBundle\Model\WorkflowRegistry` to `Oro\Bundle\WorkflowBundle\Model\WorkflowManagerRegistry`
- Changed fifth constructor argument from `Oro\Bundle\WorkflowBundle\Model\WorkflowRegistry` to `Oro\Bundle\WorkflowBundle\Model\WorkflowManagerRegistry` for next classes:
  * `Oro\Bundle\WorkflowBundle\Acl\Extension` 
  * `Oro\Bundle\WorkflowBundle\Acl\Extension\WorkflowAclExtension`
  * `Oro\Bundle\WorkflowBundle\Acl\Extension\WorkflowTransitionAclExtension`
- Added fourth argument `object $prevEntity` to `Oro\Bundle\WorkflowBundle\EventListener\Extension\TransitionEventTriggerExtension::addSchedule()`
- Changes in Oro\Bundle\WorkflowBundle\Helper\TransitionEventTriggerHelper:
  * added third argument `object $prevEntity` to method `isRequirePass()`
  * added fifth argument `object $prevEntity = null` to static method `buildContextValues()`
- Added fifth argument `Oro\Bundle\WorkflowBundle\Model\Tools\StartedWorkflowsBag $startedWorkflowsBag` to `Oro\Bundle\WorkflowBundle\Model\WorkflowManager`
- Changes in `Oro\Bundle\WorkflowBundle\Model\WorkflowRegistry`:
  * added third constructor argument `Oro\Bundle\WorkflowBundle\Model\Filter\WorkflowDefinitionFilters $definitionFilters`
  * removed public method `public function addDefinitionFilter(WorkflowDefinitionFilterInterface $definitionFilter)`
