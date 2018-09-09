<?php
namespace Framework\Cache\Exceptions;

use RuntimeException;
use Psr\SimpleCache\CacheException as PsrCacheException;

class CacheException extends RuntimeException implements PsrCacheException {
	
}