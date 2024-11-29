<?php


use Foamycastle\Cachual\CacheItem;
use PHPUnit\Framework\TestCase;

class CacheItemTest extends TestCase
{

    public function test__serialize()
    {
        $cacheItem = new CacheItem('idGenerator', (string)\Foamycastle\UUID\UUIDBuilder::Version1());
        $serialized = serialize($cacheItem);
        $this->expectsOutput();
        echo $serialized;
    }

    public function testIsExpired()
    {
        $cacheItem = new CacheItem('idGenerator', (string)\Foamycastle\UUID\UUIDBuilder::Version1(),5);
        while(!$cacheItem->isExpired()) {
            $this->assertFalse($cacheItem->isExpired());
            sleep(1);
        }
        $this->assertTrue($cacheItem->isExpired());
    }

    public function test__construct()
    {

    }

    public function test__unserialize()
    {
        $cacheItem = new CacheItem('idGenerator', \Foamycastle\UUID\UUIDBuilder::Version1());
        $serialized = serialize($cacheItem);
        unserialize($serialized, ['allowed_classes' => [$cacheItem::class]]);
    }

    public function testGenerateId()
    {
        $cacheItem = new CacheItem('idGenerator', \Foamycastle\UUID\UUIDBuilder::Version1());
        $this->assertIsString($cacheItem->generateId());
        $this->expectsOutput();
        echo $cacheItem->generateId().PHP_EOL;
    }

}
