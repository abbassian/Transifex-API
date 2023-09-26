<?php declare(strict_types=1);

namespace Autoborna\Transifex\Tests;

use Autoborna\Transifex\Promise;
use PHPUnit\Framework\Assert;

final class PromiseTest extends ApiConnectorTestCase
{
    public function testInstantiation(): void
    {
        $promise = new Promise('someid', 'https://some.link');
        $promise->setFilePath('/some/file/path');

        Assert::assertSame('someid', $promise->getId());
        Assert::assertSame('https://some.link', $promise->getCheckLink());
        Assert::assertSame('/some/file/path', $promise->getFilePath());
    }
}
