<?php

namespace Foamycastle\Cachual;

interface CachePoolApi extends \ArrayAccess, \IteratorAggregate
{

    /**
     * Commit a cache item to the pool
     * @param int|string $key the item's key
     * @param mixed $value the item's value
     * @param int $ttl in seconds, the time the cache item will be valid
     * @return bool returns true on successful commit, false on failure to commit
     */
    function set(int|string $key, mixed $value, int $ttl=0, bool $locked=false):bool;

    /**
     * Retrieve an item from the cache pool
     * @param int|string $key the item's key
     * @return mixed the item's data
     */
    function get(int|string $key): mixed;

    /**
     * Indicates that a certain key is present in the cache pool
     * @param int|string $key the item's key
     * @return bool TRUE if an item possessing the specified `$key` exists in the pool
     */
    function has(int|string $key): bool;

    /**
     * Reset an item's TTL
     * @param int|string $key the item's key
     * @param int $ttl the number of seconds the item will remain valid
     * @return bool
     */
    function renew(int|string $key, int $ttl):bool;

    /**
     * Remove an item from the cache pool
     * @param int|string $key the item's key
     * @return bool TRUE if the item was successfully removed, FALSE if the item did not exist
     */
    function delete(int|string $key):bool;

    /**
     * Replace the item's value with a new value
     * @param int|string $key the item's key
     * @param mixed $value the data that replaces old data
     * @return bool TRUE if the data was replaced, FALSE if the data was not replaced
     */
    function replace(int|string $key, mixed $value):bool;


}