<?php
$is_category = is_category();
?>

<aside class="sidebar-left">
	<? if( get_field('title-left', $blog_page_id) ){ ?>
		<h2><?= get_field('title-left', $blog_page_id); ?></h2>
	<? } ?>

	<h3><?= pll__('filter-by-category'); ?></h3>
	<div class="categories">
		<?
		$categories = get_terms(array(
			'taxonomy' => 'category'
		));
		?>
		<ul>
			<?
			if( ! empty ($categories)){
				foreach( $categories as $category ){
					$class = $is_category && get_queried_object_id() == $category->term_id ? 'active' : '' ;
			?>
					<li>
						<a href="<?= get_term_link($category); ?>" class="<?= $class; ?>">
							<span class="label"><?= $category->name; ?></span>
						</a>
					</li>
			<?
				}
			}
			?>
		</ul>
	</div>
</aside>