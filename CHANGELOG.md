## 2.4.0 (Unreleased)

## 2.3.6 (2017-09-12)
## 2.3.5 (2017-09-07)
## 2.3.4 (2017-09-04)
## 2.3.3 (2017-09-30)
## 2.3.2 (2017-08-22)
## 2.3.1 (2017-08-16)
## 2.3.0 (2017-07-28)

## 2.2.6 (2017-08-31)
## 2.2.5 (2017-08-21)
## 2.2.4 (2017-08-16)
## 2.2.3 (2017-07-27)
## 2.2.2 (2017-06-30)
## 2.2.1 (2017-06-14)
## 2.2.0 (2017-05-31)
[Show detailed list of changes](file-incompatibilities-2-2-0.md)

### Added
#### ApiBundle
* Added `form_event_subscriber` option to `Resources/config/oro/api.yml`. It can be used to add an event subscriber(s) to a form of such actions as `create`, `update`, `add_relationship`, `update_relationship` and `delete_relationship`. For details see `/src/Oro/Bundle/ApiBundle/Resources/doc/configuration.md`
#### WorkflowBundle
* Added parameter `$activeOnly` (boolean) with default `false` to method `WorkflowDefinitionRepository::getAllRelatedEntityClasses`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/WorkflowBundle/Entity/Repository/WorkflowDefinitionRepository.php#L56 "Oro\Bundle\WorkflowBundle\Entity\Repository\WorkflowDefinitionRepository::getAllRelatedEntityClasses")</sup>
* Service `oro_workflow.cache` added with standard `\Doctrine\Common\Cache\Cache` interface under namespace `oro_workflow`
* Added processor tag `oro_workflow.processor` and `oro_workflow.processor_bag` service to collect processors.
### Changed
#### ApiBundle
* Changed implementation of `LoadExtendedAssociation`<sup>[[?]](https://github.com/orocrm/platform/tree/2.1.0/src/Oro/Bundle/ApiBundle/Processor/Subresource/Shared/LoadExtendedAssociation.php "Oro\Bundle\ApiBundle\Processor\Subresource\Shared\LoadExtendedAssociation")</sup> and `LoadNestedAssociation`<sup>[[?]](https://github.com/orocrm/platform/tree/2.1.0/src/Oro/Bundle/ApiBundle/Processor/Subresource/Shared/LoadNestedAssociation.php "Oro\Bundle\ApiBundle\Processor\Subresource\Shared\LoadNestedAssociation")</sup> processors
    * now they are extend new base processor `LoadCustomAssociation`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/ApiBundle/Processor/Subresource/Shared/LoadCustomAssociation.php "Oro\Bundle\ApiBundle\Processor\Subresource\Shared\LoadCustomAssociation")</sup>
* Static class `FormUtil`<sup>[[?]](https://github.com/orocrm/platform/tree/2.1.0/src/Oro/Bundle/ApiBundle/Form/FormUtil.php#L15 "Oro\Bundle\ApiBundle\Form\FormUtil")</sup> was replaced with `FormHelper`<sup>[[?]](https://github.com/orocrm/platform/tree/2.1.0/src/Oro/Bundle/ApiBundle/Form/FormHelper.php "Oro\Bundle\ApiBundle\Form\FormHelper")</sup> which is available as a service `oro_api.form_helper`
* Changed implementation of `CompleteDefinition`<sup>[[?]](https://github.com/orocrm/platform/tree/2.1.0/src/Oro/Bundle/ApiBundle/Processor/Config/Shared/CompleteDefinition.php#L130 "Oro\Bundle\ApiBundle\Processor\Config\Shared\CompleteDefinition")</sup> processor. All logic was moved to the following classes:
    * `CompleteAssociationHelper`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/ApiBundle/Processor/Config/Shared/CompleteDefinition/CompleteAssociationHelper.php "Oro\Bundle\ApiBundle\Processor\Config\Shared\CompleteDefinition\CompleteAssociationHelper")</sup><sup>[[?]](https://github.com/orocrm/platform/tree/2.1.0/src/Oro/Bundle/ApiBundle/Processor/Config/Shared/CompleteDefinition/CompleteAssociationHelper.php#L130 "Oro\Bundle\ApiBundle\Processor\Config\Shared\CompleteDefinition\CompleteAssociationHelper")</sup>
    * `CompleteCustomAssociationHelper`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/ApiBundle/Processor/Config/Shared/CompleteDefinition/CompleteCustomAssociationHelper.php "Oro\Bundle\ApiBundle\Processor\Config\Shared\CompleteDefinition\CompleteCustomAssociationHelper")</sup><sup>[[?]](https://github.com/orocrm/platform/tree/2.1.0/src/Oro/Bundle/ApiBundle/Processor/Config/Shared/CompleteDefinition/CompleteCustomAssociationHelper.php#L130 "Oro\Bundle\ApiBundle\Processor\Config\Shared\CompleteDefinition\CompleteCustomAssociationHelper")</sup>
    * `CompleteEntityDefinitionHelper`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/ApiBundle/Processor/Config/Shared/CompleteDefinition/CompleteEntityDefinitionHelper.php "Oro\Bundle\ApiBundle\Processor\Config\Shared\CompleteDefinition\CompleteEntityDefinitionHelper")</sup><sup>[[?]](https://github.com/orocrm/platform/tree/2.1.0/src/Oro/Bundle/ApiBundle/Processor/Config/Shared/CompleteDefinition/CompleteEntityDefinitionHelper.php#L130 "Oro\Bundle\ApiBundle\Processor\Config\Shared\CompleteDefinition\CompleteEntityDefinitionHelper")</sup>
    * `CompleteObjectDefinitionHelper`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/ApiBundle/Processor/Config/Shared/CompleteDefinition/CompleteObjectDefinitionHelper.php "Oro\Bundle\ApiBundle\Processor\Config\Shared\CompleteDefinition\CompleteObjectDefinitionHelper")</sup><sup>[[?]](https://github.com/orocrm/platform/tree/2.1.0/src/Oro/Bundle/ApiBundle/Processor/Config/Shared/CompleteDefinition/CompleteObjectDefinitionHelper.php#L130 "Oro\Bundle\ApiBundle\Processor\Config\Shared\CompleteDefinition\CompleteObjectDefinitionHelper")</sup>
#### DataAuditBundle
* A new string field `ownerDescription` with the database column `owner_description` was added to the entity `Audit`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/DataAuditBundle/Entity/Audit.php "Oro\Bundle\DataAuditBundle\Entity\Audit")</sup> and to the base class `AbstractAudit`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/DataAuditBundle/Entity/AbstractAudit.php "Oro\Bundle\DataAuditBundle\Entity\AbstractAudit")</sup>
#### DataGridBundle
* Class `PreExportMessageProcessor`<sup>[[?]](https://github.com/orocrm/platform/tree/2.2.0/src/Oro/Bundle/DataGridBundle/Async/Export/PreExportMessageProcessor.php "Oro\Bundle\DataGridBundle\Async\Export\PreExportMessageProcessor")</sup> now extends `PreExportMessageProcessorAbstract`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/ImportExportBundle/Async/Export/PreExportMessageProcessorAbstract.php "Oro\Bundle\ImportExportBundle\Async\Export\PreExportMessageProcessorAbstract")</sup> instead of implementing `ExportMessageProcessorAbstract` and `TopicSubscriberInterface`. Service calls `setExportHandler` with `@oro_datagrid.handler.export` and `setExportIdFetcher` with `@oro_datagrid.importexport.export_id_fetcher` were added. The constructor was removed, the parent class constructor is used.
* Class `ExportMessageProcessor`<sup>[[?]](https://github.com/orocrm/platform/tree/2.2.0/src/Oro/Bundle/DataGridBundle/Async/Export/ExportMessageProcessor.php "Oro\Bundle\DataGridBundle\Async\Export\ExportMessageProcessor")</sup> now extends `ExportMessageProcessorAbstract`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/ImportExportBundle/Async/Export/ExportMessageProcessorAbstract.php "Oro\Bundle\ImportExportBundle\Async\Export\ExportMessageProcessorAbstract")</sup> instead of implementing `ExportMessageProcessorAbstract` and `TopicSubscriberInterface`. Service calls `setExportHandler` with `@oro_datagrid.handler.export`, `setExportConnector` with `@oro_datagrid.importexport.export_connector`, `setExportProcessor` with `@oro_datagrid.importexport.processor.export` and `setWriterChain`  with `@oro_importexport.writer.writer_chain` were added. The constructor was removed, the parent class constructor is used.
#### EmailBundle
* Class `AutoResponseRuleController`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/EmailBundle/Controller/AutoResponseRuleController.php "Oro\Bundle\EmailBundle\Controller\AutoResponseRuleController")</sup>
    * action `update` now returns following data: `form`, `saved`, `data`, `metadata`
* template `Resources/views/Form/autoresponseFields.html.twig` was removed as it contained possibility to add collection item after arbitrary item, which is not needed anymore with new form
* template `Resources/views/AutoResponseRule/dialog/update.html.twig` was changed
* template `Resources/views/Configuration/Mailbox/update.html.twig` was changed
* template `EmailBundle/Resources/views/Form/fields.html.twig` was changed
* The `AutoResponseRule::$conditions`<sup>[[?]](https://github.com/orocrm/platform/tree/2.1.0/src/Oro/Bundle/EmailBundle/Entity/AutoResponseRule.php#L46 "Oro\Bundle\EmailBundle\Entity\AutoResponseRule::$conditions")</sup> property was removed. Use methods related to `definition` property instead.
#### FormBundle
* Form types `OroEncodedPlaceholderPasswordType`, `OroEncodedPasswordType` acquired `browser_autocomplete` option with default value set to `false`, which means that password autocomplete is off by default.
#### ImportExportBundle
* Message topics `oro.importexport.cli_import`, `oro.importexport.import_http_validation`, `oro.importexport.import_http` with the constants were removed.
* Class `PreCliImportMessageProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/ImportExportBundle/Async/Import/PreCliImportMessageProcessor.php "Oro\Bundle\ImportExportBundle\Async\Import\PreCliImportMessageProcessor")</sup> now extends `PreImportMessageProcessorAbstract`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/ImportExportBundle/Async/Import/PreImportMessageProcessorAbstract.php "Oro\Bundle\ImportExportBundle\Async\Import\PreImportMessageProcessorAbstract")</sup> instead of implementing `ExportMessageProcessorAbstract` and `TopicSubscriberInterface`.
* Class `PreHttpImportMessageProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/ImportExportBundle/Async/Import/PreHttpImportMessageProcessor.php "Oro\Bundle\ImportExportBundle\Async\Import\PreHttpImportMessageProcessor")</sup> now extends `PreImportMessageProcessorAbstract`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/ImportExportBundle/Async/Import/PreImportMessageProcessorAbstract.php "Oro\Bundle\ImportExportBundle\Async\Import\PreImportMessageProcessorAbstract")</sup> instead of implementing `ExportMessageProcessorAbstract` and `TopicSubscriberInterface`.
* Class `CliImportMessageProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/ImportExportBundle/Async/Import/CliImportMessageProcessor.php "Oro\Bundle\ImportExportBundle\Async\Import\CliImportMessageProcessor")</sup>
    * does not implement TopicSubscriberInterface now.
    * subscribed topic moved to tag in `mq_processor.yml`.  
    * service `oro_importexport.async.http_import` decorates `oro_importexport.async.import`
* Class `HttpImportMessageProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/ImportExportBundle/Async/Import/HttpImportMessageProcessor.php "Oro\Bundle\ImportExportBundle\Async\Import\HttpImportMessageProcessor")</sup>
    * does not implement TopicSubscriberInterface now.
    * subscribed topic moved to tag in `mq_processor.yml`.  
    * service `oro_importexport.async.cli_import` decorates `oro_importexport.async.import`
