<?php declare(strict_types=1);

namespace Autoborna\Transifex\Tests\Connector;

use Autoborna\Transifex\Connector\Resources;
use Autoborna\Transifex\Exception\ResponseException;
use Autoborna\Transifex\Tests\ApiConnectorTestCase;

final class ResourcesTest extends ApiConnectorTestCase
{
    public function testCreateResourceContent(): void
    {
        $this->prepareSuccessTest(201);
        $this->transifex->getConnector(Resources::class)->create('Autoborna Transifex', 'autoborna-transifex', 'INI');
        $expectedBody = <<<EOT
{
    "data": {
        "attributes": {
            "name": "Autoborna Transifex",
            "slug": "autoborna-transifex"
        },
        "relationships": {
            "i18n_format": {
                "data": {
                    "type": "i18n_formats",
                    "id": "INI"
                }
            },
            "project": {
                "data": {
                    "type": "projects",
                    "id": "o:some-organization:p:some-project"
                }
            }
        },
        "type": "resources"
    }
}
EOT;
        $this->assertCorrectRequestAndResponse('/resources', 'POST', 201, $expectedBody);
    }

    public function testDeleteResource(): void
    {
        $this->prepareSuccessTest();
        $this->transifex->getConnector(Resources::class)->delete('test-resource');
        $this->assertCorrectRequestAndResponse('/resources/o:some-organization:p:some-project:r:test-resource', 'DELETE');
    }

    public function testCreateResourceFailure(): void
    {
        $this->prepareFailureTest();
        $this->expectException(ResponseException::class);
        $this->transifex->getConnector(Resources::class)->create('Autoborna Transifex', 'autoborna-transifex', 'INI');
    }

    public function testGetResources(): void
    {
        $this->prepareSuccessTest();
        $this->transifex->getConnector(Resources::class)->getAll();
        $this->assertCorrectRequestAndResponse('/resources');
        $this->assertSame('filter%5Bproject%5D=o%3Asome-organization%3Ap%3Asome-project', $this->client->getRequest()->getUri()->getQuery());
    }

    public function testGetResourcesFailure(): void
    {
        $this->prepareFailureTest();
        $this->expectException(ResponseException::class);
        $this->transifex->getConnector(Resources::class)->getAll();
    }

    public function testGetContent(): void
    {
        $this->prepareSuccessTest();
        $this->transifex->getConnector(Resources::class)->getContent('test-resource');
        $this->assertCorrectRequestAndResponse('/resource_strings', 'GET');
        $this->assertSame('filter%5Bresource%5D=o%3Asome-organization%3Ap%3Asome-project%3Ar%3Atest-resource', $this->client->getRequest()->getUri()->getQuery());
    }

    public function testUploadContent(): void
    {
        $this->prepareSuccessTest(201);
        $this->transifex->getConnector(Resources::class)->uploadContent('autoborna-transifex', 'some="translation"');
        $expectedBody = <<<EOT
{
    "data": {
        "attributes": {
            "replace_edited_strings": false,
            "content": "c29tZT0idHJhbnNsYXRpb24i",
            "content_encoding": "base64"
        },
        "relationships": {
            "resource": {
                "data": {
                    "type": "resources",
                    "id": "o:some-organization:p:some-project:r:autoborna-transifex"
                }
            }
        },
        "type": "resource_strings_async_uploads"
    }
}
EOT;
        $this->assertCorrectRequestAndResponse('/resource_strings_async_uploads', 'POST', 201, $expectedBody);
    }
}
