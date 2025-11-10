<?php
/*
 * tl;dr
 *
 * 1. 	The constructor of this class sets up everything. Most of the methods are singleton helpers.
 *
 * 2. 	When using the option more_css_files, they will be merged and compiled into the single minified css file.
 *
 * 3. 	When using the option more_js_files, they will be queued in the supplied order. JS never gets merged and compiled except for main.js and functions.js.
 */

require_once('constants.php');

/*
 * The framework
 */
class FW {

	// Default public vars that can be overridden in the _constructor options
	public $queue_version 						= 1;
	public $queue 								= ['jquery'];
	public $show_admin_bar 						= 0;
	public $theme_support 						= [];
	public $options_pages						= ['Général'];
	public $tinymce_colors						= [];
	public $tinymce_color_picker				= 1;

	// Internal vars
	private $css_files							= [];
	private $js_files							= [];
	private $cpts 								= [];
	private $taxonomies 						= [];

	/*
	 * Constructor
	 *
	 * @param $options Array {
	 * 		Optional overrides for the framework
	 *
	 * 		@type int 		$queue_version 		The version of the enqueued files
	 * 		@type array		$queue				The list of handles to be enqueued. All possible handles are:
	 * 												jquery,
	 * 												aos,
	 * 												slick,
	 * 												fancybox
	 * 												sticky,
	 * 												cookie,
	 * 												match-height,
	 * 												uri,
	 * 												chosen
	 * 		@type int		$show_admin_bar		Whether or not to show the WP admin bar on the front end
	 * 		@type array 	$theme_support		An array of theme supports. Currently only supports a single key: 'woocommerce'
	 * 		@type array 	$options_pages		ACF Options pages to be created
	 * 		@type array 	$tinymce_colors		A list of custom colors for TinyMCE
	 * 		@type bool		$remove_branding	Disables the Kantaloup branding
	 * 		@type string	$gform_ajax_spinner	URL to AJAX spinner
	 * }
	 */
	function __construct( $options = [] )
	{
		// ACF is required to run the FW
		if( ! function_exists('get_field') ) {
			add_action( 'admin_notices',  [$this, 'admin_notice_acf'] );
		}

		// polylang is required to run the FW
		if( ! function_exists('pll__') ) {
			add_action( 'admin_notices',  [$this, 'admin_notice_polylang'] );
		}

		// are we showing branding or not
		if( isset( $options['remove_branding'] ) && $options['remove_branding'] ) {
			define('KANTALOUP_BRANDING', false);
		} else {
			define('KANTALOUP_BRANDING', true);
		}

		// login queue
		if( KANTALOUP_BRANDING ) {
			add_action('login_enqueue_scripts', [$this, '_login_queue']);
			add_action('login_header', [$this, '_login_top_markup']);
			add_action('login_footer', [$this, '_login_bottom_markup']);
		}

		// overrides
		if( isset($options['queue_version']) ) { $this->queue_version = $options['queue_version']; }
		if( isset($options['queue']) ) { $this->queue = $options['queue']; }
		if( isset($options['show_admin_bar']) ) { $this->show_admin_bar = $options['show_admin_bar']; }
		if( isset($options['js_files']) ) { $this->js_files = $options['js_files']; }
		if( isset($options['css_files']) ) { $this->css_files = $options['css_files']; }
		if( isset($options['options_pages']) ) { $this->options_pages = $options['options_pages']; }
		if( isset($options['tinymce_colors']) ) { $this->tinymce_colors = $options['tinymce_colors']; }
		if( isset($options['tinymce_color_picker']) ) { $this->tinymce_color_picker = $options['tinymce_color_picker']; }
		if( isset($options['theme_support']) ) { $this->theme_support = $options['theme_support']; }


		// fire the hooks
		$this->hooks();

		// add theme support
		$this->add_theme_support( $this->theme_support );

		// wp-admin only stuff
		if( is_admin() ) {
			// register the options pages
			$this->register_options_pages();

			// tinymce custom colors
			if( ! empty($this->tinymce_colors) ) {
				add_filter('tiny_mce_before_init', [$this, '_tinymce_custom_colors']);
			}

			// tinymce color picker
			if( ! $this->tinymce_color_picker ) {
				add_filter('tiny_mce_plugins', [$this, '_tinymce_remove_custom_colors']);
			}

			// load the tinyMCE addons
			require_once THEME_PATH . '/fw/tinymce-btns.php';
		}

		// load shortcodes
		require_once THEME_PATH . '/fw/shortcodes.php';

		// load ajax
		require_once THEME_PATH . '/fw/ajax.php';

		// maybe hide the admin bar
		if( ! $this->show_admin_bar ) {
			add_filter('show_admin_bar', '__return_false');
		}

		// queue scripts
		add_action('wp_enqueue_scripts', [$this, '_front_queue'], 10);

		// cpt's
		add_action('init', [$this, '_register_cpts']);

		// taxonomies
		add_action('init', [$this, '_register_taxonomies']);
	}