* Class `PreExportMessageProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/ImportExportBundle/Async/Export/PreExportMessageProcessor.php "Oro\Bundle\ImportExportBundle\Async\Export\PreExportMessageProcessor")</sup> now extends `PreExportMessageProcessorAbstract`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/ImportExportBundle/Async/Export/PreExportMessageProcessorAbstract.php "Oro\Bundle\ImportExportBundle\Async\Export\PreExportMessageProcessorAbstract")</sup> instead of implementing `ExportMessageProcessorAbstract` and `TopicSubscriberInterface`. Service calls `setExportHandler` with `@oro_importexport.handler.export` and `setDoctrineHelper` with `@oro_entity.doctrine_helper` were added.
* Class `ExportMessageProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/ImportExportBundle/Async/Export/ExportMessageProcessor.php "Oro\Bundle\ImportExportBundle\Async\Export\ExportMessageProcessor")</sup> now extends `ExportMessageProcessorAbstract`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/ImportExportBundle/Async/Export/ExportMessageProcessorAbstract.php "Oro\Bundle\ImportExportBundle\Async\Export\ExportMessageProcessorAbstract")</sup> instead of implementing `ExportMessageProcessorAbstract` and `TopicSubscriberInterface`. Service calls `setExportHandler` with `@oro_importexport.handler.export` and `setDoctrineHelper` with `@oro_entity.doctrine_helper` were added.
#### InstallerBundle
* The option `--force` was removed from `oro:install` cli command.
#### SegmentBundle
* Class `Oro/Bundle/SegmentBundle/Entity/Manager/StaticSegmentManager`:
    * method `run` now accept also a dynamic segment
#### WorkflowBundle
* Changed implemented interface of  `Variable`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/WorkflowBundle/Model/Variable.php "Oro\Bundle\WorkflowBundle\Model\Variable")</sup> class from `ParameterInterface`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/ActionBundle/Model/ParameterInterface.php "Oro\Bundle\ActionBundle\Model\ParameterInterface")</sup> to `EntityParameterInterface`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/ActionBundle/Model/EntityParameterInterface.php "Oro\Bundle\ActionBundle\Model\EntityParameterInterface")</sup>
* Class `VariableGuesser`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/WorkflowBundle/Model/VariableGuesser.php "Oro\Bundle\WorkflowBundle\Model\VariableGuesser")</sup>:
    * now extends `AbstractGuesser`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/ActionBundle/Model/AbstractGuesser.php "Oro\Bundle\ActionBundle\Model\AbstractGuesser")</sup>
    * service `oro_workflow.variable_guesser` has parent defined as `oro_action.abstract_guesser`
* Class `WorkflowItemListener`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/WorkflowBundle/EventListener/WorkflowItemListener.php "Oro\Bundle\WorkflowBundle\EventListener\WorkflowItemListener")</sup> auto start workflow part were moved into `WorkflowStartListener`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/WorkflowBundle/EventListener/WorkflowStartListener.php "Oro\Bundle\WorkflowBundle\EventListener\WorkflowStartListener")</sup>
* Class `WorkflowAwareCache`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/WorkflowBundle/EventListener/WorkflowAwareCache.php "Oro\Bundle\WorkflowBundle\EventListener\WorkflowAwareCache")</sup> added:
    * ***purpose***: to check whether an entity has been involved as some workflow related entity in cached manner to avoid DB calls
    * ***methods***:
        - `hasRelatedActiveWorkflows($entity)`
        - `hasRelatedWorkflows($entity)`
    - invalidation of cache occurs on workflow changes events: 
        - `oro.workflow.after_update`
        - `oro.workflow.after_create`
        - `oro.workflow.after_delete`
        - `oro.workflow.activated`
        - `oro.workflow.deactivated`
* Class `WorkflowReplacementSelectType`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/WorkflowBundle/Form/Type/WorkflowReplacementSelectType.php "Oro\Bundle\WorkflowBundle\Form\Type\WorkflowReplacementSelectType")</sup> renamed to `WorkflowReplacementType`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/WorkflowBundle/Form/Type/WorkflowReplacementType.php "Oro\Bundle\WorkflowBundle\Form\Type\WorkflowReplacementType")</sup>
### Deprecated
#### SegmentBundle
* Class `Oro/Bundle/SegmentBundle/Entity/Manager/StaticSegmentManager`:
    * method `bindParameters` is deprecated and will be removed.
### Removed
#### ActionBundle
* The `ButtonListener`<sup>[[?]](https://github.com/orocrm/platform/tree/2.1.0/src/Oro/Bundle/ActionBundle/Datagrid/EventListener/ButtonListener.php "Oro\Bundle\ActionBundle\Datagrid\EventListener\ButtonListener")</sup> class was removed. Logic was transferred to `DatagridActionButtonProvider`<sup>[[?]](https://github.com/laboro/dev/blob/maintenance/2.2/package/platform/src/Oro/Bundle/ActionBundle/Datagrid/Provider/DatagridActionButtonProvider.php "Oro\Bundle\ActionBundle\Datagrid\Provider\DatagridActionButtonProvider")</sup> class.
* Service `oro_action.datagrid.event_listener.button` was removed and new `oro_action.datagrid.action.button_provider` added with tag `oro_datagrid.extension.action.provider`
* The `AttributeGuesser`<sup>[[?]](https://github.com/laboro/dev/blob/maintenance/2.2/package/platform/src/Oro/Bundle/ActionBundle/Model/AttributeGuesser.php "Oro\Bundle\ActionBundle\Model\AttributeGuesser")</sup> class now extends `AbstractGuesser`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/ActionBundle/Model/AbstractGuesser.php "Oro\Bundle\ActionBundle\Model\AbstractGuesser")</sup><sup>[[?]](https://github.com/laboro/dev/blob/maintenance/2.2/package/platform/src/Oro/Bundle/ActionBundle/Model/AbstractGuesser.php "Oro\Bundle\ActionBundle\Model\AbstractGuesser")</sup>
* Service `oro_action.attribute_guesser` has parent defined as `oro_action.abstract_guesser`
#### DataGridBundle
* Removed event `oro_datagrid.datagrid.extension.action.configure-actions.before`, now it is a call of `DatagridActionProviderInterface::hasActions`<sup>[[?]](https://github.com/orocrm/platform/tree/2.2.0/src/Oro/Bundle/DataGridBundle/Extension/Action/DatagridActionProviderInterface.php#L13 "Oro\Bundle\DataGridBundle\Extension\Action\DatagridActionProviderInterface")</sup> of registered through a `oro_datagrid.extension.action.provider` tag services.
* Interface `ManagerInterface`<sup>[[?]](https://github.com/laboro/dev/blob/maintenance/2.2/package/platform/src/Oro/Bundle/DataGridBundle/Datagrid/ManagerInterface.php#L18 "Oro\Bundle\DataGridBundle\Datagrid\ManagerInterface")</sup>
    * the signature of method `getDatagrid` was changed - added new parameter `array $additionalParameters = []`.
* Added abstract entity class `AbstractGridView`<sup>[[?]](https://github.com/laboro/dev/blob/maintenance/2.2/package/platform/src/Oro/Bundle/DataGridBundle/Entity/AbstractGridView.php "Oro\Bundle\DataGridBundle\Entity\AbstractGridView")</sup>
    * entity `GridView`<sup>[[?]](https://github.com/laboro/dev/blob/maintenance/2.2/package/platform/src/Oro/Bundle/DataGridBundle/Entity/GridView.php "Oro\Bundle\DataGridBundle\Entity\GridView")</sup> extends from it
* Added abstract entity class `AbstractGridViewUser`<sup>[[?]](https://github.com/laboro/dev/blob/maintenance/2.2/package/platform/src/Oro/Bundle/DataGridBundle/Entity/AbstractGridViewUser.php "Oro\Bundle\DataGridBundle\Entity\AbstractGridViewUser")</sup>
    * entity `GridViewUser`<sup>[[?]](https://github.com/laboro/dev/blob/maintenance/2.2/package/platform/src/Oro/Bundle/DataGridBundle/Entity/GridViewUser.php "Oro\Bundle\DataGridBundle\Entity\GridViewUser")</sup> extends from it
#### EmailBundle
* Class `AutoResponseRuleType`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/EmailBundle/Form/Type/AutoResponseRuleType.php "Oro\Bundle\EmailBundle\Form\Type\AutoResponseRuleType")</sup>
    * form field `conditions` was removed. Use field `definition` instead.
#### PlatformBundle
* Service `jms_serializer.link` was removed.
#### WorkflowBundle
* Class `TransitionCustomFormHandler`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/WorkflowBundle/Form/Handler/TransitionCustomFormHandler.php "Oro\Bundle\WorkflowBundle\Form\Handler\TransitionCustomFormHandler")</sup> and service `@oro_workflow.handler.transition.form.page_form` removed (see `CustomFormProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/WorkflowBundle/Processor/Transition/CustomFormProcessor.php "Oro\Bundle\WorkflowBundle\Processor\Transition\CustomFormProcessor")</sup>)
* Class `TransitionFormHandler`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/WorkflowBundle/Form/Handler/TransitionFormHandler.php "Oro\Bundle\WorkflowBundle\Form\Handler\TransitionFormHandler")</sup> and service `@oro_workflow.handler.transition.form` removed see replacements:
    * `DefaultFormProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/WorkflowBundle/Processor/Transition/DefaultFormProcessor.php "Oro\Bundle\WorkflowBundle\Processor\Transition\DefaultFormProcessor")</sup>
    * `DefaultFormStartHandleProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/WorkflowBundle/Processor/Transition/DefaultFormStartHandleProcessor.php "Oro\Bundle\WorkflowBundle\Processor\Transition\DefaultFormStartHandleProcessor")</sup>
* Class `TransitionHelper`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/WorkflowBundle/Handler/Helper/TransitionHelper.php "Oro\Bundle\WorkflowBundle\Handler\Helper\TransitionHelper")</sup> and service `@oro_workflow.handler.transition_helper` removed (see `FormSubmitTemplateResponseProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/WorkflowBundle/Processor/Transition/Template/FormSubmitTemplateResponseProcessor.php "Oro\Bundle\WorkflowBundle\Processor\Transition\Template\FormSubmitTemplateResponseProcessor")</sup>)
* Class `StartTransitionHandler`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/WorkflowBundle/Handler/StartTransitionHandler.php "Oro\Bundle\WorkflowBundle\Handler\StartTransitionHandler")</sup> and service `@oro_workflow.handler.start_transition_handler` removed (see `StartHandleProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/WorkflowBundle/Processor/Transition/StartHandleProcessor.php "Oro\Bundle\WorkflowBundle\Processor\Transition\StartHandleProcessor")</sup>)
* Class `TransitionHandler`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/WorkflowBundle/Handler/TransitionHandler.php "Oro\Bundle\WorkflowBundle\Handler\TransitionHandler")</sup> and service `@oro_workflow.handler.transition_handler` removed (see `TransitionHandleProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/WorkflowBundle/Processor/Transition/TransitionHandleProcessor.php "Oro\Bundle\WorkflowBundle\Processor\Transition\TransitionHandleProcessor")</sup>)
* Class `TransitionWidgetHelper`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/WorkflowBundle/Helper/TransitionWidgetHelper.php "Oro\Bundle\WorkflowBundle\Helper\TransitionWidgetHelper")</sup>:
    * Constant `TransitionWidgetHelper::DEFAULT_TRANSITION_TEMPLATE`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/WorkflowBundle/Helper/TransitionWidgetHelper.php#L0 "Oro\Bundle\WorkflowBundle\Helper\TransitionWidgetHelper::DEFAULT_TRANSITION_TEMPLATE")</sup> moved into `DefaultFormTemplateResponseProcessor::DEFAULT_TRANSITION_TEMPLATE`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/WorkflowBundle/Processor/Transition/Template/DefaultFormTemplateResponseProcessor.php#L0 "Oro\Bundle\WorkflowBundle\Processor\Transition\Template\DefaultFormTemplateResponseProcessor::DEFAULT_TRANSITION_TEMPLATE")</sup>
    * Constant `TransitionWidgetHelper::DEFAULT_TRANSITION_CUSTOM_FORM_TEMPLATE`<sup>[[?]](https://github.com/oroinc/platform/tree/2.2.0/src/Oro/Bundle/WorkflowBundle/Helper/TransitionWidgetHelper.php#L0 "Oro\Bundle\WorkflowBundle\Helper\TransitionWidgetHelper::DEFAULT_TRANSITION_CUSTOM_FORM_TEMPLATE")</sup> moved into `CustomFormTemplateResponseProcessor::DEFAULT_TRANSITION_CUSTOM_FORM_TEMPLATE`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/WorkflowBundle/Processor/Transition/Template/CustomFormTemplateResponseProcessor.php#L0 "Oro\Bundle\WorkflowBundle\Processor\Transition\Template\CustomFormTemplateResponseProcessor::DEFAULT_TRANSITION_CUSTOM_FORM_TEMPLATE")</sup>
### Fixed
#### ApiBundle
* Fixed handling of `property_path` option from `api.yml` for cases when the property path contains several fields, e.g. `customerAssociation.account`
## 2.1.7 (2017-07-04)
## 2.1.6 (2017-06-30)
## 2.1.5 (2017-06-16)
## 2.1.4 (2017-06-01)
## 2.1.3 (2017-04-24)
## 2.1.2 (2017-05-11)
## 2.1.1 (2017-04-26)
## 2.1.0 (2017-03-30)
[Show detailed list of changes](file-incompatibilities-2-1-0.md)

#### Action Component
* Added interface `Oro\Component\Action\Model\DoctrineTypeMappingExtensionInterface`.
* Added Class `Oro\Component\Action\Model\DoctrineTypeMappingExtension`. That can be used as base for services definitions
#### ActionBundle
* Added aware interface `ApplicationProviderAwareInterface`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ActionBundle/Provider/ApplicationProviderAwareInterface.php "Oro\Bundle\ActionBundle\Provider\ApplicationProviderAwareInterface")</sup> and trait `ApplicationProviderAwareTrait`
* Added new action with alias `resolve_destination_page` and class `ResolveDestinationPage`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ActionBundle/Action/ResolveDestinationPage.php "Oro\Bundle\ActionBundle\Action\ResolveDestinationPage")</sup>
* Added interfaces `ParameterInterface`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ActionBundle/Model/ParameterInterface.php "Oro\Bundle\ActionBundle\Model\ParameterInterface")</sup> and `EntityParameterInterface`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ActionBundle/Model/EntityParameterInterface.php "Oro\Bundle\ActionBundle\Model\EntityParameterInterface")</sup>
* Added interfaces `ParameterInterface`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ActionBundle/Model/ParameterInterface.php "Oro\Bundle\ActionBundle\Model\ParameterInterface")</sup> and `EntityParameterInterface`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ActionBundle/Model/EntityParameterInterface.php "Oro\Bundle\ActionBundle\Model\EntityParameterInterface")</sup>
* Added new tag `oro.action.extension.doctrine_type_mapping` to collect custom doctrine type mappings used to resolve types for serialization at `AttributeGuesser`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ActionBundle/Model/AttributeGuesser.php "Oro\Bundle\ActionBundle\Model\AttributeGuesser")</sup>
* Added second optional argument `OperationFindCriteria $criteria = null`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ActionBundle/Model/Criteria/OperationFindCriteria $criteria = null.php "Oro\Bundle\ActionBundle\Model\Criteria\OperationFindCriteria $criteria = null")</sup> to method `OperationRegistry`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ActionBundle/Model/OperationRegistry.php "Oro\Bundle\ActionBundle\Model\OperationRegistry")</sup>
#### BatchBundle
* Added `BufferedIdentityQueryResultIterator`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/BatchBundle/ORM/Query/BufferedIdentityQueryResultIterator.php "Oro\Bundle\BatchBundle\ORM\Query\BufferedIdentityQueryResultIterator")</sup> that allows to iterate through changing dataset
#### DataGridBundle
* Added method `public function getName()::string` to interface `ViewInterface`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/DataGridBundle/Extension/GridViews/ViewInterface.php "Oro\Bundle\DataGridBundle\Extension\GridViews\ViewInterface")</sup>
* Class `PreExportMessageProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/DataGridBundle/Async/Export/PreExportMessageProcessor.php "Oro\Bundle\DataGridBundle\Async\Export\PreExportMessageProcessor")</sup> and its service `oro_datagrid.async.pre_export` were added.
* Class `DatagridExportIdFetcher`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/DataGridBundle/ImportExport/DatagridExportIdFetcher.php "Oro\Bundle\DataGridBundle\ImportExport\DatagridExportIdFetcher")</sup> and its service `oro_datagrid.importexport.export_id_fetcher` were added.
#### EmailBundle
* Added `EmailSynchronizerInterface`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/EmailBundle/Sync/EmailSynchronizerInterface.php "Oro\Bundle\EmailBundle\Sync\EmailSynchronizerInterface")</sup> and implemented it in `AbstractEmailSynchronizer`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/EmailBundle/Sync/AbstractEmailSynchronizer.php "Oro\Bundle\EmailBundle\Sync\AbstractEmailSynchronizer")</sup>
#### EntityBundle
* Added class `Oro\Bundle\EntityBundle\ORM\DiscriminatorMapListener' that should be used for entities with single table inheritance.
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
#### ImportExportBundle
* Class `PreCliImportMessageProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/Async/Import/PreCliImportMessageProcessor.php "Oro\Bundle\ImportExportBundle\Async\Import\PreCliImportMessageProcessor")</sup> and its service `oro_importexport.async.pre_cli_import` were added.
* Class `PreHttpImportMessageProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/Async/Import/PreHttpImportMessageProcessor.php "Oro\Bundle\ImportExportBundle\Async\Import\PreHttpImportMessageProcessor")</sup> and its service `oro_importexport.async.pre_http_import` were added.
* Class `SplitterChain`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/Splitter/SplitterChain.php "Oro\Bundle\ImportExportBundle\Splitter\SplitterChain")</sup> and its service `oro_importexport.async.send_import_error_notification` were added.
* Class `FileManager`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/File/FileManager.php "Oro\Bundle\ImportExportBundle\File\FileManager")</sup> and its service `oro_importexport.file.file_manager` were added. We should use it instead of the `FileSystemOperator`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/File/FileSystemOperator.php "Oro\Bundle\ImportExportBundle\File\FileSystemOperator")</sup>
* Command `oro:cron:import-clean-up-storage` (class `CleanupStorageCommand`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/Command/Cron/CleanupStorageCommand.php "Oro\Bundle\ImportExportBundle\Command\Cron\CleanupStorageCommand")</sup>) was added.
#### NavigationBundle
* CLass `Manager`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/NavigationBundle/Manager.php "Oro\Bundle\NavigationBundle\Manager")</sup> added method `moveMenuItems`
* Class `MenuUpdateDatasource`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/NavigationBundle/Datagrid/MenuUpdateDatasource.php "Oro\Bundle\NavigationBundle\Datagrid\MenuUpdateDatasource")</sup>:
    * changed type of property `protected $menuConfiguration` from `array` to `MenuConfiguration`
