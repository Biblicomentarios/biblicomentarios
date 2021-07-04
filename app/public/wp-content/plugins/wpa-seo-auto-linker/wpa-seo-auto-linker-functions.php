<?php
function SEOAutoInSpecChar($str) {
	$strarr = SEOAutoS2A($str);
    $str = implode("<!---->", $strarr);
    return $str;
}

function SEOAutoReSpecChar($str) {
	$strarr = explode("<!---->", $str);
    $str = implode("", $strarr);
	$str = stripslashes($str);
    return $str;
}

function SEOAutoS2A($str) {
    $chararray = array();
    for($i=0; $i < strlen($str); $i++){
        array_push($chararray,$str{$i});
    }
    return $chararray;
}

function SEOAutoTextFilter($options,$result) {
	$link		= parse_url(get_bloginfo('wpurl'));
	$host		= 'http://'.$link['host'];
	if ($options['blanko']) {
		$result = preg_replace_callback(
			'%<a(\s+.*?href=\S(?!' . $host . '))%i', 
			function($m) {
				return '<a target="_blank"'.$m[1];
		},
			$result);
	}
	if ($options['nofolo'])	{
		$result = preg_replace_callback(
			'%<a(\s+.*?href=\S(?!' . $host . '))%i', '<a rel="nofollow"\\1', $result); 
		$result = preg_replace_callback(
			'%<a(\s+.*?href=\S(?!' . $host . '))%i', 
			function($m) {
				return '<a rel="nofollow"'.$m[1];
		},
			$result);
	}
	return $result;
}
?>