<?php
	$style 	= 'color:'.option::get('product_title_color');

	$url 	= get_url($val->slug);

	$title 	= $val->title;

	printf('<p class="heading" style="%s"><a href="%s" title="%s" style="%s">%s</a></p>', $style, $url, $title, $style, $title );
?>