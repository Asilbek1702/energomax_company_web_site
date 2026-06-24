<?php
/**
 * Contact / quote form handling — CF7 hooks, email, Telegram.
 *
 * @package EnergomaxCore
 */

defined('ABSPATH') || exit;

/**
 * Register CF7 form tag for hidden product_name if needed.
 */
function energomax_cf7_register_form(): void
{
    if (!function_exists('wpcf7_add_form_tag')) {
        return;
    }

    wpcf7_add_form_tag('energomax_product_name', 'energomax_cf7_product_name_tag_handler', ['name-attr' => true]);
}
add_action('wpcf7_init', 'energomax_cf7_register_form');

/**
 * @param WPCF7_FormTag|array<string, mixed> $tag
 */
function energomax_cf7_product_name_tag_handler($tag): string
{
    if (is_object($tag) && property_exists($tag, 'name')) {
        $name = $tag->name ?: 'product_name';
    } else {
        $name = 'product_name';
    }
    return sprintf(
        '<input type="hidden" name="%s" class="energomax-product-name-field" value="" />',
        esc_attr($name)
    );
}

/**
 * Validate CF7 phone field format.
 *
 * @param mixed $result
 * @param mixed $tag
 * @return mixed
 */
function energomax_cf7_validate_submission($result, $tag)
{
    if (!is_object($tag) || !isset($tag->name)) {
        return $result;
    }

    if (!in_array($tag->name, ['phone', 'your-phone'], true)) {
        return $result;
    }

    $phone = '';
    if (isset($_POST[$tag->name])) {
        $phone = sanitize_text_field(wp_unslash($_POST[$tag->name]));
    }

    if ($phone && !preg_match('/^[\d\s\+\-\(\)]{7,20}$/', $phone) && method_exists($result, 'invalidate')) {
        $result->invalidate($tag, __('Please enter a valid phone number.', 'energomax-core'));
    }

    return $result;
}
add_filter('wpcf7_validate_tel', 'energomax_cf7_validate_submission', 10, 2);
add_filter('wpcf7_validate_tel*', 'energomax_cf7_validate_submission', 10, 2);

/**
 * Process quote on CF7 before send mail — also send Telegram.
 *
 * @param WPCF7_ContactForm $contact_form Form instance.
 */
function energomax_cf7_before_send_mail($contact_form): void
{
    if (!class_exists('WPCF7_ContactForm') || !($contact_form instanceof WPCF7_ContactForm)) {
        return;
    }

    $form_id = (int) get_option('energomax_cf7_form_id', 0);
    if ($form_id && (int) $contact_form->id() !== $form_id) {
        return;
    }

    $submission = WPCF7_Submission::get_instance();
    if (!$submission) {
        return;
    }

    $posted = $submission->get_posted_data();

    if (empty($posted['energomax_nonce']) || !wp_verify_nonce($posted['energomax_nonce'], 'energomax_quote')) {
        return;
    }

    $data = [
        'name'         => sanitize_text_field($posted['your-name'] ?? $posted['name'] ?? ''),
        'phone'        => sanitize_text_field($posted['your-phone'] ?? $posted['phone'] ?? ''),
        'email'        => sanitize_email($posted['your-email'] ?? $posted['email'] ?? ''),
        'product_name' => sanitize_text_field($posted['product_name'] ?? ''),
        'comment'      => sanitize_textarea_field($posted['your-message'] ?? $posted['comment'] ?? ''),
    ];

    energomax_send_telegram_notification($data);
}
add_action('wpcf7_before_send_mail', 'energomax_cf7_before_send_mail');

/**
 * Validate quote submission data.
 *
 * @param array<string, string> $data
 * @return true|WP_Error
 */
function energomax_validate_quote_data(array $data): bool|WP_Error
{
    if (empty($data['name'])) {
        return new WP_Error('missing_name', __('Name is required.', 'energomax-core'), ['status' => 400]);
    }
    if (empty($data['phone']) || !preg_match('/^[\d\s\+\-\(\)]{7,20}$/', $data['phone'])) {
        return new WP_Error('invalid_phone', __('Valid phone is required.', 'energomax-core'), ['status' => 400]);
    }
    if (empty($data['email']) || !is_email($data['email'])) {
        return new WP_Error('invalid_email', __('Valid email is required.', 'energomax-core'), ['status' => 400]);
    }
    return true;
}

/**
 * Process quote: email + Telegram.
 *
 * @param array<string, string> $data
 * @return true|WP_Error
 */
function energomax_process_quote_submission(array $data): bool|WP_Error
{
    $validation = energomax_validate_quote_data($data);
    if (is_wp_error($validation)) {
        return $validation;
    }

    $subject = sprintf('[Energomax] Quote request from %s', $data['name']);
    $body    = energomax_build_quote_email_body($data);
    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        'Reply-To: ' . $data['name'] . ' <' . $data['email'] . '>',
    ];

    $sent = wp_mail(ENERGOMAX_QUOTE_EMAIL, $subject, $body, $headers);
    if (!$sent) {
        return new WP_Error('mail_failed', __('Failed to send email.', 'energomax-core'), ['status' => 500]);
    }

    energomax_send_telegram_notification($data);

    return true;
}

