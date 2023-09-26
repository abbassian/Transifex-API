<?php declare(strict_types=1);

namespace Autoborna\Transifex\Tests\Connector;

use Autoborna\Transifex\Connector\Translations;
use Autoborna\Transifex\Tests\ApiConnectorTestCase;

final class TranslationsTest extends ApiConnectorTestCase
{
    public function testUpload(): void
    {
        $this->prepareSuccessTest(201);
        $this->transifex->getConnector(Translations::class)->upload('autoborna-transifex', 'cs', 'some="translation"');
        $expectedBody = <<<EOT
{
    "data": {
        "attributes": {
            "content": "c29tZT0idHJhbnNsYXRpb24i",
            "content_encoding": "base64",
            "file_type": "default"
        },
        "relationships": {
            "language": {
                "data": {
                    "type": "languages",
                    "id": "l:cs"
                }
            },
            "resource": {
                "data": {
                    "type": "resources",
                    "id": "o:some-organization:p:some-project:r:autoborna-transifex"
                }
            }
        },
        "type": "resource_translations_async_uploads"
    }
}
EOT;
        $this->assertCorrectRequestAndResponse('/resource_translations_async_uploads', 'POST', 201, $expectedBody);
    }

    public function testDownload(): void
    {
        $this->prepareSuccessTest(201);
        $this->transifex->getConnector(Translations::class)->download('autoborna-transifex', 'cs');
        $expectedBody = <<<EOT
{
    "data": {
        "relationships": {
            "language": {
                "data": {
                    "type": "languages",
                    "id": "l:cs"
                }
            },
            "resource": {
                "data": {
                    "type": "resources",
                    "id": "o:some-organization:p:some-project:r:autoborna-transifex"
                }
            }
        },
        "type": "resource_translations_async_downloads"
    }
}
EOT;
        $this->assertCorrectRequestAndResponse('/resource_translations_async_downloads', 'POST', 201, $expectedBody);
    }
}
