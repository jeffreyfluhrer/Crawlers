<?php
function htmllink($text, $to, $tooltip = "") {
	$text = htmlspecialchars_decode ( $text );
	$to = htmlspecialchars_decode ( $to );
	return "<a href=\"{$to}\" title =\"{$tooltip}\" class=\"alink\">{$text}</a>";
}
function htmlHeader($text, $to) {
	$text = htmlspecialchars_decode ( $text );
	$to = htmlspecialchars_decode ( $to );
	return "<a href=\"{$to}\" >{$text}</a>";
}

function delim($char, $count){
	$return ="";
	for($i = 0; $i < $count; $i++){
		$return .= $char;
	}
	return $return;
}

function formatError($error){
		return '<div class="ui-state-error ui-corner-all">
		<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
		<strong>Error:</strong> ' .$error. '</p> </div>';
}

function formatErrors($errors){
	$output = "";
	foreach($errors as $error){
		$output .= '<div class="ui-state-error ui-corner-all">
		<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
		<strong>Error:</strong> ' .$error. '</p> </div>';
	}
	return $output;
}