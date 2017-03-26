<?php

namespace Oro\Bundle\ImportExportBundle\Tests\Unit\Async;

use Doctrine\Common\Persistence\ManagerRegistry;

use Symfony\Component\Routing\Router;

use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\EmailBundle\Provider\EmailRenderer;
use Oro\Bundle\ImportExportBundle\Async\ImportExportResultSummarizer;
use Oro\Bundle\MessageQueueBundle\Entity\Job;

class ImportExportResultSummarizerTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeConstructedWithRequiredAttributes()
    {
        new ImportExportResultSummarizer(
            $this->createRouterMock(),
            $this->createConfigManagerMock()
        );
    }

    public function testShouldReturnCorrectSummaryInformationWithoutErrorInImport()
    {
        $result = [
            'success' => true,
            'errors' => [],
            'counts' => [
                'add' => 2,
                'replace' => 5,
                'process' => 7,
                'read' => 7,
            ],
        ];
        $expectedData['data'] = [
            'hasError' => false,
            'successParts' => 2,
            'totalParts' => 2,
            'errors' => 0,
            'process' => 14,
            'read' => 14,
            'add' => 4,
            'replace' => 10,
            'update' => 0,
            'delete' => 0,
            'error_entries' => 0,
            'fileName' => 'import.csv',
            'downloadLogUrl' => '',
        ];

        $job = new Job();

        $childJob1 = new Job();
        $childJob1->setData($result);
        $job->addChildJob($childJob1);

        $childJob2 = new Job();
        $childJob2->setData($result);
        $job->addChildJob($childJob2);

        $consolidateService = new ImportExportResultSummarizer(
            $this->createRouterMock(),
            $this->createConfigManagerMock()
        );

        $result = $consolidateService->getSummaryResultForNotification($job, 'import.csv');

        $this->assertEquals($expectedData, $result);
    }

    public function testShouldReturnCorrectSummaryInformationWithErrorLink()
    {
        $data = [
            'success' => true,
            'errors' => [
                'error 1',
                'error 2',
            ],
            'counts' => [
                'add' => 2,
                'errors' => 2,
                'replace' => 1,
                'process' => 5,
                'read' => 5,
            ],
        ];
        $expectedData['data'] = [
            'hasError' => true,
            'successParts' => 2,
            'totalParts' => 2,
            'errors' => 4,
            'process' => 10,
            'read' => 10,
            'add' => 4,
            'replace' => 2,
            'update' => 0,
            'delete' => 0,
            'error_entries' => 0,
            'fileName' => 'import.csv',
            'downloadLogUrl' => 'http://127.0.0.1/log/12345',
        ];

        $job = new Job();
        $job->setId(12345);

        $childJob1 = new Job();
        $childJob1->setData($data);
        $job->addChildJob($childJob1);

        $childJob2 = new Job();
        $childJob2->setData($data);
        $job->addChildJob($childJob2);

        $router = $this->createRouterMock();
        $router
            ->expects($this->once())
            ->method('generate')
            ->with(
                $this->equalTo('oro_importexport_job_error_log'),
                $this->equalTo(['jobId' => $job->getId()])
            )
            ->willReturn('/log/12345')
        ;

        $configManager = $this->createConfigManagerMock();
        $configManager
            ->expects($this->once())
            ->method('get')
            ->with('oro_ui.application_url')
            ->willReturn('http://127.0.0.1')
        ;

        $consolidateService = new ImportExportResultSummarizer(
            $router,
            $configManager
        );

        $result = $consolidateService->getSummaryResultForNotification($job, 'import.csv');

        $this->assertEquals($expectedData, $result);
    }

    public function testShouldReturnErrorLog()
    {
        $data = [
            'success' => true,
            'errors' => [
                'error 1',
            ],
            'counts' => [
                'add' => 2,
                'errors' => 1,
                'replace' => 1,
                'process' => 4,
                'read' => 4,
            ],
        ];

        $job = new Job();
        $job->setId(1);
        $childJob1 = new Job();
        $childJob1->setData($data);
        $job->addChildJob($childJob1);
        $childJob2 = new Job();
        $childJob2->setData(array_merge($data, ['errors' => ['error 2']]));
        $job->addChildJob($childJob2);

        $consolidateService = new ImportExportResultSummarizer(
            $this->createRouterMock(),
            $this->createConfigManagerMock(),
            $this->createRenderMock(),
            $this->createManagerRegistryMock()
        );
        $summary = $consolidateService->getErrorLog($job);

        $this->assertEquals("error 1\nerror 2\n", $summary);
    }

    public function testProcessExportData()
    {
        $expectedResult = [
            'exportResult' => [
                'success' => true,
                'url' => '127.0.0.1/export.log',
                'readsCount' => 10,
                'errorsCount' => 0,
                'entities' => 'TestEntity',
                'fileName' => 'export_result',
                'downloadLogUrl' => '127.0.0.1/1.log'
            ],
            'jobName' => 'test.job.name',
        ];

        $routerMock = $this->createRouterMock();
        $routerMock
            ->expects($this->at(0))
            ->method('generate')
            ->with('oro_importexport_export_download', ['fileName' =>'export_result'])
            ->willReturn('/export.log')
        ;
        $routerMock
            ->expects($this->at(1))
            ->method('generate')
            ->with('oro_importexport_job_error_log', ['jobId' => 1])
            ->willReturn('/1.log')
        ;

        $configManagerMock = $this->createConfigManagerMock();
        $configManagerMock
            ->expects($this->exactly(2))
            ->method('get')
            ->with('oro_ui.application_url')
            ->willReturn('127.0.0.1')
        ;

        $consolidateService = new ImportExportResultSummarizer(
            $routerMock,
            $configManagerMock
        );

        $rootJob = new Job();
        $rootJob->setId(1);
        $rootJob->setName('test.job.name');
        $childJob = new Job();
        $rootJob->addChildJob($childJob);
        $childJob->setData([
            'success' => true,
            'file' => 'test',
            'readsCount' => 10,
            'errorsCount' => 0,
            'entities' => 'TestEntity',
            'errors' => []
        ]);

        $result = $consolidateService->processSummaryExportResultForNotification($rootJob, 'export_result');

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Router
     */
    private function createRouterMock()
    {
        return $this->createMock(Router::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ConfigManager
     */
    private function createConfigManagerMock()
    {
        return $this->createMock(ConfigManager::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|EmailRenderer
     */
    private function createRenderMock()
    {
        return $this->createMock(EmailRenderer::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ManagerRegistry
     */
    private function createManagerRegistryMock()
    {
        return $this->createMock(ManagerRegistry::class);
    }
}
