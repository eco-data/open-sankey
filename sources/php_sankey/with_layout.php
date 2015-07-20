<?php

// Extracting information from the layout file
require("functions_txt_to_js.php");
require("attributes.php");
$layout_file = file($_SESSION["layout_path"]);
$layout_file = implode('',$layout_file);
$extraction = parse_txt_diagram($layout_file,$attributes_nodes,$attributes_links);
$layout_nodes = $extraction["nodes"];
$layout_links = $extraction["links"];
$layout_scale = $extraction["scale"];

$nodes_occurences = array();// built as $value (node_name)
$links_occurences = array();//built as $key (source_name) => $value (target_name)
$n = 0;
$l = 0;
$nodes = "var nodes = [\n";
$links = "var links = [\n";

// Supply Nodes and links
for($r=4; $r<$rS_max; $r++){
	array_push($nodes_occurences,$supply_matrix[$r][0]);
	$index = find_node($supply_matrix[$r][0]);
	if ($index[0]=='N'){
		$nodes .= $index . "\n"; // node not found error message
	}
	else {
		$nodes.= '{';
		foreach ($attributes_nodes as $key => $value) {
			if ($value[3]==1 && ($value[2]==1 || trim($layout_nodes[$index][$key]) != '')) {
				if ($value[0]==1) {
					$symbol = array('[',']');
				}
				else if ($value[1]==1) {
					$symbol = array('"','"');
				}
				else {
					$symbol = array('','');
				}
				// issue : sometimes, the x or y information is not saved in the layout (maybe because they are negative values, in the case of useless nodes for instance) and this causes an error 'x: ,' in the generated javascript file. This test will put 0 instead of nothing to prevent the error.
				if (trim($layout_nodes[$index][$key]) != ''){
					$nodes.= ', ' . $key . ': ' . $symbol[0] . $layout_nodes[$index][$key] . $symbol[1];
				}
				else {
					$nodes.= ', ' . $key . ': ' . $symbol[0] . 0 . $symbol[1];
				}
			}
		}
		$nodes .= '},' . "\n";
	}	
	for($c=4; $c<$cS_max; $c++){
		if($supply_matrix[$r][$c]!='' && $supply_matrix[$r][$c] != '0'){
			$index = find_link($supply_matrix[0][$c],$supply_matrix[$r][0]);
			if ($index[0]=='L'){
				$links .= $index . "\n"; // link not found error message
			}			
			else {
				$links_occurences[$supply_matrix[0][$c]] = $supply_matrix[$r][0];
				$links.= '{value: ' . $supply_matrix[$r][$c];
				foreach ($attributes_links as $key => $value) {
					if ($value[3]==1 && ($value[2]==1 || trim($layout_links[$index][$key]) != '')) {
						if ($value[0]==1) {
							$symbol = array('[',']');
						}
						else if ($value[1]==1) {
							$symbol = array('"','"');
						}
						else {
							$symbol = array('','');
						}
						$links.= ', ' . $key . ': ' . $symbol[0] . $layout_links[$index][$key] . $symbol[1];
					}
				}
				$links .= '},' . "\n";
			}
		}
	}
}

// Supply Activity nodes
for($c=4; $c<$cS_max; $c++){
	array_push($nodes_occurences,$supply_matrix[0][$c]);
	$index = find_node($supply_matrix[0][$c]);
	if ($index[0]=='N'){
		$nodes .= $index . "\n"; // node not found error message
	}
	else {
		$nodes.= '{';
		foreach ($attributes_nodes as $key => $value) {
			if ($value[3]==1 && ($value[2]==1 || trim($layout_nodes[$index][$key]) != '')) {
				if ($value[0]==1) {
					$symbol = array('[',']');
				}
				else if ($value[1]==1) {
					$symbol = array('"','"');
				}
				else {
					$symbol = array('','');
				}
				// issue : sometimes, the x or y information is not saved in the layout (maybe because they are negative values, in the case of useless nodes for instance) and this causes an error 'x: ,' in the generated javascript file. This test will put 0 instead of nothing to prevent the error.
				if (trim($layout_nodes[$index][$key]) != ''){
					$nodes.= ', ' . $key . ': ' . $symbol[0] . $layout_nodes[$index][$key] . $symbol[1];
				}
				else {
					$nodes.= ', ' . $key . ': ' . $symbol[0] . 0 . $symbol[1];
				}
			}
		}
		$nodes .= '},' . "\n"; 
	}
}


// Use Links
for($r=4; $r<$rU_max; $r++){
	for($c=4; $c<$cU_max; $c++){
		if($use_matrix[$r][$c]!='' && $use_matrix[$r][$c] != '0'){
			$index = find_link($use_matrix[$r][0],$use_matrix[0][$c]);
			if ($index[0]=='L'){
				$links .= $index . "\n"; // node not found error message
			}
			else {
				$links_occurences[$use_matrix[$r][0]] = $use_matrix[0][$c];
				$links.= '{value: ' . $use_matrix[$r][$c];
				foreach ($attributes_links as $key => $value) {
					if ($value[3]==1 && ($value[2]==1 || trim($layout_links[$index][$key]) != '')) {
						if ($value[0]==1) {
							$symbol = array('[',']');
						}
						else if ($value[1]==1) {
							$symbol = array('"','"');
						}
						else {
							$symbol = array('','');
						}
						$links.= ', ' . $key . ': ' . $symbol[0] . $layout_links[$index][$key] . $symbol[1];
					}
				}
				$links .= '},' . "\n";
			}
		}
	}
}

// Use Activity nodes
for($c=4; $c<$cU_max; $c++){
	if (!in_array($use_matrix[0][$c],$nodes_occurences)){
		$index = find_node($use_matrix[0][$c]);
		if ($index[0]=='N'){
			$nodes .= $index . "\n"; // node not found error message
		}
		else {
			$nodes.= '{';
			foreach ($attributes_nodes as $key => $value) {
				if ($value[3]==1 && ($value[2]==1 || trim($layout_nodes[$index][$key]) != '')) {
					if ($value[0]==1) {
						$symbol = array('[',']');
					}
					else if ($value[1]==1) {
						$symbol = array('"','"');
					}
					else {
						$symbol = array('','');
					}
					// issue : sometimes, the x or y information is not saved in the layout (maybe because they are negative values, in the case of useless nodes for instance) and this causes an error 'x: ,' in the generated javascript file. This test will put 0 instead of nothing to prevent the error.
					if (trim($layout_nodes[$index][$key]) != ''){
						$nodes.= ', ' . $key . ': ' . $symbol[0] . $layout_nodes[$index][$key] . $symbol[1];
					}
					else {
						$nodes.= ', ' . $key . ': ' . $symbol[0] . 0 . $symbol[1];
					}
				}
			}
			$nodes .= '},' . "\n"; 
		}
	}
}

// WRITING NODES AND LINKS

$nodes = substr($nodes, 0, -2);
$links = substr($links, 0, -2);
$nodes .= "\n];\n\n";
$links .= "\n];";
$nodes = str_replace('{, ','{',$nodes);

fputs($w, $nodes);
fputs($w, $links);
fputs($w, "\norder_nodes_links();\n");
//fputs($w, "\nrecompute_nodes();\n");
fputs($w, "\ncompute_nodes();\n");
fputs($w, "\nadd_nodes_auto();\n");
write_environment($w,$layout_scale,0,0);
fputs($w,"var max_link_value = 0;\nlinks.forEach(function(link){\nif (link.value > max_link_value) {\nmax_link_value = link.value;\n}\n});\ndocument.getElementById('filter_id').max = max_link_value;");

?>