/**
 * Build plain-text email body for quote.
 *
 * @param array<string, string> $data
 */
function energomax_build_quote_email_body(array $data): string
{
    $lines = [
        'New quote request — Energomax Group',
        '-----------------------------------',
        'Name: ' . $data['name'],
        'Phone: ' . $data['phone'],
        'Email: ' . $data['email'],
        'Product: ' . ($data['product_name'] ?: '—'),
        'Comment:',
        $data['comment'] ?: '—',
        '',
        'Sent from: ' . home_url(),
        'Date: ' . current_time('mysql'),
    ];

    return implode("\n", $lines);
}

/**
 * Send Telegram notification via configurable webhook URL.
 *
 * @param array<string, string> $data
 */
function energomax_send_telegram_notification(array $data): void
{
    $webhook_url = get_option('energomax_telegram_webhook_url', '');
    if (empty($webhook_url)) {
        return;
    }

    $message = sprintf(
        "🔔 *Новая заявка*\n\n👤 %s\n📞 %s\n✉️ %s\n📦 %s\n\n💬 %s",
        $data['name'],
        $data['phone'],
        $data['email'],
        $data['product_name'] ?: '—',
        $data['comment'] ?: '—'
    );

    wp_remote_post($webhook_url, [
        'timeout' => 10,
        'headers' => ['Content-Type' => 'application/json'],
        'body'    => wp_json_encode([
            'text'       => $message,
            'parse_mode' => 'Markdown',
            'source'     => 'energomax-core',
            'payload'    => $data,
        ]),
    ]);
}

/**
 * Verify quote nonce.
 */
function energomax_verify_quote_nonce(string $nonce): bool
{
    return (bool) wp_verify_nonce($nonce, 'energomax_quote');
}

/**
 * Register settings for Telegram webhook URL.
 */
function energomax_register_form_settings(): void
{
    register_setting('energomax_settings', 'energomax_telegram_webhook_url', [
        'type'              => 'string',
        'sanitize_callback' => 'esc_url_raw',
        'default'           => '',
    ]);

    register_setting('energomax_settings', 'energomax_cf7_form_id', [
        'type'              => 'integer',
        'sanitize_callback' => 'absint',
        'default'           => 0,
    ]);
}
add_action('admin_init', 'energomax_register_form_settings');

/**
 * Add Energomax settings page under Settings.
 */
function energomax_add_settings_page(): void
{
    add_options_page(
        __('Energomax Settings', 'energomax-core'),
        __('Energomax', 'energomax-core'),
        'manage_options',
        'energomax-settings',
        'energomax_render_settings_page'
    );
}
add_action('admin_menu', 'energomax_add_settings_page');

/**
 * Render settings page.
 */
function energomax_render_settings_page(): void
{
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php settings_fields('energomax_settings'); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <label for="energomax_telegram_webhook_url">
                            <?php esc_html_e('Telegram Webhook URL', 'energomax-core'); ?>
                        </label>
                    </th>
                    <td>
                        <input
                            type="url"
                            id="energomax_telegram_webhook_url"
                            name="energomax_telegram_webhook_url"
                            value="<?php echo esc_attr(get_option('energomax_telegram_webhook_url', '')); ?>"
                            class="regular-text"
                            placeholder="https://api.telegram.org/bot.../sendMessage"
                        />
                        <p class="description">
                            <?php esc_html_e('Telegram bot webhook URL for quote notifications.', 'energomax-core'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="energomax_cf7_form_id">
                            <?php esc_html_e('Contact Form 7 ID', 'energomax-core'); ?>
                        </label>
                    </th>
                    <td>
                        <input
                            type="number"
                            id="energomax_cf7_form_id"
                            name="energomax_cf7_form_id"
                            value="<?php echo esc_attr((string) get_option('energomax_cf7_form_id', 0)); ?>"
                            class="small-text"
                            min="0"
                        />
                        <p class="description">
                            <?php esc_html_e('CF7 form ID to hook Telegram notifications (0 = all forms).', 'energomax-core'); ?>
                        </p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * Output hidden nonce field helper for CF7 forms.
 * Add to CF7 form: [hidden energomax_nonce default:shortcode_attr]
 */
function energomax_cf7_nonce_shortcode(): string
{
    return '<input type="hidden" name="energomax_nonce" value="' . esc_attr(wp_create_nonce('energomax_quote')) . '" />';
}
add_shortcode('energomax_nonce', 'energomax_cf7_nonce_shortcode');

/**
 * Enqueue quote form JS for product_name auto-population.
 */
function energomax_enqueue_quote_form_script(): void
{
    wp_enqueue_script(
        'energomax-quote-form',
        ENERGOMAX_CORE_URL . 'assets/js/quote-form.js',
        [],
        ENERGOMAX_CORE_VERSION,
        true
    );
}
add_action('wp_enqueue_scripts', 'energomax_enqueue_quote_form_script');
