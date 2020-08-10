<?php

namespace Posty;

use RuntimeException;
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
     * @var \Posty\Columns\ColumnRepository|null
     */
    private ?ColumnRepository $columns;

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
        $this->postType = $type ?? sanitize_title($singular);
        $this->labels = $this->getDefaultLabels();
        $this->arguments = $this->getDefaultArguments();
    }

    /**
     * Returns a new instance of Posty.
     *
     * @param string      $singular
     * @param string      $plural
     * @param string|null $type
     * @return \Posty\Posty
     */
    public static function make(string $singular, string $plural, string $type = null): Posty
    {
        return new static($singular, $plural, $type);
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
        $labels = $this->getValueFromArrayOrClosure($value, $this->getLabels());

        if(!$labels) {
            throw new RuntimeException('Invalid label data');
        }

        $this->labels = array_merge($this->labels, $labels);

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
        $arguments = $this->getValueFromArrayOrClosure($value, $this->getArguments());

        if(!$arguments) {
            throw new RuntimeException('Invalid argument data');
        }

        $this->arguments = array_merge($this->arguments, $arguments);

        return $this;
    }

    /**
     * Registers the custom post type, plus any columns.
     *
     * @return void
     */
    public function register(): void
    {
        add_action('init', function () {

            // Register the post type.
            register_post_type($this->postType, $this->arguments);

            // Add the columns
            if(isset($this->columns)) {
                $this->columns->register();
            }
        });
    }

    /**
     * Returns the column repository.
     *
     * @param \Posty\Columns\ColumnRepository|null $columns
     * @return \Posty\Columns\ColumnRepository
     */
    public function columns(ColumnRepository $columns = null): ColumnRepository
    {
        if(!isset($this->columns)) {
            $this->columns = $columns ?? new ColumnRepository($this->postType);
        }

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
            'rewrite'     => ['slug' => sanitize_title($this->pluralName)],
            'has_archive' => true,
            'supports'    => ['title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions'],
        ];
    }
}