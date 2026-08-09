<?php

/**
 * Third-Party Integrations
 * 
 * Handles integration with external services and widgets
 */

/**
 * Add Userback Widget
 * 
 * Injects Userback feedback widget into the site
 */
function add_userback_widget()
{
    $url = $_SERVER['HTTP_HOST'];
?>
        <script>
            window.Userback = window.Userback || {};
            Userback.access_token = "A-c22iREl0imgnpUa8Zt1AphZ1Z";
            (function(d) {
                var s = d.createElement('script');
                s.async = true;
                s.src = 'https://static.userback.io/widget/v1.js';
                (d.head || d.body).appendChild(s);
            })(document);
        </script>
<?php
}
add_action('wp_head', 'add_userback_widget');

