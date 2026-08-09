</main>

<?php
$option = get_fields('option');
$newsletter_text = $option['newsletter_text_wysi-' . LANG];
$newsletter_form = $option['newsletter_form'];
?>

<footer id="footer" class="relative z-50 bg-deep-blue overflow-x-clip rounded-t-4xl">
	<div class="relative !px-8 sm:!px-12 max-w-content mx-auto py-0">
		<div class="shapes-container absolute top-0 left-0 w-full h-full overflow-hidden">
			<div class="shape absolute top-0 right-[230px] -rotate-90 h-[580px] w-[310px]">
				<?= file_get_contents(THEME_PATH . '/assets/images/shapes/zero.svg'); ?>
			</div>
			<div class="shape absolute -top-12 -right-8 rotate-45 h-[480px] w-[330px]">
				<?= file_get_contents(THEME_PATH . '/assets/images/shapes/half-circle.svg'); ?>
			</div>
		</div>
		<div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
			<div class="lg:col-span-7 xl:col-span-6 py-12 lg:py-14">
				
				<?php if( $newsletter_text ){ ?>
					<div class="the-content max-w-[370px]">
						<?= tlth_colored_text($newsletter_text, 'teal'); ?>
					</div>
				<?php } ?>
				
				<?php if( $newsletter_form ){ ?>
					<div class="max-w-[400px]">
						<?php gravity_form( $newsletter_form['id'], false, false, false, [], true ); ?>
					</div>
				<?php } ?>
				
				<nav class="footer-nav mb-8">
					<ul class="flex flex-wrap gap-x-2 gap-y-4">
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'footer',
								'container' => '',
								'items_wrap' => '%3$s',
								'fallback_cb' => false
							)
						);
						?>
					</ul>
				</nav>
				
				<div class="footer-bottom text-muted-blue text-xs lg:text-sm">
					<div class="flex flex-wrap items-center gap-x-4 gap-y-2">
						<?php if( $option['footer_copyright'] ){ ?>
							<span><?= $option['footer_copyright']; ?></span>
						<?php } else { ?>
							<span>&copy; <?= date('Y'); ?>. Ton Livre Ton Histoire.</span>
						<?php } ?>
						
						<?php if( have_rows('footer-links-row', 'option') ){ ?>
							<?php while( have_rows('footer-links-row', 'option') ){ the_row();
								$link = get_sub_field('footer-link', 'option');
								if ($link) { ?>
									<a href="<?= esc_url($link['url']); ?>" target="<?= esc_attr($link['target']); ?>" class="hover:text-teal transition-colors duration-200">
										<?= esc_html($link['title']); ?>
									</a>
								<?php }
							} ?>
						<?php } ?>
					</div>
				</div>
				
			</div>
			
			<!-- Right Content: Mascot Illustration -->
			<div class="relative hidden lg:block lg:col-span-5 xl:col-span-6 melon-illustration h-[calc(100%+130px)] -mt-[130px] -mr-24 overflow-hidden">
				<div class="bras absolute bottom-0 right-0">
					<?= file_get_contents(THEME_PATH . '/assets/images/melon/melon_bras.svg'); ?>
				</div>
				<div class="corps absolute bottom-0 right-0">
					<?= file_get_contents(THEME_PATH . '/assets/images/melon/melon_corps.svg'); ?>
				</div>
			</div>
			
		</div>
	</div>

</footer>

<?php // include(THEME_PATH . '/includes/newsletter-pop.php'); ?>
<?php wp_footer(); ?>
</body>
</html>