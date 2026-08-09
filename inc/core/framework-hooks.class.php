<?php
/*
 * This class manages all of the hooks and filters that the framework uses
 */

class TLTH_Hooks extends TLTH {

	function __construct()
	{
		// remove the Editor from the wp-admin
		if( ! defined('DISALLOW_FILE_EDIT') ) {
			define('DISALLOW_FILE_EDIT', true);
		}

		// remove wp branding
		add_action('wp_before_admin_bar_render', [$this, '_remove_wp_branding']);

		// add GTM to the front end header
		add_action("wp_head", [$this, '_header_analytics']);
		add_action("wp_head", [$this, '_global_script_variables']);

		// adds the current user role to the body_class()
		add_action('admin_body_class', [$this, '_body_classes']);

		// set the default link type for embedded images in the wysiwyg's
		update_option('image_default_link_type', 'none');

		// remove the embedded lightbox for wysisyg galleries
		add_filter('use_default_gallery_style', '__return_false');

		// allow svg mime type
		add_filter('upload_mimes', [$this, '_cc_mime_types']);

		// more svg support
		add_filter('wp_check_filetype_and_ext', [$this, '_wp_check_filetype_and_ext'], 10, 4);
		add_filter('wp_check_filetype_and_ext', [$this, '_wp_svgs_allow_svg_upload'], 10, 4);

		// show svg thumbnails in the media viewer in the admin panel
		add_filter('wp_prepare_attachment_for_js', [$this, '_common_svg_media_thumbnails'], 10, 3);

		// automatically converts emails in tinyMCE to the WordPress antispam filter
		add_filter('the_content', [$this, 'convert_all_mailtos_to_antispambot_mailtos']);

		// automatically converts emails in ACF tinyMCE's to the WordPress antispam filter
		add_filter('acf/format_value/type=wysiwyg', [$this, 'convert_all_mailtos_to_antispambot_mailtos']);

		// disables pingbacks
		add_filter('xmlrpc_enabled', '__return_false');

		// Don't allow subscribers any access to the wp-admin
		add_action('admin_init', [$this, '_lock_down_wp_admin_to_subscribers']);

		// Disable some default WP emails
		add_filter('send_password_change_email', '__return_false');
		add_filter('send_email_change_email', '__return_false');
	}

	/*
	 * Removes the WordPress branding from the wp-admin
	 */
	public function _remove_wp_branding()
	{
		global $wp_admin_bar;

		$wp_admin_bar->remove_menu('customize');		// @todo fix this
		$wp_admin_bar->remove_menu('wp-logo');          // Remove the WordPress logo
		$wp_admin_bar->remove_menu('about');            // Remove the about WordPress link
		$wp_admin_bar->remove_menu('wporg');            // Remove the WordPress.org link
		$wp_admin_bar->remove_menu('documentation');    // Remove the WordPress documentation link
		$wp_admin_bar->remove_menu('support-forums');   // Remove the support forums link
		$wp_admin_bar->remove_menu('feedback');         // Remove the feedback link
		$wp_admin_bar->remove_menu('updates');          // Remove the updates link
		$wp_admin_bar->remove_menu('comments');         // Remove the comments link
		$wp_admin_bar->remove_menu('new-content');      // Remove the content link
		$wp_admin_bar->remove_menu('wpseo-menu');
	}

	/*
	 * Adds the ACF Google Analytics to the front end header
	 */
	public function _header_analytics()
	{
		if( ! function_exists('acf_add_options_sub_page') ) { return; }

		$analytics = get_field('gtm-top', 'option');

		if( ! empty($analytics) ) echo strip_tags($analytics, '<script><iframe><noscript><meta>');
	}

	/*
	 * Adds global varibale sin the script tags in header
	 */
	public function _global_script_variables()
	{ ?>

		<script>
			window.ajax_url = '<?php echo admin_url('admin-ajax.php'); ?>';
			window.theme_url = '<?php echo THEME_URL; ?>';
			<?php if( get_field('google-maps-marker-img', 'option') ){ ?>
				window.google_maps_marker_img_url = '<?php echo get_field('google-maps-marker-img', 'option')['url']; ?>';
			<?php } ?>
			<?php if( get_field('google-maps-styles', 'option') ){ ?>
				window.google_maps_styles = <?php echo get_field('google-maps-styles', 'option'); ?>;
			<?php } ?>
		</script>

	<?php
	}

