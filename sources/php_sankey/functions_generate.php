<?php

// FUNCTIONS FIND_NODE & FIND LINK (LAYOUT)
function find_node($node_name){
	foreach($GLOBALS['layout_nodes'] as $key => $value){
		if($value['name'] == $node_name){
			return $value['id'];
		}
	}
	return 'NODE "' . $node_name . '" NOT FOUND IN LAYOUT FILE';
}

function find_link($source_name,$target_name){
	foreach($GLOBALS['layout_links'] as $key => $value){
		if($value['source_name'] == $source_name && $value['target_name'] == $target_name){
			return $value['id'];
		}
	}
	return 'LINK "' . $source_name . '" / "'. $target_name .'" NOT FOUND IN LAYOUT FILE';
}

// FIND SEPARATOR OF CSV ARRAY
function find_separator(&$array){
	$comma = 0;
	$semi_colon = 0;
	foreach ($array as $key => &$value) { // reference
		$value = str_replace("\n","",$value);
		$value = str_replace("\r","",$value);
		$comma += substr_count($value,",");
		$semi_colon += substr_count($value,";");
		if ($_SESSION["charset"] == "iso-8859-1") {
			$value = mb_convert_encoding($value,"utf-8","iso-8859-1");
		}
		else if ($_SESSION["charset"] == "macintosh") {
			$value = iconv("macintosh","utf-8",$value);
		}
	}
	unset($value); // delete reference
	if ($comma > $semi_colon) {
		$separator = ",";
	}
	else {
		$separator = ";";
	}
	return $separator;
}

?>