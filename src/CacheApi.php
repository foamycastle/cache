<?php

namespace Foamycastle\Cachual;

use Exception;
use Throwable;

interface CacheApi
{
    function set(string $key,mixed $value, int $ttl=0):self;
    function get(string $key):mixed;
    function getAll(string $key):array;
    function has(string $key):bool;
    function delete(string $key):bool;
    function clear():bool;

    /**
     * Return true if the previous operation was a success
     * @return bool
     */
    function success():bool;

    /**
     * If the previous operation was not a success, this function returns the
     * error message
     * @return Throwable|null
     */
    function previousError(): ?Throwable;

    public static function At(string $location):self;
}