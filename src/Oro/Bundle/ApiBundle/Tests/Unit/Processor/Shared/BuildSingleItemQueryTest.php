<?php

namespace Oro\Bundle\ApiBundle\Tests\Unit\Processor\Shared;

use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\QueryBuilder;

use Oro\Bundle\ApiBundle\Collection\Criteria;
use Oro\Bundle\ApiBundle\Config\EntityDefinitionConfig;
use Oro\Bundle\ApiBundle\Processor\Shared\BuildSingleItemQuery;
use Oro\Bundle\ApiBundle\Tests\Unit\Fixtures\Entity;
use Oro\Bundle\ApiBundle\Tests\Unit\Processor\Get\GetProcessorOrmRelatedTestCase;
use Oro\Bundle\ApiBundle\Util\CriteriaConnector;
use Oro\Bundle\EntityBundle\ORM\EntityClassResolver;

class BuildSingleItemQueryTest extends GetProcessorOrmRelatedTestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $criteriaConnector;

    /** @var BuildSingleItemQuery */
    protected $processor;

    protected function setUp()
    {
        parent::setUp();

        $this->criteriaConnector = $this->getMockBuilder(CriteriaConnector::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->processor = new BuildSingleItemQuery($this->doctrineHelper, $this->criteriaConnector);
    }

    public function testProcessWhenQueryIsAlreadyBuilt()
    {
        $qb = $this->getQueryBuilderMock();

        $this->context->setQuery($qb);
        $this->processor->process($this->context);

        $this->assertSame($qb, $this->context->getQuery());
    }

    public function testProcessWhenCriteriaObjectDoesNotExist()
    {
        $this->processor->process($this->context);

        $this->assertFalse($this->context->hasQuery());
    }

    public function testProcessForNotManageableEntity()
    {
        $className = 'Test\Class';

        $this->notManageableClassNames = [$className];

        $this->context->setClassName($className);
        $this->context->setConfig(new EntityDefinitionConfig());
        $this->processor->process($this->context);

        $this->assertNull($this->context->getQuery());
    }

    public function testProcessForManageableEntityWithSingleId()
    {
        $entityClass = Entity\User::class;
        $entityId = 123;

        $resolver = $this->getMockBuilder(EntityClassResolver::class)
            ->disableOriginalConstructor()
            ->getMock();
        $criteria = new Criteria($resolver);

        $this->criteriaConnector->expects($this->once())
            ->method('applyCriteria');

        $this->context->setCriteria($criteria);
        $this->context->setClassName($entityClass);
        $this->context->setId($entityId);
        $this->processor->process($this->context);

        $this->assertTrue($this->context->hasQuery());
        /** @var QueryBuilder $query */
        $query = $this->context->getQuery();
        $this->assertEquals(
            sprintf('SELECT e FROM %s e WHERE e.id = :id', $entityClass),
            $query->getDQL()
        );
        /** @var Parameter $parameter */
        $parameter = $query->getParameters()->first();
        $this->assertEquals('id', $parameter->getName());
        $this->assertEquals($entityId, $parameter->getValue());
    }

    public function testProcessForManageableEntityWithCompositeId()
    {
        $entityClass = Entity\CompositeKeyEntity::class;
        $entityId = ['id' => 123, 'title' => 'test'];

        $resolver = $this->getMockBuilder(EntityClassResolver::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->context->setCriteria(new Criteria($resolver));
        $this->context->setClassName($entityClass);
        $this->context->setId($entityId);
        $this->processor->process($this->context);

        $this->assertTrue($this->context->hasQuery());
        /** @var QueryBuilder $query */
        $query = $this->context->getQuery();
        $this->assertEquals(
            sprintf('SELECT e FROM %s e WHERE e.id = :id1 AND e.title = :id2', $entityClass),
            $query->getDQL()
        );
        /** @var Parameter $parameter */
        $parameters = $query->getParameters();
        $idParameter = $parameters[0];
        $this->assertEquals('id1', $idParameter->getName());
        $this->assertEquals($entityId['id'], $idParameter->getValue());
        $titleParameter = $parameters[1];
        $this->assertEquals('id2', $titleParameter->getName());
        $this->assertEquals($entityId['title'], $titleParameter->getValue());
    }

    public function testProcessForResourceBasedOnManageableEntity()
    {
        $entityClass = Entity\UserProfile::class;
        $parentResourceClass = Entity\User::class;
        $entityId = 123;
        $this->notManageableClassNames = [$entityClass];

        $config = new EntityDefinitionConfig();
        $config->setParentResourceClass($parentResourceClass);

        $resolver = $this->getMockBuilder(EntityClassResolver::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->criteriaConnector->expects($this->once())
            ->method('applyCriteria');

        $this->context->setCriteria(new Criteria($resolver));
        $this->context->setClassName($entityClass);
        $this->context->setId($entityId);
        $this->context->setConfig($config);
        $this->processor->process($this->context);

        $this->assertTrue($this->context->hasQuery());
        /** @var QueryBuilder $query */
        $query = $this->context->getQuery();
        $this->assertEquals(
            sprintf('SELECT e FROM %s e WHERE e.id = :id', $parentResourceClass),
            $query->getDQL()
        );
        /** @var Parameter $parameter */
        $parameter = $query->getParameters()->first();
        $this->assertEquals('id', $parameter->getName());
        $this->assertEquals($entityId, $parameter->getValue());
    }

    public function testProcessForResourceBasedOnNotManageableEntity()
    {
        $entityClass = 'Test\Class';
        $parentResourceClass = 'Test\ParentClass';
        $this->notManageableClassNames = [$entityClass, $parentResourceClass];

        $config = new EntityDefinitionConfig();
        $config->setParentResourceClass($parentResourceClass);

        $resolver = $this->getMockBuilder(EntityClassResolver::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->context->setCriteria(new Criteria($resolver));
        $this->context->setClassName($entityClass);
        $this->context->setConfig($config);
        $this->processor->process($this->context);

        $this->assertNull($this->context->getQuery());
    }
}
