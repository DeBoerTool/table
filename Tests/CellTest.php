<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Dbt\Table\Tests;

use Dbt\Table\Cell;

class CellTest extends UnitTestCase
{
    /** @test */
    public function getting_a_string_value (): void
    {
        $str = $this->rs(16);
        $cell = new Cell($str);

        $this->assertSame($str, $cell->value());
    }

    /** @test */
    public function equality (): void
    {
        $str = $this->rs(16);
        $cell = new Cell($str);

        $this->assertFalse($cell->equals($this->rs(16)));
        $this->assertTrue($cell->equals($cell->value()));
        $this->assertTrue($cell->equals($cell));
    }

    /** @test */
    public function getting_a_json_value (): void
    {
        $arr = [$this->rs(4) => $this->rs(16)];
        $str = json_encode($arr);
        $cell = new Cell($str, true);

        $this->assertSame($str, $cell->value());
        $this->assertSame($arr, json_decode($cell->value(), true));
    }

    /** @test */
    public function serializing_strings_to_json (): void
    {
        $str = $this->rs(16);
        $cell = new Cell($str);

        $this->assertSame(
            sprintf('"%s"', $str),
            json_encode($cell)
        );
    }

    /** @test */
    public function serializing_json_to_json (): void
    {
        $arr = [$this->rs(4) => $this->rs(16)];
        $str = json_encode($arr);
        $cell = new Cell($str, true);

        $this->assertSame($str, json_encode($cell));
    }

    /** @test */
    public function casting_to_string (): void
    {
        $str = $this->rs(16);
        $cell = new Cell($str);

        $this->assertSame($str, (string) $cell);
    }

    /** @test */
    public function cloning (): void
    {
        $cell = new Cell('');
        $cloned = clone $cell;

        $this->assertSame($cell->value(), $cloned->value());
        $this->assertNotSame($cell, $cloned);
    }

    /** @test */
    public function making_from_scalar (): void
    {
        $cell = Cell::make(10);

        $this->assertSame('10', $cell->value());
    }

    /** @test */
    public function making_from_array (): void
    {
        $arr = [$this->rs(10), $this->rs(10)];
        $cell = Cell::make($arr);

        $this->assertTrue($cell->isJson());
        $this->assertSame(json_encode($arr), $cell->value());
    }
}
