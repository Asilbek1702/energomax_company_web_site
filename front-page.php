<?php
/**
 * Front page template.
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php em_header(); ?>

<!-- HERO -->
<section class="em-hero">
	<div class="em-hero-glow"></div>
	<div class="em-hero-kicker">
		<span class="dot"></span>
		ПРОИЗВОДСТВО · ТАШКЕНТ, УЗБЕКИСТАН
	</div>
	<h1>Питаем<br>промышленность<br><span class="outline">Узбекистана</span></h1>
	<div class="em-hero-sub">
		<div class="em-hero-lead">
			<p>Силовые трансформаторы, КТП и щитовое оборудование от 0,4 до 35 кВ. Расчёт мощности под ваш объект — за 1 рабочий день.</p>
			<div class="em-hero-ctas">
				<a class="em-btn-primary" href="<?php echo esc_url( home_url( '/products/' ) ); ?>">Подобрать оборудование</a>
				<a class="em-btn-ghost" href="#quote">Отправить заявку →</a>
			</div>
		</div>
		<div class="em-hero-stats">
			<div class="em-hstat">
				<div class="em-hstat-num">0.4–35</div>
				<div class="em-hstat-label">кВ напряжение</div>
			</div>
			<div class="em-hstat">
				<div class="em-hstat-num">6.3</div>
				<div class="em-hstat-label">МВА мощность</div>
			</div>
			<div class="em-hstat">
				<div class="em-hstat-num">24K</div>
				<div class="em-hstat-label">м² производство</div>
			</div>
		</div>
	</div>
</section>

<?php em_divider(); ?>

<!-- TRUST STRIP -->
<div class="em-trust">
	<div class="em-trust-label">Поставки для</div>
	<div class="em-trust-track">
		<span class="em-trust-item">O'ZBEKENERGO</span>
		<span class="em-trust-item">UZ TEMIR YO'LLARI</span>
		<span class="em-trust-item">UZBEKNEFTGAZ</span>
		<span class="em-trust-item">NAVOIY GMK</span>
		<span class="em-trust-item">UZBEKISTAN AIRWAYS</span>
		<span class="em-trust-item">O'ZBEKENERGO</span>
		<span class="em-trust-item">UZ TEMIR YO'LLARI</span>
	</div>
</div>

<!-- PRODUCT SECTION -->
<section class="em-section">
	<div class="em-section-head">
		<div>
			<div class="em-section-tag"><span class="bar"></span>Каталог продукции</div>
			<h2>Каталог продукции,<br>который говорит языком инженера</h2>
		</div>
		<div class="sub">Параметры, диапазоны и применение — на первом экране карточки.</div>
	</div>
	<div class="em-product-grid" id="em-home-products">
		<!-- Transformer -->
		<div class="em-product-card">
			<div class="pc-spec">0.4–35 кВ / 25–6300 кВА</div>
			<h3>Силовые трансформаторы</h3>
			<p>ТМГ, ECO, GEAFOL — масляные и сухие, для промышленных объектов и городских подстанций.</p>
			<div class="em-pc-tags">
				<span class="em-pc-tag">масляные</span>
				<span class="em-pc-tag">сухие</span>
				<span class="em-pc-tag">IP54</span>
			</div>
			<a class="em-pc-link" href="<?php echo esc_url( home_url( '/products/?category=transformers' ) ); ?>">
				Все модели <span class="em-pc-arrow">→</span>
			</a>
		</div>
		<!-- KTP -->
		<div class="em-product-card">
			<div class="pc-spec">6–10 кВ / под ключ</div>
			<h3>Трансформаторные подстанции</h3>
			<p>Блочные и столбовые КТП для распределения электроэнергии — с проектом и монтажом.</p>
			<div class="em-pc-tags">
				<span class="em-pc-tag">ГКТП</span>
				<span class="em-pc-tag">КТПС</span>
				<span class="em-pc-tag">БКТП</span>
			</div>
			<a class="em-pc-link" href="<?php echo esc_url( home_url( '/products/?category=ktp' ) ); ?>">
				Все модели <span class="em-pc-arrow">→</span>
			</a>
		</div>
		<!-- Switchgear -->
		<div class="em-product-card">
			<div class="pc-spec">до 1 кВ / типовые и инд.</div>
			<h3>Щитовое оборудование</h3>
			<p>ЩО-70, ГРЩ, ВРУ, АВР — сборка и монтаж по типовым и индивидуальным схемам.</p>
			<div class="em-pc-tags">
				<span class="em-pc-tag">типовые</span>
				<span class="em-pc-tag">индивидуальные</span>
			</div>
			<a class="em-pc-link" href="<?php echo esc_url( home_url( '/products/?category=switchgear' ) ); ?>">
				Все модели <span class="em-pc-arrow">→</span>
			</a>
		</div>
	</div>
</section>

<?php em_divider(); ?>

<!-- LED SECTION -->
<section class="em-section em-led-section">
	<div class="em-section-head">
		<div>
			<div class="em-section-tag"><span class="bar"></span>LED-освещение</div>
			<h2>LED-освещение со спецификацией на витрине</h2>
		</div>
		<div class="sub">Мощность, поток, IP-защита и цена — сравнение моделей в один клик.</div>
	</div>
	<div class="em-led-grid" id="em-home-led">
		<div class="em-led-card">
			<div class="em-led-thumb"><div class="em-led-bulb"></div></div>
			<div class="em-led-body">
				<h4>EMX Led Street Light 65W</h4>
				<div class="em-led-specs">
					<span class="em-led-chip">65W</span>
					<span class="em-led-chip">7800LM</span>
					<span class="em-led-chip">IP65</span>
				</div>
				<div class="em-led-price-row">
					<div class="em-led-price"><span>от</span>480 000 сум</div>
					<a href="<?php echo esc_url( home_url( '/products/?category=led' ) ); ?>" class="em-led-cta">Запросить</a>
				</div>
			</div>
		</div>
		<div class="em-led-card">
			<div class="em-led-thumb"><div class="em-led-bulb"></div></div>
			<div class="em-led-body">
				<h4>EMX Led Highway Light 270W</h4>
				<div class="em-led-specs">
					<span class="em-led-chip">270W</span>
					<span class="em-led-chip">32400LM</span>
					<span class="em-led-chip">IP66</span>
				</div>
				<div class="em-led-price-row">
					<div class="em-led-price"><span>от</span>1 650 000 сум</div>
					<a href="<?php echo esc_url( home_url( '/products/?category=led' ) ); ?>" class="em-led-cta">Запросить</a>
				</div>
			</div>
		</div>
		<div class="em-led-card">
			<div class="em-led-thumb"><div class="em-led-bulb"></div></div>
			<div class="em-led-body">
				<h4>EMX-FLOODLIGHT 1250W</h4>
				<div class="em-led-specs">
					<span class="em-led-chip">1250W</span>
					<span class="em-led-chip">150000LM</span>
					<span class="em-led-chip">IP66</span>
				</div>
				<div class="em-led-price-row">
					<div class="em-led-price"><span>от</span>6 200 000 сум</div>
					<a href="<?php echo esc_url( home_url( '/products/?category=led' ) ); ?>" class="em-led-cta">Запросить</a>
				</div>
			</div>
		</div>
	</div>
</section>

<?php em_divider(); ?>

<!-- GEO SECTION -->
<section class="em-geo">
	<div class="em-geo-head">
		<div class="em-section-tag" style="color:var(--electric);"><span class="bar" style="background:var(--electric);"></span>География поставок</div>
		<h2>Продукция работает на объектах в 12 странах</h2>
		<p>От Узбекистана и стран СНГ до Ближнего Востока и Юго-Восточной Азии.</p>
	</div>
	<div class="em-geo-grid">
		<div class="em-geo-map">
			<span class="em-geo-map-label">Карта поставок · live</span>
			<div class="em-geo-dots">
				<div class="em-geo-grid-bg"></div>
				<div class="em-geo-pin big" style="left:18%;top:62%;"></div>
				<div class="em-geo-pin" style="left:34%;top:30%;"></div>
				<div class="em-geo-pin" style="left:42%;top:22%;"></div>
				<div class="em-geo-pin" style="left:50%;top:46%;"></div>
				<div class="em-geo-pin" style="left:57%;top:18%;"></div>
				<div class="em-geo-pin" style="left:64%;top:54%;"></div>
				<div class="em-geo-pin" style="left:72%;top:34%;"></div>
				<div class="em-geo-pin" style="left:80%;top:60%;"></div>
				<div class="em-geo-pin" style="left:88%;top:28%;"></div>
				<div class="em-geo-pin" style="left:26%;top:48%;"></div>
				<div class="em-geo-pin" style="left:62%;top:72%;"></div>
				<div class="em-geo-pin" style="left:46%;top:66%;"></div>
			</div>
		</div>
		<div class="em-geo-stats">
			<div class="em-geo-stat">
				<div class="em-geo-stat-num">12</div>
				<div class="em-geo-stat-label">стран используют продукцию</div>
			</div>
			<div class="em-geo-stat">
				<div class="em-geo-stat-num">18</div>
				<div class="em-geo-stat-label">лет на рынке</div>
			</div>
			<div class="em-geo-stat">
				<div class="em-geo-stat-num">300+</div>
				<div class="em-geo-stat-label">постоянных клиентов</div>
			</div>
		</div>
	</div>
</section>

<?php em_divider(); ?>

<!-- CTA BAND -->
<section class="em-cta-band" id="quote">
	<h2>Опишите параметры объекта —<br>получите <span class="electric">расчёт за день</span></h2>
	<?php em_quote_form(); ?>
</section>

<?php em_footer(); ?>

<?php wp_footer(); ?>
</body>
</html>
<?php
// Prevent default template loading.
