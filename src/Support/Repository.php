<?php

namespace Posty\Support;

use ArrayAccess;

class Repository implements ArrayAccess
{

    /**
     * @var array The repository items.
     */
    protected array $items = [];

    /**
     * Returns true if the given item exists within the repository
     *
     * @param mixed $offset
     * @return bool|void
     */
    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    /**
     * Returns the given item from the repository.
     *
     * @param mixed $offset
     * @return mixed|void
     */
    public function offsetGet($offset)
    {
        return $this->items[$offset] ?? null;
    }

    /**
     * Sets the given item in the repository.
     *
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if(is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    /**
     * Removes the given item from the repository.
     *
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }
}