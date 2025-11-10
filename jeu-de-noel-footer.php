</main>

<footer id="footer">

	<div id="copyrights">
		<div class="d-flex justify-content-between">
			<?php
			$footer_disclaimer = get_field("footer-disclaimer-" . LANG, "option");

			// echo FW::get_copyright();

			if( $footer_disclaimer ){ ?>
				<p><?= $footer_disclaimer ?></p>
			<?php }

			echo FW::rubik_footer( LANG );
			?>

		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>