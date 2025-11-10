<?php
$body = isset($body) ? $body : "";

?>

<!doctype html>
<html>
<head>
	<meta name="viewport" content="width=device-width" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?= $subject; ?></title>
	<style>
        /* -------------------------------------
			GLOBAL RESETS
		------------------------------------- */

        /*All the styling goes here*/

        img {
            border: none;
            -ms-interpolation-mode: bicubic;
            max-width: 100%;
        }
        body {
            background-color: #e7eff6;
            font-family: sans-serif;
            -webkit-font-smoothing: antialiased;
            font-size: 14px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }
        table {
            border-collapse: separate;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
            width: 100%; }
        table td {
            font-family: sans-serif;
            font-size: 14px;
            vertical-align: top;
        }

        /* -------------------------------------
			BODY & CONTAINER
		------------------------------------- */
        .body {
            background-color: #e7eff6;
            width: 100%;
            margin: auto;
        }
        .container {
            padding: 10px;
        }
        .content {
            box-sizing: border-box;
            margin: 0 auto;
            max-width: 700px;
            width: 700px;
            padding: 65px 10px 30px;
        }

        /* -------------------------------------
			HEADER, FOOTER, MAIN
		------------------------------------- */
        .logo-section {
            background: #fff;
            padding: 60px 20px 0;
        }
        .banner-content {
            background: #ffffff;
            padding: 50px 0 0;
        }
        .banner-content h1 {
            font-size: 20px;
            font-weight: 700;
            color: #3A3C3D;
            margin-bottom: 0;
        }
        .banner-content.has-image {
            background-size: cover;
            background-repeat: no-repeat;
            padding: 120px 20px;
        }
        .banner-content.has-image h1 {
            color: #3A3C3D;
        }

        .main {
            background: #ffffff;
            border-radius: 3px;
            width: 100%;
        }
        .wrapper {
            box-sizing: border-box;
            padding: 20px;
        }
        .the-content {
            text-align: center;
            padding: 0 60px;
        }
        .the-content h2 {
            margin-top: 25px;
            margin-bottom: 35px;
        }
        .the-content p {
            color: #3A3C3D;
            margin-bottom: 25px;
        }
        .the-content p a{
            color: #09956F!important;

        }

        .border div {
            background: #e7eff6;
            width: 90px;
            height: 4px;
        }


        .footer td,
        .footer p,
        .footer span,
        .footer a {
            color: #343432;
            font-size: 12px;
            text-align: center;
            text-decoration: none;
        }
        /* -------------------------------------
			TYPOGRAPHY
		------------------------------------- */
        h1,
        h2,
        h3,
        h4 {
            color: #000000;
            font-family: sans-serif;
            font-weight: 700;
            line-height: 1.4;
            margin: 0;
            margin-bottom: 30px;
        }
        h1 {
            font-size: 30px;
            font-weight: 300;
            text-align: center;
        }
        h2 {
            font-size: 24px;
            text-align: center;
            color: #3A3C3D;
        }
        p,
        ul,
        ol {
            font-family: sans-serif;
            font-size: 16px;
            font-weight: normal;
            margin: 0;
            margin-bottom: 15px;
        }
        p li,
        ul li,
        ol li {
            list-style-position: inside;
            margin-left: 5px;
        }
        a {
            color: #FA4614;
            text-decoration: underline;
        }
        /* -------------------------------------
			BUTTONS
		------------------------------------- */
        .btn {
            font:bold 20px/100% 'Open Sans'!important;
            color:white;
            text-align:center;
            padding:15px 45px;
            background:#32A37C;
            display:inline-block;
            cursor:pointer;
            border:0px;
            border-radius:50px;
            -webkit-box-shadow: 0 5px 5px -3px rgba(0,0,0,0.2);
            box-shadow: 0 5px 5px -3px rgba(0,0,0,0.2);
        }

        .btn table {
            width: 100%;
        }
        .btn table td {
            border-radius: 5px;
            text-align: center;
        }

		.promo-code{
			width:100%;
			color:white;
			font-size:25px;
			margin:0;
			min-width:420px;
		}

		.signature {
			padding: 25px 0;
			margin-bottom:30px;

		}
		.signature a{
			font-size:16px;
			color:#3a3c3d;
		}


        /* -------------------------------------
			RESPONSIVE AND MOBILE FRIENDLY STYLES
		------------------------------------- */
        @media only screen and (max-width: 620px) {
            table[class=body] h1 {
                font-size: 28px !important;
                margin-bottom: 10px !important;
            }
            table[class=body] p,
            table[class=body] ul,
            table[class=body] ol,
            table[class=body] td,
            table[class=body] span,
            table[class=body] a {
                font-size: 16px !important;
            }
            table[class=body] .wrapper,
            table[class=body] .article {
                padding: 10px !important;
            }
            table[class=body] .content {
                padding: 0 !important;
            }
            table[class=body] .container {
                padding: 0 !important;
                width: 100% !important;
            }
            table[class=body] .main {
                border-left-width: 0 !important;
                border-radius: 0 !important;
                border-right-width: 0 !important;
            }
            table[class=body] .btn table {
                width: 100% !important;
            }
            table[class=body] .btn a {
                width: 100% !important;
            }
            table[class=body] .img-responsive {
                height: auto !important;
                max-width: 100% !important;
                width: auto !important;
            }
            .content {
                padding: 30px 10px 20px;
                width: 100%;
            }
            .logo-section {
                padding: 20px;
            }
            .logo-section img{
                max-width: 150px;
            }
            .banner-content {
                padding: 30px 20px 0;
            }
            .banner-content h1 {
                font-size: 22px;
            }
            .banner-content.has-image {
                padding: 30px;
            }
            .the-content h2 {
                margin-top: 15px;
                margin-bottom: 15px;
            }
            .border {
                padding: 25px 0;
            }
            .the-content {
                padding: 0;
            }
            .article {
                padding: 0 15px;
            }
            .article-content {
                padding: 15px 20px;
            }
            .article-content time {
                font-size: 17px;
            }
            .article-content h3 {
                font-size: 20px;
            }
            .article-content .categories span {
                font-size: 16px;
                font-weight: 700;
                color: #c8ced3;
            }
            .article-content .read-more {
                font-size: 20px;
                font-weight: 700;
                color: #07ceab;
                text-decoration: none;
            }

            .footer {
                padding: 0 0 10px;
            }

            .btn a {
                font-size: 20px;
                padding: 15px 25px;
            }

            h2 {
                font-size: 24px;
            }
            p,
            ul,
            ol {
                font-family: sans-serif;
                font-size: 16px;
                font-weight: normal;
                margin: 0;
                margin-bottom: 15px;
            }
        }
        /* -------------------------------------
			PRESERVE THESE STYLES IN THE HEAD
		------------------------------------- */
        @media all {
            .ExternalClass {
                width: 100%;
            }
            .ExternalClass,
            .ExternalClass p,
            .ExternalClass span,
            .ExternalClass font,
            .ExternalClass td,
            .ExternalClass div {
                line-height: 100%;
            }
            .apple-link a {
                color: inherit !important;
                font-family: inherit !important;
                font-size: inherit !important;
                font-weight: inherit !important;
                line-height: inherit !important;
                text-decoration: none !important;
            }
            .btn-box:hover {
                background-color: #860A47 !important;
            }
            .btn-box a:hover {
                background-color: #860A47 !important;
                border-color: #860A47 !important;
            }
        }
	</style>
