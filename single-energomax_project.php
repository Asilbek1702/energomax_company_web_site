<?php
/**
 * Single: energomax_project
 */
defined( 'ABSPATH' ) || exit;

if ( ! have_posts() ) {
	wp_redirect( home_url( '/projects/' ) );
	exit;
}

the_post();
$post_id = get_the_ID();
$title   = get_the_title();
$thumb   = get_the_post_thumbnail_url( $post_id, 'full' );

function em_proj_field( string $key ): mixed {
	if ( function_exists( 'get_field' ) ) return get_field( $key );
	return get_post_meta( get_the_ID(), $key, true );
}

$client  = em_proj_field( 'client_name' );
$object  = em_proj_field( 'object_name' );
$power   = em_proj_field( 'supply_power' );
$year    = em_proj_field( 'supply_year' );
$country = em_proj_field( 'country' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo esc_html( $title ); ?> — <?php bloginfo( 'name' ); ?></title>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php em_header(); ?>

<div class="em-breadcrumb">
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Главная</a> /
	<a href="<?php echo esc_url( home_url( '/projects/' ) ); ?>">Проекты</a> /
	<?php echo esc_html( $title ); ?>
</div>

<main class="em-single">
	<?php if ( $thumb ) : ?>
	<div style="border-radius:18px;overflow:hidden;margin-bottom:48px;max-height:480px;">
		<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $title ); ?>" style="width:100%;object-fit:cover;">
	</div>
	<?php endif; ?>

	<div class="em-single-grid">
		<div>
			<div class="em-single-category">Проект / Поставка</div>
			<?php energomax_the_page_h1(); ?>

			<?php if ( get_the_content() ) : ?>
			<div class="em-single-content" style="margin-top:24px;">
				<?php the_content(); ?>
			</div>
			<?php endif; ?>
		</div>

		<div>
			<div style="background:var(--ice);border-radius:18px;padding:32px;">
				<h3 style="font-size:18px;margin-bottom:20px;">Детали проекта</h3>
				<table class="em-specs-table">
					<?php if ( $client ) : ?><tr><td>Заказчик</td><td><?php echo esc_html( $client ); ?></td></tr><?php endif; ?>
					<?php if ( $object ) : ?><tr><td>Объект</td><td><?php echo esc_html( $object ); ?></td></tr><?php endif; ?>
					<?php if ( $power )  : ?><tr><td>Мощность</td><td><?php echo esc_html( $power ); ?></td></tr><?php endif; ?>
					<?php if ( $year )   : ?><tr><td>Год поставки</td><td><?php echo esc_html( (string) $year ); ?></td></tr><?php endif; ?>
					<?php if ( $country ): ?><tr><td>Страна</td><td><?php echo esc_html( $country ); ?></td></tr><?php endif; ?>
				</table>

				<div style="margin-top:24px;">
					<a href="<?php echo esc_url( home_url( '/contacts/' ) ); ?>#quote" class="em-btn-primary">Запросить похожий проект</a>
				</div>
			</div>
		</div>
	</div>
</main>

<?php em_footer(); ?>
<?php wp_footer(); ?>
</body>
</html>
