<?php

namespace Foamycastle\Cachual;

use Foamycastle\Collection\Collection;
use Foamycastle\Collection\CollectionItem;

class CachePool extends Collection implements CachePoolApi
{

    /**
     * A collection of cached items
     * @var CacheItemApi[]
     */
    protected array $collection;
    function __construct(
        string $name,
    ){
        parent::__construct();
    }

    function set(int|string $key, mixed $value, int $ttl = 0, bool $locked = false): bool
    {
        /** @var CacheItemApi&CollectionItem $findExisting */
        $findExisting=$this->findByKey($key);
        if($findExisting!==null) {
            if($findExisting->isLocked() || $findExisting->isExpired()) {
                return false;
            }
            $oid=$findExisting->getObjectId();
            $newItem=new CacheItem(
                    $findExisting->getKey(),$value,$findExisting->getTTL()
                );
            unset($this->collection[$oid]);
            $this->collection[$newItem->getObjectId()]=$newItem;
        }else{
            $newItem=new CacheItem(
                $key,$value,$ttl,$locked
            );
            $this->collection[$newItem->getObjectId()]=$newItem;
        }
        return true;
    }

    function get(int|string $key): mixed
    {
        /** @var CacheItemApi&CollectionItem $findByKey */
        $findByKey=$this->findByKey($key);
        if($findByKey!==null) {
            if($findByKey->isExpired()) {
                unset($this->collection[$findByKey->getObjectId()]);
                return null;
            }
            return $findByKey->getValue();
        }
        return null;
    }

    function has(int|string $key): bool
    {
        return $this->findByKey($key) !== null;
    }

    function renew(int|string $key, int $ttl): bool
    {
        /** @var CacheItemApi&CollectionItem $findByKey */
        $findByKey=$this->findByKey($key);
        if($findByKey!==null) {
            $oldId=$findByKey->getObjectId();
            $newItem=new CacheItem(
                $findByKey->getKey(),$findByKey->getValue(),$ttl,$findByKey->isLocked()
            );
            unset($this->collection[$oldId]);
            $this->collection[$newItem->getObjectId()]=$newItem;
            return true;
        }
        return false;
    }

    function delete(int|string $key): bool
    {
        /** @var CacheItemApi&CollectionItem $findByKey */
        $findByKey=$this->findByKey($key);
        if($findByKey!==null) {
            $oldId=$findByKey->getObjectId();
            unset($this->collection[$oldId]);
            return true;
        }
        return false;
    }


    function replace(int|string $key, mixed $value): bool
    {
        /** @var CacheItemApi&CollectionItem $findByKey */
        $findByKey=$this->findByKey($key);
        if($findByKey!==null) {
            $oldId=$findByKey->getObjectId();
            $newItem=new CacheItem(
                $findByKey->getKey(),$value,$findByKey->getTTL(),$findByKey->isLocked()
            );
            unset($this->collection[$oldId]);
            $this->collection[$newItem->getObjectId()]=$newItem;
            return true;
        }
        return false;
    }

}