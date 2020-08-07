<?php

namespace Posty\Support;

use Closure;

class Arr
{

    /**
     * Sorts an array from the keys of another.
     *
     * @param array $arrayToSort
     * @param array $arrayToSortFrom
     * @return array
     */
    public static function sortByArray(array $arrayToSort, array $arrayToSortFrom): array
    {
        return array_merge(array_flip($arrayToSortFrom), $arrayToSort);
    }

    /**
     * Moves an item to another position within the array.
     *
     * @param int   $itemAtIndex
     * @param int   $toIndex
     * @param array $array
     * @return array
     */
    public static function move(int $itemAtIndex, int $toIndex, array $array): array
    {
        $out = array_splice($array, $itemAtIndex, 1);
        array_splice($array, $toIndex, 0, $out);
        return $array;
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