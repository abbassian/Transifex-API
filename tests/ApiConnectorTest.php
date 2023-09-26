<?php declare(strict_types=1);

namespace Autoborna\Transifex\Tests;

use Autoborna\Transifex\Config;
use Autoborna\Transifex\Connector\Resources;
use Autoborna\Transifex\Exception\MissingCredentialsException;
use Autoborna\Transifex\Transifex;

/**
 * Test class for \Autoborna\Transifex\ApiConnector.
 */
final class ApiConnectorTest extends ApiConnectorTestCase
{
    /**
     * @testdox The API does not connect when API credentials are not available
     */
    public function testApiFailureWhenNoAuthenticationIsSet(): void
    {
        $this->expectException(MissingCredentialsException::class);
        $this->expectExceptionMessage('The API token is required.');

        $transifex = new Transifex($this->client, $this->requestFactory, $this->streamFactory, $this->uriFactory, new Config());
        $transifex->getConnector(Resources::class)->getAll();
    }

    /**
     * @testdox When a custom base URL is set in the options the API request goes to that URL
     */
    public function testCustomBaseUrlIsUsed(): void
    {
        $this->prepareSuccessTest();

        $this->config->setBaseUri('https://api.transifex.com');

        $this->transifex->getConnector(Resources::class)->getAll();

        $this->assertCorrectRequestAndResponse('/resources');

        $this->assertSame(
            'api.transifex.com',
            $this->client->getRequest()->getUri()->getHost(),
            'The API did not use the right host.'
        );
    }
}
