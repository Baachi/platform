<?php

namespace Oro\Bundle\EmailBundle\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

use JMS\JobQueueBundle\Entity\Job;

use Oro\Bundle\EmailBundle\Command\ConvertEmailBodyToTextBody;

/**
 * Adds job to collect email body representations.
 * Will be deleted in 2.0
 */
class CollectEmailBodyJobFixture extends AbstractFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $job = new Job(ConvertEmailBodyToTextBody::COMMAND_NAME, []);
        $manager->persist($job);
        $manager->flush($job);
    }
}
