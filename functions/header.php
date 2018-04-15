<?php
function pageHeader($type){
	$return = "<div class ='nav-background'><div class='nav-container'><nav class='cl-effect-1'>";
	$indexes = Config::get($type);
	foreach($indexes as $index =>$page){
		$index = ucfirst($index);
		$return .= htmlHeader($index, $page);
	}
	$return .= "</nav></div></div>";
	return $return;
}

