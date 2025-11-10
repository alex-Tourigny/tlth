<?php
$blog_page_id = get_option('page_for_posts');
get_header();
?>
	<div class="page-top">
		<div class="wrapper">
			<div class="page-title" data-aos="fade-up">
				<h1><a href="<?= get_permalink( get_option( 'page_for_posts' ) );?>"><?= get_the_title( get_option( 'page_for_posts' ));?></a></h1>
			</div>
		</div>
	</div>

	<div class="blog-directory">
		<div class="wrapper">
			<div class="row justify-content-center">

				<div class="col-lg-6 col-xl-3 order-1 order-lg-1 order-xl-0">
					<div class="the-sidebar">
						<div class="sidebar__inner">
							<? include( THEME_PATH . '/includes/blog/sidebar-left.php'); ?>
						</div>
					</div>
				</div>

				<div class="col-lg-12 col-xl-6 order-0 order-lg-0 order-xl-1">
					<?
					if( have_posts() ){ ?>
						<div class="blog-articles">
							<?
							while( have_posts() ){
							the_post();
								include(THEME_PATH . '/includes/blog/featured-block.php');
							}
							?>
						</div>

						<nav class="pagination">
							<? echo paginate_links(
								array(
									'prev_text' => '<span>...<span>',
									'next_text' => '<span>...<span>',
								)
							);
							?>
						</nav>

					<? } wp_reset_postdata();?>
				</div>

				<div class="col-lg-6 col-xl-3 order-2 order-lg-2 order-xl-2 sidebar">
					<div class="the-sidebar">
						<div class="sidebar__inner">
							<? include( THEME_PATH . '/includes/blog/sidebar-right.php'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

<?php if( shortcode_exists('instagram-feed') ){ ?>
	<section id="instagram-feed">

		<div class="slice-intro">
			<h4>
				<a href="<?= get_field('instagram', 'option'); ?>" target="_blank">
					<?= pll__('instagram');?>
				</a>
			</h4>

			<h2><?= pll__('news-instagram');?></h2>
		</div>

		<div class="wrapper big">
			<?= do_shortcode('[instagram-feed]'); ?>
		</div>
	</section>
<?php } ?>

<?php get_footer(); ?>