	/*
	 * Loads the FW_Hooks class and inits it
	 */
	private function hooks()
	{
		require_once 'framework-hooks.class.php';

		new FW_Hooks();
	}

	/*
	 * The files for the login page
	 */
	public function _login_queue()
	{
		wp_enqueue_style('login-style', THEME_URL . '/fw/core/login/login.css');
		wp_enqueue_style('slick-style', THEME_URL . '/fw/core/login/slick.css');

		wp_enqueue_script('jquery');
		wp_enqueue_script('login-js', THEME_URL . '/fw/core/login/login.js');
		wp_enqueue_script('slick-js', THEME_URL . '/fw/core/login/slick.min.js');
	}

	public function _login_top_markup()
	{
		echo '<div class="login-left">';
	}

	public function _login_bottom_markup()
	{
		echo '</div>';

		echo '<div class="login-right">';
		echo '<div class="tagline">';
		echo '<img class="tagline-text" src="' . THEME_URL . '/assets/images/login/login-tagline.svg">';
		echo '<img class="tagline-arrow" src="' . THEME_URL . '/assets/images/login/login-arrow.svg">';
		echo '</div>';

		ob_start();
		include( THEME_PATH . '/fw/core/framework.login.api.php' );
		echo ob_get_clean();

		echo '</div>';
	}

	/*
	 * Enqueue all of the base files
	 *
	 * @param $files Array All the handles to be enqueued
	 */
	public function _front_queue( $files = [] )
	{
		// jQuery
		if( in_array('jquery', $this->queue) ) {
			//wp_deregister_script('jquery');
			wp_enqueue_script('jquery');
			//wp_enqueue_script('jquery', THEME_URL . '/assets/js/lib/jquery-3.5.1.min.js', [], $this->queue_version, true);
		}

		// AOS
		if( in_array('aos', $this->queue) ) {
			wp_enqueue_style('aos', THEME_URL . '/assets/js/lib/aos/aos.css', [], $this->queue_version);
			wp_enqueue_script('aos', THEME_URL . '/assets/js/lib/aos/aos.js', [], $this->queue_version, true);
		}

		// Slick
		if( in_array('slick', $this->queue) ) {
			wp_enqueue_style('slick', THEME_URL . '/assets/js/lib/slick/slick.css', [], $this->queue_version);
			wp_enqueue_script('slick', THEME_URL . '/assets/js/lib/slick/slick.min.js', [], $this->queue_version, true);
		}

		// Fancybox3
		if( in_array('fancybox', $this->queue) ) {
			wp_enqueue_style('fancybox', THEME_URL . '/assets/js/lib/fancybox/jquery.fancybox.min.css', [], $this->queue_version);
			wp_enqueue_script('fancybox', THEME_URL . '/assets/js/lib/fancybox/jquery.fancybox.min.js', [], $this->queue_version, true);
		}

		// Sticky elements
		if( in_array('sticky', $this->queue) ) {
			wp_enqueue_script('sticky', THEME_URL . '/assets/js/lib/sticky.js', [], $this->queue_version, true);
		}

		// jQuery cookie
		if( in_array('cookie', $this->queue) ) {
			wp_enqueue_script('cookie', THEME_URL . '/assets/js/lib/js.cookie.min.js', [], $this->queue_version, true);
		}

		// Match height
		if( in_array('match-height', $this->queue) ) {
			wp_enqueue_script('matchheight', THEME_URL . '/assets/js/lib/jquery.matchheight.min.js', [], $this->queue_version, true);
		}

		// URI
		if( in_array('uri', $this->queue) ) {
			wp_enqueue_script('uri', THEME_URL . '/assets/js/lib/jquery.URI.min.js', [], $this->queue_version, true);
		}

		// chosen
		if( in_array('chosen', $this->queue) ) {
			wp_enqueue_script('chosen', THEME_URL . '/assets/js/lib/chosen/chosen.jquery.min.js', [], $this->queue_version, true);
			wp_enqueue_style('chosen', THEME_URL . '/assets/js/lib/chosen/chosen.min.css', [], $this->queue_version);
		}

		// scrollMagic
		if( in_array('scrollMagic', $this->queue) ) {
			wp_enqueue_script('scrollMagic', THEME_URL . '/assets/js/lib/scrollMagic/scrollmagic/minified/ScrollMagic.min.js', [], $this->queue_version, true);
		}

		// scrollMagic - Debug
		if( in_array('scrollMagicDebug', $this->queue) ) {
			wp_enqueue_script('scrollMagicDebug', THEME_URL . '/assets/js/lib/scrollMagic/scrollmagic/minified/plugins/debug.addIndicators.min.js', [], $this->queue_version, true);
		}

		// CSS Files
		if( ! empty($this->css_files) ) {
			foreach( $this->css_files as $file ) {
				if( substr($file, -4, 4) === '.css' ) {
					wp_enqueue_style(FW_Hooks::random_string(6), THEME_URL . '/assets/css/' . $file);
				}
			}
		}

		// JS file
		if( ! empty($this->js_files) ) {
			foreach( $this->js_files as $file ) {
				if( substr($file, -3, 3) === '.js' ) {
					wp_enqueue_script(FW_Hooks::random_string(6), THEME_URL . '/assets/js/' . $file, ['jquery'], $this->queue_version, true);
				}
			}
		}
	}

