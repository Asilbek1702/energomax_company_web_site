<?php
/**
 * Taxonomies registration.
 *
 * @package EnergomaxCore
 */

defined('ABSPATH') || exit;

/**
 * Register product_category taxonomy.
 */
function energomax_register_taxonomies(): void
{
    $labels = [
        'name'              => _x('Product Categories', 'taxonomy general name', 'energomax-core'),
        'singular_name'     => _x('Product Category', 'taxonomy singular name', 'energomax-core'),
        'search_items'      => __('Search Categories', 'energomax-core'),
        'all_items'         => __('All Categories', 'energomax-core'),
        'parent_item'       => __('Parent Category', 'energomax-core'),
        'parent_item_colon' => __('Parent Category:', 'energomax-core'),
        'edit_item'         => __('Edit Category', 'energomax-core'),
        'update_item'       => __('Update Category', 'energomax-core'),
        'add_new_item'      => __('Add New Category', 'energomax-core'),
        'new_item_name'     => __('New Category Name', 'energomax-core'),
        'menu_name'         => __('Categories', 'energomax-core'),
    ];

    register_taxonomy('product_category', ['energomax_product'], [
        'labels'            => $labels,
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'rewrite'           => ['slug' => 'product-category', 'with_front' => false],
    ]);
}
add_action('init', 'energomax_register_taxonomies');

/**
 * Seed default product category terms on activation.
 */
function energomax_seed_product_categories(): void
{
    $terms = [
        'transformers' => [
            'name' => __('Transformers', 'energomax-core'),
            'slug' => 'transformers',
        ],
        'ktp' => [
            'name' => __('KTP / Substations', 'energomax-core'),
            'slug' => 'ktp',
        ],
        'switchgear' => [
            'name' => __('Switchgear', 'energomax-core'),
            'slug' => 'switchgear',
        ],
        'led' => [
            'name' => __('LED Lighting', 'energomax-core'),
            'slug' => 'led',
        ],
    ];

    foreach ($terms as $slug => $data) {
        if (!term_exists($slug, 'product_category')) {
            wp_insert_term($data['name'], 'product_category', ['slug' => $data['slug']]);
        }
    }
}
