<?php
/**
 * Single: energomax_product
 */
defined( 'ABSPATH' ) || exit;

if ( ! have_posts() ) {
	wp_redirect( home_url( '/products/' ) );
	exit;
}

the_post();
$post_id  = get_the_ID();
$title    = get_the_title();
$content  = get_the_content();
$thumb    = get_the_post_thumbnail_url( $post_id, 'full' );

// ACF specs helper.
function em_field( string $key ): mixed {
	if ( function_exists( 'get_field' ) ) {
		return get_field( $key );
	}
	return get_post_meta( get_the_ID(), $key, true );
}

$terms    = get_the_terms( $post_id, 'product_category' );
$category = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';
$cat_slug = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->slug : '';

// Category-specific specs.
$specs = [];
switch ( $cat_slug ) {
	case 'transformers':
		if ( $v = em_field( 'voltage_class' ) )   $specs['Класс напряжения']  = $v . ' кВ';
		if ( $v = em_field( 'power_kva' ) )        $specs['Мощность']          = $v . ' кВА';
		if ( $v = em_field( 'cooling_type' ) )     $specs['Тип охлаждения']    = $v;
		if ( $v = em_field( 'protection_ip' ) )    $specs['Степень защиты']    = 'IP' . $v;
		if ( $v = em_field( 'climate_version' ) )  $specs['Климатическое исполнение'] = $v;
		break;
	case 'ktp':
		if ( $v = em_field( 'ktp_type' ) )          $specs['Тип КТП']           = $v;
		if ( $v = em_field( 'voltage_hv' ) )        $specs['Напряжение ВН']     = $v . ' кВ';
		if ( $v = em_field( 'voltage_lv' ) )        $specs['Напряжение НН']     = $v . ' кВ';
		if ( $v = em_field( 'transformer_power' ) ) $specs['Мощность тр-ра']    = $v . ' кВА';
		if ( $v = em_field( 'installation_type' ) ) $specs['Исполнение']        = $v;
		break;
	case 'switchgear':
		if ( $v = em_field( 'panel_type' ) )      $specs['Тип щита']          = $v;
		if ( $v = em_field( 'sections_count' ) )  $specs['Секций / вводов']   = $v;
		if ( $v = em_field( 'rated_current' ) )   $specs['Номинальный ток']   = $v . ' А';
		break;
	case 'led':
		if ( $v = em_field( 'power_watt' ) )      $specs['Мощность']          = $v . ' Вт';
		if ( $v = em_field( 'lumens' ) )           $specs['Световой поток']    = $v . ' Лм';
		if ( $v = em_field( 'protection_ip' ) )   $specs['Степень защиты']    = 'IP' . $v;
		if ( $v = em_field( 'color_temp_k' ) )    $specs['Цветовая температура'] = $v . ' K';
		if ( $v = em_field( 'lifespan_hours' ) )  $specs['Срок службы']       = number_format( (int) $v ) . ' ч';
		break;
}

$price_range = em_field( 'price_range' );
$price_from  = em_field( 'price_from' );
$currency    = em_field( 'currency' ) ?: 'UZS';
$datasheet   = em_field( 'datasheet_pdf' );
$pdf_url     = is_array( $datasheet ) ? ( $datasheet['url'] ?? '' ) : '';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo esc_html( $title ); ?> — <?php bloginfo( 'name' ); ?></title>
<?php wp_head(); ?>
</head>
<body <?php body_class( 'single-energomax_product' ); ?> data-product-name="<?php echo esc_attr( $title ); ?>">
<?php wp_body_open(); ?>

<?php em_header(); ?>

<div class="em-breadcrumb">
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Главная</a> /
	<a href="<?php echo esc_url( home_url( '/products/' ) ); ?>">Продукция</a>
	<?php if ( $category ) : ?> / <a href="<?php echo esc_url( home_url( '/products/?category=' . $cat_slug ) ); ?>"><?php echo esc_html( $category ); ?></a><?php endif; ?>
	/ <?php echo esc_html( $title ); ?>
</div>

<main class="em-single">
	<div class="em-single-grid">
		<!-- Image -->
		<div>
			<div class="em-single-image">
				<?php if ( $thumb ) : ?>
					<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $title ); ?>">
				<?php else : ?>
					<span class="em-no-img">⚡</span>
				<?php endif; ?>
			</div>
		</div>

		<!-- Meta -->
		<div class="em-single-meta">
			<?php if ( $category ) : ?>
			<div class="em-single-category"><?php echo esc_html( $category ); ?></div>
			<?php endif; ?>

			<?php energomax_the_page_h1(); ?>

			<?php if ( ! empty( $specs ) ) : ?>
			<table class="em-specs-table">
				<?php foreach ( $specs as $label => $value ) : ?>
				<tr>
					<td><?php echo esc_html( $label ); ?></td>
					<td><?php echo esc_html( (string) $value ); ?></td>
				</tr>
				<?php endforeach; ?>
				<?php if ( $price_from ) : ?>
				<tr>
					<td>Цена от</td>
					<td style="color:var(--electric);"><?php echo esc_html( number_format( (int) $price_from ) . ' ' . $currency ); ?></td>
				</tr>
				<?php elseif ( $price_range ) : ?>
				<tr>
					<td>Цена</td>
					<td style="color:var(--electric);"><?php echo esc_html( $price_range ); ?></td>
				</tr>
				<?php endif; ?>
			</table>
			<?php endif; ?>

			<?php if ( $pdf_url ) : ?>
			<a href="<?php echo esc_url( $pdf_url ); ?>" class="em-pdf-link" target="_blank" rel="noopener">
				📄 Скачать спецификацию PDF
			</a>
			<?php endif; ?>

			<a href="#em-quote" class="em-btn-primary">Запросить цену</a>
		</div>
	</div>

	<?php if ( $content ) : ?>
	<div class="em-single-content">
		<?php the_content(); ?>
	</div>
	<?php endif; ?>

	<!-- Quote form -->
	<div id="em-quote">
		<?php em_quote_form( $title ); ?>
	</div>
</main>

<?php em_footer(); ?>
<?php wp_footer(); ?>
</body>
</html>
