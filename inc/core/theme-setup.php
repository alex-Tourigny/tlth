<?php

/**
 * Theme Setup Configuration
 * 
 * Basic theme setup including thumbnails, lazy loading, and other core features
 */

define ("WP_ROCKET_WHITE_LABEL_FOOTPRINT", true);

// Initialize language and user constants after plugins are loaded
add_action('init', function() {
	// Define LANG constant with fallback
	if( !defined('LANG') ) {
		if( function_exists('pll_current_language') ) {
			define('LANG', pll_current_language() );
		} else {
			define('LANG', 'fr'); // Default to French
		}
	}
	
	// Define user constants if logged in
	if( is_user_logged_in() ) {
		if( !defined('CURRENT_USER_EMAIL') ) {
			$current_user = wp_get_current_user();
			define('CURRENT_USER_EMAIL', $current_user->user_email);
		}
		if( !defined('CURRENT_USER_ROLE') ) {
			$current_user = wp_get_current_user();
			define('CURRENT_USER_ROLE', $current_user->roles[0] ?? 'subscriber');
		}
	}
}, 20); // Priority 20 to ensure plugins have loaded

// Add theme support for post thumbnails
add_action('after_setup_theme', function () {
    add_theme_support('post-thumbnails');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
});

// Lazy-loading: ensure images have loading=lazy
add_filter('wp_get_attachment_image_attributes', function ($attr) {
    if (empty($attr['loading'])) {
        $attr['loading'] = 'lazy';
    }
    return $attr;
});

