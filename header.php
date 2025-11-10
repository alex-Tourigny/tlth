<?php
$body_class = [];

if (is_shop_notice_enabled()) {
	$body_class[] = 'shop-notice-enabled';
}
?>

<!doctype html>
<html lang="<?php echo LANG; ?>">

<head>
	<title><?php is_front_page() ? the_title() : wp_title(''); ?> | <?php bloginfo('name'); ?></title>
	<link rel="stylesheet" href="https://use.typekit.net/fje3iel.css" />
	<script defer src="https://umami.alextourigny.ca/script.js" data-website-id="969a3755-2087-4176-b405-9ab3e8327e2a"></script>
	<?php wp_head(); ?>
</head>

<body <?php body_class(implode(' ', $body_class)); ?>>

	<header id="header">
		<div class="wrapper very-big d-flex align-items-center justify-content-between">

			<a href="<?= pll_home_url(); ?>" id="site-logo">
				<?= file_get_contents(THEME_PATH . '/assets/images/tlth-logo.svg'); ?>
			</a>
			<?php if (get_field("partner-logo", "option")) { ?>
				<a class="partner-logo" href="<?= get_field("partner-link", "option") ? get_field("partner-link", "option")["url"] : "#"; ?>" target="<?= get_field("partner-link", "option") ? get_field("partner-link", "option")["target"] : null; ?>">
					<?= FW::get_image(get_field("partner-logo", "option"), "partner-logo") ?>
				</a>
			<?php } ?>

			<div id="site-nav">
				<?php if (is_user_logged_in()) { ?>
					<div class="user woocommerce">
						<a href="<?= get_permalink(get_option('woocommerce_myaccount_page_id')); ?>">
							<span class="user-name"><?= CURRENT_USER_EMAIL; ?></span>
							<?= file_get_contents(THEME_PATH . '/assets/images/inner-user-drop.svg'); ?>
						</a>

						<div class="woocommerce-MyAccount-navigation">
							<ul class="sub-menu">
								<?php foreach (wc_get_account_menu_items() as $endpoint => $label) : ?>
									<li class="<?php echo wc_get_account_menu_item_classes($endpoint); ?>">
										<a href="<?php echo esc_url(wc_get_account_endpoint_url($endpoint)); ?>"><?php echo esc_html($label); ?></a>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
				<?php } ?>

				<a class="cart" href="<?= wc_get_cart_url(); ?>">
					<span id="cart-total" class="total"><?= wc_price(WC()->cart->cart_contents_total); ?></span>
					<?= file_get_contents(THEME_PATH . '/assets/images/icon-awesome-cart.svg'); ?>
					<span id="cart-count" class="cart-count"><?= WC()->cart->get_cart_contents_count(); ?></span>
				</a>

				<a href="<?= get_permalink(wc_get_page_id('shop')); ?>" class="shop-page btn red small"><?= woocommerce_page_title() ?></a>
				<?php /*
				<div class="lang">
					<nav>
						<ul>
							<?php pll_the_languages(); ?>
							<div class="switch">
								<?= file_get_contents(THEME_PATH . '/assets/images/tlth-lang-switch.svg'); ?>
							</div>
						</ul>
					</nav>
				</div>
			*/ ?>

				<a href="javascript:;" id="site-burger" class="mobile-nav-trigger">
					<div class="bar-1"></div>
					<div class="bar-2"></div>
					<div class="bar-3"></div>
				</a>
			</div>

		</div>
	</header>

	<div class="main-nav">
		<a href="javascript:;" class="mobile-nav-trigger">
			<div id="close-nav">
				<div class="x-bars">
					<div class="bar-1"></div>
					<div class="bar-2"></div>
				</div>
			</div>
		</a>

		<div class="inner-nav-top">
			<?php if (is_user_logged_in()) { ?>
				<div class="inner-user">
					<div class="user woocommerce">
						<a href="<?= get_permalink(get_option('woocommerce_myaccount_page_id')); ?>">
							<span class="user-name"><?= CURRENT_USER_EMAIL; ?></span>
							<?= file_get_contents(THEME_PATH . '/assets/images/inner-user-drop.svg'); ?>
						</a>

						<div class="woocommerce-MyAccount-navigation">
							<ul class="sub-menu">
								<?php foreach (wc_get_account_menu_items() as $endpoint => $label) : ?>
									<li class="<?php echo wc_get_account_menu_item_classes($endpoint); ?>">
										<a href="<?php echo esc_url(wc_get_account_endpoint_url($endpoint)); ?>"><?php echo esc_html($label); ?></a>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
				</div>

			<?php } else { ?>
				<a href="<?= get_permalink(get_option('woocommerce_myaccount_page_id')); ?>" class="user-sign-in"> <?= pll__('sign-in'); ?></a>
			<?php } ?>

			<div class="inner-cart">
				<a class="cart" href="<?= wc_get_cart_url(); ?>">
					<span class="total"><?= wc_price(WC()->cart->cart_contents_total); ?></span>
					<?= file_get_contents(THEME_PATH . '/assets/images/icon-awesome-cart.svg'); ?>
					<span id="cart-count" class="cart-count"><?= WC()->cart->get_cart_contents_count(); ?></span>
				</a>
			</div>
		</div>

		<nav class="inner-primary-menu">
			<ul>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container' => '',
						'items_wrap' => '%3$s'
					)
				);
				?>
			</ul>
		</nav>

		<div class="footer-bottom">
			<?php /*
		<div class="lang">
			<nav>
				<ul>
					<?php pll_the_languages(); ?>
					<div class="switch">
						<?= file_get_contents(THEME_PATH . '/assets/images/tlth-lang-switch.svg'); ?>
					</div>
				</ul>
			</nav>
		</div>

 */ ?>

			<div class="inner-socials">
				<?php include(THEME_PATH . '/includes/social-icons.php'); ?>
			</div>


		</div>
		<?php if (get_field("partner-logo", "option")) { ?>
			<a class="partner-logo" href="<?= get_field("partner-link", "option") ? get_field("partner-link", "option")["url"] : "#"; ?>" target="<?= get_field("partner-link", "option") ? get_field("partner-link", "option")["target"] : null; ?>">
				<?= FW::get_image(get_field("partner-logo", "option"), "partner-logo") ?>
			</a>
		<?php } ?>
	</div>

	<main id="main">