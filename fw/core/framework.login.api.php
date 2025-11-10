<?php
$transient_name = 'rubik_login';

if( isset($_GET['rubik_login_reset']) ){
	delete_transient( $transient_name );
}

// If login is cached in a transient, use that one, so we don't overload the server with requests
if( false !== ( get_transient( $transient_name ) ) ) {
	$markup = get_transient( $transient_name );
} else {
	$args = array(
		'headers' => array(
			'Content-Type' => 'application/json'
		)
	);

	$response = wp_remote_get('https://agencerubik.com/wp-json/rubik/v1/client_login_slides', $args);

	if ( !is_array($response) || $response['response']['code'] != 200) return;

	$markup = $response['body'];

	$expiry = 604800; // 1 week
	set_transient($transient_name, $markup, $expiry);
}

if( empty($markup) ) return;

$json_decoded_markup = json_decode($markup);
?>

<div id="login-slider">
	<?php
	foreach($json_decoded_markup as $slide){
		echo $slide;
	}
	?>
</div>
