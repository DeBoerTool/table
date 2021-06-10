# Table

A set of objects to represent tabular data with fixed-width rows. Values are represented with strings.

## Installation

You can install the package via composer:

```bash
composer require dbt/table
```

## Usage

Create a new table:

```php
use Dbt\Table\Table;
use Dbt\Table\Row;
use Dbt\Table\Cell;

/**
 * Standard construction.
 */
$table = new Table(
    new Row(
        new Cell('row 0 / cell 0'),
        new Cell('row 0 / cell 1')
    ),
    new Row(
        new Cell('row 1 / cell 0'),
        new Cell('row 1 / cell 1')
    )
);

/**
 * From array. 
 */
$table = Table::fromArray([
    ['row 0 / cell 0', 'row 0 / cell 1'],
    ['row 1 / cell 0', 'row 1 / cell 1'],
]);

/*
 * Rows must have the same length. This will throw a LengthException:
 */
$table = Table::fromArray([
    ['row 0, cell 0', 'row 0, cell 1'],
    ['row 1, cell 0'], // Too short!
]);
``` 

Tables and rows are list objects that can be iterated over and pushed to:

```php
use Dbt\Table\Table;
use Dbt\Table\Row;
use Dbt\Table\Cell;

$table = Table::fromArray([['row 0 / cell 0'], ['row 1 / cell 0']]);

/**
 * @var int $rowIndex
 * @var \Dbt\Table\Row $row 
 */
foreach ($table as $rowIndex => $row) {
    var_dump(count($table), count($row));
    
    /**
     * @var int $cellIndex
     * @var \Dbt\Table\Cell $cell 
     */
    foreach ($row as $cellIndex => $cell) {
        var_dump($cell->value());
    }
}

$table->push(new Row(new Cell('row 2 / cell 0')));
```

Cells can be created from scalars or arrays of scalars:

```php
use \Dbt\Table\Cell;

/**
 * This int will be cast to a string. 
 */
$cell = Cell::make(1);

/**
 * This array will be serialized to a JSON string. 
 */
$cell = Cell::make(['string', 1, 9.9, true]);
```

Tables, Rows, and Cells are not intended to be modified after being set. Though you can push to a Table and a Row, you cannot modify existing values.

## Etc.

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
