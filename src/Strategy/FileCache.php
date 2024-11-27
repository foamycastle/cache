<?php

namespace Foamycastle\Cachual\Strategy;

use Foamycastle\Cachual\Cache;
use Foamycastle\Cachual\CacheApi;
use Foamycastle\Collection\Collection;
use Foamycastle\Collection\CollectionItem;
use SplFileInfo;
use SplFileObject;

class FileCache extends Cache
{
    public const REPOSITORY_PATH='/tmp/.cachualData/';
    public const REPOSITORY_NAME='data';
    protected \SplObjectStorage $cache;
    protected SplFileInfo $fileInfo;
    protected SplFileObject $fileObject;
    protected bool $openRepo=false;
    public function __construct(
        protected string $filePath = self::REPOSITORY_PATH,
        protected string $repoName = self::REPOSITORY_NAME,
    )
    {
        $this->setLastOpSuccess(true);
        $this->fileInfo = new SplFileInfo($this->buildItemPath($this->repoName));

        //verify repo existence
        if($this->fileInfo->getRealPath()===false || !file_exists($this->fileInfo->getRealPath())) {
            if(!@mkdir($this->fileInfo->getPath(), 0755, true)) {
                $this->setLastOpSuccess(false);
                $this->setLastError(
                    new \Exception('Failed to create repository path "'.$this->filePath.'"')
                );
            }
            if(!$this->openOrCreateRepositoryItem('w')) {
                return;
            }
        }
        //read directory permissions
        $permission=$this->fileInfo->getPerms();
        if($permission!==false) {
            if($permission<octdec('0755')) {
                if(!@chmod($this->fileInfo->getRealPath(), 0755)){
                    $this->setLastOpSuccess(false);
                    $this->setLastError(
                        new \Exception('Failed to set permissions "'.$this->filePath.'"')
                    );
                    return;
                }
            }
        }
        //verify permissions to read and write
        if(!$this->fileInfo->isWritable() || !$this->fileInfo->isReadable()) {
            $this->setLastOpSuccess(false);
            $this->setLastError(
                new \Exception('Cannot perform IO operations on repo path "'.$this->filePath.'"')
            );
            return;
        }
    }
    public function __destruct()
    {
        if($this->openRepo) {
            $this->fileObject=$this->fileInfo->openFile('w');
            $contents=serialize($this->cacheItems);
            if($this->fileObject->fwrite($contents)===false) {
                $this->setLastOpSuccess(false);
                $this->setLastError(
                    new \Exception('Failed to write to file "'.$this->filePath.'"')
                );

            }
            $this->openRepo=false;
        }
    }

    protected function openOrCreateRepositoryItem(string $mode = 'w'): mixed
    {
        $itemPath=$this->buildItemPath($this->repoName);
        if(!file_exists($itemPath)) {
            if(!@touch($itemPath)) {
                $this->setLastOpSuccess(false);
                $this->setLastError(
                    new \Exception('Touch operation failed on repository item "'.$name.'"')
                );
                return false;
            }
            $this->fileObject = new SplFileObject(
                $itemPath,
                'w'
            );
            $this->cacheItems=Collection::FromArray([]);
            $this->openRepo = true;
            return true;
        }
        return $this->openRepositoryItem($mode);
    }

    protected function openRepositoryItem(string $mode='r'): mixed
    {
        $itemPath=$this->buildItemPath($this->repoName);
        $this->fileObject = new SplFileObject(
            $itemPath,
            $mode
        );
        if($this->fileObject->getSize()==0){
            $this->openRepo = true;
            $this->cacheItems=Collection::FromArray([]);
            return true;
        }
        $contents=$this->fileObject->fread($this->fileObject->getSize());
        $this->cacheItems=unserialize($contents,['allowed_classes'=>[Collection::class]]);
        $this->openRepo = true;
        return true;
    }

    protected function closeRepositoryItem($item): mixed
    {
        // TODO: Implement closeRepositoryItem() method.
    }

    protected function isTTLExpired(\SplObjectStorage $item): bool
    {
        // TODO: Implement isTTLExpired() method.
    }

    protected function buildRepositoryPath(): string
    {
        return str_ends_with($this->filePath, '/')
            ? rtrim($this->filePath, '/')
            : $this->filePath;
    }
    private function buildItemPath(string $name):string
    {
        return $this->buildRepositoryPath().DIRECTORY_SEPARATOR.$name.'.cache';
    }


    function set(string $key, mixed $value, int $ttl = 0): CacheApi
    {
        if(!$this->openRepo){
            if(!$this->openOrCreateRepositoryItem('w')) {
                $this->setLastOpSuccess(false);
                $this->setLastError(
                    new \Exception('Failed to open repository item "'.$key.'"')
                );
                return $this;
            }
        }
        $this->cacheItems[$key]=$value;
        return $this;
    }

    function get(string $key): mixed
    {
        // TODO: Implement get() method.
    }

    function getAll(string $key): array
    {
        // TODO: Implement getAll() method.
    }

    function has(string $key): bool
    {
        // TODO: Implement has() method.
    }

    function delete(string $key): bool
    {
        // TODO: Implement delete() method.
    }

    function clear(): bool
    {
        // TODO: Implement clear() method.
    }

    public static function At(string $location): CacheApi
    {
        // TODO: Implement At() method.
    }

}