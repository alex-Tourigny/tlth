<aside class="sidebar-right">
	<? if( get_field('title-right', $blog_page_id) ){ ?>
		<h2><?= get_field('title-right', $blog_page_id); ?></h2>
	<? } ?>

	<h3><?= pll__('recent-articles'); ?></h3>

	<div class="recents">
		<?
		$recent_posts = new WP_Query(
			array(
				'post_type' => 'post',
				'posts_per_page' => 3,
				'order_by' => 'date'
			)
		);

		if( $recent_posts->have_posts() ){ ?>
			<ul>
				<?
				while( $recent_posts->have_posts() ){
					$recent_posts->the_post();

					include(THEME_PATH . '/includes/blog/recents-block.php');
				}
				?>
			</ul>
		<? } wp_reset_postdata(); ?>
	</div>

	<h3><?= pll__('most-popular'); ?></h3>

	<div class="recents popular">
		<?
		$recent_posts = new WP_Query(
			array(
				'post_type' => 'post',
				'posts_per_page' => 3,
				'order_by' => 'ASC'
			)
		);

		if( $recent_posts->have_posts() ){ ?>
			<ul>
				<?
				while( $recent_posts->have_posts() ){
					$recent_posts->the_post();

					include(THEME_PATH . '/includes/blog/recents-block.php');
				}
				?>
			</ul>
		<? } wp_reset_postdata(); ?>
	</div>
</aside>