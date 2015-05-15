<?php

session_start();

require("root.php");

$download_title = basename($_SESSION["download_file_path"],".txt");

header('Content-Type: text/plain');
header('Content-Disposition: attachment;filename="'. $download_title . '"');
$r=fopen($_SESSION["download_file_path"],'r');
fpassthru($r);
fclose($r);

?>