<?php

namespace Dbt\Table\Exceptions;

use Closure;
use Exception;

class NoSuchCellException extends Exception
{
    /**
     * @param array<array{0: int, 1: string|\Closure}> $tuples
     */
    public static function of (array $tuples): self
    {
        return new self(sprintf(
            'No row with cells at index(es) "%s" with value(s) "%s".',
            implode(', ', array_map(
                fn (array $tuple) => $tuple[0],
                $tuples
            )),
            implode(', ', array_map(
                fn (array $tuple) => self::toString($tuple[1]),
                $tuples
            )),
        ));
    }

    /**
     * @param Closure|mixed $value
     * @return string
     */
    protected static function toString ($value): string
    {
        if ($value instanceof Closure) {
            return '[function]';
        }

        return (string) $value;
    }
}
