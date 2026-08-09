<?php

/**
 * Navigation Configuration
 * 
 * Handles menu registration and styling
 */

// Register navigation menus
add_action('init', function () {
    register_nav_menus(array(
        'primary' => __('Navigation primaire', 'tlth'),
        'footer' => __('Navigation footer', 'tlth'),
    ));
});

// Add Tailwind classes to menu links
add_filter('nav_menu_link_attributes', function ($atts, $item, $args) {
    if (isset($args->theme_location) && $args->theme_location === 'primary') {
        $atts['class'] = 'text-deep-blue text-[15px] font-normal no-underline hover:text-teal transition-colors duration-300';
    }
    if (isset($args->theme_location) && $args->theme_location === 'footer') {
        $atts['class'] = 'text-white hover:text-teal transition-colors duration-200 text-[15px]';
    }
    return $atts;
}, 10, 3);

// Remove default WordPress menu item classes
add_filter('nav_menu_css_class', function ($classes, $item, $args) {
    if (isset($args->theme_location) && ($args->theme_location === 'primary' || $args->theme_location === 'footer')) {
        $classes = array(); // Remove default WordPress menu classes
    }
    return $classes;
}, 10, 3);

