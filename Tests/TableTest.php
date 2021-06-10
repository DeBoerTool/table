<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Dbt\Table\Tests;

use Dbt\Table\Cell;
use Dbt\Table\Row;
use Dbt\Table\Table;
use Exception;
use LengthException;

class TableTest extends UnitTestCase
{
    /** @test */
    public function a_table_is_a_list_of_rows (): void
    {
        $rows = [new Row(new Cell('')), new Row(new Cell(''))];
        $table = new Table(...$rows);

        $this->assertCount(2, $table);
        $this->assertCount(2, $table->all());
        $this->assertInstanceOf(Row::class, $table->row(0));
    }

    /** @test */
    public function the_first_row_defines_the_expected_length (): void
    {
        $this->expectException(LengthException::class);

        $rows = [
            new Row(new Cell('')),
            new Row(new Cell(''), new Cell(''))
        ];

        new Table(...$rows);
    }

    /** @test */
    public function rows_are_cloned_when_pushed_onto_the_stack (): void
    {
        $cell = new Cell('');
        $row = new Row($cell);

        $table = new Table($row);

        $table->push($row);

        $this->assertCount(2, $table);
        $this->assertNotSame($table[0], $table[1]);
        $this->assertNotSame($table[0][0], $table[1][0]);
        $this->assertNotSame($table->get(0), $table->get(1));
        $this->assertNotSame($table->get(0)->get(0), $table->get(1)->get(0));
        $this->assertSame($table[0], $table->get(0));
    }

    /** @test */
    public function making_from_array (): void
    {
        $values = [[$this->rs(16)], [$this->rs(16)]];

        $table = Table::fromArray($values);

        foreach ($table as $index => $row) {
            $this->assertSame($table[$index][0]->value(), $values[$index][0]);
        }
    }

    /** @test */
    public function the_headers_are_the_first_row (): void
    {
        $cell = new Cell('');
        $row = new Row($cell);

        $table = new Table($row);

        $this->assertSame($table[0], $table->headers());
    }

    /** @test */
    public function getting_rows_without_headers_as_a_new_table (): void
    {
        $fullTable = Table::fromArray([
            ['row 0 / cell 0'],
            ['row 1 / cell 0'],
            ['row 2 / cell 0'],
        ]);

        $this->assertCount(3, $fullTable);

        $table = $fullTable->exceptHeaders();

        $this->assertCount(2, $table);
        $this->assertSame(
            $table->row(0)->cell(0)->value(),
            'row 1 / cell 0'
        );
        $this->assertSame(
            $table->row(1)->cell(0)->value(),
            'row 2 / cell 0'
        );
    }

    /** @test */
    public function json_serialize (): void
    {
        $str = $this->rs(10);
        $cell = new Cell($str);
        $row = new Row($cell);
        $table = new Table($row);

        $this->assertSame($table->all(), $table->jsonSerialize());
        $this->assertSame(
            sprintf('[["%s"]]', $str),
            json_encode($table),
        );
    }

    /** @test */
    public function cloning_a_table_clones_its_rows (): void
    {
        $cell = new Cell($this->rs(16));
        $row = new Row($cell, $cell);

        $original = new Table($row, $row);
        $cloned = clone $original;

        foreach ($original as $index => $row) {
            $this->assertNotSame($original[$index], $cloned[$index]);
        }
    }

    /** @test */
    public function offset_exists (): void
    {
        $this->assertFalse(isset((new Table())[0]));
    }

    /** @test */
    public function offset_get (): void
    {
        $table = new Table(new Row(new Cell('')));

        $this->assertSame($table[0], $table->get(0));
    }

    /** @test */
    public function attempting_to_set_an_index (): void
    {
        $this->expectException(Exception::class);

        $table = new Table();

        $table[0] = new Row();
    }

    /** @test */
    public function attempting_to_unset_an_index (): void
    {
        $this->expectException(Exception::class);

        $table = new Table();

        unset($table[0]);
    }
}