### Changed
#### ActionBundle
* The service `oro_action.twig.extension.operation` was marked as `private`
#### AddressBundle
* The service `oro_address.twig.extension.phone` was marked as `private`
#### AsseticBundle
* The service `oro_assetic.twig.extension` was marked as `private`
#### AttachmentBundle
* The service `oro_attachment.twig.file_extension` was marked as `private`
* Class `FileManager`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/AttachmentBundle/Manager/FileManager.php "Oro\Bundle\AttachmentBundle\Manager\FileManager")</sup>
    * method `writeStreamToStorage` was changed to `public`
#### ChainProcessor Component
* Fixed an issue with invalid execution order of processors. The issue was that processors from different groups are intersected. During the fix the calculation of internal priorities of processors was changed, this may affect existing configuration of processors in case if you have common (not bound to any action) processors and ungrouped processors which should work with regular grouped processors.
    The previous priority rules:
    | Processor type | Processor priority | Group priority |
    |----------------|--------------------|----------------|
    | initial common processors | from -255 to 255 |  |
    | initial ungrouped processors | from -255 to 255 |  |
    | grouped processors | from -255 to 255 | from -254 to 252 |
    | final ungrouped processors | from -65535 to -65280 |  |
    | final common processors | from min int to -65536 |  |
    The new priority rules:
    | Processor type | Processor priority | Group priority |
    |----------------|--------------------|----------------|
    | initial common processors | greater than or equals to 0 |  |
    | initial ungrouped processors | greater than or equals to 0 |  |
    | grouped processors | from -255 to 255 | from -255 to 255 |
    | final ungrouped processors | less than 0 |  |
    | final common processors | less than 0 |  |
    So, the new rules means that:
        * common and ungrouped processors with the priority greater than or equals to 0 will be executed before grouped processors
        * common and ungrouped processors with the priority less than 0 will be executed after grouped processors
        * now there are no any magic numbers for priorities of any processors
#### ConfigBundle
* The service `oro_config.twig.config_extension` was marked as `private`
#### CurrencyBundle
* The service `oro_currency.twig.currency` was marked as `private`
#### DashboardBundle
* The service `oro_dashboard.twig.extension` was marked as `private`
#### DataGridBundle
* Class `GridController`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/DataGridBundle/Controller/GridController.php "Oro\Bundle\DataGridBundle\Controller\GridController")</sup>
    * renamed method `filterMetadata` to `filterMetadataAction`
* Class `ExportHandler`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/DataGridBundle/Handler/ExportHandler.php "Oro\Bundle\DataGridBundle\Handler\ExportHandler")</sup> (service `oro_datagrid.handler`) changed its service calls: it doesn't call `setRouter` and `setConfigManager` any more but calls `setFileManager` now.
* Topic `oro.datagrid.export` doesn't start datagrid export any more. Use `oro.datagrid.pre_export` topic instead.
#### DependencyInjection Component
* Class `Oro\Component\DependencyInjection\ServiceLinkRegistry` together with
`Oro\Component\DependencyInjection\ServiceLinkRegistryAwareInterface` for injection awareness. Can be used to provide
injection of a collection of services that are registered in system, but there no need to instantiate
all of them on every runtime. The registry has `@service_container` dependency (`Symfony\Component\DependencyInjection\ContainerInterface`)
and uses `Oro\Component\DependencyInjection\ServiceLink` instances internally. It can register public services by `ServiceLinkRegistry::add`
with `service_id` and `alias`. Later service can be resolved from registry by its alias on demand (method `::get($alias)`).
* Class `Oro\Component\DependencyInjection\Compiler\TaggedServiceLinkRegistryCompilerPass` to easily setup a tag by 
which services will be gathered into `Oro\Component\DependencyInjection\ServiceLinkRegistry` and then injected to 
provided service (usually that implements `Oro\Component\DependencyInjection\ServiceLinkRegistryAwareInterface`).
#### EmailBundle
* Class `AssociationManager`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/EmailBundle/Async/Manager/AssociationManager.php "Oro\Bundle\EmailBundle\Async\Manager\AssociationManager")</sup>
    * changed the return type of `getOwnerIterator` method from `BufferedQueryResultIterator` to `\Iterator`
* The service `oro_email.twig.extension.email` was marked as `private`
#### EmbeddedFormBundle
* The service `oro_embedded_form.back_link.twig.extension` was marked as `private`
#### EntityBundle
* The service `oro_entity.twig.extension.entity` was marked as `private`
#### EntityConfigBundle
* Class `ConfigCache`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/EntityConfigBundle/Config/ConfigCache.php "Oro\Bundle\EntityConfigBundle\Config\ConfigCache")</sup>:
    * changed the visibility of `cache` property from `protected` to `private`
    * changed the visibility of `modelCache` property from `protected` to `private`
    * the implementation was changed significantly, by performance reasons. The most of `protected` methods were removed or marked as `private`
#### EntityExtendBundle
* Class `ExtendExtension`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/EntityExtendBundle/Migration/Extension/ExtendExtension.php "Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtension")</sup>
    * calls to `addManyToManyRelation`, `addManyToOneRelation` methods now create unidirectional relations.
    To create bidirectional relation you _MUST_ call `*InverseRelation` method respectively
    * call to `addOneToManyRelation` creates bidirectional relation according to Doctrine [documentation](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-bidirectional)
    * throw exception when trying to use not allowed option while creating relation in migration
