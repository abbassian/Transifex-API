<?php declare(strict_types=1);

namespace Autoborna\Transifex\Tests;

use GuzzleHttp\Psr7\Response;
use Http\Factory\Guzzle\RequestFactory;
use Http\Factory\Guzzle\StreamFactory;
use Http\Factory\Guzzle\UriFactory;
use Autoborna\Transifex\Config;
use Autoborna\Transifex\ConfigInterface;
use Autoborna\Transifex\Tests\Client\TransifexTestClient;
use Autoborna\Transifex\Transifex;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

/**
 * Base test case for ApiConnector instances.
 */
abstract class ApiConnectorTestCase extends TestCase
{
    protected ConfigInterface $config;
    protected TransifexTestClient $client;
    protected RequestFactoryInterface $requestFactory;
    protected StreamFactoryInterface $streamFactory;
    protected UriFactoryInterface $uriFactory;
    protected Transifex $transifex;
    protected array $historyContainer = [];
    protected string $sampleString = '{"a":1,"b":2,"c":3,"d":4,"e":5}';
    protected string $errorString = '{"message": "Generic Error"}';

    protected function setUp(): void
    {
        $this->client         = new TransifexTestClient();
        $this->requestFactory = new RequestFactory();
        $this->streamFactory  = new StreamFactory();
        $this->uriFactory     = new UriFactory();
        $this->config         = new Config();

        $this->config->setApiToken('some-api-token');
        $this->config->setOrganization('some-organization');
        $this->config->setProject('some-project');
    
        $this->transifex = new Transifex($this->client, $this->requestFactory, $this->streamFactory, $this->uriFactory, $this->config);
    }

    /**
     * Asserts the request and response are in the intended state.
     */
    public function assertCorrectRequestAndResponse(string $path, string $method = 'GET', int $code = 200, string $body = ''): void
    {
        static::assertCorrectRequestMethod($method, $this->client->getRequest()->getMethod());
        static::assertCorrectRequestPath($path, $this->client->getRequest()->getUri()->getPath());
        static::assertCorrectResponseCode($code, $this->client->getResponse()->getStatusCode());
        static::assertSame($body, $this->client->getRequest()->getBody()->__toString());
    }

    /**
     * Asserts that a request used the intended method
     */
    public static function assertCorrectRequestMethod(
        string $expected,
        string $actual,
        string $message = 'The API did not use the right HTTP method.'
    ): void {
        static::assertSame($expected, $actual, $message);
    }

    /**
     * Asserts that a request connected to the correct path
     */
    public static function assertCorrectRequestPath(
        string $expected,
        string $actual,
        string $message = 'The API did not request the right endpoint.'
    ): void {
        static::assertSame($expected, $actual, $message);
    }

    /**
     * Asserts that a response has the correct HTTP status code
     */
    public static function assertCorrectResponseCode(
        int $expected,
        int $actual,
        string $message = 'The API did not return the right HTTP code.'
    ): void {
        static::assertSame($expected, $actual, $message);
    }

    /**
     * Prepares the mock response for a failed API connection.
     */
    protected function prepareFailureTest(): void
    {
        $this->client->setResponse(new Response(500, [], $this->errorString));
    }

    /**
     * Prepares the mock response for a successful API connection.
     *
     * @param int $code The expected HTTP code
     */
    protected function prepareSuccessTest(int $code = 200): void
    {
        $this->client->setResponse(new Response($code, [], $this->sampleString));
    }
}