	public function _body_classes( $classes )
	{
		global $current_user;
		$user_roles = $current_user->roles;
		$user_role = 'user-role-' . array_shift($user_roles);

		if( is_array($classes) ) {
			$classes[] = $user_role;
		} else {
			$classes .= $user_role;
			return $classes;
		}
	}

	public function _cc_mime_types( $mimes )
	{
		$mimes['svg'] = 'image/svg+xml';
		$mimes['svgz'] = 'image/svg+xml';

		return $mimes;
	}

	public function _wp_check_filetype_and_ext( $checked, $file, $filename, $mimes ) {

		if ( ! $checked['type'] ) {

			$check_filetype		= wp_check_filetype( $filename, $mimes );
			$ext				= $check_filetype['ext'];
			$type				= $check_filetype['type'];
			$proper_filename	= $filename;

			if ( $type && 0 === strpos( $type, 'image/' ) && $ext !== 'svg' ) {
				$ext = $type = false;
			}

			$checked = compact( 'ext','type','proper_filename' );
		}

		return $checked;

	}

	public function _wp_svgs_allow_svg_upload( $data, $file, $filename, $mimes ) {

		global $wp_version;
		if ( $wp_version !== '4.7.1' || $wp_version !== '4.7.2' ) {
			return $data;
		}

		$filetype = wp_check_filetype( $filename, $mimes );

		return [
			'ext'				=> $filetype['ext'],
			'type'				=> $filetype['type'],
			'proper_filename'	=> $data['proper_filename']
		];

	}

	public function _common_svg_media_thumbnails( $response, $attachment, $meta )
	{
		if($response['type'] === 'image' && $response['subtype'] === 'svg+xml' && class_exists('SimpleXMLElement')) {
			try {
				$path = get_attached_file($attachment->ID);
				if(@file_exists($path))
				{
					$svg = new SimpleXMLElement(@file_get_contents($path));
					$src = $response['url'];
					$width = (int) $svg['width'];
					$height = (int) $svg['height'];

					//media gallery
					$response['image'] = compact( 'src', 'width', 'height' );
					$response['thumb'] = compact( 'src', 'width', 'height' );

					//media single
					$response['sizes']['full'] = array(
						'height'        => $height,
						'width'         => $width,
						'url'           => $src,
						'orientation'   => $height > $width ? 'portrait' : 'landscape',
					);
				}
			}
			catch(Exception $e){}
		}

		return $response;
	}

	/*
	 * Automatically converts all emails in the_content to the WP antispam style
	 */
	function convert_all_mailtos_to_antispambot_mailtos( $content )
	{
		// Search content for mailto:
		$mailto_found = preg_match_all('`\<a([^>]+)href\=\"mailto\:([^">]+)\"([^>]*)\>`ism', $content, $emails_found, PREG_SET_ORDER);

		// No emails found, return normal content
		if(! $mailto_found || empty($emails_found) ) return $content;

		// Replace each found email by an anti-spam one
		foreach($emails_found as $email_found){
			$email = $email_found[2];
			$fixed_email = antispambot($email);

			$content = str_replace($email, $fixed_email, $content);
		}

		return $content;
	}

	public static function i18n_404_content()
	{
		acf_add_options_sub_page('Page 404');

		$language_terms = get_terms(
			[
				'taxonomy' => 'term_language',
				'hide_empty' => true
			]
		);

		$fields = [];
		foreach($language_terms as $language)
		{
			$slug = str_replace('pll_', '', $language->slug);

			$fields[] = [
				'key' => '404-content-' . $slug,
				'label' => 'Contenu 404 (' . $language->name . ')',
				'name' => '404-content-' . $slug,
				'type' => 'wysiwyg',
			];
		}

		acf_add_local_field_group(
			[
				'key' => 'kantaloup_404',
				'title' => 'Option: 404',
				'fields' => $fields,
				'location' => [
					[
						[
							'param' => 'options_page',
							'operator' => '==',
							'value' => 'acf-options-page-404',
						],
					],
				],
				'menu_order' => 0,
				'position' => 'normal',
				'style' => 'seamless',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
			]
		);
	}

	public static function _lock_down_wp_admin_to_subscribers()
	{
		$role = get_role('subscriber');
		$role->remove_cap( 'read' );
	}
}