	/*
	 * Adds WordPress theme supports
	 */
	private function add_theme_support( $supports )
	{
		add_theme_support('menus');
		add_theme_support('post-thumbnails');

		if( ! empty($supports) ) {
			foreach( $supports as $support ) {
				add_theme_support($support);
			}
		}
	}

	/*
	 * Registers an array of polylang strings
	 *
	 * @param $strings array An array of arrays with the same parameters as the native pll function
	 */
	public function register_strings( $strings )
	{
		if( ! function_exists('pll_register_string') ) { return; }

		foreach( $strings as $string ) {
			pll_register_string($string[0], $string[1], $string[2]);
		}
	}

	/*
	 * Saves CPT args in this object, which is later used to register the CPT's
	 */
	public function register_cpt( $cpt, $labels, $args )
	{
		$args['labels'] = $labels;

		$this->cpts[] = [
			'slug' => $cpt,
			'args' => $args
		];
	}

	/*
	 * Hook called by WordPress to register all the CPT's saved in this object
	 */
	public function _register_cpts()
	{
		if( empty($this->cpts) ) { return; }

		foreach( $this->cpts as $cpt ) {
			register_post_type($cpt['slug'], $cpt['args']);
		}
	}

	/*
	 * Saves taxonomy args in this object, which is later used to register the taxonomies
	 */
	public function register_taxonomies( $slug, $cpt, $args )
	{
		$this->taxonomies[] = [
			'slug' => $slug,
			'cpt' => $cpt,
			'args' => $args
		];
	}

	/*
	 * Hook called by WordPress to register all the taxonomies saved in this object
	 */
	public function _register_taxonomies()
	{
		if( empty($this->taxonomies) ) { return; }

		foreach( $this->taxonomies as $tax ) {
			register_taxonomy($tax['slug'], $tax['cpt'], $tax['args']);
		}
	}

	/*
	 * Registers nav menus
	 */
	public function register_nav_menus( $menus )
	{
		register_nav_menus($menus);
	}

	/*
	 * Registers ACF options pages
	 */
	public function register_options_pages()
	{
		if( ! function_exists('acf_add_options_sub_page') ) { return; }

		acf_add_options_page();

		foreach( $this->options_pages as $page ) {
			acf_add_options_sub_page($page);
		}

		acf_add_options_sub_page('Admin');
	}

	/*
	 * Replaces all colors with custom colors in the TinyMCE color picker
	 *
	 * https://www.tinymce.com/docs/plugins/textcolor/#textcolor_map
	 */
	public function _tinymce_custom_colors( $init )
	{
		if( empty($this->tinymce_colors) ) { return; }

		$colors_str = '';

		// convert the colors to the weird tinymce format
		foreach( $this->tinymce_colors as $hex => $name ) {
			$colors_str .= "'$hex', '$name',";
		}

		// overwrite all default colors with the custom colors
		$init['textcolor_map'] = '['.$colors_str.']';

		return $init;
	}

	/*
	 * Hides the "Custom..." color picker in TinyMCE
	 */
	public function _tinymce_remove_custom_colors( $plugins )
	{
		foreach ( $plugins as $key => $plugin_name ) {
			if ( 'colorpicker' === $plugin_name ) {
				unset( $plugins[ $key ] );

				return $plugins;
			}
		}

		return $plugins;
	}

	/*
	 * Returns the featured image of a post
	 */
	public static function featured_image( $id = null )
	{
		if( empty($id) ) {
			$id = get_the_ID();
		}

		$attach_id = get_post_thumbnail_id( $id );

		if( $attach_id != '' ){
			return wp_get_attachment_image($attach_id, 'full');
		} else {
			return null;
		}
	}

	/*
	 * For simple inline translations
	 */
	public static function trans( $fr, $en )
	{
		if( LANG == 'fr' ) {
			return $fr;
		} elseif ( LANG == 'en' ) {
			return $en;
		}
	}

	/*
	 * Truncate a string based on the number of words
	 */
	public static function truncate_by_words( $string, $number_of_words, $return_with_dots = false, $strip_paragraphs = false )
	{
		if($strip_paragraphs){
			$string = preg_replace('[\r\n]+', ' ', $string);
		}

		$string_clean = strip_tags(trim($string));
		$words_arr = explode( ' ', $string_clean );
		$word_count = count( $words_arr );

		// If there are not enough words to truncate to the requested length, return original string
		if( $word_count <= $number_of_words ) {
			return $string;
		}

		$x = 1;
		$truncated = '';

		foreach( $words_arr as $w ) {
			$truncated .= $w . ' ';

			if( $x >= $number_of_words ) break;
			$x++;
		}

		if( $return_with_dots ) {
			$dots = '...';
		} else {
			$dots = '';
		}

		$that_last_space = trim( $truncated );
		$final_string = trim( $that_last_space, ',-/"' );

		return $final_string . $dots;
	}

