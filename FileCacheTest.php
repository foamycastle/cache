<?php

namespace Foamycastle\Cachual\Strategy;

use Foamycastle\UUID\UUIDBuilder;
use PHPUnit\Framework\TestCase;

class FileCacheTest extends TestCase
{

    public function test__construct()
    {
        $cache = new FileCache();
        $cache->set('foo', 'bar');
    }

}
