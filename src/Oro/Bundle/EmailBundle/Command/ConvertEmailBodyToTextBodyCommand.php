<?php

namespace Oro\Bundle\EmailBundle\Command;

use Doctrine\DBAL\Connection;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Oro\Bundle\EmailBundle\Entity\EmailBody;
use Oro\Bundle\EmailBundle\Tools\EmailBodyHelper;

/**
 * Converts email body representations.
 * Will be deleted in 2.0
 */
class ConvertEmailBodyToTextBodyCommand extends ContainerAwareCommand
{
    const COMMAND_NAME = 'oro:email:convert-body-to-text';

    const BATCH_SIZE = 500;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName(static::COMMAND_NAME)
            ->setDescription('Converts emails body. Generates and stores textual email body representation.');

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Conversion of emails body is started.</info>');
        $container = $this->getContainer();

        /** @var Connection $connection */
        $connection = $container->get('doctrine')->getConnection();
        $tableName = $container->get('oro_entity.orm.native_query_executor_helper')->getTableName(EmailBody::class);
        $selectQuery = 'select id, body from ' . $tableName . ' where body is not null and text_body is null '
            . 'order by created desc limit :limit';
        $pageNumber = 0;
        $emailBodyHelper = new EmailBodyHelper();
        while (true) {
            $output->writeln(sprintf('<info>Process page %s.</info>', $pageNumber + 1));
            $data = $connection->fetchAll(
                $selectQuery,
                ['limit' => self::BATCH_SIZE],
                ['limit' => 'integer']
            );

            // exit if we have no data anymore
            if (count($data) === 0) {
                break;
            }

            foreach ($data as $dataArray) {
                $connection->update(
                    $tableName,
                    ['text_body' => $emailBodyHelper->getTrimmedClearText($dataArray['body'])],
                    ['id' => $dataArray['id']],
                    ['textBody' => 'string']
                );
            }

            $pageNumber++;
        }

        $output->writeln('<info>Job complete.</info>');
    }
}
