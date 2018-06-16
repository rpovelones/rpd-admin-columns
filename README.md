# WordPress Admin Columns

An easy to use API for adding columns in the admin area.

## Setup
Simply specify your post type and the columns you want to add via the main `RPD_Admin_Columns` class. Then hook into `after_setup_theme` to register your columns.

```php
function add_admin_columns() {

  $my_post_type = new RPD_Admin_Columns( 'my_post_type', [
    [
      'key'      => 'my_custom_column_key', // a unique key for this column
      'title'    => 'my_column_title',      // the display title
      'callback' => 'my_column_callback',   // name of function to render the column contents
      'sortable' => 1,                      // true/false - should column be sortable?
      'sort_key' => 'my_column_meta_key'    // the meta_key for sorting
    ],
    [
      'key'      => 'my_custom_col_2',
      'title'    => 'col_2_title',
      'callback' => 'col_2_callback'
    ]
  ]);
  $my_post_type->register_columns();

}
add_action('after_setup_theme', __NAMESPACE__ . '\\add_admin_columns');
```

## Callbacks
The callback function is where you specify what actually gets output in the column. Each column can have a unique callback or share one with another column.

```php
function my_column_callback() {
  echo 'This is the content for my custom column!';
}
```