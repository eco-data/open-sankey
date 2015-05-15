<?php

require("functions_generate.php");

// READING THE uncertainties SUPPLY FILE
$supply_array = file($_SESSION["uncert_supply_path"]);
// In case Line-ending is CR (\r), file() doesn't work.
if (count($supply_array) == 1) {
	$supply_array = explode("\r",$supply_array[0]);
}
$separator = find_separator($supply_array);
// Mise en mémoire sous forme de matrice
$rS_max = 0;
foreach($supply_array as $r => $row) {
	$supply_cells = explode($separator,$row);
	$cS_max = 0;
	foreach($supply_cells as $c => $cell) {
		$supply_matrix[$r][$c] = $cell;
		$cS_max +=1;
	}
	$rS_max +=1;
}

// READING THE uncertainties USE FILE
$use_array = file($_SESSION["uncert_use_path"]);
// In case Line-ending is CR (\r), file() doesn't work.
if (count($use_array) == 1) {
	$use_array = explode("\r",$use_array[0]);
}
$separator = find_separator($use_array);
// Mise en mémoire sous forme de matrice
$rU_max = 0;
foreach($use_array as $r => $row) {
	$use_cells = explode($separator,$row);
	$cU_max = 0;
	foreach($use_cells as $c => $cell) {
		$use_matrix[$r][$c] = $cell;
		$cU_max +=1;
	}
	$rU_max +=1;
}


$layout_links = $extraction["links"]; // NB: variable $extraction["links"] still exists (from "diagram_txt_to_js.php") ; in order to use the find_link() function that calls to $GLOBALS["layout_links"].
$js_uncert = "\n\n";

//Supply links
for($r=4; $r<$rS_max; $r++){
	for($c=4; $c<$cS_max; $c++){
		if($supply_matrix[$r][$c]!='' && $supply_matrix[$r][$c] != '0'){
			$index = find_link($supply_matrix[0][$c],$supply_matrix[$r][0]);//NB: encoding has not been handled in this section => accents will prevent this operation...
			$js_uncert .= "links[$index].sd_value = ". $supply_matrix[$r][$c] . ";\n";
		}
	}
}

//Use links
for($r=4; $r<$rU_max; $r++){
	for($c=4; $c<$cU_max; $c++){
		if($use_matrix[$r][$c]!='' && $use_matrix[$r][$c] != '0'){
			$index = find_link($use_matrix[$r][0],$use_matrix[0][$c]);
			$js_uncert .= "links[$index].sd_value = ". $use_matrix[$r][$c] . ";\n";
		}
	}
}

$js_uncert .= "redraw_nodes_and_links();";

// Writing at the end of the current diagram js file.
file_put_contents($_SESSION['diagram_path'],$js_uncert,FILE_APPEND);

?>