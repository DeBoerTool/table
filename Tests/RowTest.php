<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Dbt\Table\Tests;

use Dbt\Table\Cell;
use Dbt\Table\Row;
use Exception;

class RowTest extends UnitTestCase
{
    /** @test */
    public function a_row_is_a_list_of_cells (): void
    {
        $cells = [new Cell($this->rs(10)), new Cell($this->rs(10))];
        $row = new Row(...$cells);

        $this->assertCount(2, $row);
        $this->assertCount(2, $row->all());
    }

    /** @test */
    public function cells_are_cloned_when_pushed_onto_the_stack (): void
    {
        $cell = new Cell('');
        $row = new Row();

        $row->push($cell);
        $row->push($cell);

        $this->assertCount(2, $row);
        $this->assertNotSame($row[0], $row[1]);
        $this->assertNotSame($row->cell(0), $row->cell(1));
        $this->assertSame($row[0], $row->cell(0));
    }

    /** @test */
    public function making_from_array (): void
    {
        $values = [$this->rs(16), $this->rs(16)];

        $row = Row::fromArray($values);

        foreach ($row as $index => $cell) {
            $this->assertSame($cell->value(), $values[$index]);
        }
    }

    /** @test */
    public function casting_to_an_array (): void
    {
        $cells = ['test', ['an array']];

        $row = Row::fromArray($cells);

        $this->assertSame($cells, $row->toArray());
    }

    /** @test */
    public function json_serialize (): void
    {
        $str = $this->rs(10);
        $cell = new Cell($str);
        $row = new Row($cell);

        $this->assertSame($row->all(), $row->jsonSerialize());
        $this->assertSame(
            sprintf('["%s"]', $str),
            json_encode($row),
        );
    }

    /** @test */
    public function iterating (): void
    {
        $cells = [new Cell($this->rs(16)), new Cell($this->rs(16))];
        $row = new Row(...$cells);

        foreach ($row as $index => $cell) {
            $this->assertSame($cells[$index]->value(), $cell->value());
        }
    }

    /** @test */
    public function cloning_a_row_clones_its_cells (): void
    {
        $cells = [new Cell($this->rs(16)), new Cell($this->rs(16))];
        $row = new Row(...$cells);
        $cloned = clone $row;

        $this->assertNotSame($row, $cloned);

        foreach ($row as $index => $cell) {
            $this->assertNotSame($cell, $cloned[$index]);
        }
    }

    /** @test */
    public function offset_exists (): void
    {
        $this->assertFalse(isset((new Row())[0]));
    }

    /** @test */
    public function offset_get (): void
    {
        $cell = new Cell('');
        $row = new Row($cell);

        $this->assertSame($row[0]->value(), $cell->value());
    }

    /** @test */
    public function attempting_to_set_an_index (): void
    {
        $this->expectException(Exception::class);

        $row = new Row();

        $row[0] = new Cell('');
    }

    /** @test */
    public function attempting_to_unset_an_index (): void
    {
        $this->expectException(Exception::class);

        $row = new Row();

        unset($row[0]);
    }
}
