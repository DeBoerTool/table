<?php

namespace Dbt\Table;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Exception;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

class Row implements JsonSerializable, Countable, IteratorAggregate,
                     ArrayAccess
{
    /** @var \Dbt\Table\Cell[] */
    private array $stack;

    public function __construct (Cell ...$cells)
    {
        $this->stack = [];

        foreach ($cells as $cell) {
            $this->push($cell);
        }
    }

    /**
     * @param array<int, array|string|int|float|bool> $values
     * @return \Dbt\Table\Row
     * @throws \JsonException
     */
    public static function fromArray (array $values): self
    {
        $map = fn ($value): Cell => Cell::make($value);

        return new self(...array_map($map, $values));
    }

    public function all (): array
    {
        return $this->stack;
    }

    public function push (Cell $cell): void
    {
        $this->stack[] = clone $cell;
    }

    public function get (int $index): Cell
    {
        return $this->stack[$index];
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
    public function offsetGet ($offset): Cell
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
