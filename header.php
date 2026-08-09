<?php
$body_class = [];

if (TLTH::is_shop_notice_enabled()) {
	$body_class[] = 'shop-notice-enabled';
}
?>

<!doctype html>
<html lang="<?php echo LANG; ?>">

<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php is_front_page() ? the_title() : wp_title(''); ?> | <?php bloginfo('name'); ?></title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap" rel="stylesheet">

	<!-- <script defer src="https://umami.alextourigny.ca/script.js" data-website-id="969a3755-2087-4176-b405-9ab3e8327e2a"></script> -->
	<?php wp_head(); ?>
</head>

<body <?php body_class(implode(' ', $body_class)); ?>>

	<div class="shapes-container body-shapes<?= get_field('body_shapes_switched') ? ' switched' : '' ?> overflow-hidden">
		<div class="shape shape-zero">
			<?= file_get_contents(THEME_PATH . '/assets/images/shapes/zero.svg'); ?>
		</div>
		<div class="shape shape-zero">
			<?= file_get_contents(THEME_PATH . '/assets/images/shapes/zero.svg'); ?>
		</div>
		<div class="shape shape-zero">
			<?= file_get_contents(THEME_PATH . '/assets/images/shapes/zero.svg'); ?>
		</div>
	</div>

	<header id="header" class="">
		<?php include(THEME_PATH . '/includes/shop-notice.php'); ?>
		<div class="header-content max-w-content mx-auto px-8 flex gap-7 items-center justify-between py-2 md:py-4">

			<!-- Logo -->
			<a href="<?= pll_home_url(); ?>" id="site-logo" class="flex-shrink-0 h-auto w-[175px]">
				<?= file_get_contents(THEME_PATH . '/assets/images/tlth_logo.svg'); ?>
			</a>
			
			<!-- Right Side Actions -->
			<div id="site-nav" class="flex items-center gap-4">
				<nav id="primary-nav" class="main-nav hidden lg:flex items-center gap-8 flex-1 justify-center">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'container' => '',
							'menu_class' => 'flex items-center gap-8',
							'fallback_cb' => false
						)
					);
					?>
					<div class="mobile-nav-actions lg:hidden">
						<a href="<?= get_permalink(wc_get_page_id('shop')); ?>" class="shop-page-btn flex items-center gap-2 bg-deep-blue text-white px-6 py-2 rounded-full hover:bg-dark transition-colors duration-300">
							<span class="shop-page-btn__icon relative flex-shrink-0 w-[14px] h-4 overflow-visible" aria-hidden="true">
								<span class="shop-page-btn__book block">
									<?= file_get_contents(THEME_PATH . '/assets/images/icon-book.svg'); ?>
								</span>
							</span>
							<span><?= pll__('Boutique'); ?></span>
						</a>
						<a href="<?= get_permalink(get_option('woocommerce_myaccount_page_id')); ?>" class="account-icon-btn flex items-center gap-2 text-deep-blue transition-colors duration-300">
							<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 640 640" fill="currentColor"><path d="M463 448.2C440.9 409.8 399.4 384 352 384L288 384C240.6 384 199.1 409.8 177 448.2C212.2 487.4 263.2 512 320 512C376.8 512 427.8 487.3 463 448.2zM64 320C64 178.6 178.6 64 320 64C461.4 64 576 178.6 576 320C576 461.4 461.4 576 320 576C178.6 576 64 461.4 64 320zM320 336C359.8 336 392 303.8 392 264C392 224.2 359.8 192 320 192C280.2 192 248 224.2 248 264C248 303.8 280.2 336 320 336z"/></svg>
							<span><?= esc_html(pll__('Mon compte')); ?></span>
						</a>
					</div>
				</nav>

				<!-- Shop Button (Desktop) -->
				<a href="<?= get_permalink(wc_get_page_id('shop')); ?>" class="shop-page-btn hidden group lg:flex items-center gap-2 bg-deep-blue text-white px-6 py-2 rounded-full hover:bg-dark transition-colors duration-300">
					<span class="shop-page-btn__icon relative flex-shrink-0 w-[14px] h-4 overflow-visible" aria-hidden="true">
						<span class="shop-page-btn__book block group-hover:rotate-[-20deg] transition-transform duration-300">
							<?= file_get_contents(THEME_PATH . '/assets/images/icon-book.svg'); ?>
						</span>
						<span class="shop-page-btn__emitter"></span>
					</span>
					<span><?= pll__('Boutique'); ?></span>
				</a>

				<!-- Account -->
				<a href="<?= get_permalink(get_option('woocommerce_myaccount_page_id')); ?>" class="account-icon-btn hidden lg:flex items-center justify-center w-10 h-10 rounded-full text-deep-blue transition-colors duration-300" aria-label="<?= esc_attr(pll__('Mon compte')); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" viewBox="0 0 640 640" fill="currentColor"><path d="M463 448.2C440.9 409.8 399.4 384 352 384L288 384C240.6 384 199.1 409.8 177 448.2C212.2 487.4 263.2 512 320 512C376.8 512 427.8 487.3 463 448.2zM64 320C64 178.6 178.6 64 320 64C461.4 64 576 178.6 576 320C576 461.4 461.4 576 320 576C178.6 576 64 461.4 64 320zM320 336C359.8 336 392 303.8 392 264C392 224.2 359.8 192 320 192C280.2 192 248 224.2 248 264C248 303.8 280.2 336 320 336z"/></svg>
				</a>

				<!-- Cart -->
				<a class="cart-icon-wrapper relative flex items-center gap-2" href="<?= wc_get_cart_url(); ?>">
					<div class="relative">
						<?= file_get_contents(THEME_PATH . '/assets/images/icon-cart.svg'); ?>
						<?php if (WC()->cart->get_cart_contents_count() > 0) { ?>
							<span id="cart-count" class="cart-count absolute -top-2 -right-2 bg-primary text-white text-xs w-5 h-5 flex items-center justify-center rounded-full font-bold"><?= WC()->cart->get_cart_contents_count(); ?></span>
						<?php } ?>
					</div>
				</a>

				<!-- Mobile Menu Toggle -->
				<button id="site-burger" class="mobile-nav-trigger lg:hidden flex flex-col justify-center items-center w-8 h-8 gap-1.5">
					<div class="bar-1 w-6 h-0.5 bg-deep-blue transition-all duration-300"></div>
					<div class="bar-2 w-6 h-0.5 bg-deep-blue transition-all duration-300"></div>
					<div class="bar-3 w-6 h-0.5 bg-deep-blue transition-all duration-300"></div>
				</button>
			</div>

		</div>
	</header>

	<main id="main">