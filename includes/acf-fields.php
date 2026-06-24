<?php
/**
 * ACF local field groups and JSON sync path.
 *
 * @package EnergomaxCore
 */

defined('ABSPATH') || exit;

/**
 * Set ACF JSON save/load paths.
 */
function energomax_acf_json_save_point(string $path): string
{
    return ENERGOMAX_CORE_PATH . 'acf-json';
}
add_filter('acf/settings/save_json', 'energomax_acf_json_save_point');

/**
 * @param array<int, string> $paths
 * @return array<int, string>
 */
function energomax_acf_json_load_point(array $paths): array
{
    $paths[] = ENERGOMAX_CORE_PATH . 'acf-json';
    return $paths;
}
add_filter('acf/settings/load_json', 'energomax_acf_json_load_point');

/**
 * Register all ACF field groups locally.
 */
function energomax_register_acf_field_groups(): void
{
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group([
        'key'      => 'group_energomax_transformer',
        'title'    => 'Transformer',
        'fields'   => [
            [
                'key'   => 'field_em_voltage_class',
                'label' => 'Voltage Class',
                'name'  => 'voltage_class',
                'type'  => 'text',
                'instructions' => 'e.g. 10 кВ',
            ],
            [
                'key'   => 'field_em_power_kva',
                'label' => 'Power (kVA)',
                'name'  => 'power_kva',
                'type'  => 'number',
            ],
            [
                'key'           => 'field_em_cooling_type',
                'label'         => 'Cooling Type',
                'name'          => 'cooling_type',
                'type'          => 'select',
                'choices'       => [
                    'масляный' => 'масляный',
                    'сухой'    => 'сухой',
                ],
                'return_format' => 'value',
            ],
            [
                'key'   => 'field_em_protection_ip',
                'label' => 'Protection IP',
                'name'  => 'protection_ip',
                'type'  => 'text',
            ],
            [
                'key'   => 'field_em_climate_version',
                'label' => 'Climate Version',
                'name'  => 'climate_version',
                'type'  => 'text',
            ],
            [
                'key'   => 'field_em_price_range',
                'label' => 'Price Range',
                'name'  => 'price_range',
                'type'  => 'text',
                'placeholder' => 'По запросу',
            ],
            [
                'key'           => 'field_em_datasheet_pdf',
                'label'         => 'Datasheet PDF',
                'name'          => 'datasheet_pdf',
                'type'          => 'file',
                'return_format' => 'array',
                'mime_types'    => 'pdf',
            ],
        ],
        'location' => [
            [
                [
                    'param'    => 'post_taxonomy',
                    'operator' => '==',
                    'value'    => 'product_category:transformers',
                ],
            ],
        ],
        'position' => 'normal',
        'style'    => 'default',
        'active'   => true,
    ]);

    acf_add_local_field_group([
        'key'      => 'group_energomax_ktp',
        'title'    => 'KTP / Substation',
        'fields'   => [
            [
                'key'   => 'field_em_ktp_type',
                'label' => 'KTP Type',
                'name'  => 'ktp_type',
                'type'  => 'text',
                'placeholder' => 'КТПС / ГКТП / БКТП',
            ],
            [
                'key'   => 'field_em_voltage_hv',
                'label' => 'HV Voltage',
                'name'  => 'voltage_hv',
                'type'  => 'text',
            ],
            [
                'key'   => 'field_em_voltage_lv',
                'label' => 'LV Voltage',
                'name'  => 'voltage_lv',
                'type'  => 'text',
            ],
            [
                'key'   => 'field_em_transformer_power',
                'label' => 'Transformer Power',
                'name'  => 'transformer_power',
                'type'  => 'number',
            ],
            [
                'key'           => 'field_em_installation_type',
                'label'         => 'Installation Type',
                'name'          => 'installation_type',
                'type'          => 'select',
                'choices'       => [
                    'наружное'  => 'наружное',
                    'внутреннее' => 'внутреннее',
                ],
                'return_format' => 'value',
            ],
            [
                'key'   => 'field_em_ktp_price_range',
                'label' => 'Price Range',
                'name'  => 'price_range',
                'type'  => 'text',
            ],
            [
                'key'           => 'field_em_ktp_datasheet_pdf',
                'label'         => 'Datasheet PDF',
                'name'          => 'datasheet_pdf',
                'type'          => 'file',
                'return_format' => 'array',
                'mime_types'    => 'pdf',
            ],
        ],
        'location' => [
            [
                [
                    'param'    => 'post_taxonomy',
                    'operator' => '==',
                    'value'    => 'product_category:ktp',
                ],
            ],
        ],
        'position' => 'normal',
        'style'    => 'default',
        'active'   => true,
    ]);

    acf_add_local_field_group([
        'key'      => 'group_energomax_switchgear',
        'title'    => 'Switchgear',
        'fields'   => [
            [
                'key'   => 'field_em_panel_type',
                'label' => 'Panel Type',
                'name'  => 'panel_type',
                'type'  => 'text',
                'placeholder' => 'ЩО-70 / ГРЩ / ВРУ / АВР',
            ],
            [
                'key'   => 'field_em_sections_count',
                'label' => 'Sections Count',
                'name'  => 'sections_count',
                'type'  => 'number',
            ],
            [
                'key'   => 'field_em_rated_current',
                'label' => 'Rated Current',
                'name'  => 'rated_current',
                'type'  => 'number',
            ],
            [
                'key'   => 'field_em_switchgear_price_range',
                'label' => 'Price Range',
                'name'  => 'price_range',
                'type'  => 'text',
            ],
            [
                'key'           => 'field_em_switchgear_datasheet_pdf',
                'label'         => 'Datasheet PDF',
                'name'          => 'datasheet_pdf',
                'type'          => 'file',
                'return_format' => 'array',
                'mime_types'    => 'pdf',
            ],
        ],
        'location' => [
            [
                [
                    'param'    => 'post_taxonomy',
                    'operator' => '==',
                    'value'    => 'product_category:switchgear',
                ],
            ],
        ],
        'position' => 'normal',
        'style'    => 'default',
        'active'   => true,
    ]);

    acf_add_local_field_group([
        'key'      => 'group_energomax_led',
        'title'    => 'LED Lighting',
        'fields'   => [
            [
                'key'   => 'field_em_power_watt',
                'label' => 'Power (W)',
                'name'  => 'power_watt',
                'type'  => 'number',
            ],
            [
                'key'   => 'field_em_lumens',
                'label' => 'Lumens',
                'name'  => 'lumens',
                'type'  => 'number',
            ],
            [
                'key'   => 'field_em_led_protection_ip',
                'label' => 'Protection IP',
                'name'  => 'protection_ip',
                'type'  => 'text',
            ],
            [
                'key'   => 'field_em_color_temp_k',
                'label' => 'Color Temp (K)',
                'name'  => 'color_temp_k',
                'type'  => 'number',
            ],
            [
                'key'   => 'field_em_lifespan_hours',
                'label' => 'Lifespan (hours)',
                'name'  => 'lifespan_hours',
                'type'  => 'number',
            ],
            [
                'key'   => 'field_em_price_from',
                'label' => 'Price From',
                'name'  => 'price_from',
                'type'  => 'number',
            ],
            [
                'key'           => 'field_em_currency',
                'label'         => 'Currency',
                'name'          => 'currency',
                'type'          => 'select',
                'choices'       => [
                    'UZS' => 'UZS',
                    'USD' => 'USD',
                ],
                'return_format' => 'value',
            ],
            [
                'key'           => 'field_em_led_datasheet_pdf',
                'label'         => 'Datasheet PDF',
                'name'          => 'datasheet_pdf',
                'type'          => 'file',
                'return_format' => 'array',
                'mime_types'    => 'pdf',
            ],
        ],
        'location' => [
            [
                [
                    'param'    => 'post_taxonomy',
                    'operator' => '==',
                    'value'    => 'product_category:led',
                ],
            ],
        ],
        'position' => 'normal',
        'style'    => 'default',
        'active'   => true,
    ]);

    acf_add_local_field_group([
        'key'      => 'group_energomax_project_case',
        'title'    => 'Project Case',
        'fields'   => [
            [
                'key'   => 'field_em_client_name',
                'label' => 'Client Name',
                'name'  => 'client_name',
                'type'  => 'text',
            ],
            [
                'key'   => 'field_em_object_name',
                'label' => 'Object Name',
                'name'  => 'object_name',
                'type'  => 'text',
            ],
            [
                'key'   => 'field_em_supply_power',
                'label' => 'Supply Power',
                'name'  => 'supply_power',
                'type'  => 'text',
            ],
            [
                'key'   => 'field_em_supply_year',
                'label' => 'Supply Year',
                'name'  => 'supply_year',
                'type'  => 'number',
            ],
            [
                'key'   => 'field_em_country',
                'label' => 'Country',
                'name'  => 'country',
                'type'  => 'text',
            ],
        ],
        'location' => [
            [
                [
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'energomax_project',
                ],
            ],
        ],
        'position' => 'normal',
        'style'    => 'default',
        'active'   => true,
    ]);
}
add_action('acf/init', 'energomax_register_acf_field_groups');

/**
 * Restrict datasheet PDF uploads to own domain on save.
 *
 * @param mixed $value
 * @return mixed
 */
function energomax_validate_datasheet_domain($value)
{
    if (empty($value) || !is_array($value)) {
        return $value;
    }

    $url  = $value['url'] ?? '';
    $host = wp_parse_url($url, PHP_URL_HOST);
    $site = wp_parse_url(home_url(), PHP_URL_HOST);

    if ($host && $site && strtolower($host) !== strtolower((string) $site)) {
        return null;
    }

    return $value;
}

foreach (
    [
        'field_em_datasheet_pdf',
        'field_em_ktp_datasheet_pdf',
        'field_em_switchgear_datasheet_pdf',
        'field_em_led_datasheet_pdf',
    ] as $field_key
) {
    add_filter('acf/update_value/key=' . $field_key, 'energomax_validate_datasheet_domain');
}
