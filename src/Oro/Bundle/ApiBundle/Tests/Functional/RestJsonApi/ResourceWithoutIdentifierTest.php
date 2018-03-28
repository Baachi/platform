<?php

namespace Oro\Bundle\ApiBundle\Tests\Functional\RestJsonApi;

use Oro\Bundle\ApiBundle\Tests\Functional\Environment\Model\TestResourceWithoutIdentifier;
use Oro\Bundle\ApiBundle\Tests\Functional\RestJsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class ResourceWithoutIdentifierTest extends RestJsonApiTestCase
{
    public function testGet()
    {
        $entityType = $this->getEntityType(TestResourceWithoutIdentifier::class);
        $response = $this->request(
            'GET',
            $this->getUrl('oro_rest_api_list', ['entity' => $entityType])
        );

        self::assertResponseStatusCodeEquals($response, Response::HTTP_OK);
        self::assertResponseContentTypeEquals($response, self::JSON_API_CONTENT_TYPE);
        $this->assertResponseContains(
            [
                'meta' => [
                    'name' => 'test'
                ]
            ],
            $response
        );
    }

    public function testPost()
    {
        $data = [
            'meta' => [
                'name' => 'test'
            ]
        ];

        $entityType = $this->getEntityType(TestResourceWithoutIdentifier::class);
        $response = $this->request(
            'POST',
            $this->getUrl('oro_rest_api_list', ['entity' => $entityType]),
            $data
        );

        self::assertResponseStatusCodeEquals($response, Response::HTTP_NO_CONTENT);
    }

    public function testPatch()
    {
        $data = [
            'meta' => [
                'name' => 'test'
            ]
        ];

        $entityType = $this->getEntityType(TestResourceWithoutIdentifier::class);
        $response = $this->request(
            'PATCH',
            $this->getUrl('oro_rest_api_list', ['entity' => $entityType]),
            $data
        );

        self::assertResponseStatusCodeEquals($response, Response::HTTP_NO_CONTENT);
    }

    public function testDelete()
    {
        $entityType = $this->getEntityType(TestResourceWithoutIdentifier::class);
        $response = $this->request(
            'DELETE',
            $this->getUrl('oro_rest_api_list', ['entity' => $entityType])
        );

        self::assertResponseStatusCodeEquals($response, Response::HTTP_NO_CONTENT);
    }

    public function testValidationErrorPath()
    {
        $data = [
            'meta' => [
                'name' => ''
            ]
        ];

        $entityType = $this->getEntityType(TestResourceWithoutIdentifier::class);
        $response = $this->request(
            'POST',
            $this->getUrl('oro_rest_api_list', ['entity' => $entityType]),
            $data
        );

        $this->assertResponseValidationError(
            [
                'title'  => 'not blank constraint',
                'source' => ['pointer' => '/meta/name']
            ],
            $response
        );
    }

    public function testNotAllowedMethodsItemHandler()
    {
        $this->appendEntityConfig(
            TestResourceWithoutIdentifier::class,
            [
                'actions' => [
                    'update' => false,
                    'delete' => false
                ]
            ],
            true
        );

        $this->assertNotAllowedMethod('PATCH', 'GET, POST');
        $this->assertNotAllowedMethod('DELETE', 'GET, POST');
        $this->assertNotAllowedMethod('OPTIONS', 'GET, POST');
        $this->assertNotAllowedMethod('HEAD', 'GET, POST');
    }

    public function testNotAllowedMethodsListHandler()
    {
        $this->appendEntityConfig(
            TestResourceWithoutIdentifier::class,
            [
                'actions' => [
                    'create' => false,
                    'delete' => false
                ]
            ],
            true
        );

        $this->assertNotAllowedMethod('POST', 'GET, PATCH');
        $this->assertNotAllowedMethod('DELETE', 'GET, PATCH');
        $this->assertNotAllowedMethod('OPTIONS', 'GET, PATCH');
        $this->assertNotAllowedMethod('HEAD', 'GET, PATCH');
    }

    public function testNotAllowedMethodsWhenGetActionIsExcluded()
    {
        $this->appendEntityConfig(
            TestResourceWithoutIdentifier::class,
            [
                'actions' => [
                    'get' => false
                ]
            ],
            true
        );

        $this->assertNotAllowedMethod('GET', 'PATCH, POST, DELETE');
        $this->assertNotAllowedMethod('OPTIONS', 'PATCH, POST, DELETE');
        $this->assertNotAllowedMethod('HEAD', 'PATCH, POST, DELETE');
    }

    /**
     * @param string $method
     * @param string $expectedAllowedMethods
     */
    private function assertNotAllowedMethod($method, $expectedAllowedMethods)
    {
        $entityType = $this->getEntityType(TestResourceWithoutIdentifier::class);
        $response = $this->request(
            $method,
            $this->getUrl('oro_rest_api_list', ['entity' => $entityType])
        );
        self::assertResponseStatusCodeEquals($response, 405, $method);
        self::assertResponseContentTypeEquals($response, self::JSON_API_CONTENT_TYPE, $method);
        self::assertEquals($expectedAllowedMethods, $response->headers->get('Allow'), $method);
    }
}
