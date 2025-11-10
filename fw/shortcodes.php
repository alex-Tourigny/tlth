<?php
function bouton_shortcode( $attributes, $content = "" )
{
	// If the URL is missing then return a hidden span that makes it easy to find broken shortcodes
	if( empty($attributes) || ! isset($attributes['href']) || empty($attributes['href']) )
		return "<span style='display:none !important'>Shortcode [bouton] is missing href attribute</span>";

	$url = $attributes['href'];
	$target = $attributes['target'];
	$class = $attributes['color'];

	if( ! empty($target) ) {
		$target_text = 'target="_blank"';
	} else {
		$target_text = '';
	}

	return "<a href='{$url}' {$target_text} class='button in-content outline {$class}'>{$content}</a>";
}
add_shortcode( 'bouton', 'bouton_shortcode' );

function tooltip_shortcode( $attributes, $content = "" )
{
	return "<span class='tooltip' data-text='{$attributes['texte']}'>{$content}</span>";
}
add_shortcode( 'tooltip', 'tooltip_shortcode' );