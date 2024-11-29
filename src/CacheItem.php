<?php

namespace Foamycastle\Cachual;

use Foamycastle\Collection\CollectionItem;
use Foamycastle\Collection\CollectionItemInterface;
use Serializable;

class CacheItem extends CollectionItem implements CacheItemApi
{
    protected bool $isObject = false;
    public function __construct(
        string|int $key,
        mixed $value,
        protected int $ttl = 0,
        protected bool $locked=false
    )
    {
        parent::__construct($key,$value);

        if(is_object($value)) {
            $this->isObject = true;
        }

        //if ttl is specified, calculate the expiration value
        if($this->ttl>0){
            $this->ttl += time();
        }
    }

    function isExpired(): bool
    {
        if ($this->ttl == 0) return false;
        return $this->ttl < time();
    }

    public function __serialize(): array
    {
        try{
            $valueSerialized = serialize($this->value);
        }catch (\Exception $e){
            if(is_object($this->value)) {
                $valueSerialized = get_class($this->value);
            }
        }
        return [
            'key' => $this->key,
            'object' => $this->isObject,
            'locked' => $this->locked,
            'value' => $this->isObject
                ? $valueSerialized
                : $this->value,
            'ttl' => $this->ttl,
        ];
    }
    public function __unserialize(array $data): void
    {
        if(!isset($data['key']) || !isset($data['value']) || !isset($data['ttl']) || !isset($data['object'])) {
            $this->ttl=0;
            $this->isObject=false;
            $this->locked=false;
            $this->key='__unserialize_error';
            $this->value=null;
            return;
        }
        $this->key = $data['key'];
        $this->locked = $data['locked'] ?? false;
        if($data['object'] === true) {
            $this->isObject = true;
            if(is_string($data['value'])) {
                if(class_exists($data['value'])) {
                    try{
                        $this->value = new $data['value']();
                    }catch (\Throwable $e){
                        $this->value = null;
                    }
                }
            }else {
                $this->isObject = true;
                $this->value=$data['value'];
            }
        }else{
            $this->isObject = false;
            $this->value = $data['value'];
        }
    }

    function generateId(): string
    {
        return md5($this->key.uniqid());
    }

    function getKey(): string
    {
        return $this->key;
    }

    function getValue(): mixed
    {
        return $this->value;
    }

    function isObject(): bool
    {
        return $this->isObject;
    }
    function isLocked(): bool
    {
        return $this->locked;
    }
    function getTTL(): int
    {
        return $this->ttl;
    }

}