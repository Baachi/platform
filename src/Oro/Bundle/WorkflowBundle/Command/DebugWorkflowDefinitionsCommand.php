<?php

namespace Oro\Bundle\WorkflowBundle\Command;

use Doctrine\Common\Persistence\ObjectRepository;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Yaml\Yaml;

use Oro\Bundle\WorkflowBundle\Entity\WorkflowDefinition;

class DebugWorkflowDefinitionsCommand extends ContainerAwareCommand
{
    const NAME = 'oro:debug:workflow:definitions';
    const INLINE_DEPTH = 20;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName(static::NAME)
            ->setDescription('List workflow definitions registered within application')
            ->addArgument(
                'workflow-name',
                InputArgument::OPTIONAL,
                'Name of the workflow definition that should be dumped'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->hasArgument('workflow-name') && $input->getArgument('workflow-name')) {
            return $this->dumpWorkflowDefinition($input->getArgument('workflow-name'), $output);
        } else {
            return $this->listWorkflowDefinitions($output);
        }
    }

    /**
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function listWorkflowDefinitions(OutputInterface $output)
    {
        /** @var WorkflowDefinition[] $workflows */
        $workflows = $this->getWorkflowDefinitionRepository()->findAll();

        if (count($workflows)) {
            $table = new Table($output);
            $table->setHeaders(['System Name', 'Label', 'Related Entity', 'Type'])
                ->setRows([]);

            foreach ($workflows as $workflow) {
                $row = [
                    $workflow->getName(),
                    $workflow->getLabel(),
                    $workflow->getRelatedEntity(),
                    $workflow->isSystem() ? 'System' : 'Custom',
                ];
                $table->addRow($row);
            }
            $table->render();

            return 0;
        } else {
            $output->writeln('No workflow definitions found.');

            return 1;
        }
    }

    /**
     * @param string $workflowName
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function dumpWorkflowDefinition($workflowName, OutputInterface $output)
    {
        /** @var WorkflowDefinition $workflow */
        $workflow = $this->getWorkflowDefinitionRepository()->findOneBy(['name' => $workflowName]);

        if ($workflow) {
            $general = [
                'label' => $workflow->getLabel(),
                'entity' => $workflow->getRelatedEntity(),
                'entity_attribute' => $workflow->getEntityAttributeName(),
                'steps_display_ordered' => $workflow->isStepsDisplayOrdered(),
            ];

            $startStep = $workflow->getStartStep();
            if ($startStep) {
                $general['start_step'] = $startStep->getName();
            }

            $definition = [
                'workflows' => [
                    $workflow->getName() => array_merge($general, $workflow->getConfiguration())
                ]
            ];

            $output->write(Yaml::dump($definition, self::INLINE_DEPTH), true);

            return 0;
        } else {
            $output->writeln('No workflow definitions found.');

            return 1;
        }
    }

    /**
     * @return ObjectRepository
     */
    protected function getWorkflowDefinitionRepository()
    {
        return $this->getContainer()->get('doctrine')->getRepository(WorkflowDefinition::class);
    }
}
