<?php

session_start();

require("root.php");

$nodes = $_POST["p_nodes"];
$title = htmlspecialchars($_POST["p_title"]);

$dir = rand(0,pow(10,10));
mkdir($to_main_dir."sources/user_sankey/downloaded_diagrams/$dir");
$saved_txt_file = $to_main_dir."sources/user_sankey/downloaded_diagrams/$dir/$title.txt";

$_SESSION["download_file_path"] = $saved_txt_file;

require("save_functions.php");

$w = fopen($saved_txt_file, 'w');

 	
// Save nodes
save_nodes_array($nodes, "nodes",$w);

fclose($w);

?>