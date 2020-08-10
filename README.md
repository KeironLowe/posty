# Posty
Posty is an object orientated post type manager for WordPress. It makes it a breeze to manage your post types and any custom columns.

## Roadmap
- [x] Post Type
- [x] Columns
- [ ] Sortable Columns
- [ ] Statuses
- [ ] Tags/Taxonomies

## Installation
Posty has no dependencies, and requires PHP >= 7.4.

```
composer require keironlowe/posty
```

## Usage
Posty provides a fluent API for managing both your post types and columns. To get started, just use the `make` method, providing the singular and plural names, to create a new post type. It's important to note that **the register method must always be called last**. Any changes made after the `register` method won't take effect. 
```
Posty\Posty::make('Product', 'Products')->register();
```
This post type slug/ID will be automatically generated based on the singular name, so in this case it would be `product`. Optionally, you can pass a third argument to define this yourself.

### Setting labels and arguments
Posty handles setting up all the labels, along with some sensible default arguments, but we know that one size doesn't fit all, so you can update these using the `setLabels` and `setArguments` methods.
```
Posty\Posty::make('Product', 'Products')
    ->setLabels()
    ->setArguments()
    ->register();
```
Both `setLabels` and `setArguments` should receive an array, this can either by passed directly, or as a result of a callback function.
```
Posty\Posty::make('Product', 'Products')
    ->setLabels([
        // All labels
    ])
    ->register();
```
```
Posty\Posty::make('Product', 'Products')
    ->setLabels(function ($labels) {
        $labels['menu_name'] = 'Overwrite value'

        return $labels;
    })
    ->register();
```

### Columns
To manage the columns, we first need to grab the `ColumnRepository` instance using the `columns` method. This class has the `add`, `remove` and `reorder` methods. Each of these methods should receive an array, this can either by passed directly, or as a result of a callback function.
```
$products = Posty\Posty::make('Product', 'Products');
$columns  = $products->columns();
```

#### Adding Columns
The `add` method should receive an array of columns. Each column should be an array of key => value pairs, with two required elements, `label` and `value`

The `label` is the label for the column, and the `value` should be a function which takes the ID of the post, and returns the correct value. Optionally, there is also the `order` element, which should be an integer and allows you to reorder the column.

The ID of the field is automatically generated from the label, but in the case you need to manually set this, you can use the `id` element.
```
$columns->add([
    [
        'label' => 'Price'
        'value' => fn (int $post_id) => get_field('price', $post_id)
        'order' => 2
    ],
    [
        'label' => 'Image'
        'value' => fn (int $post_id) => get_field('image', $post_id)
        'order' => 3,
        'id'    => 'alternate_image'
    ]
]);

$columns->add(function (array $existingColumns) {
    // Return column array
});
```

#### Removing Columns
The `remove` method should receive an array of column IDs to be removed. By default, custom post types have `cb` (checkbox), `title`, `author` and `date` columns which you can remove if neccessary.
```
$columns->remove(['author', 'date']);
```

#### Reordering Columns
The `reorder` method should receive an array of column IDs in the order that you wish. By default, custom post types have `cb` (checkbox), `title`, `author` and `date` columns which you should bear in mind when reordering. Any columns that aren't included in the array will be added at the end.
```
$columns->reorder(['cb', 'title', 'price', 'image']);
``` 