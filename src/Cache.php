<?php

namespace Foamycastle\Cachual;

use Exception;
use Throwable;

abstract class Cache implements CacheApi
{
    protected string $location;
    protected bool $lastOpSuccess;
    protected Throwable $previousError;


    /**
     * Open a repository item.  If it does not exist, create it
     * @return resource|false
     */
    abstract protected function openOrCreateRepositoryItem(string $name):mixed;

    /**
     * Retrieve the repository item
     * @param string $name the key by which the item is identified
     * @return mixed
     */
    abstract protected function openRepositoryItem(string $name):mixed;

    /**
     * Close the resource used to read and write to the repository item
     * @param resource $item
     * @return mixed
     */
    abstract protected function closeRepositoryItem($item):mixed;

    /**
     * Returns True if the TTL of the cache item has elapsed
     * @param \SplObjectStorage $item
     * @return bool
     */
    abstract protected function isTTLExpired(\SplObjectStorage $item):bool;

    function success(): bool
    {
        return $this->lastOpSuccess ?? true;
    }
    function previousError(): ?Throwable
    {
        return $this->previousError ?? null;
    }
    protected function setLastError(Throwable $exception):void
    {
        $this->previousError = $exception;
    }
    protected function setLastOpSuccess(bool $success):void
    {
        $this->lastOpSuccess = $success;
    }

}