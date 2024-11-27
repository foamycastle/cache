<?php

namespace Foamycastle\Cachual\Strategy;

use Exception;
use Foamycastle\Cachual\Cache;
use Foamycastle\Cachual\CacheApi;
use SplObjectStorage;

class FileCache extends Cache
{
    protected const REPOSITORY='cacheData';
    protected function __construct(protected string $location)
    {
        if(!($this->findRepositoryDir()||$this->createRepositoryDir())){
            exit('Cache repository directory is not defined.');
        }
        chmod($this->getRepositoryDir(), 0755);
        chdir($this->location);
    }

    protected function findRepositoryDir(): bool
    {
        return is_readable($this->getRepositoryDir());
    }

    protected function verifyRepositoryDir(): bool
    {
        return @dir($this->getRepositoryDir())!==false;
    }

    protected function createRepositoryDir(): bool
    {
        return @mkdir($this->getRepositoryDir(),0755,true);
    }

    protected function getRepositoryDir(): string
    {
        return $this->location.DIRECTORY_SEPARATOR.self::REPOSITORY;
    }
    protected function openOrCreateRepositoryItem(string $name): mixed
    {
        return @touch($this->getItemPath($name));
    }

    protected function openRepositoryItem(string $name): mixed
    {
        return @fopen($this->getItemPath($name), 'r');
    }

    protected function closeRepositoryItem($item): mixed
    {
        return @fclose($item);
    }

    protected function isTTLExpired(\SplObjectStorage $item): bool
    {
        if(!is_null($item->getInfo()['ttl'])){
            return !($item->getInfo()['ttl'] == 0) && $item->getInfo()['ttl'] < time();
        }
        return false;
    }

    function set(string $key, mixed $value, int $ttl = 0): CacheApi
    {
        $itemResource=$this->openOrCreateRepositoryItem($key);
        if($itemResource===false) {
            $this->setLastOpSuccess(false);
            $this->setLastError(
                new Exception(error_get_last()['message'])
            );
            return $this;
        }else{
            $itemResource = @fopen($this->getItemPath($key), 'r+');
        }
        $storeObject=new SplObjectStorage();

        //if ttl is specified, store it as Unix seconds from now, else leave it zero for infinity
        if($ttl!=0){
            $ttl+=time();
        }

        //if the value to store is not object, make it one
        if(!is_object($value)){
            $storageObject=new \stdClass();
            $storageObject->value=$value;
        }else{
            $storageObject=$value;
        }

        $storeObject->attach($storageObject,['ttl'=>$ttl]);
        if(@fwrite($itemResource, $storeObject->serialize())===false){
            $this->setLastOpSuccess(false);
            $this->setLastError(
                new Exception('Unable to write to cache file.')
            );
        }
        $this->closeRepositoryItem($itemResource);
        return $this;
    }

    function get(string $key): mixed
    {
        //check for key existence
        if(!$this->has($key)){
            $this->setLastOpSuccess(false);
            $this->setLastError(
                new Exception('cache item does not exist.')
            );
            return null;
        }

        //attempt to load cache object from path
        $objectStore=$this->loadObjectFromPath($this->getItemPath($key));
        if($objectStore===null){
            $this->setLastOpSuccess(false);
            $this->setLastError(
                new Exception('Unable to read from cache file.')
            );
            return null;
        }

        //decide if TTL has elapsed
        if($this->isTTLExpired($objectStore)){
            $this->setLastOpSuccess(true);
            $this->setLastError(
                new Exception('Cache item expired.')
            );
            $this->delete($key);
            return null;
        }

        //return value or object
        return $this->getValueFromCurrentStore($objectStore);
    }

    function getAll(string $key): array
    {
        //gather all cache object files
        $dirList=glob($this->getRepositoryDir().DIRECTORY_SEPARATOR."*$key*.cache");
        if($dirList===false){
            return [];
        }

        //return all values in an array
        $outputArray=[];
        foreach ($dirList as $item) {
            $objectStore=$this->loadObjectFromPath($item);
            if($objectStore===null || $this->isTTLExpired($objectStore)) continue;
            $outputArray[basename($item,'.cache')]=$this->getValueFromCurrentStore($objectStore);
        }
        return $outputArray;
    }

    function has(string $key): bool
    {
        return file_exists($this->getItemPath($key));
    }

    function delete(string $key): bool
    {
        if($this->has($key)) {
            return unlink($this->getItemPath($key));
        }
        return true;
    }

    function clear(): bool
    {
        $dirList=glob($this->getRepositoryDir().DIRECTORY_SEPARATOR."*.cache");
        return array_walk($dirList,function(&$item){
            $this->delete($item);
        });
    }

    public static function At(string $location):CacheApi
    {
        return new static($location);
    }

    private function getItemPath(string $itemName):string
    {
        return $this->getRepositoryDir().DIRECTORY_SEPARATOR.$itemName.'.cache';
    }

    private function loadObjectFromPath(string $path):?SplObjectStorage
    {
        $objectStore=new SplObjectStorage();
        $rawData=@file_get_contents($path);
        if($rawData!==false){
            $objectStore->unserialize($rawData);
            return $objectStore;
        }
        return null;
    }

    private function getValueFromCurrentStore(SplObjectStorage $objectStore):mixed
    {
        return ($objectStore->current() instanceof \stdClass)
            ? $objectStore->current()->value
            : $objectStore->current();
    }



}