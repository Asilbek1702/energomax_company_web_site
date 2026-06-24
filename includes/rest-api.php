<?php
/**
 * Custom REST API endpoints.
 *
 * @package EnergomaxCore
 */

defined('ABSPATH') || exit;

/**
 * Register REST routes.
 */
function energomax_register_rest_routes(): void
{
    register_rest_route(ENERGOMAX_REST_NAMESPACE, '/products', [
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => 'energomax_rest_get_products',
        'permission_callback' => '__return_true',
        'args'                => [
            'category'       => ['type' => 'string', 'sanitize_callback' => 'sanitize_text_field'],
            'min_power'      => ['type' => 'number', 'sanitize_callback' => 'floatval'],
            'max_power'      => ['type' => 'number', 'sanitize_callback' => 'floatval'],
            'voltage_class'  => ['type' => 'string', 'sanitize_callback' => 'sanitize_text_field'],
            'per_page'       => ['type' => 'integer', 'default' => 20, 'sanitize_callback' => 'absint'],
            'page'           => ['type' => 'integer', 'default' => 1, 'sanitize_callback' => 'absint'],
        ],
    ]);

    register_rest_route(ENERGOMAX_REST_NAMESPACE, '/projects', [
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => 'energomax_rest_get_projects',
        'permission_callback' => '__return_true',
        'args'                => [
            'per_page' => ['type' => 'integer', 'default' => 20, 'sanitize_callback' => 'absint'],
            'page'     => ['type' => 'integer', 'default' => 1, 'sanitize_callback' => 'absint'],
        ],
    ]);

    register_rest_route(ENERGOMAX_REST_NAMESPACE, '/quote', [
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => 'energomax_rest_post_quote',
        'permission_callback' => 'energomax_rest_quote_permission',
        'args'                => [
            'name'         => ['type' => 'string', 'required' => true, 'sanitize_callback' => 'sanitize_text_field'],
            'phone'        => ['type' => 'string', 'required' => true, 'sanitize_callback' => 'sanitize_text_field'],
            'email'        => ['type' => 'string', 'required' => true, 'sanitize_callback' => 'sanitize_email'],
            'product_name' => ['type' => 'string', 'sanitize_callback' => 'sanitize_text_field'],
            'comment'      => ['type' => 'string', 'sanitize_callback' => 'sanitize_textarea_field'],
            'nonce'        => ['type' => 'string', 'required' => true],
        ],
    ]);
}
add_action('rest_api_init', 'energomax_register_rest_routes');

/**
 * Permission callback for quote endpoint with rate limiting.
 *
 * @param WP_REST_Request $request Request object.
 */
function energomax_rest_quote_permission(WP_REST_Request $request): bool|WP_Error
{
    if (!energomax_verify_quote_nonce($request->get_param('nonce'))) {
        return new WP_Error('invalid_nonce', __('Invalid security token.', 'energomax-core'), ['status' => 403]);
    }

    if (!energomax_check_rate_limit('quote')) {
        return new WP_Error('rate_limit', __('Too many requests. Please try again later.', 'energomax-core'), ['status' => 429]);
    }

    return true;
}

/**
 * GET /products handler.
 *
 * @param WP_REST_Request $request Request object.
 * @return WP_REST_Response
 */
function energomax_rest_get_products(WP_REST_Request $request): WP_REST_Response
{
    $args = [
        'post_type'      => 'energomax_product',
        'post_status'    => 'publish',
        'posts_per_page' => $request->get_param('per_page'),
        'paged'          => $request->get_param('page'),
    ];

    $tax_query = [];
    $category  = $request->get_param('category');
    if ($category) {
        $tax_query[] = [
            'taxonomy' => 'product_category',
            'field'    => 'slug',
            'terms'    => $category,
        ];
    }
    if ($tax_query) {
        $args['tax_query'] = $tax_query;
    }

    $meta_query = [];
    $voltage    = $request->get_param('voltage_class');
    if ($voltage) {
        $meta_query[] = [
            'key'     => 'voltage_class',
            'value'   => $voltage,
            'compare' => 'LIKE',
        ];
    }

    $min_power = $request->get_param('min_power');
    $max_power = $request->get_param('max_power');
    $filter_power = ($min_power !== null && $min_power !== '') || ($max_power !== null && $max_power !== '');

    if ($meta_query) {
        $args['meta_query'] = $meta_query;
    }

    $query   = new WP_Query($args);
    $results = [];

    foreach ($query->posts as $post) {
        if ($filter_power && !energomax_product_matches_power_range($post->ID, $min_power, $max_power)) {
            continue;
        }
        $results[] = energomax_format_product_for_api($post);
    }

    return new WP_REST_Response([
        'items' => $results,
        'total' => (int) $query->found_posts,
        'pages' => (int) $query->max_num_pages,
    ], 200);
}

/**
 * GET /projects handler.
 *
 * @param WP_REST_Request $request Request object.
 * @return WP_REST_Response
 */
