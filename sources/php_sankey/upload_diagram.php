<?php

session_start();

$_SESSION["access"] = true;

$_SESSION["sankey_type"] = "saved_diagram";

require("upload_file.php"); // copy_uploaded_file function
$destination = "../user_sankey/diagrams_uploads";
$allowedExts = array("txt");
$allowedExts_uncert = array("csv");

if (!copy_uploaded_file("diagram_file", $destination, $allowedExts, ".txt")) {
	$_SESSION["error"] = "diagram";
	$url = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	$url = str_replace('sources/php_sankey/upload_diagram.php',$_SESSION['language'][0].'/start.php',$url);
	echo "<meta http-equiv='refresh' content='0; url=http://$url'>";
}
else {
	unset($_SESSION["error"]);
	require("diagram_txt_to_js.php");
	$_SESSION["diagram_path"] = $_SESSION["file_path"];
	$uncert_supply_name = str_replace('.js', '_uncert_S.csv', $_SESSION["diagram_path"]);
	if (copy_uploaded_file("uncertainties_file_supply", $destination, $allowedExts_uncert,$uncert_supply_name,1)) {
		$_SESSION["uncert_supply_path"] = $_SESSION["file_path"];
		$uncert_use_name = str_replace('.js', '_uncert_U.csv', $_SESSION["diagram_path"]);
		if (copy_uploaded_file("uncertainties_file_use", $destination, $allowedExts_uncert,$uncert_use_name,1)) {
			$_SESSION["uncert_use_path"] = $_SESSION["file_path"];
			require("uncertainties.php");
			$_SESSION["file_path"] = $_SESSION["diagram_path"];
		}
	}
	$url = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	$url = str_replace('sources/php_sankey/upload_diagram.php',$_SESSION['language'][0].'/sankey.php',$url);
	echo "<meta http-equiv='refresh' content='0; url=http://$url'>";
}

?>