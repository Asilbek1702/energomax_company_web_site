<?php
/**
 * Plugin Name:       Energomax Core
 * Plugin URI:        https://energomaxgroup.com
 * Description:       Backend core for Energomax Group corporate site — CPTs, ACF, REST API, forms, SEO.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.1
 * Author:            Energomax Group
 * Text Domain:       energomax-core
 * Domain Path:       /languages
 *
 * @package EnergomaxCore
 */

defined('ABSPATH') || exit;

define('ENERGOMAX_CORE_VERSION', '1.0.0');
define('ENERGOMAX_CORE_FILE', __FILE__);
define('ENERGOMAX_CORE_PATH', plugin_dir_path(__FILE__));
define('ENERGOMAX_CORE_URL', plugin_dir_url(__FILE__));
define('ENERGOMAX_QUOTE_EMAIL', 'sales@energomaxgroup.com');
define('ENERGOMAX_REST_NAMESPACE', 'energomax/v1');

/**
 * Bootstrap plugin modules.
 */
function energomax_core_init(): void
{
    load_plugin_textdomain('energomax-core', false, dirname(plugin_basename(__FILE__)) . '/languages');

    $includes = [
        'includes/cpt.php',
        'includes/taxonomies.php',
        'includes/acf-fields.php',
        'includes/rest-api.php',
        'includes/form-handler.php',
        'includes/seo.php',
        'includes/admin-columns.php',
        'includes/quick-guide.php',
        'includes/performance.php',
        'includes/multilingual.php',
        'includes/security.php',
    ];

    foreach ($includes as $file) {
        $path = ENERGOMAX_CORE_PATH . $file;
        if (file_exists($path)) {
            require_once $path;
        }
    }
}
add_action('plugins_loaded', 'energomax_core_init');

/**
 * Plugin activation: flush rewrites, seed taxonomy terms, set defaults.
 */
function energomax_core_activate(): void
{
    require_once ENERGOMAX_CORE_PATH . 'includes/cpt.php';
    require_once ENERGOMAX_CORE_PATH . 'includes/taxonomies.php';

    energomax_register_post_types();
    energomax_register_taxonomies();
    energomax_seed_product_categories();

    if (false === get_option('energomax_telegram_webhook_url')) {
        add_option('energomax_telegram_webhook_url', '');
    }
    if (false === get_option('energomax_organization_schema')) {
        add_option('energomax_organization_schema', [
            'name'      => 'Energomax Group',
            'url'       => 'https://energomaxgroup.com',
            'telephone' => '+998712345678',
            'address'   => [
                'streetAddress'   => 'Tashkent',
                'addressLocality' => 'Tashkent',
                'addressCountry'  => 'UZ',
            ],
        ]);
    }

    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'energomax_core_activate');

/**
 * Plugin deactivation: flush rewrite rules.
 */
function energomax_core_deactivate(): void
{
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'energomax_core_deactivate');

/**
 * Admin notice when ACF is missing.
 */
function energomax_core_acf_notice(): void
{
    if (function_exists('acf_add_local_field_group')) {
        return;
    }
    ?>
    <div class="notice notice-warning">
        <p>
            <?php
            echo esc_html__(
                'Energomax Core requires Advanced Custom Fields (ACF) plugin. Please install and activate ACF.',
                'energomax-core'
            );
            ?>
        </p>
    </div>
    <?php
}
add_action('admin_notices', 'energomax_core_acf_notice');