function energomax_rest_get_projects(WP_REST_Request $request): WP_REST_Response
{
    $query = new WP_Query([
        'post_type'      => 'energomax_project',
        'post_status'    => 'publish',
        'posts_per_page' => $request->get_param('per_page'),
        'paged'          => $request->get_param('page'),
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);

    $results = [];
    foreach ($query->posts as $post) {
        $results[] = [
            'id'            => $post->ID,
            'title'         => get_the_title($post),
            'client'        => (string) energomax_get_field('client_name', $post->ID),
            'object'        => (string) energomax_get_field('object_name', $post->ID),
            'power'         => (string) energomax_get_field('supply_power', $post->ID),
            'year'          => (int) energomax_get_field('supply_year', $post->ID),
            'country'       => (string) energomax_get_field('country', $post->ID),
            'thumbnail_url' => get_the_post_thumbnail_url($post, 'energomax-card') ?: '',
            'permalink'     => get_permalink($post),
        ];
    }

    return new WP_REST_Response([
        'items' => $results,
        'total' => (int) $query->found_posts,
        'pages' => (int) $query->max_num_pages,
    ], 200);
}

/**
 * POST /quote handler.
 *
 * @param WP_REST_Request $request Request object.
 * @return WP_REST_Response|WP_Error
 */
function energomax_rest_post_quote(WP_REST_Request $request): WP_REST_Response|WP_Error
{
    $data = [
        'name'         => $request->get_param('name'),
        'phone'        => $request->get_param('phone'),
        'email'        => $request->get_param('email'),
        'product_name' => $request->get_param('product_name') ?: '',
        'comment'      => $request->get_param('comment') ?: '',
    ];

    $validation = energomax_validate_quote_data($data);
    if (is_wp_error($validation)) {
        return $validation;
    }

    $sent = energomax_process_quote_submission($data);
    if (is_wp_error($sent)) {
        return $sent;
    }

    return new WP_REST_Response([
        'success' => true,
        'message' => __('Your request has been sent. We will contact you shortly.', 'energomax-core'),
    ], 200);
}

/**
 * Check if product power falls within min/max range (any power field).
 */
function energomax_product_matches_power_range(int $post_id, $min_power, $max_power): bool
{
    $values = array_filter([
        energomax_get_field('power_kva', $post_id),
        energomax_get_field('transformer_power', $post_id),
        energomax_get_field('power_watt', $post_id),
    ], static fn ($v) => $v !== '' && $v !== null);

    if (empty($values)) {
        return false;
    }

    foreach ($values as $value) {
        $num = (float) $value;
        if ($min_power !== null && $min_power !== '' && $num < (float) $min_power) {
            continue;
        }
        if ($max_power !== null && $max_power !== '' && $num > (float) $max_power) {
            continue;
        }
        return true;
    }

    return false;
}

/**
 * Format a product post for API response.
 *
 * @param WP_Post $post Post object.
 * @return array<string, mixed>
 */
function energomax_format_product_for_api(WP_Post $post): array
{
    $terms    = wp_get_post_terms($post->ID, 'product_category', ['fields' => 'slugs']);
    $category = !empty($terms) && !is_wp_error($terms) ? $terms[0] : '';

    return [
        'id'            => $post->ID,
        'title'         => get_the_title($post),
        'category'      => $category,
        'specs'         => energomax_get_product_specs($post->ID, $category),
        'thumbnail_url' => get_the_post_thumbnail_url($post, 'energomax-card') ?: '',
        'permalink'     => get_permalink($post),
        'excerpt'       => get_the_excerpt($post),
    ];
}

/**
 * Get ACF specs for a product based on category.
 *
 * @return array<string, mixed>
 */
function energomax_get_product_specs(int $post_id, string $category): array
{
    $field_map = [
        'transformers' => [
            'voltage_class', 'power_kva', 'cooling_type', 'protection_ip',
            'climate_version', 'price_range', 'datasheet_pdf',
        ],
        'ktp' => [
            'ktp_type', 'voltage_hv', 'voltage_lv', 'transformer_power',
            'installation_type', 'price_range', 'datasheet_pdf',
        ],
        'switchgear' => [
            'panel_type', 'sections_count', 'rated_current', 'price_range', 'datasheet_pdf',
        ],
        'led' => [
            'power_watt', 'lumens', 'protection_ip', 'color_temp_k',
            'lifespan_hours', 'price_from', 'currency', 'datasheet_pdf',
        ],
    ];

    $fields = $field_map[$category] ?? array_unique(array_merge(...array_values($field_map)));
    $specs  = [];

    foreach ($fields as $field) {
        $value = energomax_get_field($field, $post_id);
        if ($field === 'datasheet_pdf' && is_array($value)) {
            $specs[$field] = $value['url'] ?? '';
        } else {
            $specs[$field] = $value;
        }
    }

    return $specs;
}

/**
 * Safe ACF field getter with fallback.
 *
 * @return mixed
 */
function energomax_get_field(string $field, int $post_id)
{
    if (function_exists('get_field')) {
        return get_field($field, $post_id);
    }
    return get_post_meta($post_id, $field, true);
}

/**
 * Expose REST nonce to frontend.
 */
function energomax_localize_rest_script(): void
{
    wp_localize_script('energomax-quote-form', 'energomaxRest', [
        'root'  => esc_url_raw(rest_url(ENERGOMAX_REST_NAMESPACE)),
        'nonce' => wp_create_nonce('energomax_quote'),
    ]);
}
add_action('wp_enqueue_scripts', 'energomax_localize_rest_script', 20);
