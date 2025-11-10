<?php
/*
 * Constants defined here so that PHPStorm suggests them in its autocomplete
 */
define('THEME_URL', get_template_directory_uri() );
define('THEME_PATH', get_stylesheet_directory() );

if( function_exists('pll_current_language') ) {
	define('LANG', pll_current_language() );
}

if( is_user_logged_in() ) {
	$current_user = wp_get_current_user();
	define('CURRENT_USER_ID', $current_user->ID);
	define('CURRENT_USER_NAME', $current_user->user_firstname);
	define('CURRENT_USER_FIRST_NAME', $current_user->user_firstname);
	define('CURRENT_USER_LAST_NAME', $current_user->user_lastname);
	define('CURRENT_USER_EMAIL', $current_user->user_email);
	define('CURRENT_USER_ROLE', $current_user->roles[0]);
}

define ("WP_ROCKET_WHITE_LABEL_FOOTPRINT", true);

if( function_exists('get_field') ) {
	if (get_field('google-maps-api-key', 'option')) {
		define('GOOGLE_MAPS_API_KEY', get_field('google-maps-api-key', 'option'));
	}
}