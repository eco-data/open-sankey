<?php

session_start();
 
function copy_uploaded_file($post_name, $destination, $allowedExts, $final_extension, $use_name=0) {
	$temp = explode(".", $_FILES[$post_name]["name"]);
	$extension = end($temp);
	echo $_FILES[$post_name]["type"];
	echo $_FILES[$post_name]["size"];
	echo $_FILES[$post_name]["error"];
	if ((($_FILES[$post_name]["type"] == "text/plain") || ($_FILES[$post_name]["type"] == "text/csv") || ($_FILES[$post_name]["type"] == "application/vnd.ms-excel"))
		&& $_FILES[$post_name]["size"] < 1000000
//		&& in_array($extension, $allowedExts)
	) {
		if ($_FILES[$post_name]["error"] > 0) {
//	    	echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
	    }
	  	else {
//		    echo "Upload: " . $_FILES["file"]["name"] . "<br>";
//		    echo "Type: " . $_FILES["file"]["type"] . "<br>";
//		    echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
//		    echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br>";
			if ($use_name) {
				$_SESSION["file_path"] = $final_extension;
			}
			else {
				$_SESSION["file_path"] = $destination . "/" . rand(0,pow(10,10)) . $final_extension;
			}
      		move_uploaded_file($_FILES[$post_name]["tmp_name"],  $_SESSION["file_path"]);
//	      	echo "Stored in: " . "upload/" . $_FILES["file"]["name"];
			return true;
	    }
	  }
	return false;
}

?>