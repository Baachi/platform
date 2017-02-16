<?php

namespace Oro\Bundle\IntegrationBundle\Action;

use Doctrine\ORM\EntityManagerInterface;
use Oro\Bundle\IntegrationBundle\Entity\Channel;

class ChannelDisableActionHandler implements ChannelActionHandlerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Channel $channel
     *
     * @return bool
     */
    public function handleAction(Channel $channel)
    {
        $channel->setEnabled(false);
        $this->entityManager->flush();

        return true;
    }
}
