<?php
$classes = [];

$classes[] = 'slice-intro';
$classes[] = 'padding-' . get_sub_field('padding');


$top_sub_title = isset($top_sub_title) ? $top_sub_title : get_sub_field('top-subtitle');
$top_title = isset($top_title) ? $top_title : get_sub_field('title');
$top_after_title = isset($top_after_title) ? $top_after_title : get_sub_field('subtitle');
$top_content = isset($top_content) ? $top_content : get_sub_field('content');
?>

<div class="<?= implode(' ', $classes); ?>" id="intro">
    <div class="wrapper big">
		<div class="slice-content">
			<?php if( $top_sub_title ) {?>
				<h4 data-aos="fade-right"><?= $top_sub_title; ?></h4>
			<?php } ?>
			<?php if( $top_title ) { ?>
				<h2 data-aos="fade-right"><?= stylized_string_red( $top_title ); ?></h2>
			<?php } ?>
			<?php if( $top_after_title ) { ?>
				<h5 data-aos="fade-right"><?= $top_after_title; ?></h5>
			<?php } ?>
			<?php if( $top_content ) { ?>
				<div class="the-content" data-aos="fade-right"><?= $top_content; ?></div>
			<?php } ?>
		</div>
    </div>
</div>

<?php
unset($top_sub_title);
unset($top_title);
unset($top_after_title);
unset($top_content);
?>