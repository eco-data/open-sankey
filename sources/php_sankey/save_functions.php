<?php

require("attributes.php");

function write_attribute_to_txt($file, $element, $attribute_name, $attribute_type){
	if ($attribute_type[0]==1) {
		$txt = "$attribute_name = ";
		foreach ($element[$attribute_name] as $i => $l) {
 			if ($i == 0) {
 				$txt .= $l;
 			}
 			else {
 				$txt .= "," . $l;
 			}
 		}
 		$txt .= "\n";
	}
	else {
		if (isset($element[$attribute_name])) {
			$txt = "$attribute_name = " . $element[$attribute_name] . "\n";
		}
	}
	fputs($file,$txt);
}

// SAVE FUNCTIONS
function save_nodes_array($variable, $name, $file) {
	fputs($file, $name . "\n" );
 	foreach ($variable as $d) {
 		fputs($file,"\n");
 		foreach($GLOBALS["attributes_nodes"] as $key => $value) {
 			write_attribute_to_txt($file, $d, $key, $value);
 		}
  	}
 	fputs($file,"\n");
};

function save_links_array($variable, $name, $file){
	fputs($file,$name . "\n" );
 	foreach ($variable as $d) {
 		fputs($file,"\n");
 		foreach($GLOBALS["attributes_links"] as $key => $value) {
 			write_attribute_to_txt($file, $d, $key, $value);
 		}
 	}
 	fputs($file,"\n");
};


?>