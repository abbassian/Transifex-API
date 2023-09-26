<?php declare(strict_types=1);

namespace Autoborna\Transifex\Tests\Connector;

use Autoborna\Transifex\Connector\Statistics;
use Autoborna\Transifex\Exception\ResponseException;
use Autoborna\Transifex\Tests\ApiConnectorTestCase;

final class StatisticsTest extends ApiConnectorTestCase
{
    public function testGetStatisticsWithLanguage(): void
    {
        $this->prepareSuccessTest();
        $this->transifex->getConnector(Statistics::class)->getLanguageStats('autoborna-transifex', 'cs');
        $this->assertCorrectRequestAndResponse('/resource_language_stats');
        $this->assertSame('filter%5Bresource%5D=o%3Asome-organization%3Ap%3Asome-project%3Ar%3Aautoborna-transifex&filter%5Bproject%5D=o%3Asome-organization%3Ap%3Asome-project&filter%5Blanguage%5D=l%3Acs', $this->client->getRequest()->getUri()->getQuery());
    }

    public function testGetStatisticsWithoutLanguage(): void
    {
        $this->prepareSuccessTest();
        $this->transifex->getConnector(Statistics::class)->getLanguageStats('autoborna-transifex');
        $this->assertCorrectRequestAndResponse('/resource_language_stats');
        $this->assertSame('filter%5Bresource%5D=o%3Asome-organization%3Ap%3Asome-project%3Ar%3Aautoborna-transifex&filter%5Bproject%5D=o%3Asome-organization%3Ap%3Asome-project', $this->client->getRequest()->getUri()->getQuery());
    }

    public function testGetStatisticsFailure(): void
    {
        $this->prepareFailureTest();
        $this->expectException(ResponseException::class);
        $this->transifex->getConnector(Statistics::class)->getLanguageStats('autoborna-transifex');
    }
}
