<?php

require('languages.php');

function print_header($page_title, $active_language) {	
	echo "
	<!DOCTYPE html>
	<html lang='$active_menu'>
  		<head>
    <meta charset='utf-8'>
    <title>$title</title>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta name='description' content='Sankey'>
    <meta name='author' content='eco-data'>
    <!-- Le style -->
    <link href='". $GLOBALS["to_main_dir"] . "sources/css/bootstrap.css' rel='stylesheet'>
    <link href='". $GLOBALS["to_main_dir"] . "sources/css/main.css' rel='stylesheet'>
    <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
    </style>
    "; // end of echo
    
    echo "
    <link href='". $GLOBALS["to_main_dir"] . "sources/css/bootstrap-responsive.css' rel='stylesheet'>
    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src='http://html5shim.googlecode.com/svn/trunk/html5.js'></script>
    <![endif]-->
  </head>
  <body>
    <div class='navbar navbar-inverse navbar-fixed-top'>
      <div class='navbar-inner'>
        <div class='container'>
          <a class='btn btn-navbar' data-toggle='collapse' data-target='.nav-collapse'>
            <span class='icon-bar'></span>
            <span class='icon-bar'></span>
            <span class='icon-bar'></span>
          </a>
          <a class='brand' href='#'><img src='' alt=''></a>
          <div class='nav-collapse collapse'>
            <ul class='nav'>
	"; // end of echo.

	foreach ($GLOBALS['languages'] as $key => $value) {
		if ($key == $active_language[0]) {
			echo "<li class='active'><a href='". print_url_lang($key) . "'>$value</a></li>";
		}
		else {
			echo "<li class='dropdown'><a href='" . print_url_lang($key) . "'>$value</a></li>";
		}
	}
	
	echo "
			</ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
    "; 
}

function print_url_lang($destination_lang){
	// retrieve current url
	$url = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	$search = '/'.$_SESSION['language'][0].'/';
	$replace = '/'. $destination_lang . '/';
	$new_url = str_replace($search,$replace,$url);
	return 'http://'.$new_url; // absolute path
}

?>