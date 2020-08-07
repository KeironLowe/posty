<?php

namespace Posty\Columns;

use Posty\Support\Arr;
use Posty\Traits\Values;
use Posty\Support\Repository;

class ColumnRepository extends Repository
{
    use Values;

    /**
     * Creates a new instance of ColumnRepository.
     */
    public function __construct()
    {
        $this->items = $this->getDefaultColumns();
    }

    /**
     * Returns all the columns.
     *
     * @return Column[]
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Adds a new column.
     *
     * @param mixed[]|\Closure $columns
     * @return $this
     */
    public function add($columns): self
    {
        $columns = $this->getValueFromArrayOrClosure($columns);

        if(!is_array($columns)) {
            return $this;
        }

        foreach($columns as $columnData) {
            $column = Column::fromArray($columnData);

            if(!is_null($column->getOrder())) {
                $this->items = Arr::insert($column, $column->getOrder(), $this->items);
            }

            if(is_null($column->getOrder())) {
                $this->items[] = $column;
            }
        }

        return $this;
    }

    /**
     * Removes a column.
     *
     * @param string[]|\Closure $columns
     * @return $this
     */
    public function remove($columns): self
    {
        $columns = $this->getValueFromArrayOrClosure($columns);

        foreach($columns as $columnId) {
            $columnIndex = Arr::getIndexWhere(fn (Column $column) => $column->getId() === $columnId, $this->all());
            if($columnIndex) {
                unset($this->items[$columnIndex]);
            }
        }

        return $this;
    }

    /**
     * Reorders the columns to match the given array
     *
     * @param string[]|\Closure $columns
     * @return $this
     */
    public function reorder($columns): self
    {
        $columns = $this->getValueFromArrayOrClosure($columns);

        return $this;
    }

    /**
     * Returns the default columns from the overview screen.
     *
     * @TODO Find a way to return the real values, rather than hardcode it.
     *
     * @return array
     */
    protected function getDefaultColumns(): array
    {
        $defaultColumns = [
            'cb' => '<input type="checkbox" />',
            'title' => "Title",
            'author' => "Author",
            'date' => "Date"
        ];

        return array_map(static function ($value, $index) {
           return (new Column($value, null, null, $index))->setAsADefaultField();
        }, $defaultColumns, array_keys($defaultColumns));
    }
}