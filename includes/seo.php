<?php
/**
 * SEO: title patterns, meta descriptions, Schema.org, sitemaps, H1 enforcement.
 *
 * @package EnergomaxCore
 */

defined('ABSPATH') || exit;

/**
 * Auto-generate document title for products.
 *
 * @param array<int, string> $title_parts
 * @return array<int, string>
 */
function energomax_document_title_parts(array $title_parts): array
{
    if (!is_singular('energomax_product')) {
        return $title_parts;
    }

    $post_id = get_the_ID();
    if (!$post_id) {
        return $title_parts;
    }

    $name    = get_the_title($post_id);
    $voltage = (string) energomax_get_field('voltage_class', $post_id);
    $power   = energomax_get_field('power_kva', $post_id);

    if (!$power) {
        $power = energomax_get_field('transformer_power', $post_id);
    }

    $parts = [$name];
    if ($voltage) {
        $parts[] = $voltage;
    }
    if ($power) {
        $parts[] = $power . ' кВА';
    }
    $parts[] = 'Energomax';

    $title_parts['title'] = implode(' — ', $parts);

    return $title_parts;
}
add_filter('document_title_parts', 'energomax_document_title_parts', 20);

/**
 * Output meta description tag.
 */
function energomax_output_meta_description(): void
{
    if (!is_singular()) {
        return;
    }

    $post_id = get_the_ID();
    if (!$post_id) {
        return;
    }

    $description = '';

    if (has_excerpt($post_id)) {
        $description = get_the_excerpt($post_id);
    } else {
        $content     = get_post_field('post_content', $post_id);
        $description = wp_strip_all_tags($content);
    }

    $description = wp_trim_words($description, 25, '…');
    $description = mb_substr($description, 0, 155);

    if ($description) {
        echo '<meta name="description" content="' . esc_attr($description) . '" />' . "\n";
    }
}
add_action('wp_head', 'energomax_output_meta_description', 1);

/**
 * Output Schema.org JSON-LD.
 */
function energomax_output_schema_markup(): void
{
    $schemas = [];

    $org = get_option('energomax_organization_schema', []);
    if (!empty($org['name'])) {
        $schemas[] = [
            '@context'    => 'https://schema.org',
            '@type'       => 'Organization',
            'name'        => $org['name'],
            'url'         => $org['url'] ?? home_url(),
            'telephone'   => $org['telephone'] ?? '',
            'address'     => [
                '@type'           => 'PostalAddress',
                'streetAddress'   => $org['address']['streetAddress'] ?? '',
                'addressLocality' => $org['address']['addressLocality'] ?? '',
                'addressCountry'  => $org['address']['addressCountry'] ?? 'UZ',
            ],
        ];
    }

    if (is_singular('energomax_product')) {
        $post_id = get_the_ID();
        if ($post_id) {
            $price_range = (string) energomax_get_field('price_range', $post_id);
            $price_from  = energomax_get_field('price_from', $post_id);
            $currency    = (string) energomax_get_field('currency', $post_id);

            $offer = [
                '@type'         => 'Offer',
                'availability'  => 'https://schema.org/InStock',
                'priceCurrency' => $currency ?: 'UZS',
            ];

            if ($price_from) {
                $offer['price'] = (string) $price_from;
            } elseif ($price_range && $price_range !== 'По запросу') {
                $offer['price'] = $price_range;
            } else {
                $offer['priceSpecification'] = [
                    '@type' => 'PriceSpecification',
                    'price' => '0',
                    'priceCurrency' => $currency ?: 'UZS',
                    'description' => 'По запросу',
                ];
            }

            $schemas[] = [
                '@context'    => 'https://schema.org',
                '@type'       => 'Product',
                'name'        => get_the_title($post_id),
                'description' => wp_strip_all_tags(get_the_excerpt($post_id) ?: get_post_field('post_content', $post_id)),
                'image'       => get_the_post_thumbnail_url($post_id, 'full') ?: '',
                'url'         => get_permalink($post_id),
                'brand'       => [
                    '@type' => 'Brand',
                    'name'  => 'Energomax Group',
                ],
                'offers'      => $offer,
            ];
        }
    }

    foreach ($schemas as $schema) {
        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
    }
}
add_action('wp_head', 'energomax_output_schema_markup', 5);

/**
 * Register CPTs in WordPress core sitemap (WP 5.5+).
 *
 * @param WP_Sitemaps_Provider[] $providers
 * @return WP_Sitemaps_Provider[]
 */
function energomax_register_sitemap_providers($provider, string $name)
{
    return $provider;
}
add_filter('wp_sitemaps_add_provider', 'energomax_register_sitemap_providers', 10, 2);

/**
 * Ensure CPTs appear in default sitemap post types.
 *
 * @param array<string, bool> $post_types
 * @return array<string, bool>
 */
function energomax_sitemap_post_types(array $post_types): array
{
    $post_types['energomax_product'] = true;
    $post_types['energomax_project'] = true;
    return $post_types;
}
add_filter('wp_sitemaps_post_types', 'energomax_sitemap_post_types');

/**
 * Add product_category taxonomy to sitemap.
 *
 * @param array<string, bool> $taxonomies
 * @return array<string, bool>
 */
function energomax_sitemap_taxonomies(array $taxonomies): array
{
    $taxonomies['product_category'] = true;
    return $taxonomies;
}
add_filter('wp_sitemaps_taxonomies', 'energomax_sitemap_taxonomies');

/**
 * Enforce single semantic H1 from post title on singular CPT templates.
 */
function energomax_enforce_single_h1(): void
{
    if (!is_singular(['energomax_product', 'energomax_project'])) {
        return;
    }

    add_filter('the_content', 'energomax_strip_extra_h1_from_content', 5);
}
add_action('template_redirect', 'energomax_enforce_single_h1');

/**
 * Strip H1 tags from post content — theme should render title as sole H1.
 *
 * @param string $content Post content.
 */
function energomax_strip_extra_h1_from_content(string $content): string
{
    return preg_replace('/<h1[^>]*>.*?<\/h1>/is', '', $content) ?? $content;
}

/**
 * Template tag: render the canonical page H1.
 */
function energomax_the_page_h1(): void
{
    if (!is_singular()) {
        return;
    }
    echo '<h1 class="energomax-page-title">' . esc_html(get_the_title()) . '</h1>';
}

/**
 * Register organization schema settings.
 */
function energomax_register_schema_settings(): void
{
    register_setting('energomax_settings', 'energomax_organization_schema', [
        'type'              => 'array',
        'sanitize_callback' => 'energomax_sanitize_org_schema',
        'default'           => [],
    ]);
}
add_action('admin_init', 'energomax_register_schema_settings');

/**
 * @param mixed $input
 * @return array<string, mixed>
 */
function energomax_sanitize_org_schema($input): array
{
    if (!is_array($input)) {
        return [];
    }
    return [
        'name'      => sanitize_text_field($input['name'] ?? ''),
        'url'       => esc_url_raw($input['url'] ?? ''),
        'telephone' => sanitize_text_field($input['telephone'] ?? ''),
        'address'   => [
            'streetAddress'   => sanitize_text_field($input['address']['streetAddress'] ?? ''),
            'addressLocality' => sanitize_text_field($input['address']['addressLocality'] ?? ''),
            'addressCountry'  => sanitize_text_field($input['address']['addressCountry'] ?? 'UZ'),
        ],
    ];
}
