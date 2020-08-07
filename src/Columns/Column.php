<?php

namespace Posty\Columns;

use Closure;

class Column
{

    /**
     * @var string
     */
    private string $label;

    /**
     * @var \Closure|null
     */
    private ?Closure $value;

    /**
     * @var int|null
     */
    private ?int $order;

    /**
     * @var string
     */
    private string $id;

    /**
     * @var bool
     */
    private bool $isADefaultField = false;

    /**
     * Creates a new instance of Column.
     *
     * @param string        $label
     * @param \Closure|null $value
     * @param int|null      $order
     * @param string|null   $id
     */
    public function __construct(string $label, Closure  $value = null, int $order = null, string $id = null)
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

        return new Column($column['label'], $column['value'], $column['order'] ?? null, $column['id'] ?? null);
    }

    /**
     * Set's this field as being default, meaning it's been added by WP.
     *
     * @return $this
     */
    public function setAsADefaultField(): self
    {
        $this->isADefaultField = true;

        return $this;
    }

    /**
     * Returns true if this field has been added by WP.
     *
     * @return bool
     */
    public function isADefaultField(): bool
    {
        return $this->isADefaultField;
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
     * Returns the column value.
     *
     * @return string
     */
    public function getValue(): string
    {
        return ($this->value)();
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
}