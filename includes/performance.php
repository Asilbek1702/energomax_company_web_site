<?php
/**
 * Performance optimizations: image sizes, script attributes, disable bloat.
 *
 * @package EnergomaxCore
 */

defined('ABSPATH') || exit;

/**
 * Register custom image sizes for product cards.
 */
function energomax_register_image_sizes(): void
{
    add_image_size('energomax-card', 600, 400, true);
    add_image_size('energomax-card-sm', 300, 200, true);
}
add_action('after_setup_theme', 'energomax_register_image_sizes');

/**
 * Add human-readable names in media settings.
 *
 * @param array<string, string> $sizes
 * @return array<string, string>
 */
function energomax_image_size_names(array $sizes): array
{
    $sizes['energomax-card']    = __('Energomax Card (600×400)', 'energomax-core');
    $sizes['energomax-card-sm'] = __('Energomax Card Small (300×200)', 'energomax-core');
    return $sizes;
}
add_filter('image_size_names_choose', 'energomax_image_size_names');

/**
 * Enable WebP upload support.
 *
 * @param array<string, string> $mimes
 * @return array<string, string>
 */
function energomax_enable_webp_upload(array $mimes): array
{
    $mimes['webp'] = 'image/webp';
    return $mimes;
}
add_filter('upload_mimes', 'energomax_enable_webp_upload');

/**
 * Add defer/async to enqueued scripts.
 *
 * @param string $tag    Script tag HTML.
 * @param string $handle Script handle.
 * @param string $src    Script source URL.
 */
function energomax_script_loader_tag(string $tag, string $handle, string $src): string
{
    $defer_handles = ['energomax-quote-form'];

    if (in_array($handle, $defer_handles, true)) {
        if (strpos($tag, ' defer') === false) {
            $tag = str_replace(' src', ' defer src', $tag);
        }
    }

    return $tag;
}
add_filter('script_loader_tag', 'energomax_script_loader_tag', 10, 3);

/**
 * Disable emoji scripts and embeds on CPT archive/single pages.
 */
function energomax_disable_bloat_on_cpt_pages(): void
{
    if (!energomax_is_cpt_frontend()) {
        return;
    }

    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

    wp_deregister_script('wp-embed');
}
add_action('wp_enqueue_scripts', 'energomax_disable_bloat_on_cpt_pages', 100);

/**
 * Check if current page is a product/project CPT frontend view.
 */
function energomax_is_cpt_frontend(): bool
{
    return is_singular(['energomax_product', 'energomax_project'])
        || is_post_type_archive(['energomax_product', 'energomax_project'])
        || is_tax('product_category');
}

/**
 * Write cache headers snippet to uploads on activation for .htaccess reference.
 */
function energomax_create_htaccess_snippet(): void
{
    $upload_dir = wp_upload_dir();
    $dir        = $upload_dir['basedir'] . '/energomax-cache';

    if (!file_exists($dir)) {
        wp_mkdir_p($dir);
    }

    $snippet = <<<'HTACCESS'
# Energomax Core — static asset cache headers
# Copy relevant sections to your site root .htaccess

<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/webp "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType application/pdf "access plus 1 month"
    ExpiresByType font/woff2 "access plus 1 year"
</IfModule>

<IfModule mod_headers.c>
    <FilesMatch "\.(ico|jpg|jpeg|png|gif|webp|svg|css|js|woff2|pdf)$">
        Header set Cache-Control "public, max-age=31536000, immutable"
    </FilesMatch>
</IfModule>
HTACCESS;

    file_put_contents($dir . '/htaccess-snippet.txt', $snippet);
}
add_action('admin_init', 'energomax_create_htaccess_snippet');
