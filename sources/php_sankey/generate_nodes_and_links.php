<?php

//	Creation of .js nodes and links variable file
$_SESSION["file_path"] = "../user_sankey/auto_diagrams/" . rand(0,pow(10,10)) . ".js";
$w = fopen($_SESSION["file_path"], 'w');
require("functions_generate.php");

// READING THE SUPPLY FILE
$supply_array = file($_SESSION["supply_path"]);
// In case Line-ending is CR (\r), file() doesn't work.
if (count($supply_array) == 1) {
	$supply_array = explode("\r",$supply_array[0]);
}
$separator = find_separator($supply_array);

// Memorization in matrix format
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

// READING THE USE FILE
$use_array = file($_SESSION["use_path"]);
// In case Line-ending is CR (\r), file() doesn't work.
if (count($use_array) == 1) {
	$use_array = explode("\r",$use_array[0]);
}
$separator = find_separator($use_array);
// Memorization in matrix format
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

if (isset($_SESSION["layout_path"])) {
	require("with_layout.php");
}
else {
	require("without_layout.php");
}

?>