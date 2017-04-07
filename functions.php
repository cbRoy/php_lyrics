<?php
include "functions.url.php";

function parse_document($doc, $selectors) {
	$output = array();
	foreach ($selectors as $name => $selector) {
		if(!$selector) continue;
		foreach ($doc->find($selector[0]) as $k => $v) {
			$value = call_user_func($selector[1], pq($v));
			if (!isset($output[$k])) {
				$output[$k] = array();
			}
			$output[$k][$name] = $value;
		}
	}
	return $output;
}

function dump($data){
	echo "<PRE>";
	var_dump($data);
	echo "</PRE>";
}
