<?php

namespace Dbt\Table\Exceptions;

use Exception;

class NoSuchCellException extends Exception
{
    /**
     * @param array<array{0: int, 1: string}> $tuples
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
                fn (array $tuple) => $tuple[1],
                $tuples
            )),
        ));
    }
}
