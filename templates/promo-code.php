<?php
/*
 * Template Name: Code promotionnel
 */

get_header();  ?>

        <section class="page-top">
            <div class="wrapper">
                <div class="page-title">
                    <h1><?= the_title(); ?></h1>
                </div>

                <div class="content-block">
					<?php if( get_field('promo-code-content') ){ ?>
                        <div class="the-content"><?= get_field('promo-code-content'); ?></div>
					<?php } ?>
                </div>

				<?php if( ! empty( get_field('promo-code-form-id') ) ){ ?>
                    <div class="promo-code-block">

                        <div class="row align-items-center justify-content-between">

                            <div class="col-12 col-lg-7 col-xl-8 d-flex align-items-center justify-content-center">
								<?php if( get_field('promo-code-amount') ){ ?>
                                    <span class="percent"><?= get_field('promo-code-amount') . '%'; ?></span>
								<?php } ?>

								<?php if( get_field('promo-code-block-title') || get_field('promo-code-block-subtitle') ){ ?>
                                    <div class="text-container">
                                        <h3><?= get_field('promo-code-block-title'); ?></h3>
                                        <div class="the-content"><p><?= get_field('promo-code-block-subtitle'); ?></p></div>
                                    </div>
								<?php } ?>
                            </div>


                            <div class="col-12 col-lg-5 col-xl-4 d-flex align-items-center justify-content-center">
                                <div class="buttons">
                                    <div class="dummy-button"><span><?= pll__('Monlivre10'); ?></span></div>
                                    <a href="javascript:;" class="promo-button" data-fancybox data-src="#code-form"><span><?= pll__('Voir le code'); ?></span><?= file_get_contents(THEME_PATH . '/assets/images/icon-show-code.svg'); ?></a>
                                </div>
                            </div>


                        </div>

                    </div>
				<?php } ?>

				<?php if( get_field('promo-code-button') ){ ?>
                    <div class="button-container">
						<?= FW::button( get_field('promo-code-button'), ['btn', 'red'] ); ?>
                    </div>
				<?php } ?>

            </div>
        </section>

		<?php if( get_field('promo-code-form-id') ){ ?>
            <div id="code-form" class="popup">

                <div class="inner">
                    <h3><?= pll__('Nous vous enverrons votre code promotionnel!'); ?></h3>

                    <?php if( get_field('promo-code-form-description') ){ ?>
                        <div class="the-content"><?= get_field('promo-code-form-description'); ?></div>
                    <?php } ?>

                    <? gravity_form( get_field('promo-code-form-id'), false, false, false, [], true); ?>

                    <div class="text-container"><span><?= pll__('* Vous avez 24h pour utiliser votre code'); ?></span></div>
                </div>

            </div>
		<?php } ?>

<?php get_footer(); ?>