<?php

namespace Posty\Columns;

use Posty\Support\Arr;
use Posty\Traits\Values;
use Posty\Support\Repository;
use RuntimeException;

class ColumnRepository extends Repository
{
    use Values;

    private string $postType;

    /**
     * Creates a new instance of ColumnRepository.
     *
     * @param string $postType
     */
    public function __construct(string $postType)
    {
        $this->postType = $postType;
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
        $columnData = $this->getValueFromArrayOrClosure($columns);

        if(!is_array($columnData)) {
            throw new RuntimeException('Invalid column data');
        }

        foreach($columnData as $column) {
            $column = Column::fromArray($column);

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
     * Remove columns.
     *
     * @param string[]|\Closure $columns
     * @return $this
     */
    public function remove($columns): self
    {
        $columnData = $this->getValueFromArrayOrClosure($columns, $this->getColumnIds());

        foreach($columnData as $columnId) {
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
        $columnData = $this->getValueFromArrayOrClosure($columns, $this->getColumnIds());

        usort($this->items, static function (Column $a, Column $b) use ($columnData) {
            $aIndex = array_search($a->getId(), $columnData, true);
            $bIndex = array_search($b->getId(), $columnData, true);

            return $aIndex > $bIndex;
        });

        return $this;
    }

    /**
     * Registers the columns.
     *
     * @return void
     */
    public function register(): void
    {
        // Add the columns
        add_filter('manage_' . $this->postType . '_posts_columns', function () {
            return $this->getHeadings();
        });

        // Add the column values
        add_action('manage_' . $this->postType . '_posts_custom_column', function ($column, $post_id) {
            echo $this->find($column)->getValue($post_id);
        }, 10, 2);

        // Add them to list of sortable columns
        add_action('manage_edit-' . $this->postType . '_sortable_columns', function (array $columns) {
            return $this->getSortableColumnsIds($columns);
        });

        // Implement the sorting
        add_action('pre_get_posts', function ($query) {
            foreach($this->getSortableColumns() as $column) {

                if(!is_admin() || !$query->is_main_query()) {
                    return;
                }

                $columnId = $column->getId();
                if ($columnId === $query->get('orderby') ) {
                    $query->set('orderby', 'meta_value');
                    $query->set('meta_key', $columnId);

                    if($column->getSortType() === 'numeric') {
                        $query->set('meta_type', $column->getSortType());
                    }
                }
            }
        });
    }

    /**
     * Returns an array of column headings.
     *
     * @return array
     */
    protected function getHeadings(): array
    {
        $columns = [];

        foreach($this->items as $column) {
            $columns[$column->getId()] = $column->getLabel();
        }

        return $columns;
    }

    /**
     * Returns the column with the given id
     *
     * @param string $id
     * @return \Posty\Columns\Column
     */
    protected function find(string $id): Column
    {
        $column = Arr::findWhere(static function (Column $column) use ($id) {
            return $column->getId() === $id;
        }, $this->items);

        if(!$column) {
            throw new RuntimeException('Column instance with the ID of "' . $id . '" not found');
        }

        return $column;
    }

    /**
     * Returns an array of the column IDs
     *
     * @return array
     */
    protected function getColumnIds(): array
    {
        return array_map(static function (Column $column) {
            return $column->getId();
        }, $this->items);
    }

    /**
     * Returns an array of columns which are sortable.
     *
     * @return Column[]
     */
    protected function getSortableColumns(): array
    {
        return array_filter($this->all(), fn (Column $column) => !is_null($column->getSortType()));
    }

    /**
     * Returns an array of columns which are sortable.
     *
     * @param array $existingColumns
     * @return Column[]
     */
    protected function getSortableColumnsIds(array $existingColumns = []): array
    {
        $customSortableColumns = $this->getSortableColumns();

        foreach($customSortableColumns as $column) {
            $columnId = $column->getId();
            $existingColumns[$columnId] = $columnId;
        }

        return $existingColumns;
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
           return (new Column($value, null, null, $index));
        }, $defaultColumns, array_keys($defaultColumns));
    }
}