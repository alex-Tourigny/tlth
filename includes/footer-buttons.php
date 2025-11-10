<?php if( have_rows('button-row-' . LANG, 'option') ){ ?>
<nav>
	<ul>
	<?php while( have_rows('button-row-' . LANG, 'option') ){
		the_row();
		$link = get_sub_field('btn-' . LANG, 'option');
		$color = get_sub_field('btn-color-' . LANG, 'option');
		?>
		<li>
			<a href="<?= $link['url']; ?>" href="<?= $link['target']; ?>" class="<?= $color; ?>"><?= $link['title']; ?></a>
		</li>
	<?php } ?>
	</ul>
</nav>
<?php } ?>


