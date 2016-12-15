<?php

namespace Oro\Bundle\ImportExportBundle\Async\Import;

use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Oro\Bundle\ImportExportBundle\Async\Topics;

class ChunkHttpImportMessageProcessor extends AbstractChunkImportMessageProcessor
{
    protected function processData(array $body)
    {
        $this->httpImportHandler->setImportingFileName($body['filePath']);

        return $this->httpImportHandler->handleImport(
            $body['jobName'],
            $body['processorAlias'],
            $body['options']
        );
    }

    protected function getSummaryMessage(array $data)
    {
        return sprintf(
            'Import for the %s is completed, success: %s, info: %s, errors url: %s, message: %s',
            $data['filePath'],
            $data['success'],
            $data['importInfo'],
            $data['errorsUrl'],
            $data['message']
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedTopics()
    {
        return [Topics::IMPORT_HTTP];
    }
}
