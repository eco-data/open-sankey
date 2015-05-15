<?php

$nodes_occurences = array();// built as $value (node_name)
$links_occurences = array();//built as $key (source_name) => $value (target_name)
$nodes = "var nodes = [\n";
$links = "var links = [\n";

// Product nodes and links
for($r=4; $r<$rS_max; $r++){
	array_push($nodes_occurences,$supply_matrix[$r][0]);
	$nodes .= '{name: "' . $supply_matrix[$r][0] . '", orientation: "' . $supply_matrix[$r][1] . '", color: "' . $supply_matrix[$r][2] . '", merged_name: "' . $supply_matrix[$r][3] . '"';
	$nodes .= '},' . "\n";
	for($c=4; $c<$cS_max; $c++){
		if($supply_matrix[$r][$c]!='' && $supply_matrix[$r][$c] != '0'){
			$links_occurences[$supply_matrix[0][$c]] = $supply_matrix[$r][0];
			$links .= '{source_name: "' . $supply_matrix[0][$c] . '", target_name: "' . $supply_matrix[$r][0] . '", value: ' . $supply_matrix[$r][$c] . ', color: "' . $supply_matrix[$r][2] . '"';
			$links .= '},' . "\n";
		}
	}
}

// Activity nodes
for($c=4; $c<$cS_max; $c++){
	$nodes .= '{name: "' . $supply_matrix[0][$c] . '", orientation: "' . $supply_matrix[1][$c] . '", color: "' . $supply_matrix[2][$c] . '", merged_name: "' . $supply_matrix[3][$c] . '"';
	$nodes .= '},' . "\n";
	array_push($nodes_occurences,$supply_matrix[0][$c]);
}

// Use links
for($r=4; $r<$rU_max; $r++){
	for($c=4; $c<$cU_max; $c++){
		if($use_matrix[$r][$c]!='' && $use_matrix[$r][$c] != '0'){
			$links_occurences[$use_matrix[$r][0]] = $use_matrix[0][$c];
			$links .= '{source_name: "' . $use_matrix[$r][0] . '", target_name: "' . $use_matrix[0][$c] . '", value: ' . $use_matrix[$r][$c] . ', color: "' . $use_matrix[$r][2] . '"';
			$links .= '},' . "\n";
		}
	}
}

// Activity (industries) nodes
for($c=4; $c<$cU_max; $c++){
	if (!in_array($use_matrix[0][$c],$nodes_occurences)){
		array_push($nodes_occurences,$use_matrix[0][$c]);
		$nodes .= '{name: "' . $use_matrix[0][$c] . '", orientation: "' . $use_matrix[1][$c] . '", color: "' . $use_matrix[2][$c] . '", merged_name: "' . $use_matrix[3][$c] . '"';
		$nodes .= '},' . "\n";
	}
}

$nodes = substr($nodes, 0, -2);
$links = substr($links, 0, -2);
$nodes .= "\n];\n\n";
$links .= "\n];";

fputs($w, $nodes);
fputs($w, $links);

?>