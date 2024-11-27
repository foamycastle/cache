<?php

namespace Foamycastle\Cachual\Strategy;

use Foamycastle\Cachual\CacheStrategy;
use Foamycastle\UUID\UUIDBuilder;
use PHPUnit\Framework\TestCase;

class FileCacheTest extends TestCase
{

    public function testSet()
    {
        $cache=CacheStrategy::Create(CacheStrategy::FileCache,'/tmp');
        $key=UUIDBuilder::Version6();
        $cache->set($key, 'BIG LONG TEXT MESSAGE',15);
        $this->assertFileExists("/tmp/cacheData/$key.cache");
    }

    public function testDelete()
    {

    }

    public function testAt()
    {
        $strategy = CacheStrategy::Create(CacheStrategy::FileCache,'/tmp');
        $this->assertDirectoryExists('/tmp/cacheData');
        $this->assertInstanceOf(FileCache::class, $strategy);
    }

    public function testGetAll()
    {
        $cache=CacheStrategy::Create(CacheStrategy::FileCache,'/tmp');
        $key='1eface';
        $output=$cache->getAll($key);
        $this->assertIsArray($output);
        $this->expectsOutput();
        print_r($output);
    }

    public function testHas()
    {
        $cache=CacheStrategy::Create(CacheStrategy::FileCache,'/tmp');
        $key='1efacede-3a6f-6aef-8000-18c04d015a9f';
        $this->assertTrue($cache->has($key));
    }

    public function testGet()
    {
        $cache=CacheStrategy::Create(CacheStrategy::FileCache,'/tmp');
        $key='1efacede-3a6f-6aef-8000-18c04d015a9f';
        $this->assertEquals('bar',$cache->has($key));
    }

}
