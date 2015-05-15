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
	$nodes.= '{id: ' . $index;
	$nodes.= ', name: "' . $layout_nodes[$index]['name'] . '"';
	$nodes.= ', color: "' . $layout_nodes[$index]['color'] . '"';
	$nodes.= ', orientation: "' . $layout_nodes[$index]['orientation'] . '"';
	$nodes.= ', merged_name: "' . $layout_nodes[$index]['merged_name'] . '"';
	$nodes.= ', x: ' . $layout_nodes[$index]['x'];
	$nodes.= ', y: ' . $layout_nodes[$index]['y'];
	$nodes.= ', x_label: ' . $layout_nodes[$index]['x_label'];
	$nodes.= ', y_label: ' . $layout_nodes[$index]['y_label'];
	$nodes.= ', input_links: [' . $layout_nodes[$index]['input_links'] . ']';
	$nodes.= ', output_links: [' . $layout_nodes[$index]['output_links'] . ']';
//	$nodes.= ', input_offsets: [' . $layout_nodes[$index]['input_offsets'] . ']';
//	$nodes.= ', output_offsets: [' . $layout_nodes[$index]['output_offsets'] . ']';
//	$nodes.= ', total_input_offset: ' . $layout_nodes[$index]['total_input_offset'];
//	$nodes.= ', total_output_offset: ' . $layout_nodes[$index]['total_output_offset'];
	$nodes .= '},' . "\n"; 
	for($c=4; $c<$cS_max; $c++){
		if($supply_matrix[$r][$c]!='' && $supply_matrix[$r][$c] != '0'){
			$index = find_link($supply_matrix[0][$c],$supply_matrix[$r][0]);
			$links_occurences[$supply_matrix[0][$c]] = $supply_matrix[$r][0];
			$links .= '{id: ' . $index;
			$links .= ', color: "' . $layout_links[$index]['color'] . '"';
			$links .= ', source_name: "' . $layout_links[$index]['source_name'] . '"';
			$links .= ', target_name: "' . $layout_links[$index]['target_name'] . '"';
			$links .= ', source: ' . $layout_links[$index]['source'];
			$links .= ', target: ' . $layout_links[$index]['target'];
			$links .= ', value: ' . $supply_matrix[$r][$c];
			$links .= ', x_label: ' . $layout_links[$index]['x_label'];
			$links .= ', y_label: ' . $layout_links[$index]['y_label'];
			$links .= ', x_center: ' . $layout_links[$index]['x_center'];
			$links .= '},' . "\n";
		}
	}
}

// Supply Activity nodes
for($c=4; $c<$cS_max; $c++){
	array_push($nodes_occurences,$supply_matrix[0][$c]);
	$index = find_node($supply_matrix[0][$c]);
	$nodes.= '{id: ' . $index;
	$nodes.= ', name: "' . $layout_nodes[$index]['name'] . '"';
	$nodes.= ', color: "' . $layout_nodes[$index]['color'] . '"';
	$nodes.= ', orientation: "' . $layout_nodes[$index]['orientation'] . '"';
	$nodes.= ', merged_name: "' . $layout_nodes[$index]['merged_name'] . '"';
	$nodes.= ', x: ' . $layout_nodes[$index]['x'];
	$nodes.= ', y: ' . $layout_nodes[$index]['y'];
	$nodes.= ', x_label: ' . $layout_nodes[$index]['x_label'];
	$nodes.= ', y_label: ' . $layout_nodes[$index]['y_label'];
	$nodes.= ', input_links: [' . $layout_nodes[$index]['input_links'] . ']';
	$nodes.= ', output_links: [' . $layout_nodes[$index]['output_links'] . ']';
//	$nodes.= ', input_offsets: [' . $layout_nodes[$index]['input_offsets'] . ']';
//	$nodes.= ', output_offsets: [' . $layout_nodes[$index]['output_offsets'] . ']';
//	$nodes.= ', total_input_offset: ' . $layout_nodes[$index]['total_input_offset'];
//	$nodes.= ', total_output_offset: ' . $layout_nodes[$index]['total_output_offset'];
	$nodes .= '},' . "\n";
}


