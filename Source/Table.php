<?php

namespace Dbt\Table;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Dbt\Table\Exceptions\NoSuchCellException;
use Exception;
use IteratorAggregate;
use JsonSerializable;
use LengthException;
use Traversable;

class Table implements JsonSerializable, Countable, IteratorAggregate,
                       ArrayAccess
{
    /** @var \Dbt\Table\Row[] */
    private array $stack;
    private int $length;

    public function __construct (Row ...$rows)
    {
        $this->stack = [];
        $this->length = 0;

        foreach ($rows as $row) {
            $this->push($row);
        }
    }

    /**
     * @param array[] $rows
     * @throws \JsonException
     */
    public static function fromArray (array $rows): self
    {
        $map = fn (array $row): Row => Row::fromArray($row);

        return new self(...array_map($map, $rows));
    }

    public function push (Row $row): void
    {
        /**
         * The length of the first row defines the expected length of each row
         * in the list.
         */
        if (count($this->stack) === 0) {
            $this->length = count($row);
        }

        if (($length = count($row)) !== $this->length) {
            throw new LengthException(sprintf(
                'Expected row length of %s, got %s.',
                $this->length,
                $length,
            ));
        }

        $this->stack[] = clone $row;
    }

    public function all (): array
    {
        return $this->stack;
    }

    /**
     * @deprecated
     */
    public function get (int $index): Row
    {
        return $this->row($index);
    }

    public function row (int $index): Row
    {
        return $this->stack[$index];
    }

    public function headers (): Row
    {
        return $this->stack[0];
    }

    public function exceptHeaders (): Table
    {
        $stack = $this->stack;

        unset($stack[0]);

        return new Table(...$stack);
    }

    /**
     * @throws \Dbt\Table\Exceptions\NoSuchCellException
     */
    public function findByCell (int $cellIndex, string $value): Row
    {
        foreach ($this->exceptHeaders() as $row) {
            if ($row->cell($cellIndex)->equals($value)) {
                return $row;
            }
        }

        throw NoSuchCellException::of([[$cellIndex, $value]]);
    }

    /**
     * @param array<array{0: int, 1: string}> $indexesAndValues
     * @return \Dbt\Table\Row
     * @throws \Dbt\Table\Exceptions\NoSuchCellException
     */
    public function findByMultipleCells (array $indexesAndValues): Row
    {
        foreach ($this->exceptHeaders() as $row) {
            $matches = 0;

            foreach ($indexesAndValues as $tuple) {
                if ($row->get($tuple[0])->equals($tuple[1])) {
                    $matches++;
                }
            }

            if ($matches === count($indexesAndValues)) {
                return $row;
            }
        }

        throw NoSuchCellException::of($indexesAndValues);
    }

    public function jsonSerialize (): array
    {
        return $this->all();
    }

    public function count (): int
    {
        return count($this->stack);
    }

    public function getIterator (): Traversable
    {
        return new ArrayIterator($this->all());
    }

    public function __clone ()
    {
        foreach ($this->stack as $index => $cell) {
            $this->stack[$index] = clone $cell;
        }
    }

    /**
     * @param int $offset
     */
    public function offsetExists ($offset): bool
    {
        return isset($this->stack[$offset]);
    }

    /**
     * @param int $offset
     */
    public function offsetGet ($offset): Row
    {
        return $this->stack[$offset];
    }

    /**
     * @param int $offset
     * @param mixed $value
     * @throws \Exception
     */
    public function offsetSet ($offset, $value)
    {
        throw new Exception('Not implemented.');
    }

    /**
     * @param int $offset
     * @throws \Exception
     */
    public function offsetUnset ($offset)
    {
        throw new Exception('Not implemented.');
    }
}
