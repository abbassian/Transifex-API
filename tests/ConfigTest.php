<?php declare(strict_types=1);

namespace Autoborna\Transifex\Tests;

use Autoborna\Transifex\Config;
use Autoborna\Transifex\Exception\MissingCredentialsException;
use PHPUnit\Framework\Assert;

final class ConfigTest extends ApiConnectorTestCase
{
    public function testInstantiation(): void
    {
        $config = new Config();
        $config->setApiToken('some-token');
        $config->setOrganization('some-org');
        $config->setProject('some-project');

        Assert::assertSame('some-token', $config->getApiToken());
        Assert::assertSame('some-org', $config->getOrganization());
        Assert::assertSame('some-project', $config->getProject());
        Assert::assertSame('https://rest.api.transifex.com', $config->getBaseUri());

        $config->setBaseUri('https://some.link');
        Assert::assertSame('https://some.link', $config->getBaseUri());

        $config->validate();
    }

    public function testValidateWithMissingApiToken(): void
    {
        $config = new Config();
        $config->setOrganization('some-org');
        $config->setProject('some-project');

        $this->expectException(MissingCredentialsException::class);

        $config->validate();
    }
}
