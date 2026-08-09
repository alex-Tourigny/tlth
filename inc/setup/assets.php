<?php

/**
 * Asset Enqueuing Configuration
 * 
 * Handles enqueuing of CSS and JavaScript files for frontend and editor
 */

// Enqueue global compiled Tailwind CSS (dist)
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('tlth-theme', get_template_directory_uri() . '/dist/css/main.css', [], time());

    // Enqueue header JavaScript
    wp_enqueue_script('tlth-main', get_template_directory_uri() . '/dist/js/main.js', ['jquery'], time(), true);
    wp_enqueue_script(
        'tlth-scroll-animations',
        get_template_directory_uri() . '/dist/js/scroll-animations.js',
        ['jquery'],
        time(),
        true
    );
    wp_enqueue_script(
        'tlth-shop-confetti',
        get_template_directory_uri() . '/dist/js/shop-btn-confetti.js',
        [],
        time(),
        true
    );

    wp_enqueue_style('slick-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css', [], '1.8.1');
    wp_enqueue_style('slick-theme-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css', [], '1.8.1');
    wp_enqueue_script('slick-js', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', ['jquery'], '1.8.1', true);
});

// Enqueue editor styles for Gutenberg (editor.css after main so overrides apply)
add_action('enqueue_block_editor_assets', function () {
    wp_enqueue_style('tlth-theme', get_template_directory_uri() . '/dist/css/main.css', [], time());
    wp_enqueue_style('tlth-editor', get_template_directory_uri() . '/dist/css/editor.css', ['tlth-theme'], time());
});

// Automatically enqueue block assets (CSS and JS) only for blocks used on the page
add_action('wp_enqueue_scripts', function () {
    $blocks_dir = get_template_directory() . '/src/blocks';
    $dist_blocks_dir = get_template_directory() . '/dist/blocks';

    // Get all blocks used on the current page
    $used_blocks = [];
    $contents_to_check = [];

    if (is_singular()) {
        global $post;
        if ($post) {
            $contents_to_check[] = $post->post_content;
        }
    } elseif (is_home() || is_front_page()) {
        $front_page_id = get_option('page_on_front');
        if ($front_page_id) {
            $front_page = get_post($front_page_id);
            if ($front_page) {
                $contents_to_check[] = $front_page->post_content;
            }
        }
    }

    foreach ($contents_to_check as $content) {
        if (has_blocks($content)) {
            $blocks = parse_blocks($content);
            foreach ($blocks as $block) {
                if (isset($block['blockName']) && strpos($block['blockName'], 'acf/') === 0) {
                    $block_name = str_replace('acf/', '', $block['blockName']);
                    $used_blocks[] = $block_name;
                }
            }
        }
    }

    // Remove duplicates
    $used_blocks = array_unique($used_blocks);

    if (in_array('team-slider', $used_blocks, true)) {
        wp_enqueue_style(
            'swiper-bundle',
            'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
            [],
            '11.2.6'
        );
        wp_enqueue_script(
            'swiper-bundle',
            'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
            [],
            '11.2.6',
            true
        );
    }

    // For dedicated PHP page templates, blocks are included directly (not via Gutenberg),
    // so they won't appear in post_content. Enqueue their assets explicitly.
    $template_blocks = [
        'templates/a-propos.php' => ['page-hero', 'split-img', 'features-grid', 'unique-statement', 'team-slider', 'faq-accordion'],
        'templates/ecole.php'    => ['page-hero', 'discount-section', 'unique-statement', 'split-img', 'campaign-steps', 'cta-banner'],
    ];
    $direct_include_blocks = [];

    $current_template = get_page_template_slug();
    if (isset($template_blocks[$current_template])) {
        $direct_include_blocks = array_merge($direct_include_blocks, $template_blocks[$current_template]);
    }

    // WooCommerce single product includes split-img when group-tarif-check is enabled.
    if (is_product()) {
        global $product;
        if ($product) {
            $product_fields = get_fields($product->get_id());
            if (!empty($product_fields['group-tarif-check'])) {
                $direct_include_blocks[] = 'split-img';
            }
        }
    }

    if (is_shop() || is_product_taxonomy()) {
        $direct_include_blocks[] = 'shop-archive-promo';
    }

    if ($direct_include_blocks) {
        $used_blocks = array_unique(array_merge($used_blocks, $direct_include_blocks));
    }

    if (is_dir($blocks_dir)) {
        foreach (glob($blocks_dir . '/*', GLOB_ONLYDIR) as $block_dir) {
            $block_name = basename($block_dir);

            // Only enqueue assets for blocks that are actually used on the page
            if (in_array($block_name, $used_blocks)) {
                // Check for CSS file in dist folder only (compiled CSS)
                $css_file = $dist_blocks_dir . '/' . $block_name . '/style.css';
                if (file_exists($css_file) && filesize($css_file) > 0) {
                    wp_enqueue_style(
                        'tlth-' . $block_name . '-style',
                        get_template_directory_uri() . '/dist/blocks/' . $block_name . '/style.css',
                        [],
                        time()
                    );
                }

                // Check for and enqueue JS file (only in src folder)
                $js_file = $block_dir . '/script.js';
                if (file_exists($js_file) && filesize($js_file) > 0) {
                    $script_deps = ['jquery'];
                    if ($block_name === 'team-slider') {
                        $script_deps[] = 'swiper-bundle';
                    }
                    wp_enqueue_script(
                        'tlth-' . $block_name . '-script',
                        get_template_directory_uri() . '/src/blocks/' . $block_name . '/script.js',
                        $script_deps,
                        time(),
                        true
                    );
                }
            }
        }
    }
});

// Enqueue block CSS for admin/editor area
add_action('enqueue_block_editor_assets', function () {
    wp_enqueue_style(
        'swiper-bundle',
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
        [],
        '11.2.6'
    );
    wp_enqueue_script(
        'swiper-bundle',
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
        [],
        '11.2.6',
        true
    );

    $team_slider_js = get_template_directory() . '/src/blocks/team-slider/script.js';
    if (file_exists($team_slider_js) && filesize($team_slider_js) > 0) {
        wp_enqueue_script(
            'tlth-team-slider-script',
            get_template_directory_uri() . '/src/blocks/team-slider/script.js',
            ['jquery', 'swiper-bundle'],
            filemtime($team_slider_js),
            true
        );
    }

    $blocks_dir = get_template_directory() . '/src/blocks';
    $dist_blocks_dir = get_template_directory() . '/dist/blocks';

    if (is_dir($blocks_dir)) {
        foreach (glob($blocks_dir . '/*', GLOB_ONLYDIR) as $block_dir) {
            $block_name = basename($block_dir);

            // Check for CSS file in dist folder only (compiled CSS)
            $css_file = $dist_blocks_dir . '/' . $block_name . '/style.css';

            if (file_exists($css_file) && filesize($css_file) > 0) {
                wp_enqueue_style(
                    'tlth-' . $block_name . '-editor-style',
                    get_template_directory_uri() . '/dist/blocks/' . $block_name . '/style.css',
                    [],
                    filemtime($css_file)
                );
            }
        }
    }
});

