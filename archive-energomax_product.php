<?php
/**
 * Archive: energomax_product
 */
defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Продукция — <?php bloginfo( 'name' ); ?></title>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php em_header(); ?>

<div class="em-breadcrumb">
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Главная</a> / Продукция
</div>

<main class="em-archive">
	<div class="em-archive-header">
		<h1>Каталог продукции</h1>
		<p style="color:var(--grey);font-size:15px;margin-top:8px;">Силовые трансформаторы, КТП, щитовое оборудование и LED-освещение</p>
	</div>

	<!-- Category filters -->
	<div class="em-filters">
		<button class="em-filter-btn active" data-category="">Все категории</button>
		<button class="em-filter-btn" data-category="transformers">Трансформаторы</button>
		<button class="em-filter-btn" data-category="ktp">КТП / Подстанции</button>
		<button class="em-filter-btn" data-category="switchgear">Щитовое оборудование</button>
		<button class="em-filter-btn" data-category="led">LED-освещение</button>
	</div>

	<div class="em-product-grid" id="em-product-grid">
		<div class="em-loading">Загрузка продуктов...</div>
	</div>

	<div id="em-load-more-wrap" style="text-align:center;margin-top:40px;display:none;">
		<button id="em-load-more" class="em-btn-primary" style="background:var(--space);">Загрузить ещё</button>
	</div>
</main>

<?php em_divider(); ?>

<!-- Quick CTA -->
<section class="em-cta-band" id="quote" style="padding:60px 6%;">
	<h2 style="font-size:32px;">Нужна консультация по подбору?</h2>
	<p style="color:#8C96B5;margin:12px auto 28px;max-width:500px;">Опишите параметры объекта — подберём оптимальную модель за 1 рабочий день.</p>
	<?php em_quote_form(); ?>
</section>

<?php em_footer(); ?>

<script>
(function(){
	var restBase = '<?php echo esc_js( rest_url( 'energomax/v1/products' ) ); ?>';
	var grid     = document.getElementById('em-product-grid');
	var loadWrap = document.getElementById('em-load-more-wrap');
	var loadBtn  = document.getElementById('em-load-more');
	var currentPage = 1;
	var currentCat  = '';

	function productCard(p) {
		var specs = p.specs || {};
		var specLine = '';
		if (specs.voltage_class) specLine += specs.voltage_class;
		if (specs.power_kva)     specLine += (specLine ? ' / ' : '') + specs.power_kva + ' кВА';
		if (specs.power_watt)    specLine += (specLine ? ' / ' : '') + specs.power_watt + ' Вт';
		if (specs.voltage_hv)    specLine += (specLine ? ' / ' : '') + specs.voltage_hv;

		var price = specs.price_range || (specs.price_from ? 'от ' + specs.price_from + ' ' + (specs.currency || 'UZS') : 'По запросу');

		var thumb = p.thumbnail_url
			? '<img src="' + p.thumbnail_url + '" alt="' + p.title + '" style="width:100%;height:180px;object-fit:cover;border-radius:12px 12px 0 0;margin-bottom:20px;">'
			: '<div style="height:120px;border-radius:12px;background:var(--space-2);margin-bottom:20px;display:flex;align-items:center;justify-content:center;font-size:40px;opacity:.2;">⚡</div>';

		return '<div class="em-product-card">'
			+ thumb
			+ (specLine ? '<div class="pc-spec">' + specLine + '</div>' : '')
			+ '<h3>' + p.title + '</h3>'
			+ (p.excerpt ? '<p>' + p.excerpt + '</p>' : '<p style="flex-grow:1;"></p>')
			+ '<div style="margin-top:auto;">'
			+ '<div style="font-weight:700;color:#fff;margin-bottom:16px;font-family:\'Courier New\',monospace;">' + price + '</div>'
			+ '<a class="em-pc-link" href="' + p.permalink + '">Подробнее <span class="em-pc-arrow">→</span></a>'
			+ '</div></div>';
	}

	function fetchProducts(page, category, append) {
		var url = restBase + '?per_page=9&page=' + page;
		if (category) url += '&category=' + encodeURIComponent(category);

		if (!append) {
			grid.innerHTML = '<div class="em-loading">Загрузка...</div>';
			loadWrap.style.display = 'none';
		}

		fetch(url)
			.then(function(r){ return r.json(); })
			.then(function(data){
				if (!data.items || !data.items.length) {
					if (!append) grid.innerHTML = '<div class="em-loading">Продукты не найдены.</div>';
					return;
				}
				if (!append) grid.innerHTML = '';
				data.items.forEach(function(p){
					grid.insertAdjacentHTML('beforeend', productCard(p));
				});
				loadWrap.style.display = (data.pages > page) ? 'block' : 'none';
			})
			.catch(function(){
				grid.innerHTML = '<div class="em-loading">Ошибка загрузки.</div>';
			});
	}

	// Init: check URL param
	var urlCat = new URLSearchParams(window.location.search).get('category') || '';
	currentCat = urlCat;
	if (urlCat) {
		document.querySelectorAll('.em-filter-btn').forEach(function(btn){
			btn.classList.toggle('active', btn.dataset.category === urlCat);
		});
	}
	fetchProducts(1, currentCat, false);

	// Filters
	document.querySelectorAll('.em-filter-btn').forEach(function(btn){
		btn.addEventListener('click', function(){
			document.querySelectorAll('.em-filter-btn').forEach(function(b){ b.classList.remove('active'); });
			btn.classList.add('active');
			currentCat = btn.dataset.category;
			currentPage = 1;
			fetchProducts(1, currentCat, false);
		});
	});

	// Load more
	loadBtn && loadBtn.addEventListener('click', function(){
		currentPage++;
		fetchProducts(currentPage, currentCat, true);
	});
})();
</script>

<?php wp_footer(); ?>
</body>
</html>
