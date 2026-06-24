<?php
/**
 * Admin list table custom columns for products and projects.
 *
 * @package EnergomaxCore
 */

defined('ABSPATH') || exit;

/**
 * Product admin columns.
 *
 * @param array<string, string> $columns
 * @return array<string, string>
 */
function energomax_product_admin_columns(array $columns): array
{
    $new = [];
    foreach ($columns as $key => $label) {
        $new[$key] = $label;
        if ($key === 'title') {
            $new['product_category'] = __('Category', 'energomax-core');
            $new['voltage']          = __('Voltage', 'energomax-core');
            $new['power']            = __('Power', 'energomax-core');
            $new['price_range']      = __('Price', 'energomax-core');
            $new['has_pdf']          = __('PDF', 'energomax-core');
        }
    }
    return $new;
}
add_filter('manage_energomax_product_posts_columns', 'energomax_product_admin_columns');

/**
 * Render product column values.
 *
 * @param string $column
 * @param int    $post_id
 */
function energomax_product_admin_column_content(string $column, int $post_id): void
{
    switch ($column) {
        case 'product_category':
            $terms = get_the_terms($post_id, 'product_category');
            if ($terms && !is_wp_error($terms)) {
                echo esc_html(implode(', ', wp_list_pluck($terms, 'name')));
            } else {
                echo '—';
            }
            break;

        case 'voltage':
            $voltage = energomax_get_field('voltage_class', $post_id)
                ?: energomax_get_field('voltage_hv', $post_id);
            echo esc_html($voltage ? (string) $voltage : '—');
            break;

        case 'power':
            $power = energomax_get_field('power_kva', $post_id)
                ?: energomax_get_field('transformer_power', $post_id)
                ?: energomax_get_field('power_watt', $post_id);
            if ($power) {
                $unit = energomax_get_field('power_watt', $post_id) ? ' W' : ' kVA';
                echo esc_html((string) $power . $unit);
            } else {
                echo '—';
            }
            break;

        case 'price_range':
            $price = energomax_get_field('price_range', $post_id);
            if (!$price) {
                $from     = energomax_get_field('price_from', $post_id);
                $currency = energomax_get_field('currency', $post_id);
                $price    = $from ? $from . ' ' . $currency : '';
            }
            echo esc_html($price ? (string) $price : '—');
            break;

        case 'has_pdf':
            $pdf = energomax_get_field('datasheet_pdf', $post_id);
            $has = is_array($pdf) && !empty($pdf['url']);
            echo $has
                ? '<span class="dashicons dashicons-yes" style="color:green;" title="PDF"></span>'
                : '<span class="dashicons dashicons-no" style="color:#ccc;" title="No PDF"></span>';
            break;
    }
}
add_action('manage_energomax_product_posts_custom_column', 'energomax_product_admin_column_content', 10, 2);

/**
 * Make product columns sortable where applicable.
 *
 * @param array<string, string> $columns
 * @return array<string, string>
 */
function energomax_product_sortable_columns(array $columns): array
{
    $columns['power']       = 'power_kva';
    $columns['price_range'] = 'price_range';
    return $columns;
}
add_filter('manage_edit-energomax_product_sortable_columns', 'energomax_product_sortable_columns');

/**
 * Project admin columns.
 *
 * @param array<string, string> $columns
 * @return array<string, string>
 */
function energomax_project_admin_columns(array $columns): array
{
    $new = [];
    foreach ($columns as $key => $label) {
        $new[$key] = $label;
        if ($key === 'title') {
            $new['client']   = __('Client', 'energomax-core');
            $new['year']     = __('Year', 'energomax-core');
            $new['country']  = __('Country', 'energomax-core');
        }
    }
    return $new;
}
add_filter('manage_energomax_project_posts_columns', 'energomax_project_admin_columns');

/**
 * Render project column values.
 *
 * @param string $column
 * @param int    $post_id
 */
function energomax_project_admin_column_content(string $column, int $post_id): void
{
    switch ($column) {
        case 'client':
            echo esc_html((string) energomax_get_field('client_name', $post_id) ?: '—');
            break;
        case 'year':
            $year = energomax_get_field('supply_year', $post_id);
            echo esc_html($year ? (string) $year : '—');
            break;
        case 'country':
            echo esc_html((string) energomax_get_field('country', $post_id) ?: '—');
            break;
    }
}
add_action('manage_energomax_project_posts_custom_column', 'energomax_project_admin_column_content', 10, 2);

/**
 * Make project year column sortable.
 *
 * @param array<string, string> $columns
 * @return array<string, string>
 */
function energomax_project_sortable_columns(array $columns): array
{
    $columns['year'] = 'supply_year';
    return $columns;
}
add_filter('manage_edit-energomax_project_sortable_columns', 'energomax_project_sortable_columns');

/**
 * Handle sorting by meta key in admin.
 *
 * @param WP_Query $query
 */
function energomax_admin_column_orderby(WP_Query $query): void
{
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    $orderby = $query->get('orderby');

    $map = [
        'power_kva'    => 'power_kva',
        'price_range'  => 'price_range',
        'supply_year'  => 'supply_year',
    ];

    if (isset($map[$orderby])) {
        $query->set('meta_key', $map[$orderby]);
        $query->set('orderby', 'meta_value_num');
    }
}
add_action('pre_get_posts', 'energomax_admin_column_orderby');
