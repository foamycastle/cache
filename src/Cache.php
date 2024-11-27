<?php

namespace Foamycastle\Cachual;

use Exception;
use Foamycastle\Collection\Collection;
use Throwable;

abstract class Cache implements CacheApi
{
    protected bool $lastOpSuccess;
    protected Throwable $previousError;

    protected Collection $cacheItems;


    /**
     * Open a repository item.  If it does not exist, create it
     * @return resource|false
     */
    abstract protected function openOrCreateRepositoryItem(string $mode='r'):mixed;

    /**
     * Retrieve the repository item
     * @param string $name the key by which the item is identified
     * @return mixed
     */
    abstract protected function openRepositoryItem(string $mode='r'):mixed;

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

    /**
     * Build the URI that identifies the repository location
     * @return string
     */
    abstract protected function buildRepositoryPath():string;

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