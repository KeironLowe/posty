<?php

namespace Posty\Columns;

use Closure;

class Column
{
    private string $label;
    private ?Closure $value;
    private ?int $order;
    private string $id;
    private ?string $sort = null;

    /**
     * Creates a new instance of Column.
     *
     * @param string        $label
     * @param \Closure|null $value
     * @param int|null      $order
     * @param string|null   $id
     */
    public function __construct(string $label, Closure $value = null, int $order = null, string $id = null)
    {
        $this->label = $label;
        $this->value = $value;
        $this->order  = $order;
        $this->id = $id ?? sanitize_title($label);
    }

    /**
     * Takes an array of column data and creates a new instance.
     *
     * @param array<mixed> $column
     * @return \Posty\Columns\Column
     */
    public static function fromArray(array $column): self
    {
        if(!isset($column['label'], $column['value'])) {
            throw new \RuntimeException('Column missing required data.');
        }

        $instance = new Column($column['label'], $column['value'], $column['order'] ?? null, $column['id'] ?? null);

        if (isset($column['sort'])) {

            if($column['sort'] === 'alphabetically') {
                $instance->sortAlphabetically();
            }

            if($column['sort'] === 'numerically') {
                $instance->sortNumerically();
            }
        }

        return $instance;
    }

    /**
     * Returns the column order.
     *
     * @return int|null
     */
    public function getOrder(): ?int
    {
        return $this->order;
    }

    /**
     * Sets the column order.
     *
     * @param int $index
     * @return $this
     */
    public function setOrder(int $index): self
    {
        $this->order = $index;

        return $this;
    }

    /**
     * Returns the column label.
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Sets the column label.
     *
     * @param string $label
     * @return \Posty\Columns\Column
     */
    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Returns the column value.
     *
     * @param int|null $postId
     * @return string|null
     */
    public function getValue(int $postId = null): ?string
    {
        return ($this->value)($postId);
    }

    /**
     * Sets the column value.
     *
     * @param \Closure $value
     * @return \Posty\Columns\Column
     */
    public function setValue(Closure $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Returns the column ID.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Sets the column value.
     *
     * @param string $id
     * @return \Posty\Columns\Column
     */
    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Sets both the label and the id.
     *
     * @param string      $label
     * @param string|null $id
     * @return $this
     */
    public function setLabelAndId(string $label, string $id = null): self
    {
        $this->label = $label;
        $this->id    = $id ?? sanitize_title($label);

        return $this;
    }

    /**
     * Returns the sorting type for this column.
     *
     * @return string|null
     */
    public function getSortType(): ?string
    {
        return $this->sort;
    }

    /**
     * Enables numeric sorting for the column
     *
     * @return $this
     */
    public function sortNumerically(): self
    {
        $this->sort = 'numeric';

        return $this;
    }

    /**
     * Enables alphabetical sorting for the column
     *
     * @return $this
     */
    public function sortAlphabetically(): self
    {
        $this->sort = 'alphabetically';

        return $this;
    }
}