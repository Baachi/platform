<?php

namespace Oro\Bundle\ApiBundle\Provider;

use Oro\Bundle\ApiBundle\Processor\CollectResources\CollectResourcesContext;
use Oro\Bundle\ApiBundle\Request\ApiResource;
use Oro\Bundle\ApiBundle\Request\RequestType;
use Oro\Component\ChainProcessor\ActionProcessorInterface;

/**
 * Provides an information about all registered Data API resources.
 */
class ResourcesProvider
{
    /** @var ActionProcessorInterface */
    private $processor;

    /** @var ResourcesCache */
    private $resourcesCache;

    /** @var ResourcesWithoutIdentifierLoader */
    private $resourcesWithoutIdentifierLoader;

    /** @var array [request cache key => [ApiResource, ...], ...] */
    private $resources = [];

    /** @var array [request cache key => [entity class => accessible flag, ...], ...] */
    private $accessibleResources = [];

    /** @var array [request cache key => [entity class => [action name, ...], ...], ...] */
    private $excludedActions = [];

    /** @var array [request cache key => [entity class, ...], ...] */
    private $resourcesWithoutIdentifier = [];

    /**
     * @param ActionProcessorInterface         $processor
     * @param ResourcesCache                   $resourcesCache
     * @param ResourcesWithoutIdentifierLoader $resourcesWithoutIdentifierLoader
     */
    public function __construct(
        ActionProcessorInterface $processor,
        ResourcesCache $resourcesCache,
        ResourcesWithoutIdentifierLoader $resourcesWithoutIdentifierLoader
    ) {
        $this->processor = $processor;
        $this->resourcesCache = $resourcesCache;
        $this->resourcesWithoutIdentifierLoader = $resourcesWithoutIdentifierLoader;
    }

    /**
     * Gets a configuration of all resources for a given Data API version.
     *
     * @param string      $version     The Data API version
     * @param RequestType $requestType The request type, for example "rest", "soap", etc.
     *
     * @return ApiResource[]
     */
    public function getResources($version, RequestType $requestType): array
    {
        $cacheIndex = $this->getCacheKeyIndex($version, $requestType);
        if (array_key_exists($cacheIndex, $this->resources)) {
            $resources = $this->resources[$cacheIndex];
        } else {
            $resources = $this->resourcesCache->getResources($version, $requestType);
            if (null === $resources) {
                // load data
                /** @var CollectResourcesContext $context */
                $context = $this->processor->createContext();
                $context->setVersion($version);
                $context->getRequestType()->set($requestType);
                $this->processor->process($context);

                // prepare loaded data
                /** @var ApiResource[] $resources */
                $resources = array_values($context->getResult()->toArray());
                $accessibleResources = array_fill_keys($context->getAccessibleResources(), true);
                $excludedActions = [];
                foreach ($resources as $resource) {
                    $entityClass = $resource->getEntityClass();
                    if (!isset($accessibleResources[$entityClass])) {
                        $accessibleResources[$entityClass] = false;
                    }
                    $resourceExcludedActions = $resource->getExcludedActions();
                    if (!empty($resourceExcludedActions)) {
                        $excludedActions[$entityClass] = $resourceExcludedActions;
                    }
                }

                // add data to memory cache
                $this->resources[$cacheIndex] = $resources;
                $this->accessibleResources[$cacheIndex] = $accessibleResources;
                $this->excludedActions[$cacheIndex] = $excludedActions;

                // save data to the cache
                $this->resourcesCache->saveResources(
                    $version,
                    $requestType,
                    $resources,
                    $accessibleResources,
                    $excludedActions
                );
            } else {
                $this->resources[$cacheIndex] = $resources;
            }
        }

        return $resources;
    }

    /**
     * Gets a list of resources accessible through Data API.
     *
     * @param string      $version     The Data API version
     * @param RequestType $requestType The request type, for example "rest", "soap", etc.
     *
     * @return string[] The list of class names
     */
    public function getAccessibleResources($version, RequestType $requestType): array
    {
        $result = [];
        $accessibleResources = $this->loadAccessibleResources($version, $requestType);
        foreach ($accessibleResources as $entityClass => $isAccessible) {
            if ($isAccessible || $this->isResourceWithoutIdentifier($entityClass, $version, $requestType)) {
                $result[] = $entityClass;
            }
        }

        return $result;
    }

    /**
     * Checks whether a given entity is accessible through Data API.
     *
     * @param string      $entityClass The FQCN of an entity
     * @param string      $version     The Data API version
     * @param RequestType $requestType The request type, for example "rest", "soap", etc.
     *
     * @return bool
     */
    public function isResourceAccessible($entityClass, $version, RequestType $requestType): bool
    {
        $accessibleResources = $this->loadAccessibleResources($version, $requestType);

        if (!array_key_exists($entityClass, $accessibleResources)) {
            return false;
        }

        return
            $accessibleResources[$entityClass]
            || $this->isResourceWithoutIdentifier($entityClass, $version, $requestType);
    }

