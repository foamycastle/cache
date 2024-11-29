<?php


use Foamycastle\Cachual\CachePool;
use PHPUnit\Framework\TestCase;

class CachePoolTest extends TestCase
{

    public function testSet()
    {
        $pool=new CachePool('name');
        $pool->set('cachePool', 'turd money');
        $this->assertEquals('turd money', $pool->get('cachePool'));
    }

}
