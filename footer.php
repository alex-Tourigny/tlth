</main>

<?php
$partners = new WP_Query(
	array(
		'post_type' => 'partner',
		'posts_per_page' => -1,
		'orderby' => 'rand'
	)
);

if( $partners->have_posts() ){ ?>
	<footer id="subfooter">
		<div class="wrapper">
			<div class="row align-items-center">
				<div class="col-12 col-lg-3">
					<h3 class="subtitle"><?= pll__("Merci à nos partenaires"); ?></h3>
				</div>

				<div class="col-12 col-lg-9">
					<div class="row">

						<? while( $partners->have_posts() ){
							$partners->the_post(); ?>

							<div class="col-6 col-lg-3">
								<a href="<?= get_field('url'); ?>" target="_blank" class="partner-block">
									<?= FW::featured_image(); ?>
								</a>
							</div>

						<? } ?>
					</div>
				</div>
			</div>
		</div>
	</footer>
<?php } wp_reset_postdata(); ?>

<footer id="footer">

	<?
	if( get_field('bg-img', 'option') ){
		echo FW::get_image( get_field('bg-img', 'option') );
	}
	?>

	<div class="footer-content">
		<div class="wrapper">
			<a href="<?= pll_home_url(); ?>">
				<?= file_get_contents(THEME_PATH . '/assets/images/footer-circle-logo.svg'); ?>
			</a>

			<div class="footer-icons">
				<?php include(THEME_PATH . '/includes/social-icons.php'); ?>
			</div>

			<div class="footer-buttons">
				<?php include(THEME_PATH . '/includes/footer-buttons.php'); ?>
			</div>

			<div class="footer-primary-nav">
				<nav>
					<ul>
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'footer',
								'container' => '',
								'items_wrap' => '%3$s'
							)
						);
						?>
					</ul>
				</nav>
			</div>

			<div class="footer-copy">

				<div class="additional-links">

					<?php if( have_rows('footer-links-row-' . LANG, 'option') ){ ?>
						<nav>
							<ul>
								<?php while( have_rows('footer-links-row-' . LANG, 'option') ){ the_row();
									$link = get_sub_field('link-' . LANG, 'option');
									?>
								<li>
									<a href="<?= $link['url']; ?>" href="<?= $link['target']; ?>"><?= $link['title']; ?></a>
								</li>
								<?php } ?>
							</ul>
						</nav>
					<?php } ?>

				</div>
                <style>
                    #rubik-api-copyright {
                        justify-content: center;
                    }
                </style>
				<div><?= '&copy;' . ' ' . date('Y') . ' ' . pll__('copyright'); ?></div>
                <div class="text-center"><?//= FW::rubik_footer( LANG ); ?></div>
			</div>
		</div>
	</div>




</footer>
<?php  include(THEME_PATH . '/includes/newsletter-pop.php');  ?>
<?php include(THEME_PATH . '/includes/shop-notice.php'); ?>
<?php wp_footer(); ?>
</body>
</html>