    /**
     * Checks whether a given entity is configured to be used in Data API.
     *
     * @param string      $entityClass The FQCN of an entity
     * @param string      $version     The Data API version
     * @param RequestType $requestType The request type, for example "rest", "soap", etc.
     *
     * @return bool
     */
    public function isResourceKnown($entityClass, $version, RequestType $requestType): bool
    {
        $accessibleResources = $this->loadAccessibleResources($version, $requestType);

        return array_key_exists($entityClass, $accessibleResources);
    }

    /**
     * Gets a list of actions that cannot be used in Data API from for a given entity.
     *
     * @param string      $entityClass The FQCN of an entity
     * @param string      $version     The Data API version
     * @param RequestType $requestType The request type, for example "rest", "soap", etc.
     *
     * @return string[]
     */
    public function getResourceExcludeActions($entityClass, $version, RequestType $requestType): array
    {
        $excludedActions = $this->loadExcludedActions($version, $requestType);

        return array_key_exists($entityClass, $excludedActions)
            ? $excludedActions[$entityClass]
            : [];
    }

    /**
     * Gets a list of resources accessible through Data API.
     *
     * @param string      $version     The Data API version
     * @param RequestType $requestType The request type, for example "rest", "soap", etc.
     *
     * @return string[] The list of class names
     */
    public function getResourcesWithoutIdentifier($version, RequestType $requestType): array
    {
        return $this->loadResourcesWithoutIdentifier($version, $requestType);
    }

    /**
     * Checks whether a given entity is accessible through Data API.
     *
     * @param string      $entityClass The FQCN of an entity
     * @param string      $version     The Data API version
     * @param RequestType $requestType The request type, for example "rest", "soap", etc.
     *
     * @return bool
     */
    public function isResourceWithoutIdentifier($entityClass, $version, RequestType $requestType): bool
    {
        return \in_array(
            $entityClass,
            $this->loadResourcesWithoutIdentifier($version, $requestType),
            true
        );
    }

    /**
     * Removes all entries from the cache.
     */
    public function clearCache(): void
    {
        $this->resources = [];
        $this->accessibleResources = [];
        $this->excludedActions = [];
        $this->resourcesWithoutIdentifier = [];
        $this->resourcesCache->clear();
    }

    /**
     * @param string      $version
     * @param RequestType $requestType
     *
     * @return array [entity class => accessible flag]
     */
    private function loadAccessibleResources($version, RequestType $requestType): array
    {
        $cacheIndex = $this->getCacheKeyIndex($version, $requestType);
        if (!array_key_exists($cacheIndex, $this->accessibleResources)) {
            $accessibleResourcesForRequest = $this->resourcesCache->getAccessibleResources($version, $requestType);
            if (null === $accessibleResourcesForRequest) {
                $this->getResources($version, $requestType);
                $accessibleResourcesForRequest = $this->resourcesCache->getAccessibleResources($version, $requestType);
            }

            $this->accessibleResources[$cacheIndex] = $accessibleResourcesForRequest;
        } else {
            $accessibleResourcesForRequest = $this->accessibleResources[$cacheIndex];
        }

        return $accessibleResourcesForRequest;
    }

    /**
     * @param string      $version
     * @param RequestType $requestType
     *
     * @return array [entity class => [action name, ...]]
     */
    private function loadExcludedActions($version, RequestType $requestType): array
    {
        $cacheIndex = $this->getCacheKeyIndex($version, $requestType);
        if (!array_key_exists($cacheIndex, $this->excludedActions)) {
            $excludedActionsForRequest = $this->resourcesCache->getExcludedActions($version, $requestType);
            if (null === $excludedActionsForRequest) {
                $this->getResources($version, $requestType);
                $excludedActionsForRequest = $this->resourcesCache->getExcludedActions($version, $requestType);
            }

            $this->excludedActions[$cacheIndex] = $excludedActionsForRequest;
        } else {
            $excludedActionsForRequest = $this->excludedActions[$cacheIndex];
        }

        return $excludedActionsForRequest;
    }

    /**
     * @param string      $version
     * @param RequestType $requestType
     *
     * @return string[]
     */
    private function loadResourcesWithoutIdentifier($version, RequestType $requestType): array
    {
        $cacheIndex = $this->getCacheKeyIndex($version, $requestType);
        if (!array_key_exists($cacheIndex, $this->resourcesWithoutIdentifier)) {
            $resourcesWithoutId = $this->resourcesCache->getResourcesWithoutIdentifier($version, $requestType);
            if (null === $resourcesWithoutId) {
                $resourcesWithoutId = $this->resourcesWithoutIdentifierLoader->load(
                    $version,
                    $requestType,
                    $this->getResources($version, $requestType)
                );
                $this->resourcesCache->saveResourcesWithoutIdentifier($version, $requestType, $resourcesWithoutId);
            }

            $this->resourcesWithoutIdentifier[$cacheIndex] = $resourcesWithoutId;
        } else {
            $resourcesWithoutId = $this->resourcesWithoutIdentifier[$cacheIndex];
        }

        return $resourcesWithoutId;
    }

    /**
     * @param string      $version
     * @param RequestType $requestType
     *
     * @return string
     */
    private function getCacheKeyIndex($version, RequestType $requestType): string
    {
        return $version . (string)$requestType;
    }
}
