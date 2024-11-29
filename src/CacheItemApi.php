<?php

namespace Foamycastle\Cachual;

interface CacheItemApi
{

    /**
     * Indicates that this cache item is expired
     * @return bool
     */
    function isExpired():bool;

    /**
     * Generates an id string
     * @return string
     */
    function generateId():string;

    /**
     * Return the item's key data
     * @return string
     */
    function getKey():string;

    /**
     * Return the item's value data
     * @return mixed
     */
    function getValue():mixed;

    /**
     * Indicates the item is an object
     * @return bool
     */
    function isObject():bool;

    /**
     * Indicates that the item is read-only
     * @return bool
     */
    function isLocked():bool;

    /**
     * Return the expiration time of an item
     * @return int
     */
    function getTTL():int;

}