<?php declare(strict_types=1);

namespace Autoborna\Transifex\Tests\Functional;

use Autoborna\Transifex\Connector\Resources;
use Autoborna\Transifex\Connector\Statistics;
use Autoborna\Transifex\Connector\Translations;
use Autoborna\Transifex\Exception\ResponseException;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use SplQueue;

/**
 * This test is performing live API calls against the Transifex API.
 */
final class LiveTest extends TestCase
{
    private const RESOURCE_NAME = 'Test Resource';
    private const RESOURCE_SLUG = 'test-resource';

    public function testWorkflow(): void
    {
        $transifex = Factory::make();
        $resources = $transifex->getConnector(Resources::class);
        \assert($resources instanceof Resources);

        $statistics = $transifex->getConnector(Statistics::class);
        \assert($statistics instanceof Statistics);

        $translations = $transifex->getConnector(Translations::class);
        \assert($translations instanceof Translations);

        // Create a resource
        $response = $resources->create(self::RESOURCE_NAME, self::RESOURCE_SLUG, 'INI');
        $body     = \json_decode($response->getBody()->__toString(), true);

        Assert::assertSame(201, $response->getStatusCode());
        Assert::assertSame(self::RESOURCE_NAME, $body['data']['attributes']['name']);
        Assert::assertSame(self::RESOURCE_SLUG, $body['data']['attributes']['slug']);
        Assert::assertSame('INI', $body['data']['attributes']['i18n_type']);

        // Get the resources
        $response = $resources->getAll();
        $body     = \json_decode($response->getBody()->__toString(), true);
        Assert::assertSame(200, $response->getStatusCode());
        Assert::assertCount(1, $body['data'], $response->getBody()->__toString());
        Assert::assertSame(self::RESOURCE_NAME, $body['data'][0]['attributes']['name']);

        // Upload a resource content
        $response = $resources->uploadContent(self::RESOURCE_SLUG, "something=\"Something\"\nsomething.else=\"Something Else\"\n");
        $body     = \json_decode($response->getBody()->__toString(), true);
        Assert::assertSame(202, $response->getStatusCode());
        Assert::assertSame('pending', $body['data']['attributes']['status']);

        $promise  = $transifex->getApiConnector()->createPromise($response);
        $promises = new SplQueue();
        $promises->enqueue($promise); // In the real world, there would be multiple promises.

        \usleep(500000); // Give Transifex a 1/2 second so we make 1 request instead of 2.

        $successCounter = 0;

        // Assert that the resource content was uploaded successfully.
        $transifex->getApiConnector()->fulfillPromises(
            $promises,
            function (ResponseInterface $response) use (&$successCounter) {
                $successCounter++;
                Assert::assertSame(200, $response->getStatusCode());
            },
            function (ResponseException $exception) {
                $this->fail('Promise could not be fulfilled. ' . $exception->getMessage());
            }
        );

        Assert::assertSame(1, $successCounter);

        // Assert that the resource content is actually there.
        $response = $resources->getContent(self::RESOURCE_SLUG);
        $body     = \json_decode($response->getBody()->__toString(), true);
        Assert::assertSame(200, $response->getStatusCode());
        Assert::assertCount(2, $body['data']);
        Assert::assertSame('something', $body['data'][0]['attributes']['key']);
        Assert::assertSame('Something', $body['data'][0]['attributes']['strings']['other']);
        Assert::assertSame('something.else', $body['data'][1]['attributes']['key']);
        Assert::assertSame('Something Else', $body['data'][1]['attributes']['strings']['other']);

        // Upload a translation
        $response = $translations->upload(self::RESOURCE_SLUG, 'cs', "something=\"Něco\"\n");
        $body     = \json_decode($response->getBody()->__toString(), true);
        Assert::assertSame(202, $response->getStatusCode());
        Assert::assertSame('pending', $body['data']['attributes']['status']);

        $promise  = $transifex->getApiConnector()->createPromise($response);
        $promises = new SplQueue();
        $promises->enqueue($promise); // In the real world, there would be multiple promises.

        \usleep(500000); // Give Transifex a 1/2 second so we make 1 request instead of 2.

        $successCounter = 0;

        // Assert that the translation content was uploaded successfully.
        $transifex->getApiConnector()->fulfillPromises(
            $promises,
            function (ResponseInterface $response) use (&$successCounter) {
                $successCounter++;
                Assert::assertSame(200, $response->getStatusCode());
            },
            function (ResponseException $exception) {
                $this->fail('Promise could not be fulfilled. ' . $exception->getMessage());
            }
        );

        Assert::assertSame(1, $successCounter);

        \sleep(1); // Give Transifex a second to update the stats.

        // Test the statistics endpoint and that the resource stats are there.
        $response = $statistics->getLanguageStats(self::RESOURCE_SLUG);
        $body     = \json_decode($response->getBody()->__toString(), true);
        Assert::assertSame(200, $response->getStatusCode());
        Assert::assertCount(2, $body['data']);
        Assert::assertSame('l:cs', $body['data'][0]['relationships']['language']['data']['id']);
        Assert::assertSame(1, $body['data'][0]['attributes']['translated_words']);
        Assert::assertSame('l:en', $body['data'][1]['relationships']['language']['data']['id']);
        Assert::assertSame(3, $body['data'][1]['attributes']['translated_words']);

        // Download the translation
        $response = $translations->download(self::RESOURCE_SLUG, 'cs');
        $body     = \json_decode($response->getBody()->__toString(), true);
        Assert::assertSame(202, $response->getStatusCode());

        $promise  = $transifex->getApiConnector()->createPromise($response);
        $promises = new SplQueue();
        $promises->enqueue($promise); // In the real world, there would be multiple promises.

        \usleep(500000); // Give Transifex a 1/2 second so we make 1 request instead of 2.

        $successCounter     = 0;
        $translationContent = '';

        // Assert that the translation content was downloaded successfully.
        $transifex->getApiConnector()->fulfillPromises(
            $promises,
            function (ResponseInterface $response) use (&$successCounter, &$translationContent) {
                Assert::assertSame(200, $response->getStatusCode());
                $successCounter++;
                $translationContent = $response->getBody()->__toString();
            },
            function (ResponseException $exception) {
                $this->fail('Promise could not be fulfilled. ' . $exception->getMessage());
            }
        );

        Assert::assertSame(1, $successCounter);
        Assert::assertSame("something=\"Něco\"\n; something.else=\"Something Else\"\n", $translationContent);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
        $resources = Factory::make()->getConnector(Resources::class);
        Assert::assertSame(204, $resources->delete(self::RESOURCE_SLUG)->getStatusCode());
    }
}
