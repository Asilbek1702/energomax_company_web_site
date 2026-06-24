<?php
/**
 * Quick Guide admin page for content editors.
 *
 * @package EnergomaxCore
 */

defined('ABSPATH') || exit;

/**
 * Register Quick Guide submenu under Settings.
 */
function energomax_register_quick_guide_page(): void
{
    add_options_page(
        __('Energomax Quick Guide', 'energomax-core'),
        __('Energomax Quick Guide', 'energomax-core'),
        'edit_posts',
        'energomax-quick-guide',
        'energomax_render_quick_guide_page'
    );
}
add_action('admin_menu', 'energomax_register_quick_guide_page');

/**
 * Render Quick Guide page content.
 */
function energomax_render_quick_guide_page(): void
{
    if (!current_user_can('edit_posts')) {
        return;
    }

    $products_url  = admin_url('edit.php?post_type=energomax_product');
    $projects_url  = admin_url('edit.php?post_type=energomax_project');
    $media_url     = admin_url('upload.php');
    $settings_url  = admin_url('options-general.php?page=energomax-settings');
    ?>
    <div class="wrap energomax-quick-guide">
        <h1><?php esc_html_e('Energomax Quick Guide', 'energomax-core'); ?></h1>
        <p><?php esc_html_e('One-page reference for content editors.', 'energomax-core'); ?></p>

        <hr />

        <h2><?php esc_html_e('1. Adding a Product', 'energomax-core'); ?></h2>
        <ol>
            <li>
                <?php
                printf(
                    /* translators: %s: admin URL */
                    wp_kses_post(__('Go to <a href="%s">Products → Add New</a>.', 'energomax-core')),
                    esc_url(admin_url('post-new.php?post_type=energomax_product'))
                );
                ?>
            </li>
            <li><?php esc_html_e('Enter the product title and description.', 'energomax-core'); ?></li>
            <li><?php esc_html_e('Set a Featured Image (recommended: 1200×800 px).', 'energomax-core'); ?></li>
            <li>
                <?php esc_html_e('Assign a Product Category: Transformers, KTP, Switchgear, or LED.', 'energomax-core'); ?>
                <br />
                <em><?php esc_html_e('Category-specific fields appear after you select a category.', 'energomax-core'); ?></em>
            </li>
            <li><?php esc_html_e('Fill in technical specifications and price range.', 'energomax-core'); ?></li>
            <li>
                <?php
                printf(
                    wp_kses_post(__('Upload a PDF datasheet via the Datasheet PDF field (PDF only, hosted on this site).', 'energomax-core'))
                );
                ?>
            </li>
            <li><?php esc_html_e('Click Publish. The product appears at /products/.', 'energomax-core'); ?></li>
        </ol>

        <h2><?php esc_html_e('2. Adding a Project (Case Study)', 'energomax-core'); ?></h2>
        <ol>
            <li>
                <?php
                printf(
                    wp_kses_post(__('Go to <a href="%s">Projects → Add New</a>.', 'energomax-core')),
                    esc_url(admin_url('post-new.php?post_type=energomax_project'))
                );
                ?>
            </li>
            <li><?php esc_html_e('Enter project title, featured image, and description.', 'energomax-core'); ?></li>
            <li><?php esc_html_e('Fill Client Name, Object, Supply Power, Year, and Country fields.', 'energomax-core'); ?></li>
            <li><?php esc_html_e('Click Publish.', 'energomax-core'); ?></li>
        </ol>

        <h2><?php esc_html_e('3. Replacing a PDF Datasheet', 'energomax-core'); ?></h2>
        <ol>
            <li>
                <?php
                printf(
                    wp_kses_post(__('Open the product in <a href="%s">Products</a> and click Edit.', 'energomax-core')),
                    esc_url($products_url)
                );
                ?>
            </li>
            <li><?php esc_html_e('In the Datasheet PDF field, remove the old file and upload the new PDF.', 'energomax-core'); ?></li>
            <li>
                <?php
                printf(
                    wp_kses_post(__('Or upload via <a href="%s">Media Library</a> first, then select the file in the product.', 'energomax-core')),
                    esc_url($media_url)
                );
                ?>
            </li>
            <li><?php esc_html_e('Update the product. The new PDF link is live immediately.', 'energomax-core'); ?></li>
        </ol>

        <h2><?php esc_html_e('4. Multilingual Content (RU / UZ)', 'energomax-core'); ?></h2>
        <ul>
            <li><?php esc_html_e('Use Polylang or WPML language switcher in the admin bar to translate products and projects.', 'energomax-core'); ?></li>
            <li><?php esc_html_e('Each language version is a separate post linked as a translation.', 'energomax-core'); ?></li>
            <li><?php esc_html_e('Fill ACF fields for each language version independently.', 'energomax-core'); ?></li>
        </ul>

        <h2><?php esc_html_e('5. Quote Form', 'energomax-core'); ?></h2>
        <ul>
            <li><?php esc_html_e('Quote requests are sent to sales@energomaxgroup.com and Telegram (if configured).', 'energomax-core'); ?></li>
            <li>
                <?php
                printf(
                    wp_kses_post(__('Configure Telegram webhook at <a href="%s">Settings → Energomax</a>.', 'energomax-core')),
                    esc_url($settings_url)
                );
                ?>
            </li>
        </ul>

        <h2><?php esc_html_e('Quick Links', 'energomax-core'); ?></h2>
        <p>
            <a class="button" href="<?php echo esc_url($products_url); ?>"><?php esc_html_e('All Products', 'energomax-core'); ?></a>
            <a class="button" href="<?php echo esc_url($projects_url); ?>"><?php esc_html_e('All Projects', 'energomax-core'); ?></a>
            <a class="button" href="<?php echo esc_url($media_url); ?>"><?php esc_html_e('Media Library', 'energomax-core'); ?></a>
            <a class="button" href="<?php echo esc_url($settings_url); ?>"><?php esc_html_e('Energomax Settings', 'energomax-core'); ?></a>
        </p>
    </div>
    <?php
}
