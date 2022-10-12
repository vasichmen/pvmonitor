<?php


namespace App\Services;

use Illuminate\Support\Collection;

abstract class AbstractService
{
    protected function makeCollection(mixed $items)
    {
        if ($items instanceof Collection) {
            return $items;
        }

        if (is_array($items)) {
            return collect($items);
        }

        return collect()->add($items);
    }
}
