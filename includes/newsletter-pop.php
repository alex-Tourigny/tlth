<?
$show_newsletter_pop = isset($_COOKIE['show-newsletter-badge']) ? $_COOKIE['show-newsletter-badge'] : true;
if( $show_newsletter_pop === 'false') return;

$link_type = get_field('link-type', 'option');
$form_id = get_field('form-id-' . LANG, 'option');
$form_page_link = get_field('form-link-' . LANG, 'option');

$newsletter_form_id = $form_id;
?>

<div class="newsletter-tab">

	<a href="javascript:;" class="close-tab"><?= file_get_contents(THEME_PATH . '/assets/images/tlth-pastille-close.svg')?></a>

	<a href="<?= $link_type == "popup" ? "javascript:;" : $form_page_link['url'] ?>" class="newsletter-pop" <?= $link_type == 'popup' ? 'data-fancybox data-src="#newsletter-form"' : null ?>>
		<?
		if( get_field('news-img-' . LANG, 'option') ){
			echo FW::get_image( get_field('news-img-' . LANG, 'option') );
		}
		?>
	</a>

	<?php if( $link_type == "popup" && $newsletter_form_id ){ ?>
		<div id="newsletter-form" class="popup">
			<div class="news-img">
				<?
				if( get_field('form-inner-img-' . LANG, 'option') ){
					echo FW::get_image( get_field('form-inner-img-' . LANG, 'option') );
				}
				?>
			</div>
			<? gravity_form($newsletter_form_id, true, true, false, [], true); ?>
		</div>
	<?php } ?>
</div>

