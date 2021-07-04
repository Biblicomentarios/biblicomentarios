<?php
// block.php

function php_everywhere_render_block($attributes, $content) {
	
	//don't give any output in backend
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return '';
	}
	else{
		if(is_admin()) {
			return '';
		}
	}
	
    ob_start();
	eval(' ?>'.urldecode(base64_decode($attributes['code'])).'<?php ');
	$var = ob_get_contents();
	ob_end_clean();
	return $var;
}
