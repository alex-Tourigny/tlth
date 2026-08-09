</main>

<footer id="footer">

	<div id="copyrights">
		<div class="d-flex justify-content-between">
			<?php
			$footer_disclaimer = get_field("footer-disclaimer-" . LANG, "option");

			// echo TLTH::get_copyright();

			if( $footer_disclaimer ){ ?>
				<p><?= $footer_disclaimer ?></p>
			<?php } ?>

		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>