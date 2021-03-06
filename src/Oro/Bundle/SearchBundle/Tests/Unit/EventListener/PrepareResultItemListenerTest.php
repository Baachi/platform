<?php

namespace Oro\Bundle\SearchBundle\Tests\Unit\EventListener;

use Doctrine\ORM\EntityManager;
use Oro\Bundle\EntityBundle\Provider\EntityNameResolver;
use Oro\Bundle\SearchBundle\Engine\ObjectMapper;
use Oro\Bundle\SearchBundle\Event\PrepareResultItemEvent;
use Oro\Bundle\SearchBundle\EventListener\PrepareResultItemListener;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Router;

class PrepareResultItemListenerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var PrepareResultItemListener
     */
    protected $listener;

    /**
     * @var Router|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $router;

    /**
     * @var ObjectMapper|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $mapper;

    /**
     * @var EntityManager|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $em;

    /**
     * @var PrepareResultItemEvent|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $event;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $item;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $entity;

    /**
     * @var EntityNameResolver|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $entityNameResolver;

    /**
     * Set up test environment
     */
    protected function setUp()
    {
        $this->router = $this->getMockBuilder('Symfony\Component\Routing\Router')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mapper = $this->getMockBuilder('Oro\Bundle\SearchBundle\Engine\ObjectMapper')
            ->disableOriginalConstructor()
            ->getMock();

        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->item = $this->getMockBuilder('Oro\Bundle\SearchBundle\Query\Result\Item')
            ->disableOriginalConstructor()
            ->getMock();

        $this->event = $this->getMockBuilder('Oro\Bundle\SearchBundle\Event\PrepareResultItemEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $this->entity = $this->getMockBuilder('Oro\Bundle\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $this->entityNameResolver = $this->getMockBuilder(EntityNameResolver::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->listener = new PrepareResultItemListener(
            $this->router,
            $this->mapper,
            $this->em,
            $this->entityNameResolver
        );
    }

    /**
     * Check that process data doesn't execute any query if url and title already set
     */
    public function testProcessSetData()
    {
        $this->event->expects($this->once())
            ->method('getEntity');

        $this->event->expects($this->once())
            ->method('getResultItem')
            ->will($this->returnValue($this->item));

        $this->item->expects($this->once())
            ->method('getRecordUrl')
            ->will($this->returnValue('url'));

        $this->item->expects($this->once())
            ->method('getRecordTitle')
            ->will($this->returnValue('title'));

        $this->em->expects($this->never())
            ->method('getRepository');

        $this->listener->process($this->event);
    }

    /**
     * Generates url from existed entity
     */
    public function testProcessUrlFromEntity()
    {
        $this->event->expects($this->once())
            ->method('getEntity')
            ->will($this->returnValue($this->entity));

        $this->event->expects($this->once())
            ->method('getResultItem')
            ->will($this->returnValue($this->item));

        $this->item->expects($this->once())
            ->method('getRecordUrl')
            ->will($this->returnValue(false));

        $this->item->expects($this->once())
            ->method('getRecordTitle')
            ->will($this->returnValue('title'));

        $this->item->expects($this->once())
            ->method('getEntityName')
            ->will($this->returnValue(get_class($this->entity)));

        $metadataMock = $this->getMockBuilder('\Doctrine\ORM\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->getMock();

        $this->em->expects($this->once())
            ->method('getClassMetadata')
            ->with(get_class($this->entity))
            ->will($this->returnValue($metadataMock));

        $this->mapper->expects($this->exactly(2))
            ->method('getEntityMapParameter')
            ->with(get_class($this->entity), 'route')
            ->will($this->returnValue(['parameters' => ['parameter' => 'field'], 'name' => 'test_route']));

        $this->mapper->expects($this->once())
            ->method('getFieldValue')
            ->with($this->entity, 'field')
            ->will($this->returnValue('test_data'));

        $this->router->expects($this->once())
            ->method('generate')
            ->with('test_route', ['parameter' => 'test_data'], UrlGeneratorInterface::ABSOLUTE_URL)
            ->will($this->returnValue('test_url'));

        $this->listener->process($this->event);
    }

    /**
     * Process entity without URL params
     */
    public function testProcessEmptyUrl()
    {
        $this->event->expects($this->once())
            ->method('getEntity')
            ->will($this->returnValue($this->entity));

        $this->event->expects($this->once())
            ->method('getResultItem')
            ->will($this->returnValue($this->item));

        $this->item->expects($this->once())
            ->method('getRecordUrl')
            ->will($this->returnValue(false));

        $this->item->expects($this->once())
            ->method('getRecordTitle')
            ->will($this->returnValue('title'));

        $this->item->expects($this->once())
            ->method('getEntityName')
            ->will($this->returnValue(get_class($this->entity)));

        $metadataMock = $this->getMockBuilder('\Doctrine\ORM\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->getMock();

        $this->em->expects($this->once())
            ->method('getClassMetadata')
            ->with(get_class($this->entity))
            ->will($this->returnValue($metadataMock));

        $this->mapper->expects($this->once())
            ->method('getEntityMapParameter')
            ->with(get_class($this->entity), 'route')
            ->will($this->returnValue(false));

        $this->item->expects($this->once())
            ->method('setRecordUrl')
            ->with('');

        $this->listener->process($this->event);
    }

    /**
     * Trying to find entity and generates parameters from result item
     */
    public function testProcessUrl()
    {
        $this->event->expects($this->once())
            ->method('getEntity')
            ->will($this->returnValue(false));

        $this->event->expects($this->once())
            ->method('getResultItem')
            ->will($this->returnValue($this->item));

        $this->item->expects($this->once())
            ->method('getRecordUrl')
            ->will($this->returnValue(false));

        $this->item->expects($this->once())
            ->method('getRecordTitle')
            ->will($this->returnValue('title'));

        $this->item->expects($this->once())
            ->method('getEntityName')
            ->will($this->returnValue(get_class($this->entity)));

        $metadataMock = $this->getMockBuilder('\Doctrine\ORM\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->getMock();

        $this->em->expects($this->once())
            ->method('getClassMetadata')
            ->with(get_class($this->entity))
            ->will($this->returnValue($metadataMock));

        $repositoryMock = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->once())
            ->method('find')
            ->will($this->returnValue(false));

        $this->em->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($repositoryMock));

        $this->item->expects($this->exactly(2))
            ->method('getRecordId')
            ->will($this->returnValue(1));

        $this->mapper->expects($this->atLeastOnce())
            ->method('getEntityMapParameter')
            ->with(get_class($this->entity), 'route')
            ->will($this->returnValue(['parameters' => ['parameter' => 'field'], 'name' => 'test_route']));

        $this->router->expects($this->once())
            ->method('generate')
            ->with('test_route', ['parameter' => '1'], UrlGeneratorInterface::ABSOLUTE_URL)
            ->will($this->returnValue('test_url'));

        $this->listener->process($this->event);
    }

    /**
     * Process loading entity and using fields for title
     */
    public function testProcessTitle()
    {
        $this->event->expects($this->once())
            ->method('getEntity')
            ->will($this->returnValue(false));

        $this->event->expects($this->once())
            ->method('getResultItem')
            ->will($this->returnValue($this->item));

        $this->item->expects($this->once())
            ->method('getRecordUrl')
            ->will($this->returnValue('url'));

        $this->item->expects($this->once())
            ->method('getRecordTitle')
            ->will($this->returnValue(false));

        $this->item->expects($this->once())
            ->method('getEntityName')
            ->will($this->returnValue(get_class($this->entity)));

        $repositoryMock = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->once())
            ->method('find')
            ->will($this->returnValue($this->entity));

        $this->em->expects($this->once())
            ->method('getRepository')
            ->with(get_class($this->entity))
            ->will($this->returnValue($repositoryMock));

        $this->entityNameResolver->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('testTitle'));

        $this->item->expects($this->once())
            ->method('setRecordTitle')
            ->with('testTitle');

        $this->listener->process($this->event);
    }
}
