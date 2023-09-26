CHANGELOG for the Transifex API Package
===============

See https://github.com/autoborna/Transifex-API/releases for newer releases.

* 3.0.0 (2019-11-19)

 * Raised minimum PHP version to 7.2
 * Add support for the Organizations API
 * Renamed `TransifexObject` to `ApiConnector`
 * Moved all connector classes to the `BabDev\Transifex\Connector` namespace
 * Made all classes final except the abstract `ApiConnector`
 * The `Transifex` class now requires a `FactoryInterface` for building API connectors as a constructor argument
 * The `ApiConnector` class now requires `RequestFactoryInterface` and `UriFactoryInterface` (PSR-17) implementations as constructor arguments
 * The `$client` constructor argument of the `ApiConnector` class is now a PSR-18 `ClientInterface` and is required
 * Removed `ApiConnector::getAuthData()`, a request's Authorization header will be added with the new `ApiConnector::createRequest()` method
 * `Transifex::setOption()` no longer has a return

* 2.1.1 (2018-03-10)

 * Code incorrectly referenced `base_url` as the option name versus `base_uri`

* 2.1.0 (2018-03-10)

 * [#7](https://github.com/BabDev/Transifex-API/pulls/7) Add the ?details fragment when retrieving language details
 * [#9](https://github.com/BabDev/Transifex-API/issues/9) The base_url option is not taken into account when issuing requests

* 2.0.0 (2016-12-21)

 * Dropped support for PHP 5.x, PHP 7.0 is the minimum requirement
 * Replace `joomla/http` package with `guzzlehttp/guzzle` for HTTP adapter
 * Add support for custom namespaces to `Transifex::get()`
 * Refactor API connector methods to return a full `Psr\Http\Message\ResponseInterface` object
 * Removed magic getter in `Transifex` and associated class member vars
 * Support a default option in `Transifex::getOption()`
 * `Translationstrings::getStrings()` now typehints the `$options` parameter
 * Rename `api.url` option to `base_uri`
 * Remove support for `ArrayAccess` objects as an options param, must pass an array
 * Removed `TransifexObject::fetchUrl()`, use the Guzzle API instead
 * Removed `TransifexObject::processResponse()`, use the Guzzle API instead

* 1.3.0 (2016-06-09)

 * Deprecated `Http` class and `joomla/http` package integration
 * Deprecated `TransifexObject::fetchUrl()`, 2.0 will use the Guzzle API to replace this functionality

* 1.2.0 (2015-07-13)

 * Add `Transifex::get()` to fetch API objects
 * Deprecated magic getter in `Transifex` and associated class member vars for storing objects

* 1.1.0 (2015-07-12)

 * Deprecated `TransifexObject::processResponse()`, 2.0 will return the full `Joomla\Http\Response` object instead of processing the response internally

* 1.0.0 (2014-10-20)

 * Initial stable release
