<?php

namespace Foamycastle\Cachual\Item;

use Exception;

abstract class CacheItem implements \Serializable, \JsonSerializable, CacheItemApi
{

    /**
     * The id inside the cache pool
     * @var string
     */
    protected $id;

    /**
     * @var string $the identifier known to the user
     */
    protected $key;

    /**
     * The data contained in the cache item
     * @var scalar|array|null
     */
    protected $data;

    /**
     * Returns an automatically generated cache item ID
     * @return string
     */

    /**
     * Indicates that the data cannot be changed
     * @var bool
     */
    protected $locked;

    abstract protected function generateId():string;

    function getType(): string
    {
        return gettype($this->data ?? null);
    }
    public function assignId(?string $id = null): string|null
    {
        if($id === null) {
            $id = $this->generateId();
        }
        $oldId=($this->id ?? null);
        $this->id = $id;
        return $oldId;
    }

    public function getId(): string|null
    {
        return $this->id ?? null;
    }

    public function serialize():string
    {
        return serialize($this->data);
    }

    public function unserialize($data){
        $this->data = unserialize($data);
    }

    public function jsonSerialize(): string
    {
        return json_encode(
            $this->data ?? [],
            JSON_INVALID_UTF8_SUBSTITUTE |
            JSON_PRESERVE_ZERO_FRACTION |
            JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG
        );
    }


}