<?php

/**
 * Login Form – Custom Themed Version
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.2.0
 */

if (! defined('ABSPATH')) {
    exit;
}

do_action('woocommerce_before_customer_login_form');
?>

<div class="wc-auth-forms max-w-content mx-auto px-8 py-12 lg:py-20">
    <div class="grid grid-cols-1 <?php if ('yes' === get_option('woocommerce_enable_myaccount_registration')) echo 'lg:grid-cols-2'; ?> gap-8 lg:gap-12" id="customer_login">

        <!-- Login -->
        <div class="bg-white rounded-3xl p-8 lg:p-10 shadow-soft">
            <h2 class="text-2xl lg:text-3xl font-medium text-deep-blue mb-6"><?php esc_html_e('Login', 'woocommerce'); ?></h2>

            <form class="woocommerce-form woocommerce-form-login login !p-0 !border-0" method="post">
                <?php do_action('woocommerce_login_form_start'); ?>

                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-deep-blue mb-2">
                        <?php esc_html_e('Username or email address', 'woocommerce'); ?>
                        <span class="text-coral" aria-hidden="true">*</span>
                        <span class="screen-reader-text"><?php esc_html_e('Required', 'woocommerce'); ?></span>
                    </label>
                    <input type="text" class="woocommerce-Input woocommerce-Input--text input-text w-full px-5 py-3 rounded-full border-2 border-muted-blue/30 bg-beige text-deep-blue text-[15px] transition-all duration-300 focus:border-teal focus:outline-none focus:ring-2 focus:ring-teal/20"
                        name="username" id="username" autocomplete="username"
                        value="<?php echo (! empty($_POST['username'])) ? esc_attr(wp_unslash($_POST['username'])) : ''; ?>"
                        required aria-required="true" />
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-deep-blue mb-2">
                        <?php esc_html_e('Password', 'woocommerce'); ?>
                        <span class="text-coral" aria-hidden="true">*</span>
                        <span class="screen-reader-text"><?php esc_html_e('Required', 'woocommerce'); ?></span>
                    </label>
                    <input type="password" class="woocommerce-Input woocommerce-Input--text input-text w-full px-5 py-3 rounded-full border-2 border-muted-blue/30 bg-beige text-deep-blue text-[15px] transition-all duration-300 focus:border-teal focus:outline-none focus:ring-2 focus:ring-teal/20"
                        name="password" id="password" autocomplete="current-password"
                        required aria-required="true" />
                </div>

                <?php do_action('woocommerce_login_form'); ?>

                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mt-6">
                    <label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme flex items-center gap-2 cursor-pointer text-sm text-muted-blue">
                        <input class="woocommerce-form__input woocommerce-form__input-checkbox accent-teal w-4 h-4"
                            name="rememberme" type="checkbox" id="rememberme" value="forever" />
                        <span><?php esc_html_e('Remember me', 'woocommerce'); ?></span>
                    </label>
                    <?php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce'); ?>
                    <button type="submit" class="btn btn-primary<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>"
                        name="login" value="<?php esc_attr_e('Log in', 'woocommerce'); ?>">
                        <?php esc_html_e('Log in', 'woocommerce'); ?>
                    </button>
                </div>

                <p class="mt-4">
                    <a href="<?php echo esc_url(wp_lostpassword_url()); ?>" class="text-sm text-teal hover:text-deep-blue transition-colors duration-200">
                        <?php esc_html_e('Lost your password?', 'woocommerce'); ?>
                    </a>
                </p>

                <?php do_action('woocommerce_login_form_end'); ?>
            </form>
        </div>

        <?php if ('yes' === get_option('woocommerce_enable_myaccount_registration')) : ?>
        <!-- Register -->
        <div class="bg-white rounded-3xl p-8 lg:p-10 shadow-soft">
            <h2 class="text-2xl lg:text-3xl font-medium text-deep-blue mb-6"><?php esc_html_e('Register', 'woocommerce'); ?></h2>

            <form method="post" class="woocommerce-form woocommerce-form-register register !p-0 !border-0" <?php do_action('woocommerce_register_form_tag'); ?>>
                <?php do_action('woocommerce_register_form_start'); ?>

                <?php if ('no' === get_option('woocommerce_registration_generate_username')) : ?>
                    <div class="mb-4">
                        <label for="reg_username" class="block text-sm font-medium text-deep-blue mb-2">
                            <?php esc_html_e('Username', 'woocommerce'); ?>
                            <span class="text-coral" aria-hidden="true">*</span>
                            <span class="screen-reader-text"><?php esc_html_e('Required', 'woocommerce'); ?></span>
                        </label>
                        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text w-full px-5 py-3 rounded-full border-2 border-muted-blue/30 bg-beige text-deep-blue text-[15px] transition-all duration-300 focus:border-teal focus:outline-none focus:ring-2 focus:ring-teal/20"
                            name="username" id="reg_username" autocomplete="username"
                            value="<?php echo (! empty($_POST['username'])) ? esc_attr(wp_unslash($_POST['username'])) : ''; ?>"
                            required aria-required="true" />
                    </div>
                <?php endif; ?>

                <div class="mb-4">
                    <label for="reg_email" class="block text-sm font-medium text-deep-blue mb-2">
                        <?php esc_html_e('Email address', 'woocommerce'); ?>
                        <span class="text-coral" aria-hidden="true">*</span>
                        <span class="screen-reader-text"><?php esc_html_e('Required', 'woocommerce'); ?></span>
                    </label>
                    <input type="email" class="woocommerce-Input woocommerce-Input--text input-text w-full px-5 py-3 rounded-full border-2 border-muted-blue/30 bg-beige text-deep-blue text-[15px] transition-all duration-300 focus:border-teal focus:outline-none focus:ring-2 focus:ring-teal/20"
                        name="email" id="reg_email" autocomplete="email"
                        value="<?php echo (! empty($_POST['email'])) ? esc_attr(wp_unslash($_POST['email'])) : ''; ?>"
                        required aria-required="true" />
                </div>

                <?php if ('no' === get_option('woocommerce_registration_generate_password')) : ?>
                    <div class="mb-4">
                        <label for="reg_password" class="block text-sm font-medium text-deep-blue mb-2">
                            <?php esc_html_e('Password', 'woocommerce'); ?>
                            <span class="text-coral" aria-hidden="true">*</span>
                            <span class="screen-reader-text"><?php esc_html_e('Required', 'woocommerce'); ?></span>
                        </label>
                        <input type="password" class="woocommerce-Input woocommerce-Input--text input-text w-full px-5 py-3 rounded-full border-2 border-muted-blue/30 bg-beige text-deep-blue text-[15px] transition-all duration-300 focus:border-teal focus:outline-none focus:ring-2 focus:ring-teal/20"
                            name="password" id="reg_password" autocomplete="new-password"
                            required aria-required="true" />
                    </div>
                <?php else : ?>
                    <p class="text-sm text-muted-blue mb-4"><?php esc_html_e('A link to set a new password will be sent to your email address.', 'woocommerce'); ?></p>
                <?php endif; ?>

                <?php do_action('woocommerce_register_form'); ?>

                <div class="mt-6">
                    <?php wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce'); ?>
                    <button type="submit" class="btn btn-primary<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?> woocommerce-form-register__submit"
                        name="register" value="<?php esc_attr_e('Register', 'woocommerce'); ?>">
                        <?php esc_html_e('Register', 'woocommerce'); ?>
                    </button>
                </div>
                <?php do_action('woocommerce_register_form_end'); ?>
            </form>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php do_action('woocommerce_after_customer_login_form'); ?>
