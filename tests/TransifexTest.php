<?php declare(strict_types=1);

namespace Autoborna\Transifex\Tests;

use Autoborna\Transifex\ApiConnector;
use Autoborna\Transifex\Connector\Resources;
use Autoborna\Transifex\Exception\UnknownApiConnectorException;

final class TransifexTest extends ApiConnectorTestCase
{
    public function testGettingAConnectorThatExists(): void
    {
        $this->assertInstanceOf(Resources::class, $this->transifex->getConnector(Resources::class));
    }

    public function testGettingAConnectorThatDoesNotExist(): void
    {
        $this->expectException(UnknownApiConnectorException::class);
        $this->transifex->getConnector('unicorn'); /* @phpstan-ignore-line this is wrong on purpose to test the exception */
    }

    public function testGetApiConnector(): void
    {
        $this->assertInstanceOf(ApiConnector::class, $this->transifex->getApiConnector());
    }
}