// Use Links
for($r=4; $r<$rU_max; $r++){
	for($c=4; $c<$cU_max; $c++){
		if($use_matrix[$r][$c]!='' && $use_matrix[$r][$c] != '0'){
			$index = find_link($use_matrix[$r][0],$use_matrix[0][$c]);
			$links_occurences[$use_matrix[$r][0]] = $use_matrix[0][$c];
			$links .= '{id: ' . $index;
			$links .= ', color: "' . $layout_links[$index]['color'] . '"';
			$links .= ', source_name: "' . $layout_links[$index]['source_name'] . '"';
			$links .= ', target_name: "' . $layout_links[$index]['target_name'] . '"';
			$links .= ', source: ' . $layout_links[$index]['source'];
			$links .= ', target: ' . $layout_links[$index]['target'];
			$links .= ', value: ' . $use_matrix[$r][$c];
			$links .= ', x_label: ' . $layout_links[$index]['x_label'];
			$links .= ', y_label: ' . $layout_links[$index]['y_label'];
			$links .= ', x_center: ' . $layout_links[$index]['x_center'];
			$links .= '},' . "\n";
		}
	}
}

// Use Activity nodes
for($c=4; $c<$cU_max; $c++){
	if (!in_array($use_matrix[0][$c],$nodes_occurences)){
		$index = find_node($use_matrix[0][$c]);
		$nodes.= '{id: ' . $index;
		$nodes.= ', name: "' . $layout_nodes[$index]['name'] . '"';
		$nodes.= ', color: "' . $layout_nodes[$index]['color'] . '"';
		$nodes.= ', orientation: "' . $layout_nodes[$index]['orientation'] . '"';
		$nodes.= ', merged_name: "' . $layout_nodes[$index]['merged_name'] . '"';
		$nodes.= ', x: ' . $layout_nodes[$index]['x'];
		$nodes.= ', y: ' . $layout_nodes[$index]['y'];
		$nodes.= ', x_label: ' . $layout_nodes[$index]['x_label'];
		$nodes.= ', y_label: ' . $layout_nodes[$index]['y_label'];
		$nodes.= ', input_links: [' . $layout_nodes[$index]['input_links'] . ']';
		$nodes.= ', output_links: [' . $layout_nodes[$index]['output_links'] . ']';
//		$nodes.= ', input_offsets: [' . $layout_nodes[$index]['input_offsets'] . ']';
//		$nodes.= ', output_offsets: [' . $layout_nodes[$index]['output_offsets'] . ']';
//		$nodes.= ', total_input_offset: ' . $layout_nodes[$index]['total_input_offset'];
//		$nodes.= ', total_output_offset: ' . $layout_nodes[$index]['total_output_offset'];
		$nodes .= '},' . "\n";
	}
}

// WRITING NODES AND LINKS

$nodes = substr($nodes, 0, -2);
$links = substr($links, 0, -2);
$nodes .= "\n];\n\n";
$links .= "\n];";
$nodes = str_replace(', x_label: ,',',',$nodes);
$nodes = str_replace(', y_label: ,',',',$nodes);
$links = str_replace(', x_label: ,',',',$links);
$links = str_replace(', y_label: ,',',',$links);
$links = str_replace(', x_center: }','}',$links);

fputs($w, $nodes);
fputs($w, $links);
fputs($w, "\nrecompute_nodes(999999);\n");
fputs($w, "\norder_nodes_links();\n");
fputs($w, "\nadd_nodes_auto();\n");
write_environment($w,$layout_scale,0,0);
fputs($w,"var max_link_value = 0;\nlinks.forEach(function(link){\nif (link.value > max_link_value) {\nmax_link_value = link.value;\n}\n});\ndocument.getElementById('filter_id').max = max_link_value;");

?>