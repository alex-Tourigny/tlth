<?
/*
 * Setup WooCommerce
 */
function tlth_add_woocommerce_support()
{
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 800,
			'single_image_width'    => 1000,
			'product_grid' => array(
				'default_rows'    => 3,
				'min_rows'        => 24,
				'max_rows'        => 24,

				'default_columns' => 1,
				'min_columns'     => 1,
				'max_columns'     => 1,
			),
		)
	);

	register_sidebar(
		array(
			'name' => 'Boutique',
			'id' => 'shop',
			'description'   => 'Sidebar à afficher à la boutique',
			'before_widget' => '<li id="%1$s" class="widget %2$s">',
			'after_widget'  => '</li>',
			'before_title'  => '<h2 class="widgettitle">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'after_setup_theme', 'tlth_add_woocommerce_support' );

/**
 * Product archive: 12 products per page.
 *
 * @return int
 */
function tlth_loop_shop_per_page() {
	return 12;
}
add_filter( 'loop_shop_per_page', 'tlth_loop_shop_per_page', 20 );

/**
 * Shop pagination: numbers only (no prev/next arrows).
 *
 * @param array<string, mixed> $args paginate_links args.
 * @return array<string, mixed>
 */
function tlth_woocommerce_pagination_args( $args ) {
	$args['prev_text'] = false;
	$args['next_text'] = false;
	$args['type']      = 'list';

	return $args;
}
add_filter( 'woocommerce_pagination_args', 'tlth_woocommerce_pagination_args' );

/*
 * WooCommerce registers selectWoo (Select2-compatible) but often does not enqueue it on the storefront.
 * Plugins such as "user-registration-plugin-for-woocommerce" call jQuery.fn.select2 without listing selectWoo
 * as a dependency, so their script can run before Select2. Register runs on wp_enqueue_scripts:10 (WC);
 * we run late, enqueue selectWoo, and attach it as a dependency of any script loading afreg_front.js.
 */
function tlth_enqueue_selectwoo_for_frontend_plugins() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	$select_handle = null;
	if ( wp_script_is( 'selectWoo', 'registered' ) ) {
		$select_handle = 'selectWoo';
	} elseif ( wp_script_is( 'select2', 'registered' ) ) {
		$select_handle = 'select2';
	}

	if ( ! $select_handle ) {
		return;
	}

	wp_enqueue_script( $select_handle );

	if ( wp_style_is( 'select2', 'registered' ) ) {
		wp_enqueue_style( 'select2' );
	}

	global $wp_scripts;
	if ( ! ( $wp_scripts instanceof WP_Scripts ) ) {
		return;
	}

	foreach ( $wp_scripts->registered as $script ) {
		if ( empty( $script->src ) || strpos( $script->src, 'afreg_front' ) === false ) {
			continue;
		}
		if ( ! in_array( $select_handle, $script->deps, true ) ) {
			$script->deps[] = $select_handle;
		}
		break;
	}
}
add_action( 'wp_enqueue_scripts', 'tlth_enqueue_selectwoo_for_frontend_plugins', 99 );

/**
 * ACF `bg-color` slug for book cards and single-product gallery (same source as shop grid).
 *
 * @param int $post_id Product post ID.
 * @return string Slug (e.g. coral) or `other`.
 */
function tlth_product_book_bg_color_slug( $post_id ) {
	$post_id = absint( $post_id );
	$bg_list = array( 'primary', 'deep-blue', 'coral', 'teal', 'rose', 'red' );
	$slug    = get_field( 'bg-color', $post_id );

	if ( ! $slug ) {
		$slug = $bg_list[ $post_id % count( $bg_list ) ];
	}

	if ( 'other' === $slug ) {
		return 'other';
	}

	$allowed = array_merge(
		$bg_list,
		array( 'light-blue', 'gold', 'dark-blue', 'yellow', 'beige', 'sky-blue' )
	);

	if ( ! in_array( (string) $slug, $allowed, true ) ) {
		return $bg_list[ $post_id % count( $bg_list ) ];
	}

	return (string) $slug;
}

/**
 * Sanitized 6-char hex from ACF `bg-color-custom` when slug is `other`.
 *
 * @param int $post_id Product post ID.
 * @return string Uppercase hex without `#`, or empty string.
 */
function tlth_product_book_hero_bg_custom_hex( $post_id ) {
	if ( 'other' !== tlth_product_book_bg_color_slug( $post_id ) ) {
		return '';
	}

	$hex = get_field( 'bg-color-custom', $post_id );
	$hex = is_string( $hex ) ? preg_replace( '/[^a-fA-F0-9]/', '', $hex ) : '';

	return ( 6 === strlen( $hex ) ) ? strtoupper( $hex ) : '';
}

/**
 * Tailwind background class for product hero / book card top panel.
 *
 * @param int $post_id Product post ID.
 * @return string Unescaped class string (pass through esc_attr in markup).
 */
function tlth_product_book_hero_bg_class( $post_id ) {
	$slug = tlth_product_book_bg_color_slug( $post_id );

	if ( 'other' === $slug ) {
		return tlth_product_book_hero_bg_custom_hex( $post_id ) ? '' : 'bg-rose';
	}

	return 'bg-' . $slug;
}

/**
 * Inline background-color for custom product hero / book card colors.
 *
 * @param int $post_id Product post ID.
 * @return string CSS declaration(s) for style attribute, or empty string.
 */
function tlth_product_book_hero_bg_style_attr( $post_id ) {
	$hex = tlth_product_book_hero_bg_custom_hex( $post_id );

	return $hex ? 'background-color: #' . $hex . ';' : '';
}

/**
 * Single product gallery: arrows only, no thumbnail strip (FlexSlider).
 *
 * @param array<string, mixed> $options Carousel options.
 * @return array<string, mixed>
 */
function tlth_single_product_carousel_options( $options ) {
	if ( ! is_product() ) {
		return $options;
	}

	$options['controlNav']     = false;
	$options['directionNav'] = true;

	return $options;
}

add_filter( 'woocommerce_single_product_carousel_options', 'tlth_single_product_carousel_options', 20 );
add_filter( 'woocommerce_single_product_image_gallery_thumbnail_columns', '__return_zero', 20 );

/**
 * Whether the cart meets WooCommerce free shipping rules for its shipping zone.
 *
 * @return bool
 */
function tlth_cart_qualifies_for_free_shipping() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart || WC()->cart->is_empty() ) {
		return false;
	}

	if ( ! wc_shipping_enabled() ) {
		return false;
	}

	$packages = WC()->cart->get_shipping_packages();
	$package  = reset( $packages );

	if ( empty( $package ) ) {
		return false;
	}

	$zone = wc_get_shipping_zone( $package );

	foreach ( $zone->get_shipping_methods( true ) as $method ) {
		if ( 'free_shipping' !== $method->id ) {
			continue;
		}

		if ( $method->is_available( $package ) ) {
			return true;
		}
	}

	return false;
}

