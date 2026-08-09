<?php

/**
 * Helper Functions
 * 
 * Utility functions used throughout the theme
 */

/**
 * Get current language with fallback
 * 
 * @return string Current language code (defaults to 'fr')
 */
function tlth_get_current_lang()
{
    if (defined('LANG')) {
        return LANG;
    }
    if (function_exists('pll_current_language')) {
        return pll_current_language();
    }
    return 'fr'; // Default fallback
}

/**
 * Custom ACF Button Helper
 * 
 * @param array $btn ACF link field array
 * @param string $class Additional CSS classes
 * @param string $data Additional data attributes
 * @return string Button HTML markup
 */
function tlth_btn($btn, $class = '', $data = '')
{
    if (!is_array($btn)) return;
    if (!isset($btn['target'])) $btn['target'] = false;

    $icon = '';
    if (strpos($class, 'calendar') !== false) {
        $icon = file_get_contents(get_template_directory() . '/assets/images/icons/calendar.svg');
    }

    return '<a href="' . $btn['url'] . '" class="btn ' . $class . '" ' . ($btn['target'] ? 'target="' . $btn['target'] . '"' : '') . ' ' . $data . '>' . $icon . '<span class="text">' . $btn['title'] . '</span></a>';
}

/**
 * Image URLs for the product flipbook preview.
 *
 * Prefers book-maker cover/image pages; falls back to preview-gallery.
 *
 * @param int $product_id Product post ID.
 * @return string[]
 */
function tlth_get_product_flipbook_image_urls( $product_id ) {
	$product_id = (int) $product_id;
	$urls       = array();

	if ( function_exists( 'get_book_maker_pdf_content' ) ) {
		$book_pages = get_book_maker_pdf_content( $product_id, array( 'mode' => 'workshop' ), true );

		if ( ! empty( $book_pages ) && is_array( $book_pages ) ) {
			foreach ( $book_pages as $page ) {
				if (
					! empty( $page['type'] )
					&& in_array( $page['type'], array( 'cover', 'image' ), true )
					&& ! empty( $page['image'] )
				) {
					$urls[] = $page['image'];
				}
			}
		}
	}

	if ( ! empty( $urls ) ) {
		return $urls;
	}

	$gallery = get_field( 'preview-gallery', $product_id );

	if ( empty( $gallery ) || ! is_array( $gallery ) ) {
		return array();
	}

	foreach ( $gallery as $image ) {
		$url = is_array( $image ) ? ( $image['url'] ?? '' ) : '';

		if ( $url ) {
			$urls[] = $url;
		}
	}

	return array_values( array_filter( $urls ) );
}

/**
 * Whether a product has images to show in the flipbook preview.
 *
 * @param int $product_id Product post ID.
 * @return bool
 */
function tlth_product_has_flipbook_preview( $product_id ) {
	return ! empty( tlth_get_product_flipbook_image_urls( $product_id ) );
}

/**
 * Render flipbook page markup from a list of image URLs.
 *
 * @param string[] $image_urls Image URLs in page order.
 * @return string
 */
function tlth_render_flipbook_inner( $image_urls ) {
	if ( empty( $image_urls ) ) {
		return '';
	}

	$total_pages = count( $image_urls );
	$html        = '<div class="flipbook-inner">';
	$i           = 1;

	foreach ( $image_urls as $url ) {
		$is_hard  = ( 1 === $i || 2 === $i || $i === $total_pages - 1 || $i === $total_pages );
		$html    .= '<div' . ( $is_hard ? ' class="hard"' : '' ) . '>';
		$html    .= '<img src="' . esc_url( $url ) . '" alt="" loading="lazy" />';
		$html    .= '</div>';
		$i++;
	}

	$html .= '</div>';

	return $html;
}

function tlth_colored_text($text, $color = 'random')
{
    if (empty($text)) return '';

    if ($color == 'random') {
        // Colors matching main.js: teal, coral, gold, light-blue
        $colors = ['#48c0b6', '#f1665d', '#ffc635', '#5695d8'];

        // Process text between /* and */ with colored letters
        $processed_text = preg_replace_callback(
            '/\/\*(.*?)\*\//',
            function ($matches) use ($colors) {
                $text = $matches[1];
                $colored_html = '';

                // Split text into individual characters (UTF-8 safe)
                $chars = mb_str_split($text, 1, 'UTF-8');

                foreach ($chars as $i => $char) {
                    $color = $colors[$i % count($colors)];
                    $colored_html .= '<span style="color: ' . esc_attr($color) . '">' . esc_html($char) . '</span>';
                }

                return '<span>' . $colored_html . '</span>';
            },
            $text
        );
    } else {
        // Replace all /*...*/ with <span class="text-{color}">...</span>
        $processed_text = preg_replace_callback(
            '/\/\*(.*?)\*\//s',
            function ($matches) use ($color) {
                return '<span class="text-' . esc_attr($color) . '">' . esc_html($matches[1]) . '</span>';
            },
            $text
        );
    }
    return $processed_text;
}

/**
 * Customize Gravity Forms Submit Button
 * 
 * @param string $button Default button HTML
 * @param array $form Form object
 * @return string Custom button HTML
 */
function tlth_gform_submit_button($button, $form)
{
    $button_text = '';
    if (preg_match('/value=[\'"]([^\'"]+)[\'"]/', $button, $matches)) {
        $button_text = $matches[1];
    } elseif (preg_match('/>([^<]+)</', $button, $matches)) {
        $button_text = $matches[1];
    }

    if ($button_text === '') {
        $button_text = __('envoyer', 'tlth');
    }

    $class = 'btn-form';
    if ($form['id'] == 41) {
        $class = 'btn-primary';
    }

    $custom_button = '<button class="btn ' . $class . '" type="submit"><span class="text">' . esc_html($button_text) . '</span></button>';
    return $custom_button;
}
add_filter('gform_submit_button', 'tlth_gform_submit_button', 10, 2);

// Register strings for Polylang
function tlth_register_strings($strings)
{
    if (! function_exists('pll_register_string')) {
        return;
    }
    foreach ($strings as $string) {
        pll_register_string($string[0], $string[1], $string[2]);
    }
}

// Shortcodes - OLD
function bouton_shortcode($attributes, $content = "")
{
    // If the URL is missing then return a hidden span that makes it easy to find broken shortcodes
    if (empty($attributes) || ! isset($attributes['href']) || empty($attributes['href']))
        return "<span style='display:none !important'>Shortcode [bouton] is missing href attribute</span>";

    $url = $attributes['href'];
    $target = $attributes['target'];
    $class = $attributes['color'];

    if (! empty($target)) {
        $target_text = 'target="_blank"';
    } else {
        $target_text = '';
    }

    return "<a href='{$url}' {$target_text} class='button in-content outline {$class}'>{$content}</a>";
}
add_shortcode('bouton', 'bouton_shortcode');

function tooltip_shortcode($attributes, $content = "")
{
    return "<span class='tooltip' data-text='{$attributes['texte']}'>{$content}</span>";
}
add_shortcode('tooltip', 'tooltip_shortcode');