* To be able to create bidirectional relation between entities and use "Reuse existing relation" functionality on UI you _MUST_ select "bidirectional" field while creating relation
* The service `oro_entity_extend.twig.extension.dynamic_fields` was marked as `private`
* The service `oro_entity_extend.twig.extension.enum` was marked as `private`
#### EntityMergeBundle
* The service `oro_entity_merge.twig.extension` was marked as `private`
#### EntityPaginationBundle
* The service `oro_entity_pagination.twig_extension.entity_pagination` was marked as `private`
#### FeatureToggleBundle
* The service `oro_featuretoggle.twig.feature_extension` was marked as `private`
#### FormBundle
* The service `oro_form.twig.form_extension` was marked as `private`
* Class `JsValidationExtension`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/FormBundle/Twig/JsValidationExtension.php "Oro\Bundle\FormBundle\Twig\JsValidationExtension")</sup> was removed. Its functionality was moved to `FormExtension`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/FormBundle/Twig/FormExtension.php "Oro\Bundle\FormBundle\Twig\FormExtension")</sup>
* Class `UpdateHandlerFacade`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/FormBundle/Model/UpdateHandlerFacade.php "Oro\Bundle\FormBundle\Model\UpdateHandlerFacade")</sup> added as a replacement of standard `UpdateHandler`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/FormBundle/Model/UpdateHandler.php "Oro\Bundle\FormBundle\Model\UpdateHandler")</sup>. So please consider to use it when for a new entity management development.
* Interface `FormHandlerInterface`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/FormBundle/Form/Handler/FormHandlerInterface.php "Oro\Bundle\FormBundle\Form\Handler\FormHandlerInterface")</sup> added for standard form handlers.
* Class `FormHandler`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/FormBundle/Form/Handler/FormHandler.php "Oro\Bundle\FormBundle\Form\Handler\FormHandler")</sup> added (service 'oro_form.form.handler.default') as default form processing mechanism.
* Tag `oro_form.form.handler` added to register custom form handlers under its `alias`.
* Class `FormHandlerRegistry`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/FormBundle/Model/FormHandlerRegistry.php "Oro\Bundle\FormBundle\Model\FormHandlerRegistry")</sup> added to collect tagged with `oro_form.form.handler` services.
* Class `CallbackFormHandler`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/FormBundle/Form/Handler/CallbackFormHandler.php "Oro\Bundle\FormBundle\Form\Handler\CallbackFormHandler")</sup> added as interface compatibility helper for callable.
* Interface `FormTemplateDataProviderInterface`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/FormBundle/Provider/FormTemplateDataProviderInterface.php "Oro\Bundle\FormBundle\Provider\FormTemplateDataProviderInterface")</sup>  added for common update template data population.
* Class `FromTemplateDataProvider`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/FormBundle/Provider/FromTemplateDataProvider.php "Oro\Bundle\FormBundle\Provider\FromTemplateDataProvider")</sup> (service `oro_form.provider.from_template_data.default`) as default update template data provider.
* Tag `oro_form.form_template_data_provider` added to register custom update template data providers.
* Class `FormTemplateDataProviderRegistry`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/FormBundle/Model/FormTemplateDataProviderRegistry.php "Oro\Bundle\FormBundle\Model\FormTemplateDataProviderRegistry")</sup> added to collect tagged with `oro_form.form_template_data_provider` services.
* Class `CallbackFormTemplateDataProvider`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/FormBundle/Provider/CallbackFormTemplateDataProvider.php "Oro\Bundle\FormBundle\Provider\CallbackFormTemplateDataProvider")</sup> added as interface compatibility helper for callable.
#### HelpBundle
* The service `oro_help.twig.extension` was marked as `private`
#### ImportExportBundle
* Class `ExportMessageProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/Async/Export/ExportMessageProcessor.php "Oro\Bundle\ImportExportBundle\Async\Export\ExportMessageProcessor")</sup>
    * changed the namespace from `Async`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/Async.php "Oro\Bundle\ImportExportBundle\Async")</sup> to `Export`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/Async/Export.php "Oro\Bundle\ImportExportBundle\Async\Export")</sup>
    * construction signature was changed now it takes next arguments:
        * ExportHandler $exportHandler,
        * JobRunner $jobRunner,
        * DoctrineHelper $doctrineHelper,
        * TokenStorageInterface $tokenStorage,
        * LoggerInterface $logger,
        * JobStorage $jobStorage
* Class `AbstractImportHandler`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/Handler/AbstractImportHandler.php "Oro\Bundle\ImportExportBundle\Handler\AbstractImportHandler")</sup> (service `oro_importexport.handler.import.abstract`) changed its service calls: it doesn't call `setRouter` and `setConfigManager` any more but calls `setReaderChain` now.
* Command `oro:import:csv` (class `ImportCommand`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/Command/ImportCommand.php "Oro\Bundle\ImportExportBundle\Command\ImportCommand")</sup>) was renamed to `oro:import:file`
* Class `ImportExportJobSummaryResultService`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/Async/ImportExportJobSummaryResultService.php "Oro\Bundle\ImportExportBundle\Async\ImportExportJobSummaryResultService")</sup> was renamed to `ImportExportResultSummarizer`. It will be moved after add supporting templates in notification process.
* Route `oro_importexport_import_error_log` with path `/import_export/import-error/{jobId}.log` was renamed to `oro_importexport_job_error_log` with path `/import_export/job-error-log/{jobId}.log`
#### IntegrationBundle
* The service `oro_integration.twig.integration` was marked as `private`
#### LocaleBundle
* The following services were marked as `private`:
    * `oro_locale.twig.date_format`
    * `oro_locale.twig.locale`
    * `oro_locale.twig.calendar`
    * `oro_locale.twig.address`
    * `oro_locale.twig.number`
    * `oro_locale.twig.localization`
    * `oro_locale.twig.date_time_organization`
#### MessageQueue Component
* Class `Oro\Component\MessageQueue\Client\Meta\DestinationsCommand` implement `ContainerAwareInterface`
* Class `Oro\Component\MessageQueue\Client\Meta\TopicsCommand` implement `ContainerAwareInterface`
* Class `Oro\Component\MessageQueue\Client\ConsumeMessagesCommand` implement `ContainerAwareInterface`
* Class `Oro\Component\MessageQueue\Client\CreateQueuesCommand` implement `ContainerAwareInterface`
* Unify percentage value for `Job::$jobProgress`. Now 100% is stored as 1 instead of 100.
* Class `Oro\Component\MessageQueue\Job\CalculateRootJobStatusService` was renamed to `Oro\Component\MessageQueue\Job\RootJobStatusCalculator`
#### MessageQueueBundle
* The service `oro_message_queue.job.calculate_root_job_status_service` was renamed to `oro_message_queue.job.root_job_status_calculator` and marked as `private`
* The service `oro_message_queue.job.calculate_root_job_progress_service` was renamed to `oro_message_queue.job.root_job_progress_calculator` and marked as `private`
#### MigrationBundle
* The service `oro_migration.twig.schema_dumper` was marked as `private`
* Class `HelpExtension`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/MigrationBundle/Twig/HelpExtension.php "Oro\Bundle\MigrationBundle\Twig\HelpExtension")</sup>
    * property `$managerRegistry` was renamed to `$doctrine`
#### NavigationBundle
* The following services were marked as `private`:
    * `oro_menu.twig.extension`
    * `oro_navigation.title_service.twig.extension`
#### PlatformBundle
* The service `oro_platform.twig.platform_extension` was marked as `private`
#### ReminderBundle
* The service `oro_reminder.twig.extension` was marked as `private`
#### RequireJSBundle
* The service `oro_requirejs.twig.requirejs_extension` was marked as `private`
#### ScopeBundle
* Class `ScopeManager`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ScopeBundle/Manager/ScopeManager.php "Oro\Bundle\ScopeBundle\Manager\ScopeManager")</sup>:
    * changed the return type of `findBy` method from `BufferedQueryResultIterator` to `BufferedQueryResultIteratorInterface`
    * changed the return type of `findRelatedScopes` method from `BufferedQueryResultIterator` to `BufferedQueryResultIteratorInterface`
#### SearchBundle
* `entityManager` instead of `em` should be used in `BaseDriver` children
* `OrmIndexer` should be decoupled from `DbalStorer` dependency
* The service `oro_search.twig.search_extension` was marked as `private`
* The `oro:search:reindex` command now works synchronously by default. Use the `--scheduled` parameter if you need the old, async behaviour
#### SecurityBundle
* Service overriding in compiler pass was replaced by service decoration for next services:
    * `sensio_framework_extra.converter.doctrine.orm`
    * `security.acl.dbal.provider`
    * `security.acl.cache.doctrine`
    * `security.acl.voter.basic_permissions`
* `AbstractOwnerTreeProvider`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/SecurityBundle/Owner/AbstractOwnerTreeProvider.php "Oro\Bundle\SecurityBundle\Owner\AbstractOwnerTreeProvider")</sup>:
    * changed the visibility of `$tree` property from `protected` to `private`
#### SegmentBundle
* The service `oro_segment.twig.extension.segment` was marked as `private`
#### SidebarBundle
* The service `oro_sidebar.twig.extension` was marked as `private`
#### SyncBundle
* The service `oro_wamp.twig.sync_extension` was marked as `private`
#### TagBundle
* The service `oro_tag.twig.tag.extension` was marked as `private`
#### TestFrameworkBundle
* `@dbIsolation annotation removed, applied as defult behavior`
* `@dbReindex annotation removed, use \Oro\Bundle\SearchBundle\Tests\Functional\SearchExtensionTrait::clearIndexTextTable`
#### ThemeBundle
* The service `oro_theme.twig.extension` was marked as `private`
#### TranslationBundle
* The service `oro_translation.twig.translation.extension` was marked as `private`
* Added `array $filtersType = []` parameter to the `generate` method, that receives an array of filter types to be applies on the route in order to support filters such as `contains` when generating routes
* Class `AddLanguageType`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/TranslationBundle/Form/Type/AddLanguageType.php "Oro\Bundle\TranslationBundle\Form\Type\AddLanguageType")</sup>
    * Changed parent from type from `locale` to `oro_choice`
* Class `TranslationPackagesProviderExtension`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/TranslationBundle/Provider/TranslationPackagesProviderExtension.php "Oro\Bundle\TranslationBundle\Provider\TranslationPackagesProviderExtension")</sup>
    * removed constant `PACKAGE_NAME`
    * added constructor
    * added method `public function addPackage(string $packageAlias, string $packageName, string $suffix = '')`
* Updated service definition for `oro_translation.extension.transtation_packages_provider`
    * changed publicity to `false`
#### UIBundle
* The following services were marked as `private`:
    * `oro_ui.twig.extension.formatter`
    * `oro_ui.twig.tab_extension`
    * `oro_ui.twig.html_tag`
    * `oro_ui.twig.placeholder_extension`
    * `oro_ui.twig.ui_extension`
#### WindowsBundle
* The service `oro_windows.twig.extension` was marked as `private`
#### WorkflowBundle
* Created action `@get_available_workflow_by_record_group`
    * class `GetAvailableWorkflowByRecordGroup`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/WorkflowBundle/Model/Action/GetAvailableWorkflowByRecordGroup.php "Oro\Bundle\WorkflowBundle\Model\Action\GetAvailableWorkflowByRecordGroup")</sup>
* Added third argument `string $responseMessage = null` to method `TransitionHelper::createCompleteResponse()`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/WorkflowBundle/Handle/Helper/TransitionHelper.php#L0 "Oro\Bundle\WorkflowBundle\Handle\Helper\TransitionHelper::createCompleteResponse()")</sup>
* Added `variable_definitions` to workflow definition
* Added new `CONFIGURE` permission for workflows
* Interface `AttributeNormalizer`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/WorkflowBundle/Serializer/Normalizer/AttributeNormalizer.php "Oro\Bundle\WorkflowBundle\Serializer\Normalizer\AttributeNormalizer")</sup>:
    * changed 2nd parameter in method's signature from `Attribute $attribute` to `ParameterInterface $attribute` in next methods:
        * `normalize`
        * `denormalize`
        * `supportsNormalization`
        * `supportsDenormalization`
* Abstract class `AbstractWorkflowTranslationFieldsIterator`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/WorkflowBundle/Translation/AbstractWorkflowTranslationFieldsIterator.php "Oro\Bundle\WorkflowBundle\Translation\AbstractWorkflowTranslationFieldsIterator")</sup>:
    * added protected method `&variableFields(array &$configuration, \ArrayObject $context)`
* The service `oro_workflow.twig.extension.workflow` was marked as `private`
* Removed implementation of `CronCommandInterface`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/CronBundle/Command/CronCommandInterface.php "Oro\Bundle\CronBundle\Command\CronCommandInterface")</sup> from `HandleProcessTriggerCommand`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/WorkflowBundle/Command/HandleProcessTriggerCommand.php "Oro\Bundle\WorkflowBundle\Command\HandleProcessTriggerCommand")</sup>.
* Removed implementation of `CronCommandInterface`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/CronBundle/Command/CronCommandInterface.php "Oro\Bundle\CronBundle\Command\CronCommandInterface")</sup> from `HandleTransitionCronTriggerCommand`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/WorkflowBundle/Command/HandleTransitionCronTriggerCommand.php "Oro\Bundle\WorkflowBundle\Command\HandleTransitionCronTriggerCommand")</sup>.
* Class `WorkflowTranslationHelper`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/WorkflowBundle/Helper/WorkflowTranslationHelper.php "Oro\Bundle\WorkflowBundle\Helper\WorkflowTranslationHelper")</sup>:
    * added public method `generateDefinitionTranslationKeys`
    * added public method `generateDefinitionTranslations`
    * changed access level from `private` to `public` for method `findValue`
### Deprecated
#### ActionBundle
* `RouteExists`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ActionBundle/Condition/RouteExists.php "Oro\Bundle\ActionBundle\Condition\RouteExists")</sup> deprecated because of:
    * work with `RouteCollection` is performance consuming
    * it was used to check bundle presence, which could be done with `service_exists`
* Implemented `EntityParameterInterface`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ActionBundle/Model/EntityParameterInterface.php "Oro\Bundle\ActionBundle\Model\EntityParameterInterface")</sup> interface in `Attribute`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ActionBundle/Model/Attribute.php "Oro\Bundle\ActionBundle\Model\Attribute")</sup> class
#### BatchBundle
* `DeletionQueryResultIterator`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/BatchBundle/ORM/Query/DeletionQueryResultIterator.php "Oro\Bundle\BatchBundle\ORM\Query\DeletionQueryResultIterator")</sup> is deprecated. Use `BufferedIdentityQueryResultIterator`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/BatchBundle/ORM/Query/BufferedIdentityQueryResultIterator.php "Oro\Bundle\BatchBundle\ORM\Query\BufferedIdentityQueryResultIterator")</sup> instead
#### CronBundle
* Interface `CronCommandInterface`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/CronBundle/Command/CronCommandInterface.php "Oro\Bundle\CronBundle\Command\CronCommandInterface")</sup>
    * deprecated method `isActive`
#### DataGridBundle
* `DeletionIterableResult`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/DataGridBundle/Datasource/Orm/DeletionIterableResult.php "Oro\Bundle\DataGridBundle\Datasource\Orm\DeletionIterableResult")</sup> is deprecated. Use `BufferedIdentityQueryResultIterator`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/BatchBundle/ORM/Query/BufferedIdentityQueryResultIterator.php "Oro\Bundle\BatchBundle\ORM\Query\BufferedIdentityQueryResultIterator")</sup> instead
* The service `oro_datagrid.twig.datagrid` was marked as `private`
#### DistributionBundle
* The method `ErrorHandler::handle`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/DistributionBundle/Error/ErrorHandler.php#L96 "Oro\Bundle\DistributionBundle\Error\ErrorHandler::handle")</sup> is deprecated. Use `ErrorHandler::handleErrors`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/DistributionBundle/Error/ErrorHandler.php#L48 "Oro\Bundle\DistributionBundle\Error\ErrorHandler::handleErrors")</sup> instead.
#### EmailBundle
* The service `oro_email.link.autoresponserule_manager` was marked as deprecated
#### EntityConfigBundle
* The service `oro_entity_config.link.config_manager` was marked as deprecated
#### EntityExtendBundle
* Class `ExtendExtension`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/EntityExtendBundle/Migration/Extension/ExtendExtension.php "Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtension")</sup>
    * deprecated `addOneToManyInverseRelation`
#### FormBundle
* Class `UpdateHandler`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/FormBundle/Model/UpdateHandler.php "Oro\Bundle\FormBundle\Model\UpdateHandler")</sup>:
    * marked as deprecated, use `UpdateHandlerFacade`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/FormBundle/Model/UpdateHandlerFacade.php "Oro\Bundle\FormBundle\Model\UpdateHandlerFacade")</sup> (service `oro_form.update_handler`) instead
#### ImportExportBundle
* Class `FileSystemOperator`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/File/FileSystemOperator.php "Oro\Bundle\ImportExportBundle\File\FileSystemOperator")</sup> is deprecated now. Use `FileManager`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/File/FileManager.php "Oro\Bundle\ImportExportBundle\File\FileManager")</sup> instead.
#### LayoutBundle
* Class `LayoutListener`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/LayoutBundle/EventListener/LayoutListener.php "Oro\Bundle\LayoutBundle\EventListener\LayoutListener")</sup>
    * the visibility of `$layoutHelper` property changed from `protected` to `private`
* Changed default value option name for `page_title` block type, from `text` to `defaultValue`
* Added alias `layout` for `oro_layout.layout_manager` service to make it more convenient to access it from container
#### LocaleBundle
* Removed the following parameters from DIC:
    * `oro_locale.twig.date_format.class`
    * `oro_locale.twig.locale.class`
    * `oro_locale.twig.calendar.class`
    * `oro_locale.twig.date_time.class`
    * `oro_locale.twig.name.class`
    * `oro_locale.twig.address.class`
    * `oro_locale.twig.number.class`
#### SearchBundle
* `DbalStorer` is deprecated. If you need its functionality, please compose your class with `DBALPersistenceDriverTrait`
* Deprecated services and classes:
    * `oro_search.search.engine.storer`
    * `DbalStorer`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/SearchBundle/Engine/Orm/DbalStorer.php "Oro\Bundle\SearchBundle\Engine\Orm\DbalStorer")</sup>
* Interface `EngineV2Interface`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/SearchBundle/Engine/EngineV2Interface.php "Oro\Bundle\SearchBundle\Engine\EngineV2Interface")</sup> marked as deprecated - please, use `EngineInterface`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/SearchBundle/Engine/EngineInterface.php "Oro\Bundle\SearchBundle\Engine\EngineInterface")</sup> instead
* `PdoMysql`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/SearchBundle/Engine/PdoMysql.php "Oro\Bundle\SearchBundle\Engine\PdoMysql")</sup> `getWords` method is deprecated. All non alphanumeric chars are removed in `BaseDriver`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/SearchBundle/Engine/BaseDriver.php "Oro\Bundle\SearchBundle\Engine\BaseDriver")</sup> `filterTextFieldValue` from fulltext search for MySQL and PgSQL
#### SecurityBundle
* The service `oro_security.twig.security_extension` was marked as `private`
#### TagBundle
* Class `AbstractTagsExtension`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/TagBundle/Grid/AbstractTagsExtension.php "Oro\Bundle\TagBundle\Grid\AbstractTagsExtension")</sup>
    * added UnsupportedGridPrefixesTrait
#### Tree Component
* `Oro\Component\Tree\Handler\AbstractTreeHandler`:
    * added method `getTreeItemList`
#### UserBundle
* The service `oro_user.twig.user_extension` was marked as `private`
* Added Configurable Permission `default` for View and Edit pages of User Role (see [configurable-permissions.md](./src/Oro/Bundle/SecurityBundle/Resources/doc/configurable-permissions.md))
* Class `StatusController`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/UserBundle/Controller/StatusController.php "Oro\Bundle\UserBundle\Controller\StatusController")</sup>
    * renamed method `setCurrentStatus` to `setCurrentStatusAction`
    * renamed method `clearCurrentStatus` to `clearCurrentStatusAction`
### Removed
#### AddressBundle
* The parameter `oro_address.twig.extension.phone.class` was removed from DIC
* The service `oro_address.provider.phone.link` was removed
#### AsseticBundle
* The parameter `oro_assetic.twig_extension.class` was removed from DIC
#### AttachmentBundle
* The parameter `oro_attachment.twig.file_extension.class` was removed from DIC
#### ConfigBundle
* The parameter `oro_config.twig_extension.class` was removed from DIC
#### CurrencyBundle
* The parameter `oro_currency.twig.currency.class` was removed from DIC
#### DashboardBundle
* The service `oro_dashboard.widget_config_value.date_range.converter.link` was removed
#### DataGridBundle
* Class `GroupConcat`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/DataGridBundle/Engine/Orm/PdoMysql/GroupConcat.php "Oro\Bundle\DataGridBundle\Engine\Orm\PdoMysql\GroupConcat")</sup> was removed. Use `GroupConcat` from package `oro/doctrine-extensions` instead.
#### EmailBundle
* `Oro/Bundle/EmailBundle/Migrations/Data/ORM/EnableEmailFeature` removed, feature enabled by default
* The parameter `oro_email.twig.extension.email.class` was removed from DIC
#### EmbeddedFormBundle
* The parameter `oro_embedded_form.back_link.twig.extension.class` was removed from DIC
#### EntityBundle
* The parameter `oro_entity.twig.extension.entity.class` was removed from DIC
* The service `oro_entity.fallback.resolver.entity_fallback_resolver.link` was removed
#### EntityConfigBundle
* The parameter `oro_entity_config.twig.extension.config.class` was removed from DIC
* The service `oro_entity_config.twig.extension.config` was marked as `private`
* The service `oro_entity_config.twig.extension.dynamic_fields_attribute_decorator` was marked as `private`
#### EntityExtendBundle
* The parameter `oro_entity_extend.twig.extension.dynamic_fields.class` was removed from DIC
* The parameter `oro_entity_extend.twig.extension.enum.class` was removed from DIC
#### EntityMergeBundle
* The parameter `oro_entity_merge.twig.extension.class` was removed from DIC
#### EntityPaginationBundle
* The parameter `oro_entity_pagination.twig_extension.entity_pagination.class` was removed from DIC
#### FormBundle
* The parameter `oro_form.twig.form.class` was removed from DIC
* The parameter `oro_form.twig.js_validation_extension.class` was removed from DIC
* The service `oro_form.twig.js_validation_extension` was removed from DIC
#### HelpBundle
* The parameter `oro_help.twig.extension.class` was removed from DIC
#### ImportExportBundle
* Class `AbstractPreparingHttpImportMessageProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/Async/Import/AbstractPreparingHttpImportMessageProcessor.php "Oro\Bundle\ImportExportBundle\Async\Import\AbstractPreparingHttpImportMessageProcessor")</sup> and its service `oro_importexport.async.abstract_preparing_http_import` were removed. You can use `PreHttpImportMessageProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/Async/Import/PreHttpImportMessageProcessor.php "Oro\Bundle\ImportExportBundle\Async\Import\PreHttpImportMessageProcessor")</sup> and `HttpImportMessageProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/Async/Import/HttpImportMessageProcessor.php "Oro\Bundle\ImportExportBundle\Async\Import\HttpImportMessageProcessor")</sup>.
* Class `PreparingHttpImportMessageProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/Async/Import/PreparingHttpImportMessageProcessor.php "Oro\Bundle\ImportExportBundle\Async\Import\PreparingHttpImportMessageProcessor")</sup> and its service `oro_importexport.async.preparing_http_import` were removed. You can use `PreHttpImportMessageProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/Async/Import/PreHttpImportMessageProcessor.php "Oro\Bundle\ImportExportBundle\Async\Import\PreHttpImportMessageProcessor")</sup> and `HttpImportMessageProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/Async/Import/HttpImportMessageProcessor.php "Oro\Bundle\ImportExportBundle\Async\Import\HttpImportMessageProcessor")</sup>.
* Class `PreparingHttpImportValidationMessageProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/Async/Import/PreparingHttpImportValidationMessageProcessor.php "Oro\Bundle\ImportExportBundle\Async\Import\PreparingHttpImportValidationMessageProcessor")</sup> and its service `oro_importexport.async.preparing_http_import_validation` were removed. You can use `PreHttpImportMessageProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/Async/Import/PreHttpImportMessageProcessor.php "Oro\Bundle\ImportExportBundle\Async\Import\PreHttpImportMessageProcessor")</sup> and `HttpImportMessageProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/Async/Import/HttpImportMessageProcessor.php "Oro\Bundle\ImportExportBundle\Async\Import\HttpImportMessageProcessor")</sup>.
* Class `AbstractChunkImportMessageProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/Async/Import/AbstractChunkImportMessageProcessor.php "Oro\Bundle\ImportExportBundle\Async\Import\AbstractChunkImportMessageProcessor")</sup> and its service `oro_importexport.async.abstract_chunk_http_import` were removed. You can use `PreHttpImportMessageProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/Async/Import/PreHttpImportMessageProcessor.php "Oro\Bundle\ImportExportBundle\Async\Import\PreHttpImportMessageProcessor")</sup> and `HttpImportMessageProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/Async/Import/HttpImportMessageProcessor.php "Oro\Bundle\ImportExportBundle\Async\Import\HttpImportMessageProcessor")</sup>.
* Class `ChunkHttpImportMessageProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/Async/Import/ChunkHttpImportMessageProcessor.php "Oro\Bundle\ImportExportBundle\Async\Import\ChunkHttpImportMessageProcessor")</sup> and its service `oro_importexport.async.chunck_http_import` were removed. You can use `PreHttpImportMessageProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/Async/Import/PreHttpImportMessageProcessor.php "Oro\Bundle\ImportExportBundle\Async\Import\PreHttpImportMessageProcessor")</sup> and `HttpImportMessageProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/Async/Import/HttpImportMessageProcessor.php "Oro\Bundle\ImportExportBundle\Async\Import\HttpImportMessageProcessor")</sup>.
* Class `ChunkHttpImportValidationMessageProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/Async/Import/ChunkHttpImportValidationMessageProcessor.php "Oro\Bundle\ImportExportBundle\Async\Import\ChunkHttpImportValidationMessageProcessor")</sup> and its service `oro_importexport.async.chunck_http_import_validation` were removed. You can use `PreHttpImportMessageProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/Async/Import/PreHttpImportMessageProcessor.php "Oro\Bundle\ImportExportBundle\Async\Import\PreHttpImportMessageProcessor")</sup> and `HttpImportMessageProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/Async/Import/HttpImportMessageProcessor.php "Oro\Bundle\ImportExportBundle\Async\Import\HttpImportMessageProcessor")</sup>.
* Class `CliImportValidationMessageProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/Async/Import/CliImportValidationMessageProcessor.php "Oro\Bundle\ImportExportBundle\Async\Import\CliImportValidationMessageProcessor")</sup> and its service `oro_importexport.async.cli_import_validation` were removed. You can use `PreCliImportMessageProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/Async/Import/PreCliImportMessageProcessor.php "Oro\Bundle\ImportExportBundle\Async\Import\PreCliImportMessageProcessor")</sup> and `CliImportMessageProcessor`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/Async/Import/CliImportMessageProcessor.php "Oro\Bundle\ImportExportBundle\Async\Import\CliImportMessageProcessor")</sup>.
* Class `SplitterCsvFiler`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/Splitter/SplitterCsvFiler.php "Oro\Bundle\ImportExportBundle\Splitter\SplitterCsvFiler")</sup> and its service `oro_importexport.splitter.csv` were removed. You can use `BatchFileManager`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/ImportExportBundle/File/BatchFileManager.php "Oro\Bundle\ImportExportBundle\File\BatchFileManager")</sup> instead.
#### InstallerBundle
* The parameter `oro_installer.listener.request.class` was removed from DIC
#### IntegrationBundle
* The parameter `oro_integration.twig.integration.class` was removed from DIC
#### LayoutBundle
* Removed the following parameters from the DI container:
    * `oro_layout.layout_factory_builder.class`
    * `oro_layout.twig.extension.layout.class`
    * `oro_layout.twig.renderer.class`
    * `oro_layout.twig.renderer.engine.class`
    * `oro_layout.twig.layout_renderer.class`
    * `oro_layout.twig.form.engine.class`
#### LocaleBundle
* Class `LocalizedFallbackValue`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/LocaleBundle/Entity/LocalizedFallbackValue.php "Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue")</sup>
    * will become not extended in 2.3 release
* Class `ExtendLocalizedFallbackValue`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/LocaleBundle/Model/ExtendLocalizedFallbackValue.php "Oro\Bundle\LocaleBundle\Model\ExtendLocalizedFallbackValue")</sup>
    * deprecated and will be removed in 2.3 release
* The service `oro_locale.twig.name` was removed
* The service `oro_translation.event_listener.language_change` was removed
#### MigrationBundle
* The parameter `oro_migration.twig.schema_dumper.class` was removed from DIC
#### NavigationBundle
* Removed the following parameters from DIC:
    * `oro_menu.twig.extension.class`
    * `oro_navigation.event.master_request_route_listener.class`
    * `oro_navigation.title_service.twig.extension.class`
    * `oro_navigation.title_service.event.request.listener.class`
    * `oro_navigation.twig_hash_nav_extension.class`
#### OrganizationBundle
* Removed the following parameters from DIC:
    * `oro_organization.twig.get_owner.class`
    * `oro_organization.twig.business_units.class`
* The following services were removed:
    * `oro_organization.twig.get_owner`
    * `oro_organization.twig.business_units`
#### PlatformBundle
* The parameter `oro_platform.twig.platform_extension.class` was removed from DIC
#### ReminderBundle
* The parameter `oro_reminder.twig.extension.class` was removed from DIC
#### SearchBundle
* The parameter `oro_search.twig_extension.class` was removed from DIC
#### SecurityBundle
* Next container parameters were removed:
    * `oro_security.acl.voter.class`
    * `oro_security.twig.security_extension.class`
    * `oro_security.twig.security_organization_extension`
    * `oro_security.twig.acl.permission_extension.class`
    * `oro_security.listener.context_listener.class`
    * `oro_security.listener.console_context_listener.class`
* `AbstractOwnerTreeProvider`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/SecurityBundle/Owner/AbstractOwnerTreeProvider.php "Oro\Bundle\SecurityBundle\Owner\AbstractOwnerTreeProvider")</sup>:
    * removed implementation of `Symfony\Component\DependencyInjection\ContainerAwareInterface`
* `OwnerTreeProvider`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/SecurityBundle/Owner/OwnerTreeProvider.php "Oro\Bundle\SecurityBundle\Owner\OwnerTreeProvider")</sup>:
    * removed constant `CACHE_KEY`
* The service `oro_security.twig.security_organization_extension` was removed
* The service `oro_security.twig.acl.permission_extension` was removed
* Class `PermissionExtension`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/SecurityBundle/Twig/Acl/PermissionExtension.php "Oro\Bundle\SecurityBundle\Twig\Acl\PermissionExtension")</sup> was removed
* Class `OroSecurityOrganizationExtension`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/SecurityBundle/Twig/OroSecurityOrganizationExtension.php "Oro\Bundle\SecurityBundle\Twig\OroSecurityOrganizationExtension")</sup> was removed
* Interface `AclExtensionInterface`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/SecurityBundle/Acl/Extension/AclExtensionInterface.php "Oro\Bundle\SecurityBundle\Acl\Extension\AclExtensionInterface")</sup>
    * signature of method `getAllowedPermissions` changed, added third argument `string|null aclGroup` default `null`
#### SegmentBundle
* The parameter `oro_segment.twig.extension.segment.class` was removed from DIC
#### SidebarBundle
* The parameter `oro_sidebar.twig.extension.class` was removed from DIC
* The parameter `oro_sidebar.request.handler.class` was removed from DIC
#### SyncBundle
* The parameter `oro_wamp.twig.class` was removed from DIC
* The service `oro_sync.twig.content.tags_extension` was removed
#### TagBundle
* The parameter `oro_tag.twig.tag.extension.class` was removed from DIC
#### ThemeBundle
* The parameter `oro_theme.twig.extension.class` was removed from DIC
#### UIBundle
* Removed the following parameters from DIC:
    * `oro_ui.twig.sort_by.class`
    * `oro_ui.twig.ceil.class`
    * `oro_ui.twig.extension.class`
    * `oro_ui.twig.mobile.class`
    * `oro_ui.twig.widget.class`
    * `oro_ui.twig.date.class`
    * `oro_ui.twig.regex.class`
    * `oro_ui.twig.skype_button.class`
    * `oro_ui.twig.form.class`
    * `oro_ui.twig.formatter.class`
    * `oro_ui.twig.placeholder.class`
    * `oro_ui.twig.tab.class`
    * `oro_ui.twig.content.class`
    * `oro_ui.twig.url.class`
    * `oro_ui.twig.js_template.class`
    * `oro_ui.twig.merge_recursive.class`
    * `oro_ui.twig.block.class`
    * `oro_ui.twig.html_tag.class`
    * `oro_ui.twig.extension.formatter.class`
    * `oro_ui.view.listener.class`
    * `oro_ui.view.content_provider.listener.class`
* Removed the following services:
    * `oro_ui.twig.sort_by_extension`
    * `oro_ui.twig.ceil_extension`
    * `oro_ui.twig.mobile_extension`
    * `oro_ui.twig.form_extension`
    * `oro_ui.twig.view_extension`
    * `oro_ui.twig.formatter_extension`
    * `oro_ui.twig.widget_extension`
    * `oro_ui.twig.date_extension`
    * `oro_ui.twig.regex_extension`
    * `oro_ui.twig.skype_button_extension`
    * `oro_ui.twig.content_extension`
    * `oro_ui.twig.url_extension`
    * `oro_ui.twig.js_template`
    * `oro_ui.twig.merge_recursive`
    * `oro_ui.twig.block`
#### UserBundle
* The parameter `oro_user.twig.user_extension.class` was removed from DIC
#### WindowsBundle
* The parameter `oro_windows.twig.extension.class` was removed from DIC
### Fixed
#### SearchBundle
* Return value types in `SearchQueryInterface`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/SearchBundle/Query/SearchQueryInterface.php "Oro\Bundle\SearchBundle\Query\SearchQueryInterface")</sup> and
`AbstractSearchQuery`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/SearchBundle/Query/AbstractSearchQuery.php "Oro\Bundle\SearchBundle\Query\AbstractSearchQuery")</sup> were fixed to support fluent interface
`Orm`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/SearchBundle/Engine/Orm.php "Oro\Bundle\SearchBundle\Engine\Orm")</sup> `setDrivers` method and `$drivers` and injected directly to `SearchIndexRepository`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/SearchBundle/Entity/Repository/SearchIndexRepository.php "Oro\Bundle\SearchBundle\Entity\Repository\SearchIndexRepository")</sup>
`OrmIndexer`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/SearchBundle/Engine/OrmIndexer.php "Oro\Bundle\SearchBundle\Engine\OrmIndexer")</sup> `setDrivers` method and `$drivers` and injected directly to `SearchIndexRepository`<sup>[[?]](https://github.com/oroinc/platform/tree/2.1.0/src/Oro/Bundle/SearchBundle/Entity/Repository/SearchIndexRepository.php "Oro\Bundle\SearchBundle\Entity\Repository\SearchIndexRepository")</sup>
## 2.0.21 (2017-08-30)
## 2.0.20 (2017-08-21)
## 2.0.19 (2017-08-16)
## 2.0.18 (2017-07-27)
## 2.0.17 (2017-07-12)
## 2.0.16 (2017-06-30)
## 2.0.15 (2017-06-16)
## 2.0.14 (2017-06-08)
## 2.0.13 (2017-06-07)
## 2.0.12 (2017-05-27)
## 2.0.11 (2017-05-22)
## 2.0.10 (2017-05-17)
## 2.0.9 (2017-05-10)
## 2.0.8 (2017-04-26)
## 2.0.7 (2017-04-14)
## 2.0.6 (2017-04-14)
## 2.0.5 (2017-04-14)
## 2.0.4 (2017-03-21)
## 2.0.3 (2017-03-21)
## 2.0.2 (2017-02-21)
## 2.0.1 (2017-02-06)
## 2.0.0 (2017-01-16)

This changelog references the relevant changes (new features, changes and bugs) done in 2.0 versions.
  * Changed minimum required php version to 5.6
  * PhpUnit 5.7 support
  * Extend fields default mode is `ConfigModel::MODE_READONLY`<sup>[[?]](https://github.com/oroinc/platform/tree/2.0.0/src/Oro/Bundle/EntityConfigBundle/Entity/ConfigModel.php#L0 "Oro\Bundle\EntityConfigBundle\Entity\ConfigModel::MODE_READONLY")</sup>
  * Added support of PHP 7.1

## 1.10.0

This changelog references the relevant changes (new features, changes and bugs) done in 1.10.0 versions.
  * The application has been upgraded to Symfony 2.8 (Symfony 2.8.10 doesn't supported because of [Symfony issue](https://github.com/symfony/symfony/issues/19840))
  * Added support php 7
  * Changed minimum required php version to 5.5.9

## 1.9.0

This changelog references the relevant changes (new features, changes and bugs) done in 1.9.0 versions.
* 1.9.0 (2016-02-15)
 * Inline editing in grids
 * Grid column management
 * New UX for Tags
 * Automated REST API for GET requests
 * Performance improvements
 * Apply range filters for numerical fields in grids
 * Manage field tooltips from the UI
 * Override calendar-view.js in customizations
 * Profiler of duplicated queries
 * Importing layout updates

## 1.8.0

This changelog references the relevant changes (new features, changes and bugs) done in 1.8.0 versions.
* 1.8.0 (2015-08-26)
 * Visual workflow configurator
 * New and extended APIs to work with emails
 * Segmentation based on Data audit
 * Improvements to search
 * Improved filtering on option set attributes, allowing for multiple selections
 * The application has been upgraded to Symfony 2.7 and migrated to Doctrine 2.5
 * Select2 component has been improved to automatically initializes select2 widget
 * Documentation for the new Oro Layout component has been added with examples of use

## 1.7.0

This changelog references the relevant changes (new features, changes and bugs) done in 1.7.0 versions.
* 1.7.0 (2015-04-28)
 * New page layouts and layout themes
 * Added Google single sign-on
 * Added Change or reset users' passwords
 * Added Grid views
 * Dashboard widget configuration
 * Email auto-response in workflow definition

## 1.6.0

This changelog references the relevant changes (new features, changes and bugs) done in 1.6.0 versions.
* 1.6.0 (2015-01-19)
 * Comments to activities.
With this feature, the users will be able to add comments to various record activities, such as calls, notes, calendar events, tasks, and so on, making it possible to leave permanent remarks to particular activities they find important, and even engage in conversations that might come in handy later.
Comments are added to every activity record separately, in a linear thread. In addition to text they might contain a file attachment (1 file/image per comment). Comments may be enabled or disabled for any activity in Entity Management. The ability to add, edit, delete, and view others’ comments is subject to user’s ACL configuration.
 * WYSIWYG rich text editor for emails and notes.
This feature allows users to create rich text emails and notes with the built-in WYSIWYG text editor. It allows to mark text as bold, italic, and underlined; change text color and background; create bullet and numbered lists; insert hyperlinks and chunks of source code.
Rich text editor may be turned off in System configuration—in this case, editor will no longer be available and all previously created rich text pieces will be stripped of any formatting to plain text.

## 1.5.0

This changelog references the relevant changes (new features, changes and bugs) done in 1.5.0 versions.
* 1.5.0 (2014-12-18)
 * Invitations to calendar events.
It is now possible to invite other Oro users to events, send them email notifications about this invitation and receive feedback about their responses or lack thereof.
To invite a user to your event, simply open its edit form and choose guests in a respectively named selector control. After you save the event with invitees, they will receive email notifications about the invitation with a link to their copy of the event in OroCRM. On the view page of that event they will be able to respond to an invitation with three options: Attend, Tentatively attend, and Not attend. Response status (including no response yet) will be displayed on the event tile in the calendar view, and next to the guest's name in the event view. An invitee will be able to change his response after the initial choice, i.e. choose to not attend a previously agreed event. For every response to an invitation, or a change in plans, you (i.e. the organizer of the event) will receive an email notification.
 * System calendars.
This feature allows developers to add so-called System calendars to Oro Platform. Use cases for such calendars include company-wide holiday calendar; organization-wide calendar of conferences and conventions, and so on. (Note that organization calendars will only be available in Enterprise Edition 1.7.0).
These calendars and their events will be automatically added to Calendar views of all users in the entire system. Events of these calendars can be managed on their view forms that are available under System > System Calendars. The permission to add or modify events might be assigned to as many people as needed—e.g. the HR and the office manager.
 * Task calendar.
Task calendar is a special kind of system calendar that displays tasks assigned to the user on the calendar view in addition to calendar events. For now, there is no way to add tasks via the calendar view, but it is possible to edit or delete existing tasks. It is not possible to view other users' task calendars either—only the personal task calendar is available.
The calendar view also features a button that leads to the grid of all tasks, similarly to the existing Events button.
 * Color coding for calendars and calendar events.
The user now may change the color of the calendar from the default one in the calendar actions popup menu. Similarly, the user can change the color of the individual event in its Edit dialogue. A palette of standard colors is offered in both cases, with the option to select a custom color with the color wheel.
Standard palettes for calendars and events may be configured in the system configuration under Display settings > Calendar settings.
 * Other minor changes to calendar view.
It is now possible to turn calendars on and off without removing them from the list by clicking on the colored square or via the popup menu.
Click on the event tile opens its View Event form, not Edit.
 * Calls, Tasks, and Calendar events as entity activities.
This is an expansion to the entity activity feature that was first released with 1.3.0 where we introduced the concept of entity activity to the platform and converted emailing to the activity mechanism. Now we are adding three more ubiquitous user actions to this list: logging calls, creating tasks, and scheduling calendar events.
In order to better accommodate the expanding lot of activities we also have customized the UI for them. Previously, every action/activity had its own button regardless of the number of activities available, so if the admin has enabled a lot of activities, users could easily get confused with a long row of buttons, especially on a low resolution screen. Now all activities and non-activity based actions other than Edit and Delete are conveniently grouped into a single More Actions dropdown button.
 * Record Activities Widget.
The Record Activities Widget replaces the Record Activity block, where activities were listed by their type in separate tabs. Instead of tabs, the widget puts all record activities—emails, calls, tasks, calendar events, etc—in a single paginated list.
The user is able to filter the list by activity type and by date of activity. It is possible to configure the the list to be sorted either by creation date or by last update date.
 * Custom fields without schema update.
It is now possible to add custom fields to entities and immediately use them without schema update. This ability comes with drawbacks: these "serialized" fields can only store textual or numeric data—they cannot be option sets, relations, or files/images; nor they are available in reports or segments. But these fields will be displayed on entity view/add forms, and may be added to grid and export/import profile if necessary.
To create such fields, click Create field button on the entity view page in Entity management, and then choose "Serialized field" in Storage type selector. To create regular field, choose "Table column."
 * Entity records pagination.
This feature allows the user to "remember" a set of entity records that existed on the grid (i.e. with filters applied) when he moves to the view page of any record, and then quickly navigate through these records with a new pagination control that appears in top right corner of the page.
Pagination only works when the user comes to a view page from the main entity grid; in any other case (e.g. search, direct link, grid on another page, segment) the pagination control will not be displayed. Pagination is preserved on a pinned page in both control and in breadcrumbs.

## 1.4.3

This changelog references the relevant changes (new features, changes and bugs) done in 1.4.3 versions.
* 1.4.3 (2014-12-05)
 * List of improvements and fixed bugs
 - Fixed extended entity is set to "false" after oro:entity-config:update with force

## 1.4.2

This changelog references the relevant changes (new features, changes and bugs) done in 1.4.2 versions.
* 1.4.2 (2014-12-02)
 * List of improvements and fixed bugs
 - Implemented form type guessers for custom fields of existing entities
 - Added support of cascade option for association in Extend Extension
 - Fixed insecure content from websockets when HTTPS used
 - Fixed IMAP Sync with date parsing exception
 - Magento Integration: Sensitive data displayed in API request logs
 - Magento Integration: Memory Issue on Error
 - Magento Integration: Duplicated jobs on two way Magento sync

## 1.4.1

This changelog references the relevant changes (new features, changes and bugs) done in 1.4.1 versions.
* 1.4.1 (2014-11-17)
 * List of improvements and fixed bugs
 - Refactor extended entity to prevent class name collisions
 - Implement form type guessers for custom fields of existing entities
 - Use route from config in email address link to avoid potential errors
 - Fixed duplicates of entities during magento import
 - Error in "oro_multiple_entity" if it's used without "default_element" option
 - Lost organization name after upgrade

## 1.4.0

This changelog references the relevant changes (new features, changes and bugs) done in 1.4.0 versions.
* 1.4.0 (2014-10-15)
 * The re-introduction of Channels.
We started the implementation of a new vision for the Channels in 1.3 version and now we bring Channels back, although under a new definition.
The general idea behind channels may be explained as follows: a channel in OroCRM represents an outside source customer and sales data, where "customer" and "sales" must be understood in the broadest sense possible. Depending on the nature of the outside source, the channel may or may not require a data integration.
This new definition leads to multiple noticeable changes across the system.
 * Integration management.
Albeit the Integrations grid still displays all integrations that exist in the system, you now may create only "non-customer" standalone integrations, such as Zendesk integration. The "customer" integrations, such as Magento integration, may be created only in scope of a channel and cannot exist without it.
 * Marketing lists.
Marketing lists serve as the basis for marketing activities, such as email campaigns (see below). They represent a target auditory of the activity—that is, people, who will be contacted when the activity takes place. Marketing lists have little value by themselves; they exist in scope of some marketing campaign and its activities.
Essentially, marketing list is a segment of entities that contain some contact information, such as email or phone number or physical address. Lists are build based on some rules using Oro filtering tool. Similarly to segments, marketing lists can be static or dynamic; the rules are the same. The user can build marketing lists of contacts, Magento customers, leads, etc.
In addition to filtering rules, the user can manually tweak contents of the marketing list by removing items ("subscribers") from it. Removed subscribers will no longer appear in the list even if they fit the conditions. It is possible to move them back in the list, too.
Every subscriber can also unsubscribe from the list. In this case, he will remain in the list, but will no longer receive email campaigns that are sent to this list. Note that subscription status is managed on per-list basis; the same contact might be subscribed to one list and unsubscribed from another.
 * Email campaigns.
Email campaign is a first example of marketing activity implemented in OroCRM. The big picture is following: Every marketing campaign might contain multiple marketing activities, e.g. an email newsletter, a context ad campaign, a targeted phone advertisement. All these activities serve the common goal of the "big" marketing campaign.
In its current implementation, email campaign is a one-time dispatch of an email to a list of subscribers. Hence, the campaign consists of three basic parts:
Recipients—represented by a Marketing list.
Email itself—the user may choose a template, or create a campaign email from scratch.
Sending rules—for now, only one-time dispatch is available.
Email campaign might be tied to a marketing campaign, but it might exist on its own as well.
 * Improved Email templates.
Previously, email templates were used only for email notifications. Now their role is expanded: it is now possible to use templates in email activities to create a new email from the template, and for email campaigns.
Support for variables in templates was extended: in addition to "contextual" variables that were related to attributes of the template entity, templates may include "system-wide" variables like current user's first name, or current time, or name of the organization. It is also possible to create a "generic" template that is not related to any entity; in this case it may contain only system variables.
New templates are subject to ACL and have owner of user type.
 * Other improvements
 <ul><li>Multiple improvements to Web API</li>
 <li>A new implementation of option sets</li>
 <li>Improved grids</li></ul>
 * Community requests.
Here is the list of Community requests that were addressed in this version.
Features & improvements
  <ul><li>#50 Add the way to filter on empty fields</li>
  <li>#116 Add custom templates to workflow transitions</li>
  <li>#118 Extending countries</li>
  <li>#136 Console command for CSV import/export</li>
  <li>#149 New "link" type for datagrid column format</li></ul>
 * Bugs fixed
  <ul><li>#47 Problems with scrolling in iOS 7</li>
  <li>#62 Problems with the Recent Emails widget</li>
  <li>#139 Error 500 after removing unique key of entity</li>
  <li>#158 Update doctrine version to 2.4.4</li></ul>

## 1.4.0-RC1

This changelog references the relevant changes (new features, changes and bugs) done in 1.4.0-RC1 versions.
* 1.4.0-RC1 (2014-09-30)
 * The re-introduction of Channels.
We started the implementation of a new vision for the Channels in 1.3 version and now we bring Channels back, although under a new definition.
The general idea behind channels may be explained as follows: a channel in OroCRM represents an outside source customer and sales data, where "customer" and "sales" must be understood in the broadest sense possible. Depending on the nature of the outside source, the channel may or may not require a data integration.
This new definition leads to multiple noticeable changes across the system.
 * Integration management.
Albeit the Integrations grid still displays all integrations that exist in the system, you now may create only "non-customer" standalone integrations, such as Zendesk integration. The "customer" integrations, such as Magento integration, may be created only in scope of a channel and cannot exist without it.
 * Marketing lists.
Marketing lists serve as the basis for marketing activities, such as email campaigns (see below). They represent a target auditory of the activity—that is, people, who will be contacted when the activity takes place. Marketing lists have little value by themselves; they exist in scope of some marketing campaign and its activities.
Essentially, marketing list is a segment of entities that contain some contact information, such as email or phone number or physical address. Lists are build based on some rules using Oro filtering tool. Similarly to segments, marketing lists can be static or dynamic; the rules are the same. The user can build marketing lists of contacts, Magento customers, leads, etc.
In addition to filtering rules, the user can manually tweak contents of the marketing list by removing items ("subscribers") from it. Removed subscribers will no longer appear in the list even if they fit the conditions. It is possible to move them back in the list, too.
Every subscriber can also unsubscribe from the list. In this case, he will remain in the list, but will no longer receive email campaigns that are sent to this list. Note that subscription status is managed on per-list basis; the same contact might be subscribed to one list and unsubscribed from another.
 * Email campaigns.
Email campaign is a first example of marketing activity implemented in OroCRM. The big picture is following: Every marketing campaign might contain multiple marketing activities, e.g. an email newsletter, a context ad campaign, a targeted phone advertisement. All these activities serve the common goal of the "big" marketing campaign.
In its current implementation, email campaign is a one-time dispatch of an email to a list of subscribers. Hence, the campaign consists of three basic parts:
Recipients—represented by a Marketing list.
Email itself—the user may choose a template, or create a campaign email from scratch.
Sending rules—for now, only one-time dispatch is available.
Email campaign might be tied to a marketing campaign, but it might exist on its own as well.
 * Improved Email templates.
Previously, email templates were used only for email notifications. Now their role is expanded: it is now possible to use templates in email activities to create a new email from the template, and for email campaigns.
Support for variables in templates was extended: in addition to "contextual" variables that were related to attributes of the template entity, templates may include "system-wide" variables like current user's first name, or current time, or name of the organization. It is also possible to create a "generic" template that is not related to any entity; in this case it may contain only system variables.
New templates are subject to ACL and have owner of user type.
 * Other improvements
 <ul><li>Multiple improvements to Web API</li>
 <li>A new implementation of option sets</li>
 <li>Improved grids</li></ul>
 * Community requests.
Here is the list of Community requests that were addressed in this version.
Features & improvements
  <ul><li>#50 Add the way to filter on empty fields</li>
  <li>#116 Add custom templates to workflow transitions</li>
  <li>#118 Extending countries</li>
  <li>#136 Console command for CSV import/export</li>
  <li>#149 New "link" type for datagrid column format</li></ul>
 * Bugs fixed
  <ul><li>#47 Problems with scrolling in iOS 7</li>
  <li>#62 Problems with the Recent Emails widget</li>
  <li>#139 Error 500 after removing unique key of entity</li>
  <li>#158 Update doctrine version to 2.4.4</li></ul>

## 1.3.1

This changelog references the relevant changes (new features, changes and bugs) done in 1.3.1 versions.

* 1.3.1 (2014-08-14)
 * Minimum PHP version: PHP 5.4.9
 * PostgreSQL support
 * Fixed issue: Not entire set of entities is exported
 * Fixed issue: Page crashes when big value is typed into the pagination control
 * Fixed issue: Error 500 on Schema update
 * Other minor issues

## 1.3.0

This changelog references the relevant changes (new features, changes and bugs) done in 1.3.0 versions.

* 1.3.0 (2014-07-23)
 * Redesign of the Navigation panel and left-side menu bar
 * Website event tracking
 * Processes
 * New custom field types for entities: File and Image
 * New control for record lookup (relations)
 * Data import in CSV format

## 1.2.0

This changelog references the relevant changes (new features, changes and bugs) done in 1.2.0 versions.

* 1.2.0 (2014-05-28)
 * Ability to delete Channels
 * Workflow view
 * Reset of Workflow data
 * Line charts in Reports
 * Fixed issues with Duplicated emails
 * Fixed Issue Use of SQL keywords as extended entity field names
 * Fixed Issue Creating one-to-many relationship on custom entity that inverses many-to-one relationship fails
 * Fixed Community requests

## 1.2.0-rc1

This changelog references the relevant changes (new features, changes and bugs) done in 1.2.0 RC1 versions.

* 1.2.0 RC1 (2014-05-12)
 * Ability to delete Channels
 * Workflow view
 * Reset of Workflow data
 * Fixed issues with Duplicated emails
 * Fixed Issue Use of SQL keywords as extended entity field names
 * Fixed Issue Creating one-to-many relationship on custom entity that inverses many-to-one relationship fails

## 1.1.0

This changelog references the relevant changes (new features, changes and bugs) done in 1.1.0 versions.

* 1.1.0 (2014-04-28)
 * Dashboard management
 * Fixed problem with creation of on-demand segments
 * Fixed broken WSSE authentication
 * Fixed Incorrectly calculated totals in grids

## 1.0.1

This changelog references the relevant changes (new features, changes and bugs) done in 1.0.1 versions.

* 1.0.1 (2014-04-18)
 * Issue #3979 � Problems with DB server verification on install
 * Issue #3916 � Memory consumption is too high on installation
 * Issue #3918 � Problems with installation of packages from console
 * Issue #3841 � Very slow installation of packages
 * Issue #3916 � Installed application is not working correctly because of knp-menu version
 * Issue #3839 � Cache regeneration is too slow
 * Issue #3525 � Broken filters on Entity Configuration grid
 * Issue #3974 � Settings are not saved in sidebar widgets
 * Issue #3962 � Workflow window opens with a significant delay
 * Issue #2203 � Incorrect timezone processing in Calendar
 * Issue #3909 � Multi-selection filters might be too long
 * Issue #3899 � Broken link from Opportunity to related Contact Request

## 1.0.0

This changelog references the relevant changes (new features, changes and bugs) done in 1.0.0 versions.

* 1.0.0 (2014-04-01)
 * Workflow management UI
 * Segmentation
 * Reminders
 * Package management
 * Page & Grand totals for grids
 * Proper formatting of Money and Percent values
 * Configurable Sidebars
 * Notification of content changes in the Pinbar

## 1.0.0-rc3

This changelog references the relevant changes (new features, changes and bugs) done in 1.0.0-rc3 versions.

* 1.0.0-rc3 (2014-02-25)
 * Embedded forms
 * CSV export

## 1.0.0-rc2

This changelog references the relevant changes (new features, changes and bugs) done in 1.0.0-rc2 versions.

* 1.0.0-rc2 (2014-01-30)
 * Package management
 * Translations management
 * FontAwesome web-application icons

## 1.0.0-rc1

This changelog references the relevant changes (new features, changes and bugs) done in 1.0.0-rc1 versions.

* 1.0.0-rc1 (2013-12-30)
 * Table reports creation wizard
 * Manageable labels of entities and entity fields
 * Record updates notification
 * Sidebars widgets
 * Mobile Web
 * Package Definition and Management
 * Themes
 * Notifications for owners
 * --force option for oro:install
 * Remove old Grid bundle
 * Basic dashboards

## 1.0.0-beta5

This changelog references the relevant changes (new features, changes and bugs) done in 1.0.0-beta5 versions.

* 1.0.0-beta5 (2013-12-05)
 * ACL management in scope of organization and business unit
 * "Option Set" Field Type for Entity Field
 * Form validation improvements
 * Tabs implementation on entity view pages
 * Eliminated registry js-component
 * Implemented responsive markup on most pages

## 1.0.0-beta4

This changelog references the relevant changes (new features, changes and bugs) done in 1.0.0-beta4 versions.

* 1.0.0-beta4 (2013-11-21)
 * Grid refactoring
 * Form validation improvements
 * Make all entities as Extended
 * JavaScript Tests
 * End support for Internet Explorer 9

## 1.0.0-beta3

This changelog references the relevant changes (new features, changes and bugs) done in 1.0.0-beta3 versions.

* 1.0.0-beta3 (2013-11-11)
 * Upgrade the Symfony framework to version 2.3.6
 * Oro Calendar
 * Email Communication
 * Removed bundle dependencies on application
 * One-to-many and many-to-many relations between extended/custom entities
 * Localizations and Internationalization of input and output

## 1.0.0-beta2

This changelog references the relevant changes (new features, changes and bugs) done in 1.0.0-beta2 versions.

* 1.0.0-beta2 (2013-10-28)
 * Minimum PHP version: PHP 5.4.4
 * Installer enhancements
 * Automatic bundles distribution for application
 * Routes declaration on Bundles level
 * System Help and Tooltips
 * RequireJS optimizer utilization
 * ACL Caching

## 1.0.0-beta1

This changelog references the relevant changes (new features, changes and bugs) done in 1.0.0-beta1 versions.

* 1.0.0-beta1 (2013-09-30)
 * New ACL implementation
 * Emails synchronization via IMAP
 * Custom entities and fields in usage
 * Managing relations between entities
 * Grid views

## 1.0.0-alpha6

This changelog references the relevant changes (new features, changes and bugs) done in 1.0.0-alpha6 versions.

* 1.0.0-alpha6 (2013-09-12)
 * Maintenance Mode
 * WebSocket messaging between browser and the web server
 * Asynchronous Module Definition of JS resources
 * Added multiple sorting for a Grid
 * System configuration

## 1.0.0-alpha5

This changelog references the relevant changes (new features, changes and bugs) done in 1.0.0-alpha5 versions.

* 1.0.0-alpha5 (2013-08-29)
 * Custom entity creation
 * Cron Job
 * Record ownership
 * Grid Improvements
 * Filter Improvements
 * Email Template Improvements
 * Implemented extractor for messages in PHP code
 * Removed dependency on SonataAdminBundle
 * Added possibility to unpin page using pin icon

## 1.0.0-alpha4

This changelog references the relevant changes (new features, changes and bugs) done in 1.0.0-alpha4 versions.

* 1.0.0-alpha4 (2013-07-31)
 * Upgrade Symfony to version 2.3
 * Entity and Entity's Field Management
 * Multiple Organizations and Business Units
 * Transactional Emails
 * Email Templates
 * Tags Management
 * Translations JS files
 * Pin tab experience update
 * Redesigned Page Header
 * Optimized load time of JS resources

## 1.0.0-alpha3

This changelog references the relevant changes (new features, changes and bugs) done in 1.0.0-alpha3 versions.

* 1.0.0-alpha3 (2013-06-27)
 * Placeholders
 * Developer toolbar works with AJAX navigation requests
 * Configuring hidden columns in a Grid
 * Auto-complete form type
 * Added Address Book
 * Localized countries and regions
 * Enhanced data change log with ability to save changes for collections
 * Removed dependency on lib ICU
