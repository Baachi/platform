<?php

namespace Oro\Bundle\SegmentBundle\Tests\Functional\DataFixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;

use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\SegmentBundle\Entity\Segment;
use Oro\Bundle\SegmentBundle\Entity\SegmentType;
use Oro\Bundle\TestFrameworkBundle\Entity\WorkflowAwareEntity;

class LoadSegmentData extends AbstractFixture
{
    const SEGMENT_DYNAMIC = 'segment_dynamic';
    const SEGMENT_STATIC = 'segment_static';

    /** @var array */
    private static $segments = [
        self::SEGMENT_DYNAMIC => [
            'name' => 'Dynamic Segment',
            'description' => 'Dynamic Segment Description',
            'entity' => WorkflowAwareEntity::class,
            'type' => SegmentType::TYPE_DYNAMIC,
            'definition' => [
                'columns' => [
                    [
                        'func' => null,
                        'label' => 'Label',
                        'name' => 'id',
                        'sorting' => ''
                    ]
                ],
                'filters' =>[]
            ]
        ],
        self::SEGMENT_STATIC => [
            'name' => 'Static Segment',
            'description' => 'Static Segment Description',
            'entity' => WorkflowAwareEntity::class,
            'type' => SegmentType::TYPE_STATIC,
            'definition' => [
                'columns' => [
                    [
                        'func' => null,
                        'label' => 'Label',
                        'name' => 'id',
                        'sorting' => ''
                    ]
                ],
                'filters' =>[]
            ]
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $organization = $manager->getRepository(Organization::class)->getFirst();
        $owner = $organization->getBusinessUnits()->first();

        foreach (self::$segments as $segmentReference => $data) {
            $segmentType = $manager->getRepository(SegmentType::class)->find($data['type']);

            $entity = new Segment();
            $entity->setName($data['name']);
            $entity->setDescription($data['description']);
            $entity->setEntity($data['entity']);
            $entity->setOwner($owner);
            $entity->setType($segmentType);
            $entity->setOrganization($organization);
            $entity->setDefinition(json_encode($data['definition']));

            $this->setReference($segmentReference, $entity);

            $manager->persist($entity);
        }

        $manager->flush();
    }
}
