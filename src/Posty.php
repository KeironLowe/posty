<?php

namespace App\Entities;

use Closure;

class Posty
{

    /**
     * @var string The name of the post type.
     */
    protected string $name;

    /**
     * @var string The singular name of the post type.
     */
    protected string $singularName;

    /**
     * @var string The plural name of the post type
     */
    protected string $pluralName;

    /**
     * @var array<string> The post type labels
     */
    protected array $labels;

    /**
     * @var array<mixed> The post type arguments.
     */
    protected array $arguments;

    /**
     * Creates a new instance of PostType.
     *
     * @param string $name
     * @param string $singular
     * @param string $plural
     */
    public function __construct(string $name, string $singular, string $plural)
    {
        $this->name = $name;
        $this->singularName = $singular;
        $this->pluralName = $plural;
        $this->labels = $this->getDefaultLabels();
        $this->arguments = $this->getDefaultArguments();
    }

    /**
     * Creates a new instance of PostType.
     *
     * @param string $name
     * @param string $singular
     * @param string $plural
     * @return static
     */
    public static function make(string $name, string $singular, string $plural): self
    {
        return new static($name, $singular, $plural);
    }

    /**
     * Registers the custom post type.
     *
     * @return $this
     */
    public function register(): self
    {
        add_action('init', function () {
            register_post_type($this->name, $this->arguments);
        });

        return $this;
    }

    /**
     * Sets the post type labels.
     *
     * @param array<string> $labels
     * @return static
     */
    public function setLabels(array $labels): self
    {
        $this->labels = array_merge($this->getDefaultLabels(), $labels);

        return $this;
    }

    /**
     * Sets the arguments for the post type.
     *
     * @param array<mixed> $arguments
     * @return $this
     */
    public function setArguments(array $arguments): self
    {
        $this->arguments = array_merge($this->getDefaultArguments(), $arguments);

        return $this;
    }

    /**
     * Removes the given column from the admin screen.
     *
     * @param string $columnToRemove
     * @return $this
     */
    public function removeColumn(string $columnToRemove): self
    {
        add_filter(
            'manage_' . $this->name . '_posts_columns',
            static function (array $existingColumns) use ($columnToRemove) {
                unset($existingColumns[$columnToRemove]);
                return $existingColumns;
            }
        );

        return $this;
    }

    /**
     * Removes the given columns from the admin screen.
     *
     * @param array<string> $columnsToRemove
     * @return $this
     */
    public function removeColumns(array $columnsToRemove): self
    {
        foreach($columnsToRemove as $columnToRemove) {
            $this->removeColumn($columnToRemove);
        }

        return $this;
    }

    /**
     * Reorders the goven columns for the admin screen.
     *
     * @param array<string>|Closure $columnOrder
     * @return $this
     */
    public function reorderColumns($columnOrder): self
    {
        add_filter(
            'manage_' . $this->name . '_posts_columns',
            static function (array $existingColumns) use ($columnOrder) {

                if(is_array($columnOrder)) {
                    return array_merge(array_flip($columnOrder), $existingColumns);
                }

                if($columnOrder instanceof Closure) {
                    return array_merge(array_flip($columnOrder(array_keys($existingColumns))), $existingColumns);
                }

                return $existingColumns;
            }
        );

        return $this;
    }

    /**
     * Adds the given column to the admin screen.
     *
     * @param string   $name
     * @param \Closure $callback
     * @param int|null $order
     * @return $this
     */
    public function addColumn(string $name, Closure $callback, int $order = null): self
    {
        $label = sanitize_title($name);

        // First, add the column
        add_filter('manage_' . $this->name . '_posts_columns', static function (array $columns) use ($name, $label) {
            $columns[$label] = $name;
            return $columns;
        });

        // Then populate it
        add_action(
            'manage_' . $this->name . '_posts_custom_column',
            static function (string $column, int $postId) use ($label, $callback) {
                if($column === $label) {
                    echo $callback($postId);
                }
            },
            10,
            2
        );

        // And finally, reorder it
        if($order) {
            $this->reorderColumns(static function ($columns) use ($label, $order) {
                $labelIndex = array_search($label, $columns, true);
                $out = array_splice($columns, $labelIndex, 1);
                array_splice($columns, $order, 0, $out);
                return $columns;
            });
        }

        return $this;
    }

    /**
     * Adds the given columns to the admin screen.
     *
     * @param array<string> $columnsToAdd
     * @return $this
     */
    public function addColumns(array $columnsToAdd): self
    {
        foreach($columnsToAdd as $columnToAdd) {
            if(!isset($columnToAdd['name'], $columnToAdd['callback'])) {
                break;
            }
            $this->addColumn($columnToAdd['name'], $columnToAdd['callback'], $columnToAdd['order']);
        }

        return $this;
    }

    /**
     * Returns the default labels.
     *
     * @return array<string>
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
     * @return array<mixed>
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