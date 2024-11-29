<?php

namespace Foamycastle\Cachual\Exception;

use Foamycastle\Cachual\Exception\CacheException;

class DoesNotImplementSerializable extends CacheException
{
    public function __construct()
    {
        parent::__construct('The object must implement Serializable.');
    }

}