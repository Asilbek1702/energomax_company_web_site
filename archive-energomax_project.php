<?php
/**
 * Archive: energomax_project
 */
defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Проекты и поставки — <?php bloginfo( 'name' ); ?></title>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php em_header(); ?>

<div class="em-breadcrumb">
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Главная</a> / Проекты
</div>

<main class="em-archive">
	<div class="em-archive-header">
		<h1>Проекты и поставки</h1>
		<p style="color:var(--grey);font-size:15px;margin-top:8px;">Завершённые объекты с указанием заказчика, мощности и географии</p>
	</div>

	<div class="em-project-grid" id="em-project-grid">
		<div class="em-loading">Загрузка проектов...</div>
	</div>
</main>

<?php em_footer(); ?>

<script>
(function(){
	var restBase = '<?php echo esc_js( rest_url( 'energomax/v1/projects' ) ); ?>';
	var grid = document.getElementById('em-project-grid');

	fetch(restBase + '?per_page=20')
		.then(function(r){ return r.json(); })
		.then(function(data){
			if (!data.items || !data.items.length) {
				grid.innerHTML = '<div class="em-loading">Проекты скоро появятся.</div>';
				return;
			}
			grid.innerHTML = '';
			data.items.forEach(function(p){
				var meta = [];
				if (p.year)    meta.push(p.year);
				if (p.country) meta.push(p.country);
				if (p.power)   meta.push(p.power);

				var thumb = p.thumbnail_url
					? '<img src="' + p.thumbnail_url + '" alt="' + p.title + '">'
					: '<div style="width:100%;height:200px;background:var(--space-2);display:flex;align-items:center;justify-content:center;font-size:48px;opacity:.15;">🏭</div>';

				grid.insertAdjacentHTML('beforeend',
					'<div class="em-project-card">'
					+ '<div class="em-project-thumb">' + thumb + '</div>'
					+ '<div class="em-project-body">'
					+ (meta.length ? '<div class="em-project-meta">' + meta.join(' · ') + '</div>' : '')
					+ '<h3>' + p.title + '</h3>'
					+ (p.client ? '<p>' + p.client + '</p>' : '')
					+ '<a href="' + p.permalink + '" style="display:inline-block;margin-top:14px;color:var(--electric);font-weight:700;font-size:13px;">Подробнее →</a>'
					+ '</div></div>'
				);
			});
		})
		.catch(function(){
			grid.innerHTML = '<div class="em-loading">Ошибка загрузки.</div>';
		});
})();
</script>

<?php wp_footer(); ?>
</body>
</html>
