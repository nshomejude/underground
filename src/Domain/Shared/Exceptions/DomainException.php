<?php

declare(strict_types=1);

namespace Domain\Shared\Exceptions;

/**
 * Base class for every violation of a domain invariant.
 *
 * Infrastructure and interface layers may catch this to translate a broken
 * business rule into a transport specific response (HTTP 422, CLI error, ...).
 */
class DomainException extends \DomainException
{
}
