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

/*
 * The TLTH Framework Class
 */
class TLTH {

	// Default public vars that can be overridden in the _constructor options
	public $queue_version 						= 1;
	public $queue 								= ['jquery'];
	public $show_admin_bar 						= 1;
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
		// ACF is required to run the FW - check after init to avoid translation warnings
		add_action('admin_init', function() {
			if( ! function_exists('get_field') ) {
				add_action( 'admin_notices',  [$this, 'admin_notice_acf'] );
			}
		});

		// polylang is required to run the FW - check after init to avoid translation warnings
		add_action('admin_init', function() {
			if( ! function_exists('pll__') ) {
				add_action( 'admin_notices',  [$this, 'admin_notice_polylang'] );
			}
		});

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
		}

		// maybe hide the admin bar
		if( ! $this->show_admin_bar ) {
			add_filter('show_admin_bar', '__return_false');
		}

		// queue scripts
		add_action('wp_enqueue_scripts', [$this, '_front_queue'], 10);
	}

	/*
	 * Loads the FW_Hooks class and inits it
	 */
	private function hooks()
	{
		require_once 'framework-hooks.class.php';

		new TLTH_Hooks();
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
			wp_enqueue_script('jquery');
		}

		// AOS
		if( in_array('aos', $this->queue) ) {
			wp_enqueue_style('aos', THEME_URL . '/assets/js/lib/aos/aos.css', [], $this->queue_version);
			wp_enqueue_script('aos', THEME_URL . '/assets/js/lib/aos/aos.js', [], $this->queue_version, true);
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

		// JS file
		if( ! empty($this->js_files) ) {
			foreach( $this->js_files as $file ) {
				if( substr($file, -3, 3) === '.js' ) {
					wp_enqueue_script(TLTH_Hooks::random_string(6), THEME_URL . '/assets/js/' . $file, ['jquery'], $this->queue_version, true);
				}
			}
		}
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
			$markup .= TLTH::get_image($icon_data);
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

					$file_attachment_id = TLTH::handle_post_file_upload($file, '');
					$file_url = TLTH::rename_attachment($file_attachment_id, $file_prefix);

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
		$rand = TLTH::random_string();

		$token = [
			'token' => $rand,
			'expiration' => time() + $expiration
		];

		add_user_meta($user_id, 'reset-pw-token', $token);

		return $rand;
	}

	/*
	 * Checks if a password reset token is valid (as created by TLTH::create_reset_pw_token())
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
					$rand = TLTH::random_string(8);

					$new_code = str_replace(".st{$i}{", ".st{$rand}{", $new_code);
					$new_code = str_replace("class=\"st{$i}\"", "class=\"st{$rand}\"", $new_code);
				}

				// Replace instances of #Layer_1, #Layer_2, etc
				for( $i = 0; $i <= $loops; $i++ ) {
					$rand = TLTH::random_string(8);

					$new_code = str_replace("id=\"Layer_{$i}\"", "id=\"Layer_{$rand}\"", $new_code);
				}

				// Replace instances of #SVGID_1_, #SVGID_2_, etc
				for( $i = 0; $i <= $loops; $i++ ) {
					$rand = TLTH::random_string(8);

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

	/*
	 * TLTH Theme Utility Functions
	 */

	/*
	 * Check if shop notice is enabled
	 */
	public static function is_shop_notice_enabled()
	{
		return get_field('shop-notice-activated', 'option');
	}

	/*
	 * Return product category colors
	 */
	public static function get_product_cat_color($term_id)
	{
		return get_field('product-cat-color', 'product_cat_' . $term_id);
	}

	/*
	 * Get product categories badges markup
	 */
	public static function get_product_categories_badges()
	{
		$product_categories = get_the_terms(get_the_ID(), 'product_cat');

		if (! empty($product_categories)) { ?>
			<div class="product-themes">
				<ul>
					<? foreach($product_categories as $product_category){ ?>
					<li>
						<a href="<?= get_term_link($product_category); ?>" class="product-theme-icon" <? if( TLTH::get_product_cat_color($product_category->term_id) ){ ?>style="background-color: <?= TLTH::get_product_cat_color($product_category->term_id); ?>" <? } ?>>
							<?
								$product_cat_img_id = get_term_meta( $product_category->term_id, 'thumbnail_id', true );

								if( ! empty($product_cat_img_id) ) {
									echo '<span class="img">' . file_get_contents( get_attached_file($product_cat_img_id) ) . '</span>';
								}
								?>

							<span class="label"><?= $product_category->name; ?></span>
						</a>
					</li>
					<? } ?>
				</ul>
			</div>
		<?php
		}
	}

	/*
	 * Check if string starts with a vowel
	 */
	public static function is_vowel($string)
	{
		$first_letter = mb_substr($string, 0, 1);

		return in_array($first_letter, ['a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U', 'É', 'È', 'Ê', 'é', 'è', 'ê']);
	}

	/*
	 * Stylized strings - replaces comment markers
	 */
	public static function stylized_string_red($string)
	{
		$string = str_replace('/*', '<span class="color-red">', $string);
		$string = str_replace('*/', '</span>', $string);

		return $string;
	}

	/*
	 * Product categories configured to appear at the end of the shop (ACF option).
	 */
	public static function get_shop_excluded_product_categories()
	{
		$product_categories = get_field('shop-product-categories-to-hide', 'option');

		return $product_categories;
	}

	/*
	 * Normalized deprioritized category IDs, including child categories.
	 */
	public static function get_shop_deprioritized_product_category_ids()
	{
		$raw = self::get_shop_excluded_product_categories();

		if ($raw === null || $raw === false || '' === $raw) {
			return [];
		}

		if (! is_array($raw)) {
			$raw = [$raw];
		}

		$ids = array_values(array_filter(array_map('intval', $raw)));

		if (empty($ids)) {
			return [];
		}

		$expanded = [];

		foreach ($ids as $id) {
			$expanded[] = $id;
			$children = get_term_children($id, 'product_cat');

			if (! is_wp_error($children) && ! empty($children)) {
				$expanded = array_merge($expanded, array_map('intval', $children));
			}
		}

		return array_values(array_unique($expanded));
	}

	/*
	 * Get Google review data
	 */
	public static function get_review_data()
	{
		$api_key = 'AIzaSyA933ZswohWGbkXhvUR1fAOnDtJVjMiaJY';
		$place_id = 'ChIJJ9rllACkyUwRdZ5wgINOFxw';
		$lang = LANG;
		$url = "https://maps.googleapis.com/maps/api/place/details/json?placeid={$place_id}&language={$lang}&fields=rating,reviews,user_ratings_total&key={$api_key}";

		// create curl resource
		$ch = curl_init();

		// set url
		curl_setopt($ch, CURLOPT_URL, $url);

		// return the transfer as a string
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		// $output contains the output string
		$output = curl_exec($ch);

		// close curl resource to free up system resources
		curl_close($ch);

		$output = json_decode($output);

		if ($output->status == 'OK') {
			return $output;
		} else {
			return [];
		}
	}

	/*
	 * Weighted random selection from values array based on weights array
	 */
	public static function weighted_random($values, $weights)
	{
		$count = count($values);
		$i = 0;
		$n = 0;
		$num = mt_rand(0, array_sum($weights));
		while ($i < $count) {
			$n += $weights[$i];
			if ($n >= $num) {
				break;
			}
			$i++;
		}
		return $values[$i];
	}

	/*
	 * Format email text - fixes accents
	 */
	public static function format_email_text($str)
	{
		$str = str_replace(['&lt;', '&gt;'], ['<', '>'], $str);

		return $str;
	}

}

