<?php

namespace Posty\Traits;

use Closure;

trait Values
{

    /**
     * Returns either the value if it's an array, or if it's a closure then the
     * results of that.
     *
     * @param string[]|\Closure $value
     * @param mixed $closureArguments
     * @return string[]
     */
    public function getValueFromArrayOrClosure($value, array $closureArguments = [])
    {
        if(is_array($value)) {
            return $value;
        }

        if($value instanceof Closure) {
            return $value(...$closureArguments);
        }

        return [];
    }
}