<?php

namespace Foamycastle\Cachual\Item;

interface CacheItemApi
{

    /**
     * Sets data in the cache item
     * @param scalar $data
     * @return bool TRUE if the put operation was successful, FALSE if the operation could not be completed
     */
    function setData(float|array|bool|int|string $data): bool;

    /**
     * Returns the data contained in the cache item
     * @return scalar|null
     */
    function getData():float|array|bool|int|string|null;

    /**
     * Return the cache item's unique Id
     * @return string|null
     */
    function getId(): string|null;

    /**
     * Assign the cache item a new ID
     * @param string|null $id If the id is null, an automatic id is generated
     * @return string|null Returns the old ID if successful, null if Unsuccessful
     */
    function assignId(string|null $id=null): string|null;

    /**
     * Return the data type in the cache item
     * @return string
     */
    function getType():string;

}