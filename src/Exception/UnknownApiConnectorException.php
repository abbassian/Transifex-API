<?php declare(strict_types=1);

namespace Autoborna\Transifex\Exception;

/**
 * Exception defining an unknown API connector.
 */
final class UnknownApiConnectorException extends \InvalidArgumentException implements TransifexException
{
}
