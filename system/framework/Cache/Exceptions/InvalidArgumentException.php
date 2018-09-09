<?php
namespace Framework\Cache\Exceptions;

use InvalidArgumentException;
use Psr\SimpleCache\InvalidArgumentException as PsrInvalidArgumentException;

class InvalidArgumentException extends InvalidArgumentException implements PsrCacheException {
	
}