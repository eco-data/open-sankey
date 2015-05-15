<?php

session_start();

$filtered_nodes = $_POST["p_filtered_nodes"];
$filtered_links = $_POST["p_filtered_links"];

require("save_functions.php");

$w = fopen($_SESSION["download_file_path"], 'a');

save_nodes_array($filtered_nodes, "filtered_nodes",$w);
save_links_array($filtered_links, "filtered_links",$w);

fclose($w);

?>