/*
 * Generate back to show button
 */
function woocommerce_back_to_shop_url()
{
	echo '<div class="back-to-shop"><a href="' . get_permalink( wc_get_page_id( 'shop' ) ) . '" class="text-link red">' . pll__('back-to-shop') . '</a></div>';
}

/**
 * Whether the product uses the book personalization wizard (variable + Gravity Form).
 *
 * @param WC_Product|null $product Product object.
 * @return bool
 */
function tlth_product_has_book_personalization_wizard( $product = null ) {
	if ( ! $product ) {
		global $product;
	}

	if ( ! $product || ! $product->is_type( 'variable' ) ) {
		return false;
	}

	return (bool) get_post_meta( $product->get_id(), '_gravity_form_data', true );
}

/*
 * Hero primary CTA: personalize (variable books) or add to cart (simple / other).
 */
function tlth_single_product_hero_primary_cta() {
	global $product;

	if ( tlth_product_has_book_personalization_wizard( $product ) ) {
		tlth_single_product_personnalise_btn();
		return;
	}

	woocommerce_template_single_add_to_cart();
}

/*
 * Output Personnalise ton livre CTA button on single product
 */
function tlth_single_product_personnalise_btn() {
	global $product;

	if ( ! tlth_product_has_book_personalization_wizard( $product ) ) {
		return;
	}
	?>
	<a href="#personnaliser-le-livre" class="btn product-hero-btn product-hero-btn--primary inline-flex items-center justify-center gap-2 px-[18px] py-2 bg-deep-blue text-white border-2 border-deep-blue rounded-full text-[15px] font-semibold hover:bg-teal hover:border-teal hover:text-white transition-colors duration-200">
		<?= file_get_contents( THEME_PATH . '/assets/images/icons/pen.svg' ); ?>
		<span class="text"><?= esc_html( function_exists('pll__') ? pll__('Personnalise ton livre') : 'Personnalise ton livre' ); ?></span>
	</a>
	<?php
}

