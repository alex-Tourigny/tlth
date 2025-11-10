<nav class="social-icons">
	<ul>
		<?
		$social_networks = ['instagram', 'facebook', 'pinterest'];
		foreach($social_networks as $social_network){
			if( ! get_field($social_network, 'option') ) continue; ?>

			<li>
				<a href="<?= get_field($social_network, 'option'); ?>" target="_blank">
					<?= file_get_contents(THEME_PATH . '/assets/images/icon-' . $social_network . '.svg'); ?>
				</a>
			</li>

		<? } ?>
	</ul>
</nav>