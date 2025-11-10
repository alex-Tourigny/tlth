<?php include(THEME_PATH . '/includes/slice-intro.php'); ?>

<?php
$product_list = get_sub_field('product-list');
if( ! empty($product_list) ){ ?>

	<div class="product-grid" id="product-section">

		<div class="wrapper big">

			<?
			foreach($product_list as &$product)
			{
				$product = $product->ID;
			}

			$products = new WP_Query(
				array(
					'post_type' => 'product',
					'posts_per_page' => -1,
					'post__in' => $product_list,
					'orderby' => 'post__in'
				)
			);

			if( $products->have_posts() ){ ?>

				<div class="row justify-content-center">

					<?
					$i = 1;
					while( $products->have_posts() ) { $products->the_post(); ?>

						<div class="col-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= $i * 50; ?>">
							<div class="product-block">

								<a href="<?= get_permalink(); ?>">
									<? if( FW::featured_image() ){ echo FW::featured_image(); } ?>
								</a>

								<h2 data-mh="product-title"><?= get_the_title(); ?></h2>

								<?= get_product_categories_badges(); ?>

								<div class="product-btn btn-spacing">
									<a href="<?= get_permalink(); ?>" class="btn purple small"><?= pll__("En savoir plus"); ?></a>
								</div>
							</div>
						</div>

					<? $i++ ; } ?>

				</div>

			<? } wp_reset_postdata(); ?>

		</div>
	</div>

<?php } ?>

	<? if( get_sub_field('show-static-grid') ){ ?>
		<div class="product-grid" id="product-section">
			<div class="wrapper">
				<div class="row justify-content-center">
					<? include(THEME_PATH . '/includes/content-rows/helpers/static-products/item.php'); ?>
				</div>
			</div>
		</div>
	<? } ?>

<?php include(THEME_PATH . '/includes/slice-outro.php'); ?>