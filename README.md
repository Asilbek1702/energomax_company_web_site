# Energomax Core

WordPress backend plugin for [energomaxgroup.com](https://energomaxgroup.com) — corporate site of Energomax Group (power transformers, KTP substations, switchgear, LED lighting).

## Requirements

- WordPress 6.0+
- PHP 8.1+
- [Advanced Custom Fields](https://wordpress.org/plugins/advanced-custom-fields/) (ACF) — required
- [Contact Form 7](https://wordpress.org/plugins/contact-form-7/) — recommended for quote form
- [Polylang](https://wordpress.org/plugins/polylang/) or [WPML](https://wpml.org/) — for RU/UZ multilingual

## Installation

1. Copy `energomax-core/` to `wp-content/plugins/`
2. Activate **Energomax Core** in WordPress admin → Plugins
3. Install and activate ACF, Contact Form 7, and Polylang (or WPML)
4. Go to **Settings → Permalinks** and click Save (flush rewrite rules)
5. Configure **Settings → Energomax** (Telegram webhook, CF7 form ID)

## Plugin Structure

```
energomax-core/
├── energomax-core.php          # Main plugin bootstrap
├── includes/
│   ├── cpt.php                 # Custom post types
│   ├── taxonomies.php          # product_category taxonomy
│   ├── acf-fields.php          # ACF local field groups
│   ├── rest-api.php            # REST endpoints
│   ├── form-handler.php        # CF7 + Telegram + email
│   ├── seo.php                 # Schema.org, titles, meta
│   ├── admin-columns.php       # Admin list columns
│   ├── quick-guide.php         # Editor quick guide page
│   ├── performance.php         # Image sizes, script defer
│   ├── multilingual.php        # Polylang / WPML hooks
│   └── security.php            # Rate limit, PDF restriction
├── acf-json/                   # ACF field group JSON sync
├── assets/js/quote-form.js     # Product name auto-fill, REST form
├── cf7-form-template.txt       # CF7 form markup template
└── README.md
```

## Custom Post Types

| CPT | Slug | Archive |
|-----|------|---------|
| `energomax_product` | `/products/` | Yes |
| `energomax_project` | `/projects/` | Yes |

## Taxonomy

`product_category` (hierarchical) — terms: `transformers`, `ktp`, `switchgear`, `led`

ACF field groups appear based on assigned category.

## REST API

Base URL: `/wp-json/energomax/v1/`

### GET `/products`

Query parameters:

| Param | Type | Description |
|-------|------|-------------|
| `category` | string | Category slug |
| `min_power` | number | Minimum power (kVA/W) |
| `max_power` | number | Maximum power |
| `voltage_class` | string | Voltage class filter |
| `per_page` | int | Items per page (default 20) |
| `page` | int | Page number |

Response:

```json
{
  "items": [
    {
      "id": 42,
      "title": "ТМГ-1000",
      "category": "transformers",
      "specs": { "voltage_class": "10 кВ", "power_kva": 1000 },
      "thumbnail_url": "https://...",
      "permalink": "https://...",
      "excerpt": "..."
    }
  ],
  "total": 1,
  "pages": 1
}
```

### GET `/projects`

Returns: `id`, `title`, `client`, `object`, `power`, `year`, `country`, `thumbnail_url`, `permalink`

### POST `/quote`

Body (JSON):

```json
{
  "name": "Иван",
  "phone": "+998901234567",
  "email": "ivan@example.com",
  "product_name": "ТМГ-1000",
  "comment": "Нужен КП",
  "nonce": "<wp_nonce>"
}
```

Rate limit: 10 requests/minute per IP.

Nonce action: `energomax_quote` — available in `window.energomaxRest.nonce` when `quote-form.js` is enqueued.

## Contact Form 7 Setup

1. Create a new CF7 form using markup from `cf7-form-template.txt`
2. Set mail recipient to `sales@energomaxgroup.com`
3. Note the form ID → **Settings → Energomax → Contact Form 7 ID**
4. Embed form: `[contact-form-7 id="123" title="Quote"]`

The hidden `product_name` field auto-fills from:
- `data-product-name` on `<body>`
- `?product=` URL parameter
- Product page H1 on single product templates

## Telegram Notifications

Set webhook URL at **Settings → Energomax**. Example Telegram Bot API URL:

```
https://api.telegram.org/bot<TOKEN>/sendMessage?chat_id=<CHAT_ID>
```

Or use a custom middleware webhook that accepts JSON:

```json
{
  "text": "message",
  "parse_mode": "Markdown",
  "payload": { "name": "...", "phone": "..." }
}
```

## SEO

- **Product title pattern:** `{Name} — {Voltage} {Power} кВА | Energomax`
- **Meta description:** excerpt or first 155 chars of content
- **Schema.org:** Organization (all pages), Product (product singles)
- **Sitemap:** CPTs and `product_category` included in WP core sitemap
- **H1:** use `energomax_the_page_h1()` in theme; extra H1s stripped from content

## Image Sizes

| Name | Dimensions |
|------|------------|
| `energomax-card` | 600×400 |
| `energomax-card-sm` | 300×200 |

WebP uploads enabled. Cache headers snippet written to `uploads/energomax-cache/htaccess-snippet.txt`.

## Multilingual (RU / UZ)

Polylang (recommended free option):

1. Add languages: Russian (default), Uzbek
2. CPTs and `product_category` auto-registered as translatable
3. Language switcher preserves translated post URL

WPML: CPTs registered via `wpml_custom_post_type_support` filter.

## Admin

- **Products** list columns: Category, Voltage, Power, Price, PDF
- **Projects** list columns: Client, Year, Country
- **Settings → Energomax Quick Guide** — editor instructions

## Security

- Nonce verification on quote submissions (CF7 + REST)
- PDF-only uploads for datasheet fields
- REST POST rate limiting (10/min/IP)
- Security headers: `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`

## Theme Integration

Single product template — add to `<body>`:

```php
<body <?php body_class(); ?> data-product-name="<?php echo esc_attr(get_the_title()); ?>">
```

Render canonical H1:

```php
<?php energomax_the_page_h1(); ?>
```

REST quote form (vanilla JS, no jQuery):

```html
<form class="energomax-rest-quote-form">
  <input type="text" name="name" required>
  <input type="tel" name="phone" required>
  <input type="email" name="email" required>
  <input type="hidden" name="product_name">
  <textarea name="comment"></textarea>
  <button type="submit">Отправить</button>
  <div class="energomax-form-message" style="display:none;"></div>
</form>
```

## Export & Handoff

For full site export to client:

1. **Tools → Export** — select all content
2. Copy `wp-content/uploads/` (media + PDFs)
3. Export database via phpMyAdmin or `wp db export`
4. Transfer admin credentials
5. Document Telegram webhook and SMTP settings

No vendor lock-in — standard WordPress plugins and open plugin code.

## License

Proprietary — Energomax Group.
