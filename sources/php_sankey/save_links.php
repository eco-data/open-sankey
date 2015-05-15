<?php

session_start();

$links = $_POST["p_links"];

require("save_functions.php");

$w = fopen($_SESSION["download_file_path"], 'a');

save_links_array($links, "links",$w);

fclose($w);

?>