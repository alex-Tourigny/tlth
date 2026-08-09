<?php
/*
 * Template Name: Boutique
 *
 * The Template for displaying product archives, including the main shop page.
 *
 * This template is based on the updated WooCommerce template (v8.6.0)
 * and includes legacy customizations.
 *
 * Customizations include:
 * - A custom header with page title and archive description.
 * - Inclusion of a custom content-rows helper.
 * - A grid layout dividing the page into a sidebar (inline product filters) and main product content.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
do_action( 'woocommerce_before_main_content' );
?>

	<section class="shop-hero pt-10 pb-8 lg:pb-24">
		<div class="max-w-content mx-auto text-center">
			<h1>
				<?= pll__('Bienvenue dans </br>la') ?> <?= tlth_colored_text("/*".pll__('boutique')."*/") ?>
			</h1>

			<?php
			/**
			 * Hook: woocommerce_archive_description.
			 *
			 * @hooked woocommerce_taxonomy_archive_description - 10
			 * @hooked woocommerce_product_archive_description - 10
			 */
			do_action( 'woocommerce_archive_description' );
			?>
		</div>
	</section>

<?php
// Include your custom content rows helper
$cr_page_id = wc_get_page_id( 'shop' );
include( THEME_PATH . '/includes/content-rows/helpers/the-rows.php' );
?>

	<section class="shop-archive pb-16 lg:pb-24">
		<div class="max-w-content mx-auto px-8">
			<div class="grid gap-5 lg:grid-cols-[280px_1fr]">
				<aside class="shop-archive__aside">
					<div class="the-sidebar">
						<div class="inner-wrapper-sticky">
						<form role="search" method="get" class="mb-6" action="<?php echo esc_url( home_url( '/' ) ); ?>">
							<label class="sr-only" for="woocommerce-product-search"><?= pll__('Rechercher') ?></label>
							<div class="relative shop-filter-search">
								<input
									type="search"
									id="woocommerce-product-search"
									name="s"
									value="<?php echo esc_attr( get_search_query() ); ?>"
									placeholder="<?= pll__('Rechercher') ?>"
									class="w-full rounded-full border border-primary pl-12 pr-4 py-2 text-[15px] text-deep-blue placeholder:text-deep-blue/60"
									autocomplete="off"
								>
								<input type="hidden" name="post_type" value="product">
							</div>
						</form>
						<div class="sidebar__inner">
							<?php
							$shop_deprioritized_cat_ids = class_exists( 'TLTH' ) ? TLTH::get_shop_deprioritized_product_category_ids() : [];

							$thematic_terms_raw = get_terms(
								array(
									'taxonomy' => 'product_cat',
									'hide_empty' => true,
								)
							);
							$thematic_terms = is_wp_error( $thematic_terms_raw ) ? [] : $thematic_terms_raw;

							$product_type_terms_raw = get_terms(
								array(
									'taxonomy' => 'product_tag',
									'hide_empty' => true,
								)
							);
							$product_type_terms = is_wp_error( $product_type_terms_raw ) ? [] : $product_type_terms_raw;

							// Current selections (shop page + layered-style query vars).
							// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only filter state from URL.
							$selected_cats = [];
							if ( isset( $_REQUEST['product_cat'] ) ) {
								$raw_cats = wp_unslash( $_REQUEST['product_cat'] );
								if ( is_array( $raw_cats ) ) {
									$selected_cats = $raw_cats;
								} else {
									$selected_cats = preg_split( '/\s*,\s*/', (string) $raw_cats, -1, PREG_SPLIT_NO_EMPTY );
								}
							}
							$selected_cats = array_values( array_filter( array_map( 'sanitize_title', $selected_cats ) ) );
							// phpcs:ignore WordPress.Security.NonceVerification.Recommended
							$selected_tags = [];
							if ( isset( $_REQUEST['product_tag'] ) ) {
								$raw_tags = wp_unslash( $_REQUEST['product_tag'] );
								if ( is_array( $raw_tags ) ) {
									$selected_tags = $raw_tags;
								} else {
									$selected_tags = preg_split( '/\s*,\s*/', (string) $raw_tags, -1, PREG_SPLIT_NO_EMPTY );
								}
							}
							$selected_tags = array_values( array_filter( array_map( 'sanitize_title', $selected_tags ) ) );

							$deprioritized_lookup = $shop_deprioritized_cat_ids ? array_flip( array_map( 'intval', $shop_deprioritized_cat_ids ) ) : [];
							$primary_thematic_terms   = [];
							$trailing_thematic_terms  = [];

							foreach ( $thematic_terms as $term ) {
								if ( $deprioritized_lookup && isset( $deprioritized_lookup[ (int) $term->term_id ] ) ) {
									$trailing_thematic_terms[] = $term;
								} else {
									$primary_thematic_terms[] = $term;
								}
							}

							$sort_terms_by_name = function ( $a, $b ) {
								return strcasecmp( $a->name, $b->name );
							};

							usort( $primary_thematic_terms, $sort_terms_by_name );
							usort( $trailing_thematic_terms, $sort_terms_by_name );
							$thematic_terms = array_merge( $primary_thematic_terms, $trailing_thematic_terms );
							usort(
								$product_type_terms,
								function ( $a, $b ) {
									return strcasecmp( $a->name, $b->name );
								}
							);
							?>
							<?php if ( $thematic_terms || $product_type_terms ) : ?>
								<ul class="shop-sidebar-widgets" role="list">
								<li class="widget widget_tlth_shop_filters">
								<form class="shop-archive-filters" method="get" action="">
									<div class="product-search-filter-terms">
									<?php if ( $thematic_terms ) : ?>
										<details class="group lg:mb-6">
											<summary class="product-search-filter-terms-heading cursor-pointer list-none [&::-webkit-details-marker]:hidden">
												<span class="text-[15px] font-bold text-deep-blue"><?php echo esc_html( pll__( 'Thématique' ) ); ?></span>
											</summary>
											<ul class="product-categories mt-4 ps-4" role="list">
												<?php foreach ( $thematic_terms as $term ) : ?>
													<?php
													$slug = $term->slug;
													$input_id = 'shop-filter-product-cat-' . $slug;
													$checked = in_array( $slug, $selected_cats, true );
													?>
													<li class="cat-item list-none <?php echo $checked ? 'current-cat' : ''; ?>">
														<label for="<?php echo esc_attr( $input_id ); ?>" class="flex cursor-pointer items-center gap-3 text-[15px] text-deep-blue">
															<input
																type="checkbox"
																id="<?php echo esc_attr( $input_id ); ?>"
																name="product_cat[]"
																value="<?php echo esc_attr( $slug ); ?>"
																class="peer sr-only"
																<?php checked( $checked ); ?>
															>
															<span class="relative flex h-[14px] w-[14px] shrink-0 rounded border border-primary bg-white peer-focus-visible:ring-2 peer-focus-visible:ring-teal/40 after:pointer-events-none after:absolute after:left-[4px] after:top-[1px] after:h-[8px] after:w-[5px] after:rotate-45 after:border-b-[2px] after:border-r-[2px] after:border-white after:opacity-0 peer-checked:after:opacity-100 peer-checked:bg-deep-blue" aria-hidden="true"></span>
															<span class="leading-snug"><?php echo esc_html( $term->name ); ?></span>
														</label>
													</li>
												<?php endforeach; ?>
											</ul>
										</details>
									<?php endif; ?>

									<?php if ( $product_type_terms ) : ?>
										<details class="group mb-0">
											<summary class="product-search-filter-category-heading cursor-pointer list-none [&::-webkit-details-marker]:hidden">
												<span class="text-[15px] font-bold text-deep-blue"><?php echo esc_html( pll__( 'Type de produit' ) ); ?></span>
											</summary>
											<ul class="product-tags mt-4 ps-4" role="list">
												<?php foreach ( $product_type_terms as $term ) : ?>
													<?php
													$slug = $term->slug;
													$input_id = 'shop-filter-product-tag-' . $slug;
													$checked = in_array( $slug, $selected_tags, true );
													?>
													<li class="list-none mb-[5px]">
														<label for="<?php echo esc_attr( $input_id ); ?>" class="flex cursor-pointer items-center gap-3 text-[15px] text-deep-blue">
															<input
																type="checkbox"
																id="<?php echo esc_attr( $input_id ); ?>"
																name="product_tag[]"
																value="<?php echo esc_attr( $slug ); ?>"
																class="peer sr-only"
																<?php checked( $checked ); ?>
															>
															<span class="relative flex h-[14px] w-[14px] shrink-0 rounded border border-primary bg-white peer-focus-visible:ring-2 peer-focus-visible:ring-teal/40 after:pointer-events-none after:absolute after:left-[4px] after:top-[1px] after:h-[8px] after:w-[5px] after:rotate-45 after:border-b-[2px] after:border-r-[2px] after:border-white after:opacity-0 peer-checked:after:opacity-100 peer-checked:bg-deep-blue" aria-hidden="true"></span>
															<span class="leading-snug"><?php echo esc_html( $term->name ); ?></span>
														</label>
													</li>
												<?php endforeach; ?>
											</ul>
										</details>
									<?php endif; ?>
									</div>
								</form>
								</li>
								</ul>
							<?php endif; ?>
						</div>
						</div>
					</div>
				</aside>

				<div>
					<?php
					/**
					 * Hook: woocommerce_shop_loop_header.
					 *
					 * @since 8.6.0
					 *
					 * @hooked woocommerce_product_taxonomy_archive_header - 10
					 */
					// do_action( 'woocommerce_shop_loop_header' );

					if ( woocommerce_product_loop() ) {

						/**
						 * Hook: woocommerce_before_shop_loop.
						 *
						 * @hooked woocommerce_output_all_notices - 10
						 * @hooked woocommerce_result_count - 20
						 * @hooked woocommerce_catalog_ordering - 30
						 */
						// echo '<div class="flex flex-wrap items-center justify-between gap-4 mb-8" data-shop-toolbar>';
						// do_action( 'woocommerce_before_shop_loop' );
						// echo '</div>';

						echo '<div data-shop-results>';
							if ( wc_get_loop_prop( 'total' ) ) {
								echo '<ul class="books-grid grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 -mx-1.5">';
								while ( have_posts() ) {
									the_post();

									/**
									 * Hook: woocommerce_shop_loop.
									 */
									do_action( 'woocommerce_shop_loop' );

									wc_get_template_part( 'content', 'product' );
								}
								echo '</ul>';
							}

							/**
							 * Hook: woocommerce_after_shop_loop.
							 *
							 * @hooked woocommerce_pagination - 10
							 */
							do_action( 'woocommerce_after_shop_loop' );
						echo '</div>';
					} else {
						/**
						 * Hook: woocommerce_no_products_found.
						 *
						 * @hooked wc_no_products_found - 10
						 */
						do_action( 'woocommerce_no_products_found' );
					}
					?>
				</div>
			</div>
		</div>
	</section>

<?php
$block = array(
	'anchor' => '',
);
include get_template_directory() . '/src/blocks/shop-archive-promo/block.php';
?>

<?php
/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'woocommerce_after_main_content' );

get_footer( 'shop' );
