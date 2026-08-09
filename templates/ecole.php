<?php
/**
 * Template Name: École / Garderie
 *
 * Page template for the École/Garderie (education) page.
 * Includes ACF-driven sections matching the Figma design.
 */

get_header();

if (have_posts()) : while (have_posts()) : the_post();

// Section 1: Page Hero
$block = ['anchor' => 'hero'];
include get_template_directory() . '/src/blocks/page-hero/block.php';

// Section 2: Group discount tiers
// No context — reads ACF fields from this page (location rule added to discount-section/acf.json)
$block = ['anchor' => 'rabais'];
include get_template_directory() . '/src/blocks/discount-section/block.php';

// Section 3: Unique statement — "nous croyons en votre travail..."
$block = ['anchor' => 'mission'];
include get_template_directory() . '/src/blocks/unique-statement/block.php';

// Section 4: Promo code request — split image
$block = [
    'anchor'  => 'code-promo',
    'context' => [
        'shapes'         => 'option-2',
        'section_title'  => get_field('promo_code_title') ?: 'Demandez votre code promotionnel',
        'description'    => get_field('promo_code_description'),
        'cta_link'       => get_field('promo_code_cta'),
        'featured_image' => get_field('promo_code_image'),
        'img_rounded'    => 'rounded-3xl',
    ],
];
include get_template_directory() . '/src/blocks/split-img/block.php';

// Section 5: Campaign steps (6-step fundraising guide)
$block = ['anchor' => 'campagne'];
include get_template_directory() . '/src/blocks/campaign-steps/block.php';

// Section 6: Revenue CTA banner — "Recevez 7$ par livre vendu!"
$block = ['anchor' => 'cta'];
include get_template_directory() . '/src/blocks/cta-banner/block.php';

endwhile; endif;

get_footer();
