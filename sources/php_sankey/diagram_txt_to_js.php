<?php

require("functions_txt_to_js.php");
require("attributes.php");

$txt = $_SESSION["file_path"];
$_SESSION["file_path"] = str_replace(".txt",".js",$_SESSION["file_path"]);
$js = fopen($_SESSION["file_path"],'w');

$txt_diagram = file($txt);
$txt_diagram = implode('',$txt_diagram);

// security
$diagram = htmlspecialchars($diagram[0]);
$diagram = str_replace('script','',$diagram);
$diagram = str_replace('(','',$diagram);
$diagram = str_replace(')','',$diagram);

// extract variables from text file
$extraction = parse_txt_diagram($txt_diagram,$attributes_nodes,$attributes_links);

// write nodes
write_array($js,"nodes","n",$extraction["nodes"],$attributes_nodes);
fputs($js, "add_nodes_auto();\n\n");

// write links
write_array($js,"links","l",$extraction["links"],$attributes_links);
fputs($js, "add_links();\n\n");

// write filtered
write_array($js,"filtered_nodes","n",$extraction["filtered_nodes"],$attributes_nodes);
fputs($js, "\n");
write_array($js,"filtered_links","l",$extraction["filtered_links"],$attributes_links);
fputs($js, "\n");

// write environment
write_environment($js,$extraction["scale"],$extraction["filter"],$extraction["filter_range"]);

?>