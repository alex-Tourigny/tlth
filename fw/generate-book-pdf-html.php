<html>
	<head>
		<style>
			@font-face {
				font-family: trace;
				font-weight: normal;
				font-style: normal;
				font-variant: normal;
				src: url('dompdf/lib/fonts/Tracequebecois.ttf') format('truetype');
			}

			@font-face {
				font-family: trace;
				font-weight: bold;
				font-style: normal;
				font-variant: normal;
				src: url('dompdf/lib/fonts/Tracequebecois-bold.ttf') format('truetype');
			}

			@font-face {
				font-family: trace;
				font-weight: bold;
				font-style: italic;
				font-variant: normal;
				src: url('dompdf/lib/fonts/Tracequebecois-bold-it.ttf') format('truetype');
			}

			@font-face {
				font-family: trace;
				font-weight: normal;
				font-style: italic;
				font-variant: normal;
				src: url('dompdf/lib/fonts/Tracequebecois-it.ttf') format('truetype');
			}

			@font-face {
				font-family: message-1;
				font-weight: normal;
				font-style: normal;
				font-variant: normal;
                src: src: url('dompdf/lib/fonts/Tracequebecois.ttf') format('truetype');
			}

			@font-face {
				font-family: message-1;
				font-weight: normal;
				font-style: italic;
				font-variant: normal;
                src: src: url('dompdf/lib/fonts/Tracequebecois-it.ttf') format('truetype');
			}

			@font-face {
				font-family: message-1;
				font-weight: bold;
				font-style: normal;
				font-variant: normal;
                src: src: url('dompdf/lib/fonts/Tracequebecois.ttf') format('truetype');
			}

			@font-face {
				font-family: message-1;
				font-weight: bold;
				font-style: italic;
				font-variant: normal;
                src: url('dompdf/lib/fonts/GochiHand-Regular.ttf') format('truetype');

			}

			/*@font-face {
				font-family: message-2;
				font-weight: normal;
				font-style: normal;
				font-variant: normal;
				src: url('dompdf/lib/fonts/ShadowsIntoLightTwo-Regular.ttf') format('truetype');
			}*/

			html, body{
				font-size: <?= $font_size; ?>
			}

			<?= file_get_contents( THEME_PATH . '/assets/css/html2pdf.css' ); ?>
		</style>
	</head>

	<body>
		<?
		foreach($pdf_pages as $page_number => $pdf_page)
		{
			$page_type = $pdf_page['type'];

			// Always skip the first blank page /// changed to skip to message
			if($page_number < 3 ) continue; ?>

			<div class="book-page">

				<?
				// Do not add content to pages that are blank, cover or image
				if( ! in_array($page_type, ['blank', 'cover', 'image']) ){

					// Page content
					$page_content = $pdf_page['content']; ?>

					<div class="page-content <?= $page_type; ?>"><?= $page_content; ?></div>
					<div class="page-number"><?= $page_number; ?></div>

				<? } ?>
			</div>

		<? } ?>
	</body>
</html>