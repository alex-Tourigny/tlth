<?php

/**
 * ACF Configuration
 * 
 * Handles ACF JSON sync, block registration, and options pages
 */

// ACF JSON sync
add_filter('acf/settings/save_json', function () {
    return get_stylesheet_directory() . '/src/blocks';
});

add_filter('acf/settings/load_json', function ($paths) {
    $paths[] = get_stylesheet_directory() . '/src/blocks';

    // Also scan subdirectories for JSON files
    $blocks_dir = get_stylesheet_directory() . '/src/blocks';
    if (is_dir($blocks_dir)) {
        foreach (glob($blocks_dir . '/*', GLOB_ONLYDIR) as $block_dir) {
            $paths[] = $block_dir;
        }
    }

    return $paths;
});

// Automatically register ACF blocks from the blocks folder
add_action('acf/init', function () {
    if (function_exists('acf_register_block_type')) {
        $blocks_dir = get_template_directory() . '/src/blocks';

        if (is_dir($blocks_dir)) {
            // Scan all directories in the blocks folder
            foreach (glob($blocks_dir . '/*', GLOB_ONLYDIR) as $block_dir) {
                $block_name = basename($block_dir);
                $block_file = $block_dir . '/block.php';

                // Only register if block.php exists
                if (file_exists($block_file)) {
                    // Convert kebab-case to Title Case for display
                    $block_title = ucwords(str_replace('-', ' ', $block_name));

                    // Register the block
                    acf_register_block_type([
                        'name'              => $block_name,
                        'title'             => $block_title,
                        'render_template'   => $block_file,
                        'mode'              => 'auto',
                        'supports'          => array(
                            'align' => false,
                            'mode'  => true,
                            'anchor' => true,
                        ),
                    ]);
                }
            }
        }
    }
});


