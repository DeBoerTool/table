<?php

namespace Dbt\Table;

use Closure;
use JsonSerializable;

class Cell implements JsonSerializable
{
    private string $value;
    private bool $isJson;

    public function __construct (string $value, bool $isJson = false)
    {
        $this->value = $value;
        $this->isJson = $isJson;
    }

    /**
     * @param string|int|float|bool|array $value
     * @throws \JsonException
     */
    public static function make ($value): self
    {
        if (is_array($value)) {
            return new self(
                json_encode($value, JSON_THROW_ON_ERROR),
                true,
            );
        }

        return new self((string) $value);
    }

    public function isJson (): bool
    {
        return $this->isJson;
    }

    public function value (): string
    {
        return $this->value;
    }

    public function valueWithDefault (string $default): string
    {
        return $this->value() === ''
            ? $default
            : $this->value;
    }

    /**
     * @param \Closure|mixed $value
     */
    public function equals ($value): bool
    {
        if ($value instanceof Closure) {
            return $value($this->value);
        }

        return $this->value === (string) $value;
    }

    public function toFloat (): float
    {
        return (float) $this->value();
    }

    public function toInt (): int
    {
        return (int) $this->value();
    }

    public function toBool (): bool
    {
        return (bool) $this->value;
    }

    /**
     * @return string|array
     * @throws \JsonException
     */
    public function jsonSerialize ()
    {
        if ($this->isJson) {
            return json_decode(
                $this->value,
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        }

        return $this->value();
    }

    public function __toString (): string
    {
        return $this->value;
    }
}