</head>
<body class="">
<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
	<tr>
		<td align="center" class="container">

			<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="content">
				<tbody>
				<tr>
					<td align="center">

						<div class="header">
							<table role="presentation" border="0" cellpadding="0" cellspacing="0">

								<tr>
									<td align="center" class="logo-section">

										<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="logo">
											<tbody>
											<tr>
												<td align="center">
													<table role="presentation" border="0" cellpadding="0" cellspacing="0">
														<tbody>
														<tr>
															<td align="center">
																<img src="https://tlth.agencerubik.dev/wp-content/uploads/LOGO_CHAPEAUMELON_TIENTLIVRE_COULEUR.jpg" width="200" alt="<?= get_bloginfo('name'); ?>" title="<?= get_bloginfo('name'); ?>" />
															</td>
														</tr>
														</tbody>
													</table>
												</td>
											</tr>
											</tbody>
										</table>

									</td>
								</tr>


							</table>
						</div>

						<!-- START CENTERED WHITE CONTAINER -->
						<table role="presentation" class="main" width="100%">

							<!-- START MAIN CONTENT AREA -->
							<tr>
								<td class="wrapper">
									<table role="presentation" border="0" cellpadding="0" cellspacing="0">
										<tr>
											<td class="the-content">
												<?= format_email_text($body); ?>
												<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn" width="100%">
													<tbody>
														<tr>
															<td align="center">
																<table role="presentation" border="0" cellpadding="0" cellspacing="0">
																	<tbody>
																		<tr>
																			<td align="center" style="text-align:center;">
																				<h3 class="promo-code"><?= $promo_code; ?></h3>
																			</td>
																		</tr>
																	</tbody>
																</table>
															</td>
														</tr>
													</tbody>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>

							<tr>
								<td align="center" class="signature">

									<a href="https://tonlivretonhistoire.ca/" target="_blank">Ton livre ton histoire</a>

								</td>
							</tr>

							<!-- END MAIN CONTENT AREA -->
						</table>
						<!-- END CENTERED WHITE CONTAINER -->

					</td>
				</tr>
				</tbody>
			</table>

		</td>
	</tr>
</table>
</body>
</html>