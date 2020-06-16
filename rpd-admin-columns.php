<?php
/**
 * RPD Admin Columns.
 * A nicer way of adding columns in the admin screen.
 * Initialize with a post type and an array of custom columns.
 *
 * @var string | $post_type
 * @var array | $cols_to_add
 */
class RPD_Admin_Columns {

  /**
   * The post type we are adding columns to.
   */
  public $post_type;

  /**
   * The columns we are adding.
   * This variable will be used throughout the class.
   */
  public $cols_to_add = [];

  /**
   * Any pre-existing columns to remove.
   */
  public $cols_to_unset = [];

  /**
   * Construct.
   */
  public function __construct( $post_type, $cols_to_add ) {
    $this->post_type = $post_type;
    $this->cols_to_add = $cols_to_add;
  }

  /**
   * Register our custom columns.
   */
  public function register_columns() {
    // add/remove columns
    add_filter("manage_{$this->post_type}_posts_columns", array( $this, "manage_columns" ), 5);
    // add content to custom columns
    add_action("manage_{$this->post_type}_posts_custom_column", array( $this, "render_columns" ), 5, 2);
    // make custom columns sortable
    add_action("manage_edit-{$this->post_type}_sortable_columns", array( $this, "make_columns_sortable" ), 5, 2);
    // add query order for custom sortable columns
    add_action("pre_get_posts", array( $this, "admin_query_ordering" ), 5, 2);
  }

  /**
   * Custom columns to add.
   *
   * @hooked manage_{$post_type}_posts_columns.
   */
  public function manage_columns( $cols ) {
    // add custom columns
    foreach ( $this->cols_to_add as $col ) {
      $key = $col['key'];
      $cols[$key] = $col['title'];
    }
    // remove columns (if necessary)
    foreach ( $this->cols_to_unset as $col ) {
      unset($cols[$col]);
    }
    return $cols;
  }

  /**
   * Render each of our custom column content.
   * This will run the callback for each column to render
   * the column's data.
   *
   * @hooked manage_{$post_type}_posts_custom_column.
   */
  public function render_columns( $col_key, $post_id ) {
    foreach ( $this->cols_to_add as $col ) {

      $func = $col['callback'];

      if ( $col_key === $col['key'] ) {
        call_user_func( $func, $post_id );
        break;
      }
    }
  }

  /**
   * Pushes columns to the $cols_to_unset[] class variable.
   *
   * @param array | $cols - an indexed array of column keys.
   */
  public function unset_columns( $cols ) {
    foreach ( $cols as $col ) {
      $this->cols_to_unset[] = $col;
    }
  }

  /**
   * Make custom columns sortable.
   * This simply enables sortability, but does not control the query.
   *
   * @hooked manage_edit-{$post_type}_sortable_columns.
   */
  public function make_columns_sortable( $cols ) {
    foreach ( $this->cols_to_add as $col ) {
      if ( isset($col['sortable']) && $col['sortable'] === 1 ) {
        $col_key = $col['key'];
        $cols[$col_key] = $col_key;
      }
    }
    return $cols;
  }

  /**
   * Pre get posts filter for sortable columns.
   * This is the logic that handles how the sorting actually works.
   *
   * @hooked pre_ge_posts.
   */
  public function admin_query_ordering( $query ) {

    if ( !is_admin() ) {
      return;
    }

    $orderby = $query->get('orderby');

    // $col['sort_key'] should be the meta_key you want to sort by.
    foreach ( $this->cols_to_add as $col ) {
      if ( $orderby === $col['key'] ) {
        $query->set('meta_key', $col['sort_key']);
        $query->set('orderby', 'meta_value');
      }
    }
  }

}
