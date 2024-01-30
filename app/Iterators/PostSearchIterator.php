<?php

namespace App\Iterators;

use Illuminate\Support\Collection;

class PostSearchIterator implements \IteratorAggregate
{

    public function __construct(protected Collection $posts)
    {
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->posts->all());
    }
}