/**
 * Product description HTML for simple (non-wizard) single-product hero.
 *
 * @param WC_Product|null $product Product object.
 * @return string
 */
function tlth_single_product_hero_description_html( $product = null ) {
	if ( ! $product ) {
		global $product;
	}

	if ( ! $product || tlth_product_has_book_personalization_wizard( $product ) ) {
		return '';
	}

	$description = $product->get_description();

	if ( ! $description ) {
		$description = $product->get_short_description();
	}

	if ( ! $description ) {
		return '';
	}

	return '<div class="product-hero-desc the-content text-deep-blue mb-6">' . apply_filters( 'the_content', $description ) . '</div>';
}

/**
 * Mark hero add-to-cart controls for theme styles.
 *
 * @param array<string, string> $attributes Button attributes.
 * @return array<string, string>
 */
function tlth_single_product_hero_add_to_cart_button_classes( $attributes ) {
	global $product;

	if ( ! is_product() || ! $product || tlth_product_has_book_personalization_wizard( $product ) ) {
		return $attributes;
	}

	$attributes['class'] = trim( ( $attributes['class'] ?? '' ) . ' product-hero-add-to-cart-btn' );

	return $attributes;
}
add_filter( 'woocommerce_product_single_add_to_cart_attributes', 'tlth_single_product_hero_add_to_cart_button_classes' );

/*
 * Generatre flipbook markup
 */
function get_product_preview_pdf_btn()
{
	global $product;

	if ( ! $product || ! tlth_product_has_flipbook_preview( $product->get_id() ) ) {
		return;
	}

	ob_start();
	include(THEME_PATH . '/woocommerce/single-product/preview-flipper.php');

	echo ob_get_clean();
}

/*
 * Add user roles
 */
function xx__update_custom_roles()
{
	if ( get_option( 'custom_roles_version' ) <= 1 ) {
		add_role( 'school', 'École', ['read' => true, 'level_0' => true]);
		add_role( 'daycare', 'Milieu de garde', ['read' => true, 'level_0' => true]);
		update_option( 'custom_roles_version', 1 );
	}
}
add_action( 'init', 'xx__update_custom_roles' );

/*
 * Email as username
 */
function my_new_customer_username( $username, $email, $new_user_args, $suffix ) {
	return $email;
}
add_filter( 'woocommerce_new_customer_username', 'my_new_customer_username', 10, 4 );

/**
 * Hide user role on My Account → Account details and ignore role changes on save.
 */
function tlth_strip_user_role_from_account_details_save( $user_id ) {
	unset( $_POST['afreg_select_user_role'] );
}
add_action( 'woocommerce_save_account_details', 'tlth_strip_user_role_from_account_details_save', 1 );