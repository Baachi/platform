UPGRADE FROM 2.2.2 to 2.2.3
===========================

Table of Contents
-----------------

- [ReportBundle](#reportbundle)
- [SegmentBundle](#segmentbundle)

ReportBundle
------------

- Class Oro\Bundle\ReportBundle\Grid\ReportDatagridConfigurationProvider was modified to use doctrine cache instead of caching DatagridConfiguration value in property $configuration.
To set values for $prefixCacheKey and $reportCacheManager in ReportDatagridConfigurationProvider, following methods were added:
     - public method `setPrefixCacheKey($prefixCacheKey)`
     - public method `setReportCacheManager(Cache $reportCacheManager)`

     They will be removed in version 2.3 and $prefixCacheKey and $reportCacheManager will be initialized in constructor

     Before
     ```PHP
        class ReportDatagridConfigurationProvider
        {
            /**
             * @var DatagridConfiguration
             */
            protected $configuration;

            public function getConfiguration($gridName)
            {
                if ($this->configuration === null) {
                    ...
                    $this->configuration = $this->builder->getConfiguration();
                }

                return $this->configuration;
            }
        }
     ```

     After
     ```PHP
        class ReportDatagridConfigurationProvider
        {
            /**
             * Doctrine\Common\Cache\Cache
             */
            protected $reportCacheManager;

            public function getConfiguration($gridName)
            {
                $cacheKey = $this->getCacheKey($gridName);

                if ($this->reportCacheManager->contains($cacheKey)) {
                    $config = $this->reportCacheManager->fetch($cacheKey);
                    $config = unserialize($config);
                } else {
                    $config = $this->prepareConfiguration($gridName);
                    $this->reportCacheManager->save($cacheKey, serialize($config));
                }

                return $config;
            }
        }
     ```

- Class Oro\Bundle\ReportBundle\EventListener\ReportCacheCleanerListener was added. It cleans cache of report grid on postUpdate event of Report entity.

SegmentBundle
-------------
- Class Oro\Bundle\SegmentBundle\Query\SegmentQueryConverterFactory was created. It was registered as the service `oro_segment.query.segment_query_converter_factory`.
    services.yml
    ```yml
    oro_segment.query.segment_query_converter_factory:
        class: 'Oro\Bundle\SegmentBundle\Query\SegmentQueryConverterFactory'
        arguments:
            - '@oro_query_designer.query_designer.manager'
            - '@oro_entity.virtual_field_provider.chain'
            - '@doctrine'
            - '@oro_query_designer.query_designer.restriction_builder'
            - '@oro_entity.virtual_relation_provider.chain'
        public: false
    ```
- Service `oro_segment.query.segment_query_converter_factory.link` was created to initialize the service `oro_segment.query.segment_query_converter_factory` in `Oro\Bundle\SegmentBundle\Query\DynamicSegmentQueryBuilder`.
    services.yml
    ```yml
    oro_segment.query.segment_query_converter_factory.link:
        tags:
            - { name: oro_service_link,  service: oro_segment.query.segment_query_converter_factory }
    ```
- Class `Oro\Bundle\SegmentBundle\Query\DynamicSegmentQueryBuilder` was changed to use service `oro_segment.query.segment_query_converter_factory.link` instead of `oro_segment.query_converter.segment.link`.
    - public method `setSegmentQueryConverterFactoryLink(ServiceLink $segmentQueryConverterFactoryLink)` was added.
- Definition of service `oro_segment.query.dynamic_segment.query_builder` was changed in services.yml.
    Before
    ```yml
    oro_segment.query.dynamic_segment.query_builder:
        class: %oro_segment.query.dynamic_segment.query_builder.class%
        arguments:
            - '@oro_segment.query_converter.segment.link'
            - '@doctrine'
    ```
    After
    ```yml
    oro_segment.query.dynamic_segment.query_builder:
        class: %oro_segment.query.dynamic_segment.query_builder.class%
        arguments:
            - '@oro_segment.query_converter.segment.link'
            - '@doctrine'
        calls:
            - [setSegmentQueryConverterFactoryLink, ['@oro_segment.query.segment_query_converter_factory.link']]
    ```
