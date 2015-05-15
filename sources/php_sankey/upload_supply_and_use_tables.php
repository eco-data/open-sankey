<?php

session_start();

$_SESSION["access"] = true;

$_SESSION["sankey_type"] = "auto_sankey";

require("upload_file.php");

$destination_1 = "../user_sankey/supply_uploads";
$destination_2 = "../user_sankey/use_uploads";
$destination_3 = "../user_sankey/layout_uploads";
$allowedExts = array("csv","txt");

if (!copy_uploaded_file("supply_file", $destination_1, $allowedExts, ".csv")){
	$_SESSION["error"] = "supply"; // invalid supply file
	$url = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	$url = str_replace('sources/php_sankey/upload_supply_and_use_tables.php',$_SESSION['language'][0].'/start.php',$url);
	echo "<meta http-equiv='refresh' content='0; url=http://$url'>";
}

else {
	$_SESSION["supply_path"] = $_SESSION["file_path"];
	if (!copy_uploaded_file("use_file", $destination_2, $allowedExts, ".csv")){
		$_SESSION["error"] = "use"; // invalid use file
		$url = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		$url = str_replace('sources/php_sankey/upload_supply_and_use_tables.php',$_SESSION['language'][0].'/start.php',$url);
		echo "<meta http-equiv='refresh' content='0; url=http://$url'>";
	}
	else {
		$_SESSION["use_path"] = $_SESSION["file_path"];
		if (!isset($_POST["charset"])) {
			$_SESSION["error"] = "charset";
			$url = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
			$url = str_replace('sources/php_sankey/upload_supply_and_use_tables.php',$_SESSION['language'][0].'/start.php',$url);
			echo "<meta http-equiv='refresh' content='0; url=http://$url'>";
		}
		else {
			$_SESSION["charset"] = htmlspecialchars($_POST["charset"]);
			if (copy_uploaded_file("layout_file", $destination_3, $allowedExts, ".txt")){
				$_SESSION["layout_path"] = $_SESSION["file_path"];
				$_SESSION["sankey_type"] = "saved_diagram";
			}		
			require("generate_nodes_and_links.php");
			unset($_SESSION["error"]);
			$url = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
			$url = str_replace('sources/php_sankey/upload_supply_and_use_tables.php',$_SESSION['language'][0].'/sankey.php',$url);
			echo "<meta http-equiv='refresh' content='0; url=http://$url'>";
		}
	}
}

?>