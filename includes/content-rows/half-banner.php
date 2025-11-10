<div class="half-hero">

	<? if( get_sub_field('background-img') ) {
		echo FW::get_image( get_sub_field('background-img') );
	} ?>

	<div class="half-featured-img">
		<? if( get_sub_field('featured-img') ){
			echo FW::get_image( get_sub_field('featured-img'), '', '' );
		} ?>
	</div>

</div>