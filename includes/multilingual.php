<?php
/**
 * Multilingual support — Polylang and WPML integration.
 *
 * @package EnergomaxCore
 */

defined('ABSPATH') || exit;

/**
 * Register CPTs and taxonomies as translatable with Polylang.
 */
function energomax_polylang_register_types(): void
{
    if (!function_exists('pll_register_post_type') || !function_exists('pll_register_taxonomy')) {
        return;
    }

    pll_register_post_type('energomax_product', true);
    pll_register_post_type('energomax_project', true);
    pll_register_taxonomy('product_category', true);
}
add_action('init', 'energomax_polylang_register_types', 20);

/**
 * Register CPTs and taxonomies with WPML.
 */
function energomax_wpml_register_types(): void
{
    if (!defined('ICL_SITEPRESS_VERSION')) {
        return;
    }

    do_action('wpml_register_single_string', 'energomax-core', 'plugin_name', 'Energomax Core');
}
add_action('init', 'energomax_wpml_register_types', 20);

/**
 * WPML: mark post types as translatable via filter.
 *
 * @param array<string, mixed> $post_types
 * @return array<string, mixed>
 */
function energomax_wpml_post_types(array $post_types): array
{
    $post_types['energomax_product'] = 1;
    $post_types['energomax_project'] = 1;
    return $post_types;
}
add_filter('wpml_custom_post_type_support', 'energomax_wpml_post_types');

/**
 * WPML: mark taxonomy as translatable.
 *
 * @param array<string, mixed> $taxonomies
 * @return array<string, mixed>
 */
function energomax_wpml_taxonomies(array $taxonomies): array
{
    $taxonomies['product_category'] = 1;
    return $taxonomies;
}
add_filter('wpml_custom_taxonomy_support', 'energomax_wpml_taxonomies');

/**
 * Polylang: preserve current URL in language switcher (default Polylang behavior).
 * Ensure CPT archives and singles are included in language switcher.
 *
 * @param array<string, mixed> $languages
 * @return array<string, mixed>
 */
function energomax_polylang_switcher_urls(array $languages): array
{
    if (!is_singular(['energomax_product', 'energomax_project'])) {
        return $languages;
    }

    $post_id = get_the_ID();
    if (!$post_id || !function_exists('pll_get_post')) {
        return $languages;
    }

    foreach ($languages as $lang => &$data) {
        $translated_id = pll_get_post($post_id, $lang);
        if ($translated_id) {
            $data['url'] = get_permalink($translated_id);
        }
    }

    return $languages;
}
add_filter('pll_the_languages', 'energomax_polylang_switcher_urls');

/**
 * Load Polylang .pot strings hint for editors.
 */
function energomax_register_polylang_strings(): void
{
    if (!function_exists('pll_register_string')) {
        return;
    }

    $strings = [
        'Products'  => 'Products',
        'Projects'  => 'Projects',
        'Quote'     => 'Request a Quote',
    ];

    foreach ($strings as $name => $string) {
        pll_register_string($name, $string, 'Energomax Core');
    }
}
add_action('init', 'energomax_register_polylang_strings', 25);
