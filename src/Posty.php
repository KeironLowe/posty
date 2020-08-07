<?php

namespace Posty;

use Posty\Traits\Values;
use Posty\Columns\ColumnRepository;

class Posty
{
    use Values;

    /**
     * @var string
     */
    private string $pluralName;

    /**
     * @var string
     */
    private string $singularName;

    /**
     * @var string
     */
    private string $postType;

    /**
     * @var string[]
     */
    private array $labels;

    /**
     * @var mixed[]
     */
    private array $arguments;

    /**
     * @var \Posty\Columns\ColumnRepository
     */
    private ColumnRepository $columns;

    /**
     * Creates a new instance of PostType.
     *
     * @param string      $singular
     * @param string      $plural
     * @param string|null $type
     */
    public function __construct(string $singular, string $plural, string $type = null)
    {
        $this->singularName = $singular;
        $this->pluralName = $plural;
        $this->postType = $type ?? sanitize_title($plural);
        $this->labels = $this->getDefaultLabels();
        $this->arguments = $this->getDefaultArguments();
        $this->columns = new ColumnRepository();
    }

    /**
     * Returns the labels
     *
     * @return string[]
     */
    public function getLabels(): array
    {
        return $this->labels;
    }

    /**
     * Sets the labels.
     *
     * @param string[]|\Closure $value
     * @return $this
     */
    public function setLabels($value): self
    {
        $this->labels = $this->getValueFromArrayOrClosure($value, [$this->getLabels()]);

        return $this;
    }

    /**
     * Returns the arguments
     *
     * @return mixed[]
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Sets the arguments.
     *
     * @param string[]|\Closure $value
     * @return $this
     */
    public function setArguments($value): self
    {
        $this->arguments = $this->getValueFromArrayOrClosure($value, [$this->getArguments()]);

        return $this;
    }

    /**
     * Returns the column repository.
     *
     * @return \Posty\Columns\ColumnRepository
     */
    public function columns(): ColumnRepository
    {
        return $this->columns;
    }

    /**
     * Returns the default labels.
     *
     * @return string[]
     */
    protected function getDefaultLabels(): array
    {
        return [
            'name'                  => $this->pluralName,
            'singular_name'         => $this->singularName,
            'add_new'               => 'Add New',
            'add_new_item'          => 'Add New '   . $this->singularName,
            'edit_item'             => 'Edit '      . $this->singularName,
            'new_item'              => 'New '       . $this->singularName,
            'all_items'             => 'All '       . $this->pluralName,
            'view_item'             => 'View '      . $this->pluralName,
            'search_items'          => 'Search '    . $this->pluralName,
            'not_found'             => 'No '        . strtolower($this->pluralName) . ' found',
            'not_found_in_trash'    => 'No '        . strtolower($this->pluralName) . ' found in Trash',
            'parent_item_colon'     => '',
            'menu_name'             => $this->pluralName
        ];
    }

    /**
     * Returns the default arguments.
     *
     * @return mixed[]
     */
    protected function getDefaultArguments(): array
    {
        return [
            'labels'      => $this->labels,
            'public'      => true,
            'rewrite'     => ['slug' => $this->postType],
            'has_archive' => true,
            'supports'    => ['title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions'],
        ];
    }
}