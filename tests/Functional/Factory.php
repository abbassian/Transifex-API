<?php declare(strict_types=1);

namespace Autoborna\Transifex\Tests\Functional;

use GuzzleHttp\Client;
use Http\Factory\Guzzle\RequestFactory;
use Http\Factory\Guzzle\StreamFactory;
use Http\Factory\Guzzle\UriFactory;
use Autoborna\Transifex\Config;
use Autoborna\Transifex\Transifex;

/**
 * Creates the Transifex object with Guzzle client and factories.
 * It takes the configuration from environmental variables provided in the phpunit.xml(.dist) file.
 */
final class Factory
{
    public static function make(): Transifex
    {
        return new Transifex(new Client(), new RequestFactory(), new StreamFactory(), new UriFactory(), Config::fromEnv());
    }
}
