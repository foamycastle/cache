<?php

namespace Foamycastle\Cachual\Exception;

use Foamycastle\Cachual\Exception\CacheException;

class DuplicateKeyNotAllowed extends CacheException
{
    public function __construct(?string $oldKey = null, ?string $newKey = null )
    {
        if(!is_null($oldKey) && !is_null($newKey)) {
            parent::__construct("Duplicate key ('{$oldKey}' to '{$newKey}') detected.");
            return;
        }

        parent::__construct("Duplicate keys are not allowed.");
    }

}