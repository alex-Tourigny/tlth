<?php
/*
 * "styled_button" TinyMCE button
 *
 * To make a new tinymce btn, copy these three functions and replace all instances of "styled_button"
 */
add_action('init', 'tinymce_styled_button');
function tinymce_styled_button()
{
	if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') && get_user_option('rich_editing') == 'true') {
		return;
	}

	add_filter('mce_external_plugins', 'tinymce_styled_button_js');
	add_filter('mce_buttons', 'tinymce_styled_button_register');
}

function tinymce_styled_button_js($plugin_array)
{
	$plugin_array['styled_button'] = THEME_URL . '/assets/js/tinymce/styled_button.js';
	return $plugin_array;
}

function tinymce_styled_button_register($buttons)
{
	$buttons[] = "styled_button";
	return $buttons;
}

/*
 * Tooltips
 */
add_action('init', 'tinymce_tooltip');
function tinymce_tooltip()
{
	if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') && get_user_option('rich_editing') == 'true') {
		return;
	}

	add_filter('mce_external_plugins', 'tinymce_tooltip_js');
	add_filter('mce_buttons', 'tinymce_tooltip_register');
}

function tinymce_tooltip_js($plugin_array)
{
	$plugin_array['tooltip'] = THEME_URL . '/assets/js/tinymce/tooltip.js';
	return $plugin_array;
}

function tinymce_tooltip_register($buttons)
{
	$buttons[] = "tooltip";
	return $buttons;
}