<?php

session_start();

require('root.php');//set variable $to_main_dir

$_SESSION["access"] = true;
$_SESSION["sankey_type"] = "manual_sankey";

$url = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
$url = str_replace('sources/php_sankey/manual_sankey.php',$_SESSION['language'][0].'/sankey.php',$url);

echo "<meta http-equiv='refresh' content='0; url=http://$url'>";

?>