<?php

session_start();

$scale = $_POST["p_scale"];
$filter = $_POST["p_filter"];
$filter_range = $_POST["p_filter_range"];

$w = fopen($_SESSION["download_file_path"], 'a');

// Save scale
fputs($w, "scale = $scale\n");

// Filter
fputs($w, "current_filter = " . $filter . "\n");
fputs($w, "max_filter = " . $filter_range);

fclose($w);

?>