<?php get_header();
$blog_page_id = get_option('page_for_posts');

?>

<div class="page-top">
	<div class="wrapper">
		<div class="row">
			<div class="col-12 col-lg-9">
				<div class="page-title" data-aos="fade-up">
					<h1><? the_title(); ?></h1>
					<a class="blog-see-all" href="<?php echo get_permalink( get_option( 'page_for_posts' ) ); ?>"><?= pll__('see-all-articles'); ?></a>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="blog-directory">
	<div class="wrapper">
		<div class="row justify-content-center">

			<div class="col-12 col-lg-9">
				<div class="featured-block-img">
					<?php if( FW::featured_image() ){ ?>
						<? echo FW::featured_image(); ?>
					<?php } ?>
					<div class="blog-post-info">
						<? the_category(); ?>

						<div class="the-content blog-date"><?= pll__('published-on'); ?> <?= date_i18n('j M Y', get_the_time('U') );?></div>
					</div>
				</div>

				<div class="the-content blog-article"><?= the_content(); ?></div>

				<div class="featured-article-section">

					<h2><?= pll__('interested-articles'); ?></h2>
					<?php
					$featured_articles = new WP_Query(
						array(
							'post_type' => 'post',
							'posts_per_page' => '2',
							'orderby' => 'rand',
							'posts__not_in' => [get_the_ID()],
						)
					);

					if( $featured_articles->have_posts() ){ ?>
					<div class="row">

						<? while( $featured_articles->have_posts() ){
						$featured_articles->the_post(); ?>
							<div class="col-12 col-lg-6">
								<div class="featured-articles">
									<? include(THEME_PATH . '/includes/blog/featured-articles.php'); ?>
								</div>
							</div>
						<? } ?>
					</div>
				</div>
				<? } wp_reset_postdata(); ?>
			</div>

			<div class="col-12 col-lg-3">
				<? include( THEME_PATH . '/includes/blog/sidebar-right.php'); ?>
			</div>
		</div>
	</div>
</div>




<?php get_footer(); ?>