	/*
	 * Returns the video ID from almost any YouTube url.
	 *
	 * @param $url string The whole YouTube URL
	 */
	public static function get_youtube_video_id( $url )
	{
		if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $url, $id)) {
			$values = $id[1];
		} else if (preg_match('/youtube\.com\/embed\/([^\&\?\/]+)/', $url, $id)) {
			$values = $id[1];
		} else if (preg_match('/youtube\.com\/v\/([^\&\?\/]+)/', $url, $id)) {
			$values = $id[1];
		} else if (preg_match('/youtu\.be\/([^\&\?\/]+)/', $url, $id)) {
			$values = $id[1];
		} else {
			return 'Not a YouTube video';
		}
		return $values;
	}

	/*
	 * Returns the YouTube thumbnail jpg hotlink
	 *
	 * @param $id int The YouTube video ID from the URL
	 */
	public static function get_youtube_thumbnail( $id )
	{
		return "http://img.youtube.com/vi/{$id}/hqdefault.jpg";
	}

	/*
	 * Used for embedding YouTube videos (usually into Fancybox)
	 *
	 * @param $id int The YouTube video ID from the URL
	 */
	public static function get_youtube_embed( $id )
	{
		return '//www.youtube.com/embed/' . $id;
	}

	/*
	 * Returns the thumbnail for a Vimeo video
	 */
	function get_vimeo_thumbnail( $id )
	{
		$data = file_get_contents("http://vimeo.com/api/v2/video/{$id}.json");
		$data = json_decode($data);

		return $data[0]->thumbnail_large;
	}

	/*
	 * Strip certain tags from string
	 *
	 * @param $text string The string of text
	 * @param $tags array Single dimension numerically indexed array. Closing tags must also be specified eg. array('<p>', '</p>').
	 */
	public static function strip_specific_tags( $text, $tags )
	{
		$e = $text;

		foreach( $tags as $tag ) {
			$e = str_replace( $tag, '', $e );
		}

		return $e;
	}

	/*
	 * Returns the post object of the top-most parent page
	 *
	 * @param $id int The page ID
	 */
	public static function get_top_most_parent_page( $id )
	{
		$ancestors = get_post_ancestors($id);

		if( empty($ancestors) ) {
			return array();
		}

		$parent = array_reverse($ancestors);
		$first_parent = get_page($parent[0]);

		return $first_parent;
	}

	/*
	 * Used to clean content for excerpts
	 */
	public static function clean_content( $content, $truncate = 0 )
	{
		$clean = strip_tags($content);
		$clean = strip_shortcodes($content);

		if( $truncate != 0 ) {
			$clean = FW::truncate_by_words($content, 50);
		}

		return trim( $clean );
	}

	/*
	 * Returns the page ID that's using a template
	 *
	 * @param $template string The template file name
	 */
	public static function get_page_id_by_template( $template )
	{
		$pages = get_posts(
			array(
				'post_type' => 'page',
				'fields' => 'ids',
				'nopaging' => true,
				'meta_key'   => '_wp_page_template',
				'meta_value' => $template . '.php'
			)
		);

		if(! empty($pages) ){
			return $pages[0];
		} else {
			return false;
		}
	}

	/*
	 * Get translation permalink
	 */
	public static function get_pll_permalink( $fr_id, $en_id ) {
		if( ! function_exists( 'pll_current_language' ) ) { return; }

		$l = pll_current_language();

		if( $l === 'en' ) {
			return get_permalink($en_id);
		} elseif( $l === 'fr' ) {
			return get_permalink($fr_id);
		} else {
			return false;
		}
	}

	/*
	 * Takes a date (dd/mm/yyyy) and returns the localized string representation
	 *
	 * @param $date string The dd/mm/yyyy date format to be converted to a written date
	 */
	public static function date_to_string( $date )
	{
		$datetime = DateTime::createFromFormat('d/m/Y', $date);
		$unix = $datetime->getTimestamp();

		$string = date_i18n( get_option('date_format'), $unix );

		return $string;
	}

	/*
	 * Returns the components of an ACF address
	 */
	public static function cut_acf_address( $address, $return = 'all' )
	{
		$address_parts = explode(', ', $address);

		switch( $return ) {
			case 'street':
				return $address_parts[0];
				break;

			case 'city':
				return $address_parts[1];
				break;

			case 'province':
				return $address_parts[2];
				break;

			case 'country':
				return $address_parts[3];
				break;

			case 'all':
				return $address_parts;
				break;
		}
	}

	/*
	 * Accepts a single item in the PHP $_FILES array and returns an attachment ID
	 */
	public static function handle_post_file_upload( $php_files_array_item, $image_meta_title )
	{
		require_once(ABSPATH . 'wp-admin/includes/media.php');
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		require_once(ABSPATH . 'wp-admin/includes/image.php');

		$temp = $php_files_array_item;

		$uploaded_file = wp_handle_upload($temp, array('test_form' => false, 'action' => 'nothing'));

		$attach_data = array(
			'post_mime_type' => $uploaded_file['type'],
			'post_title' => $image_meta_title,
		);

		$attach_id = wp_insert_attachment( $attach_data, $uploaded_file['file'] );
		$attach_data = wp_generate_attachment_metadata( $attach_id, $uploaded_file['file'] );
		wp_update_attachment_metadata( $attach_id, $attach_data );

		return $attach_id;
	}

	/*
	 * Rename uploaded file
	 */
	public static function rename_attachment($attachment_id, $prefix)
	{
		$post = get_post($attachment_id);
		$file = get_attached_file($attachment_id);
		$path = pathinfo($file);
		//dirname   = File Path
		//basename  = Filename.Extension
		//extension = Extension
		//filename  = Filename

		$newfilename =  $prefix . '_' . md5( 'seed' . microtime() );
		$newfile = $path['dirname'] . "/" . $newfilename . "." . $path['extension'];

		rename($file, $newfile);
		update_attached_file( $attachment_id, $newfile );

		return wp_get_attachment_url($attachment_id);
	}

	/*
	 * Log a user in programatically
	 *
	 * @param $email string The user's email
	 */
	public static function programatic_login( $email )
	{
		$user = get_user_by('email', $email);

		if( ! $user ) {
			return false;
		}

		wp_clear_auth_cookie();
		wp_set_current_user( $user->ID );
		wp_set_auth_cookie( $user->ID );
	}

	/*
	 * Returns a Woocommerce Stripe setting
	 *
	 * enabled
	 * title
	 * description
	 * testmode
	 * secret_key
	 * publishable_key
	 * test_secret_key
	 * test_publishable_key
	 * capture
	 * stripe_checkout
	 * stripe_checkout_locale
	 * stripe_bitcoin
	 * stripe_checkout_image
	 * saved_cards
	 * logging
	 */
	public static function get_woocommerce_stripe_setting( $option )
	{
		$woo_settings = get_option('woocommerce_stripe_settings');

		if( empty($woo_settings) ) {
			return false;
		}

		return $woo_settings[$option];
	}

	/*
	 * Simple function for listing the terms of a post
	 */
	public static function list_terms( $post_id, $tax, $wrap_in_span = true, $sep = ', ' )
	{
		$terms = get_the_terms($post_id, $tax);

		if( empty($terms) ) {
			return '';
		}

		$str = '';

		foreach( $terms as $term ) {
			if( $wrap_in_span ) {
				$str .= "<span>{$term->name}{$sep}</span>";
			} else {
				$str .= $term->name . $sep;
			}
		}

		$str = substr($str, 0, strlen($str) - strlen($sep));

		return $str;
	}

	/*
	 * Slugify a string
	 */
	public static function slugify( $text )
	{
		// replace non letter or digits by -
		$text = preg_replace('~[^\pL\d]+~u', '-', $text);

		// transliterate
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

		// remove unwanted characters
		$text = preg_replace('~[^-\w]+~', '', $text);

		// trim
		$text = trim($text, '-');

		// remove duplicate -
		$text = preg_replace('~-+~', '-', $text);

		// lowercase
		$text = strtolower($text);

		if (empty($text)) {
			return 'n-a';
		}

		return $text;
	}

	/*
	 * Generate a random string
	 */
	public static function random_string( $length = 10, $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' )
	{
		$characters = $pool;
		$charactersLength = strlen($characters);
		$randomString = '';

		for ( $i = 0; $i < $length; $i++ ) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}

		return $randomString;
	}

	/*
	 * Like the 's' parameter of the wp_query, but better
	 *
	 * @param $params array {
	 * 		'post_type' => 'post'
	 * 		'posts_per_page' => -1
	 * 		's' => 'my search query'
	 * 		'return' => 'objects' || 'ids'
	 * }
	 */
	public static function s_search( $params )
	{
		// post_type is required
		if( empty($params['post_type']) ) {
			return false;
		}

		// query is required
		if( empty($params['s']) ) {
			return false;
		}

		// If no ppp, get default
		if( empty($params['posts_per_page']) ) {
			$ppp = get_option('posts_per_page');
		} else {
			$ppp = $params['posts_per_page'];
		}

		// Vars
		global $wpdb;
		$query = $params['s'];

		// If only 1 CPT passed, convert to 1-item array
		if( ! is_array($params['post_type']) ) {
			$params['post_type'] = [$params['post_type']];
		}

		// Convert the CPT array to a comma separated list
		$cpt_w_commas = '';
		foreach( $params['post_type'] as $cpt ) {
			$cpt_w_commas .= "'$cpt',";
		}
		$cpt_w_commas = substr($cpt_w_commas, 0, -1);

		// Get the languages on this site and set the current lang for the SQL
		$site_langs = pll_the_languages(array('raw' => 1));
		$this_lang = $site_langs[LANG]['id'];

		// Run the SQL
		$db_matches = $wpdb->get_results("
									SELECT 
										$wpdb->posts.ID
									FROM 
										$wpdb->posts INNER JOIN $wpdb->term_relationships 										
										ON $wpdb->posts.ID = $wpdb->term_relationships.object_id
									WHERE 
										($wpdb->posts.post_title LIKE '%$query%' OR $wpdb->posts.post_content LIKE '%$query%')
										AND $wpdb->posts.post_type IN ($cpt_w_commas)
										AND $wpdb->posts.post_status = 'publish'
										AND $wpdb->term_relationships.term_taxonomy_id = $this_lang
									");



		if( empty($db_matches) ) {
			return $db_matches;
		}

		$return = array();

		if( empty($params['return']) || $params['return'] == 'objects' ) {

			// Add post objects
			foreach( $db_matches as $match ) {
				$return[] = get_post( $match->ID );
			}

		} else if( $params['return'] == 'ids' ) {

			// Add ID's
			foreach( $db_matches as $match ) {
				$return[] = $match->ID;
			}

		}

		// Paginate
		if( $ppp == -1 ) {
			return $return;
		} else {
			return array_slice($return, 0, $ppp);
		}
	}

	/*
	 * Build button markup from ACF link field type array
	 *
	 * @param array $btn_array ACF Link field array
	 * @param array $classes Optional Extra classes to be added to markup
	 * @param array $the_data Optional Extra data-tags to be added to the markup
	 * @param array $icon_data Optional ACF image (as array) to be used as icon
 	*/
	public static function button( $btn_array, $classes = [], $the_data = [], $icon_data = '' )
	{
		if( empty($btn_array) ) { return ''; }

		$data = [];
		if( ! empty($the_data) ){
			foreach($the_data as $datum => $value)
			{
				$data[] .= 'data-' . $datum . '="' . $value . '"';
			}
		}

		if( ! empty($icon_data) ){
			$classes[] = 'with-icon';

			$markup = '<a href="' . $btn_array['url'] . '" target="' . $btn_array['target'] . '" class="' . implode(' ', $classes) . '" ' . implode(' ' , $data) . '>';
			$markup .= FW::get_image($icon_data);
			$markup .= '<span>' . $btn_array['title'] . '</span>';
			$markup .= '</a>';
		} else {
			$markup = '<a href="' . $btn_array['url'] . '" target="' . $btn_array['target'] . '" class="' . implode(' ', $classes) . '" ' . implode(' ' , $data) . '>';
			$markup .= '<span>' . $btn_array['title'] . '</span>';
			$markup .= '</a>';
		}

		return $markup;
	}

	/*
	 * Converts an HTTP attachment url to an attachment ID
	 */
	public static function attachment_url_to_id( $url )
	{
		global $wpdb;

		$url = str_replace(['https://', 'http://'] , '', $url);
		$url = '//' . $url;

		$attachment = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE guid LIKE '%{$url}%'");

		return $attachment[0];
	}

	/*
	 * Removes all shortcodes from a string, without removing the content of the shortcodes
	 */
	public static function remove_shortcodes_keep_content( $content )
	{
		return preg_replace("~(?:\[/?)[^/\]]+/?\]~s", '', $content);
	}

	/*
	 * Return an associative array of all post_id translations from a given post_id
	 *
	 * @param $post_id post_id
	 * @return array of lang_slug => post_id
	 */
	public static function get_post_id_translations( $post_id )
	{
		$languages = pll_the_languages([
			'raw' => true
		]);

		$post_languages_ids = [];

		foreach( $languages as $language ) {
			$post_languages_ids[$language['slug']] = pll_get_post($post_id, $language['slug']);
		}

		return $post_languages_ids;
	}

	/*
	 * Return image markup from ACF image array with possibility of resizing the image
	 *
	 * @parem $acf_image_array image array from ACF
	 * @param $size size options string to be passed to Imagine
	 * @param $id id attribute to be added to image markup
	 * @parem $class class attribute to be added to image markup
	 * @return <img> markup
	 */
	public static function get_image( $acf_image_array,  $id = '', $class = '' )
	{
		$title = $acf_image_array['title'] == $acf_image_array['name'] ? '' : $acf_image_array['title'];

		$src = $acf_image_array['url'];

		return '<img src="' . $src . '" id="' . $id . '" class="' . $class . '" alt="' . $acf_image_array['alt'] . '" title="' . $title . '">';
	}

	public static function handle_ajax_data( $post_data, $post_files = '', $file_prefix = '', $allowed_file_types = ['image/jpeg', 'image/png', 'application/pdf'] )
	{
		unset($post_data['action']);

		if( isset($post_files) && ! empty($post_files) ) {

			$filesets = [];

			foreach($post_files as $field_name => $file_data)
			{
				// Is it a multi-dimensional file array?
				if( is_array($file_data['name']) ){

					foreach($file_data as $file_info_name => $file_values){

						foreach($file_values as $file_index => $file_value){
							$filesets[$field_name][$file_index][$file_info_name] = $file_value;
						}
					}

				} else {
					$filesets[$field_name][0] = $file_data;
				}
			}

			foreach($filesets as $file_name => $files){

				foreach($files as $file_index => $file) {
					$finfo = finfo_open(FILEINFO_MIME_TYPE);
					$mime = finfo_file($finfo, $file['tmp_name']);

					if (! in_array($mime, $allowed_file_types)) {
						wp_send_json_error('Type de fichier invalide');
					}

					$file_attachment_id = FW::handle_post_file_upload($file, '');
					$file_url = FW::rename_attachment($file_attachment_id, $file_prefix);

					$post_data[$file_name][$file_index] = array(
						'attachment_id' => $file_attachment_id,
						'url' => $file_url
					);
				}
			}
		}

		return $post_data;
	}

	/*
	 * Creates a password reset token in the user meta.
	 * The token is a serialized array with a random string and an expiration date.
	 *
	 * @return string The token
	 */
	public static function create_reset_pw_token( $user_id, $expiration = 86400 )
	{
		$rand = FW::random_string();

		$token = [
			'token' => $rand,
			'expiration' => time() + $expiration
		];

		add_user_meta($user_id, 'reset-pw-token', $token);

		return $rand;
	}

	/*
	 * Checks if a password reset token is valid (as created by FW::create_reset_pw_token())
	 */
	public static function check_reset_pw_token( $user_id, $token )
	{
		$all_tokens = get_user_meta($user_id, 'reset-pw-token');

		if( empty($all_tokens) ) {
			return false;
		}

		foreach( $all_tokens as $token_arr ) {
			// token match
			if( $token_arr['token'] === $token ) {
				// if current time is before the expiration
				if( time() < $token_arr['expiration'] ) {
					return true;
				}
			}
		}

		return false;
	}

	public static function get_adjacent_post( $current_id, $cpt, $dir, $args = null )
	{
		$default_args = [
			'post_type' => $cpt,
			'posts_per_page' => -1,
			'orderby' => 'menu_order',
			'order' => 'ASC'
		];

		// Which args
		if( is_null($args) ) {
			$query_args = $default_args;
		} else {
			$query_args = $args;
			$query_args['post_type'] = $cpt;
			$query_args['posts_per_page'] = -1;
		}

		$all_posts = get_posts($query_args);

		if( empty($all_posts) ) {
			return false;
		}

		$current_post_in_array = 0;
		$post_to_load = null;

		// current
		foreach( $all_posts as $k => $a_post ) {
			if( $a_post->ID == $current_id ) {
				$current_post_in_array = $k;
			}
		}

		// Get the prev post or loop to the end of the list
		if( $dir == 'prev' ) {
			if( isset($all_posts[$current_post_in_array - 1]) ) {
				$post_to_load = $all_posts[$current_post_in_array - 1]->ID;
			} else {
				$post_to_load = end($all_posts)->ID;
			}
		}

		// Get the next post or loop to the front of the list
		if( $dir == 'next' ) {
			if( isset($all_posts[$current_post_in_array + 1]) ) {
				$post_to_load = $all_posts[$current_post_in_array + 1]->ID;
			} else {
				$post_to_load = $all_posts[0]->ID;
			}
		}

		if( is_null($post_to_load) ) {
			return false;
		}

		return get_post($post_to_load);
	}

	/*
	 * When exporting SVG's from Illustrator, there is no concept of "other svg's", and the result is that
	 * the classes and ID's of one SVG can collide with another when used openly in the same document.
	 * This method randomizes the classes and ID's in order to avoid collisions. You only need to run this once.
	 *
	 * @param int $loops	If the loop is set to 20 for example, then the SVG will be checked for the classes .st0, .st1, .st2, until .st20
	 * @param str $dir		The directory to scan for SVG's
	 */
	public static function fix_svg_ids( $loops = 100, $dir = THEME_PATH . '/images/' )
	{
		$files = scandir($dir);
		$found = 0;
		$count = 0;
		$replacement_files = [];

		foreach($files as $file) {
			if( substr($file, -4, 4) === '.svg' ) {
				$old_code = file_get_contents($dir . $file);
				$new_code = $old_code;

				// Replace instances of .st0, .st1, .st2, etc
				for( $i = 0; $i <= $loops; $i++ ) {
					$rand = FW::random_string(8);

					$new_code = str_replace(".st{$i}{", ".st{$rand}{", $new_code);
					$new_code = str_replace("class=\"st{$i}\"", "class=\"st{$rand}\"", $new_code);
				}

				// Replace instances of #Layer_1, #Layer_2, etc
				for( $i = 0; $i <= $loops; $i++ ) {
					$rand = FW::random_string(8);

					$new_code = str_replace("id=\"Layer_{$i}\"", "id=\"Layer_{$rand}\"", $new_code);
				}

				// Replace instances of #SVGID_1_, #SVGID_2_, etc
				for( $i = 0; $i <= $loops; $i++ ) {
					$rand = FW::random_string(8);

					$new_code = str_replace("#SVGID_{$i}_", "#SVGID_{$rand}_", $new_code);
					$new_code = str_replace("id=\"SVGID_{$i}_\"", "id=\"SVGID_{$rand}_\"", $new_code);
				}

				if( $new_code !== $old_code ) {
					$count++;
					$replacement_files[] = $file;
				}

				$found++;

				file_put_contents($dir . $file, $new_code);
			}
		}

		echo "Found {$found} SVG's<br>\n";
		echo "Made replacements in {$count} file(s)<br>\n";
		echo "The file(s) with replacements are:<br>\n";
		foreach( $replacement_files as $replacement_file ) {
			echo $replacement_file . '<br>\n';
		}

		return;
	}

	/*
	 * Returns a 404 to the client
	 */
	public static function do_404()
	{
		global $wp_query;

		$wp_query->set_404();
		status_header(404);
		nocache_headers();

		include THEME_PATH . '/404.php';

		die();
	}

	/*
	 * Sanitizes a phone number
	 */
	public static function sanitize_phone_number( $number )
	{
		return str_replace([' ', '-', '.', 'p.', 'ext.', '+', '(', ')'], '', $number);
	}

	/*
	 * Simple function for sending an email
	 *
	 * @param int|string $to A plaintext email address, or a user ID
	 * @param string $subhect The plaintext email subject
	 * @param string $template Accepts a server path to a file, or a string of markup
	 * @param array $merge_tags An array containing subarrays with ONLY TWO VALUES, where value 0 is the merge tag, and value 1 is the merge value
	 * @param string $sender A plaintext email to be used as the sender email. Defaults to the admin email
	 * @param string $replyto A plaintext email to be used as the replyto. Defualts to "noreply" + {$admin_email_domain}
	 * @param array $attachments An array containing paths to the attachments (not URLs)
	 */
	public static function send_email( $to, $subject, $markup, $merge_tags = [], $sender = null, $replyto = null, $attachments = [] )
	{
		// Sending to a user ID instead of plaintext email
		if( is_numeric($to) ) {
			$user = get_user_by('id', $to);
			$to = $user->user_email;
		}

		// If the markup is a file, load it
		if( substr($markup, -4) === '.php' || substr($markup, -5) === '.html' ) {
			ob_start();
			include $markup;
			$markup = ob_get_clean();
		}

		// Template merge tags into the markup
		if( ! empty($merge_tags) ) {
			$s = [];
			$r = [];

			foreach( $merge_tags as $tag ) {
				$s[] = $tag[0];
				$r[] = $tag[1];
			}

			$markup = str_replace($s, $r, $markup);
		}

		$admin_email = get_option('admin_email');

		// Default sender
		if( $sender === null ) {
			$sender = $admin_email;
		}

		// Default replyto
		if( $replyto === null ) {
			$domain = explode('@', $admin_email)[1];
			$replyto = "noreply@{$domain}";
		}

		// Email headers
		$headers = "From: {$sender} <{$replyto}>\r\n";
		$headers .= "Reply-To: {$replyto}\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

		$sent = wp_mail( $to, $subject, $markup, $headers, $attachments );

		return $sent;
	}
	/*
	 * Footer copyright
	 * @param string $lang Language slug to return the copyright in
	 * @param string $mode 'natural' = white and blue logo, 'white' = all white logo, 'black' = all black logo
	 */
	public static function rubik_footer($lang = 'fr', $mode = 'natural')
	{
		$transient_name = 'rubik_footer_' . $lang;

		// If param exists, delete transient, so we can we reset footer code
		if( isset($_GET['reset_rubik_footer']) ){
			delete_transient( $transient_name );
		}

		// If footer is cached in a transient, use that one, so we don't overload the server with requests
		if( false !== ( get_transient( $transient_name ) ) ) {
			return get_transient( $transient_name );
		}

		$args = array(
			'headers' => array(
				'Content-Type' => 'application/json'
			),
			'body' => array(
				'lang' => $lang,
				'mode' => $mode
			)
		);

		// If not cached, get the response and then cache it
		$response = wp_remote_get('https://agencerubik.com/copyright', $args);

		if ( !is_array($response) || $response['response']['code'] != 200) return;

		$markup = $response['body'];

		$expiry = 604800; // 1 week
		set_transient( $transient_name, $markup, $expiry );

		return $markup;
	}

	public static function admin_notice_acf() {
		?>
		<div class="notice notice-warning is-dismissible">
			<p><strong><a href="<?= admin_url('plugin-install.php?s=advanced custom fields&tab=search&type=term'); ?>" target="_blank">Advanced Custom Fields</a></strong> should be installed for this theme to work.</p>
		</div>
		<?php
	}

	public static function admin_notice_polylang() {
		?>
		<div class="notice notice-warning is-dismissible">
			<p><strong><a href="<?= admin_url('plugin-install.php?s=polylang&tab=search&type=term'); ?>" target="_blank">Polylang</a></strong> should be installed for this theme to work.</p>
		</div>
		<?php
	}

}