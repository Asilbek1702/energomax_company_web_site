<?php
/**
 * Security: rate limiting, nonce, PDF upload restrictions.
 *
 * @package EnergomaxCore
 */

defined('ABSPATH') || exit;

/**
 * Rate limit check — 10 requests per minute per IP.
 *
 * @param string $action Action identifier.
 */
function energomax_check_rate_limit(string $action): bool
{
    $ip      = energomax_get_client_ip();
    $key     = 'em_rl_' . md5($action . $ip);
    $count   = (int) get_transient($key);
    $limit   = 10;
    $window  = MINUTE_IN_SECONDS;

    if ($count >= $limit) {
        return false;
    }

    set_transient($key, $count + 1, $window);
    return true;
}

/**
 * Get client IP address safely.
 */
function energomax_get_client_ip(): string
{
    $headers = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];

    foreach ($headers as $header) {
        if (!empty($_SERVER[$header])) {
            $ip = sanitize_text_field(wp_unslash($_SERVER[$header]));
            if (str_contains($ip, ',')) {
                $ip = trim(explode(',', $ip)[0]);
            }
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }

    return '0.0.0.0';
}

/**
 * Restrict ACF file uploads to PDF only for datasheet fields.
 *
 * @param array<string, mixed> $errors
 * @param array<string, mixed> $file
 * @return array<string, mixed>
 */
function energomax_restrict_pdf_upload(array $errors, array $file): array
{
    if (empty($file['type']) || $file['type'] === 'application/pdf') {
        return $errors;
    }

    if (!isset($_POST['_acf_field'])) {
        return $errors;
    }

    $field_key = sanitize_text_field(wp_unslash($_POST['_acf_field']));
    $pdf_keys  = [
        'field_em_datasheet_pdf',
        'field_em_ktp_datasheet_pdf',
        'field_em_switchgear_datasheet_pdf',
        'field_em_led_datasheet_pdf',
    ];

    if (in_array($field_key, $pdf_keys, true)) {
        $errors['energomax_pdf_only'] = __('Only PDF files are allowed for datasheet uploads.', 'energomax-core');
    }

    return $errors;
}
add_filter('acf/upload_prefilter', 'energomax_restrict_pdf_upload');

/**
 * Global upload MIME restriction when uploading via ACF datasheet context.
 *
 * @param array<string, string> $mimes
 * @return array<string, string>
 */
function energomax_acf_upload_mimes(array $mimes): array
{
    if (is_admin() && isset($_POST['action']) && $_POST['action'] === 'acf/upload_attachment') {
        return ['pdf' => 'application/pdf'];
    }
    return $mimes;
}
add_filter('upload_mimes', 'energomax_acf_upload_mimes', 20);

/**
 * Block direct REST API user enumeration for unauthenticated requests.
 */
function energomax_restrict_rest_users(): void
{
    if (!is_user_logged_in()) {
        add_filter('rest_endpoints', static function (array $endpoints): array {
            if (isset($endpoints['/wp/v2/users'])) {
                unset($endpoints['/wp/v2/users']);
            }
            if (isset($endpoints['/wp/v2/users/(?P<id>[\d]+)'])) {
                unset($endpoints['/wp/v2/users/(?P<id>[\d]+)']);
            }
            return $endpoints;
        });
    }
}
add_action('rest_api_init', 'energomax_restrict_rest_users', 99);

/**
 * Add security headers on frontend.
 */
function energomax_security_headers(): void
{
    if (is_admin()) {
        return;
    }

    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}
add_action('send_headers', 'energomax_security_headers');
