<?php

namespace Foamycastle\Cachual;

use Foamycastle\Cachual\Strategy\FileCache;

enum CacheStrategy: string
{
    case FileCache=FileCache::class;

    /**
     * @param CacheStrategy|string $strategy
     * @return CacheApi|null
     */
    public static function Create(self|string $strategy, string $location):?CacheApi
    {
        /** @var Cache $class */
        if($strategy instanceof self){
            $class = $strategy->value;
        }else{
            $class = self::from($strategy)->value;
        }
        return $class::At($location);
    }
}