<?php
/**
 * Custom Post Types registration.
 *
 * @package EnergomaxCore
 */

defined('ABSPATH') || exit;

/**
 * Register Energomax custom post types.
 */
function energomax_register_post_types(): void
{
    $product_labels = [
        'name'                  => _x('Products', 'Post type general name', 'energomax-core'),
        'singular_name'         => _x('Product', 'Post type singular name', 'energomax-core'),
        'menu_name'             => _x('Products', 'Admin Menu text', 'energomax-core'),
        'name_admin_bar'        => _x('Product', 'Add New on Toolbar', 'energomax-core'),
        'add_new'               => __('Add New', 'energomax-core'),
        'add_new_item'          => __('Add New Product', 'energomax-core'),
        'new_item'              => __('New Product', 'energomax-core'),
        'edit_item'             => __('Edit Product', 'energomax-core'),
        'view_item'             => __('View Product', 'energomax-core'),
        'all_items'             => __('All Products', 'energomax-core'),
        'search_items'          => __('Search Products', 'energomax-core'),
        'parent_item_colon'     => __('Parent Products:', 'energomax-core'),
        'not_found'             => __('No products found.', 'energomax-core'),
        'not_found_in_trash'    => __('No products found in Trash.', 'energomax-core'),
        'archives'              => _x('Product archives', 'The post type archive label', 'energomax-core'),
        'insert_into_item'      => _x('Insert into product', 'Overrides the "Insert into post" phrase', 'energomax-core'),
        'uploaded_to_this_item' => _x('Uploaded to this product', 'Overrides the "Uploaded to this post" phrase', 'energomax-core'),
        'filter_items_list'     => _x('Filter products list', 'Screen reader text', 'energomax-core'),
        'items_list_navigation' => _x('Products list navigation', 'Screen reader text', 'energomax-core'),
        'items_list'            => _x('Products list', 'Screen reader text', 'energomax-core'),
    ];

    register_post_type('energomax_product', [
        'labels'              => $product_labels,
        'public'              => true,
        'has_archive'         => true,
        'rewrite'             => ['slug' => 'products', 'with_front' => false],
        'menu_icon'           => 'dashicons-bolt',
        'menu_position'       => 5,
        'supports'            => ['title', 'thumbnail', 'editor', 'excerpt'],
        'show_in_rest'        => true,
        'capability_type'     => 'post',
        'hierarchical'        => false,
        'exclude_from_search' => false,
    ]);

    $project_labels = [
        'name'                  => _x('Projects', 'Post type general name', 'energomax-core'),
        'singular_name'         => _x('Project', 'Post type singular name', 'energomax-core'),
        'menu_name'             => _x('Projects', 'Admin Menu text', 'energomax-core'),
        'name_admin_bar'        => _x('Project', 'Add New on Toolbar', 'energomax-core'),
        'add_new'               => __('Add New', 'energomax-core'),
        'add_new_item'          => __('Add New Project', 'energomax-core'),
        'new_item'              => __('New Project', 'energomax-core'),
        'edit_item'             => __('Edit Project', 'energomax-core'),
        'view_item'             => __('View Project', 'energomax-core'),
        'all_items'             => __('All Projects', 'energomax-core'),
        'search_items'          => __('Search Projects', 'energomax-core'),
        'not_found'             => __('No projects found.', 'energomax-core'),
        'not_found_in_trash'    => __('No projects found in Trash.', 'energomax-core'),
        'archives'              => _x('Project archives', 'The post type archive label', 'energomax-core'),
        'filter_items_list'     => _x('Filter projects list', 'Screen reader text', 'energomax-core'),
        'items_list_navigation' => _x('Projects list navigation', 'Screen reader text', 'energomax-core'),
        'items_list'            => _x('Projects list', 'Screen reader text', 'energomax-core'),
    ];

    register_post_type('energomax_project', [
        'labels'              => $project_labels,
        'public'              => true,
        'has_archive'         => true,
        'rewrite'             => ['slug' => 'projects', 'with_front' => false],
        'menu_icon'           => 'dashicons-portfolio',
        'menu_position'       => 6,
        'supports'            => ['title', 'thumbnail', 'editor'],
        'show_in_rest'        => true,
        'capability_type'     => 'post',
        'hierarchical'        => false,
        'exclude_from_search' => false,
    ]);
}
add_action('init', 'energomax_register_post_types');
