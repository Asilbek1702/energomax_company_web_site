<?php
/**
 * Energomax Group child theme functions.
 */

defined( 'ABSPATH' ) || exit;

/* ── Enqueue ── */
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style(
		'energomax-main',
		get_stylesheet_directory_uri() . '/assets/css/main.css',
		[ 'parent-style' ],
		'1.0.0'
	);
	wp_enqueue_script(
		'energomax-main-js',
		get_stylesheet_directory_uri() . '/assets/js/main.js',
		[],
		'1.0.0',
		true
	);
} );

/* ── Nav menus ── */
add_action( 'after_setup_theme', function () {
	register_nav_menus( [
		'primary' => __( 'Primary Navigation', 'energomax-group' ),
	] );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
} );

/* ── Header template tag ── */
function em_header(): void { ?>
<header class="em-header">
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="em-logo">
		<div class="em-logo-mark">E</div>
		<span>ENERGOMAX</span>
	</a>

	<?php if ( has_nav_menu( 'primary' ) ) : ?>
	<nav>
		<ul class="em-nav">
			<?php wp_nav_menu( [
				'theme_location' => 'primary',
				'container'      => false,
				'items_wrap'     => '%3$s',
				'walker'         => new Em_Nav_Walker(),
			] ); ?>
		</ul>
	</nav>
	<?php else : ?>
	<nav>
		<ul class="em-nav">
			<li><a href="<?php echo esc_url( home_url( '/products/' ) ); ?>">Продукция</a></li>
			<li><a href="<?php echo esc_url( home_url( '/projects/' ) ); ?>">Проекты</a></li>
			<li><a href="<?php echo esc_url( home_url( '/certification/' ) ); ?>">Сертификация</a></li>
			<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">О компании</a></li>
			<li><a href="<?php echo esc_url( home_url( '/contacts/' ) ); ?>">Контакты</a></li>
		</ul>
	</nav>
	<?php endif; ?>

	<div style="display:flex;align-items:center;gap:14px;">
		<a href="<?php echo esc_url( home_url( '/contacts/' ) ); ?>#quote" class="em-header-cta">Запросить расчёт</a>
		<button class="em-burger" id="em-burger" aria-label="Меню">
			<span></span><span></span><span></span>
		</button>
	</div>
</header>
<nav class="em-mobile-nav" id="em-mobile-nav">
	<a href="<?php echo esc_url( home_url( '/products/' ) ); ?>">Продукция</a>
	<a href="<?php echo esc_url( home_url( '/projects/' ) ); ?>">Проекты</a>
	<a href="<?php echo esc_url( home_url( '/certification/' ) ); ?>">Сертификация</a>
	<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">О компании</a>
	<a href="<?php echo esc_url( home_url( '/contacts/' ) ); ?>">Контакты</a>
</nav>
<?php }

/* ── Footer template tag ── */
function em_footer(): void { ?>
<footer class="em-footer">
	<div class="em-footer-grid">
		<div>
			<h5>Energomax Group</h5>
			<p>Производство трансформаторов, КТП, щитового оборудования и LED-освещения в Ташкенте.</p>
		</div>
		<div>
			<h5>Продукция</h5>
			<a href="<?php echo esc_url( home_url( '/products/?category=transformers' ) ); ?>">Трансформаторы</a>
			<a href="<?php echo esc_url( home_url( '/products/?category=ktp' ) ); ?>">Подстанции</a>
			<a href="<?php echo esc_url( home_url( '/products/?category=switchgear' ) ); ?>">Щитовое оборудование</a>
			<a href="<?php echo esc_url( home_url( '/products/?category=led' ) ); ?>">LED-освещение</a>
		</div>
		<div>
			<h5>Компания</h5>
			<a href="<?php echo esc_url( home_url( '/projects/' ) ); ?>">Проекты</a>
			<a href="<?php echo esc_url( home_url( '/certification/' ) ); ?>">Сертификация</a>
			<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">О компании</a>
		</div>
		<div>
			<h5>Контакты</h5>
			<a href="tel:+998712927711">+998 71 292 77 11</a>
			<a href="mailto:info@energomaxgroup.com">info@energomaxgroup.com</a>
			<p>Ташкент, Узбекистан</p>
		</div>
	</div>
	<div class="em-footer-bottom">
		<span>© <?php echo esc_html( gmdate( 'Y' ) ); ?> Energomax Group</span>
		<a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>">Политика конфиденциальности</a>
	</div>
</footer>
<?php }

/* ── Divider ── */
function em_divider(): void {
	echo '<div class="em-divider"></div>';
}

/* ── Quote form ── */
function em_quote_form( string $product_name = '' ): void { ?>
<div class="em-quote-section">
	<h3>Запросить цену</h3>
	<form class="energomax-rest-quote-form">
		<div class="em-form-row">
			<div class="em-form-group">
				<label>Имя *</label>
				<input type="text" name="name" required placeholder="Иван Иванов">
			</div>
			<div class="em-form-group">
				<label>Телефон *</label>
				<input type="tel" name="phone" required placeholder="+998 90 123 45 67">
			</div>
		</div>
		<div class="em-form-group">
			<label>Email *</label>
			<input type="email" name="email" required placeholder="ivan@company.com">
		</div>
		<input type="hidden" name="product_name" value="<?php echo esc_attr( $product_name ); ?>">
		<div class="em-form-group">
			<label>Комментарий / параметры объекта</label>
			<textarea name="comment" placeholder="Опишите требования к оборудованию..."></textarea>
		</div>
		<div class="em-form-message"></div>
		<button type="submit" class="em-btn-primary">Отправить заявку</button>
	</form>
</div>
<?php }

/* ── Minimal nav walker ── */
class Em_Nav_Walker extends Walker_Nav_Menu {
	public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
		$output .= '<li><a href="' . esc_url( $data_object->url ) . '">' . esc_html( $data_object->title ) . '</a></li>';
	}
}

/* ── Remove TwentyTwentyFive header/footer blocks ── */
add_filter( 'template_include', function ( $template ) {
	// Only override when our custom templates exist.
	return $template;
} );
