<?php

namespace Posty\Support;

use Closure;

class Arr
{


    /**
     * Returns the first item which matches the given condition.
     *
     * @param \Closure $condition
     * @param array    $array
     * @return mixed|null
     */
    public static function findWhere(Closure $condition, array $array)
    {
        foreach($array as $item) {
            if($condition($item)) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Returns the
     *
     * @param \Closure $needle
     * @param array    $haystack
     * @return int|null
     */
    public static function getIndexWhere(Closure $needle, array $haystack): ?int
    {
        foreach($haystack as $index => $item) {
            if($needle($item)) {
                return $index;
            }
        }

        return null;
    }

    /**
     * Inserts an item into the array at a given index.
     *
     * @param mixed   $item
     * @param int     $index
     * @param mixed[] $array
     * @return array
     */
    public static function insert($item, int $index, array $array): array
    {
        if (0 >= $index) {
            return array_merge([$item], $array);
        }

        if ($index >= count($array)) {
            return array_merge($array, [$item]);
        }

        $previousItems = array_slice($array, 0, $index, true);
        $afterItems = array_slice($array, $index, null, true);
        return array_merge($previousItems, [$item], $afterItems);
    }
}