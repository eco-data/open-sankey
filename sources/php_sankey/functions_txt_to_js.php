<?php

// EXTRACT FUNCTION___________________________________________________

function parse_txt_diagram($txt,$attributes_nodes,$attributes_links) {
	$extraction = array();
	// nodes
	$subtxt = extract_end_begin($txt,"\n\nlinks","nodes\n\n");
	if ($subtxt){
		$nodes = extract_nodes_or_links($subtxt,$attributes_nodes);
		$extraction["nodes"] = $nodes;
	}
	// links
	$subtxt = extract_end_begin($txt,"\n\nfiltered_nodes","links\n\n");
	if ($subtxt){
		$links = extract_nodes_or_links($subtxt,$attributes_links);
		$extraction["links"] = $links;
	}
	// filtered 
	$subtxt = extract_end_begin($txt,"\n\nfiltered_links","filtered_nodes\n\n");
	if ($subtxt){
		$filtered_nodes = extract_nodes_or_links($subtxt,$attributes_nodes);
		$extraction["filtered_nodes"] = $filtered_nodes;
	}
	$subtxt = extract_end_begin($txt,"\n\nscale","filtered_links\n\n");
	if ($subtxt){
		$filtered_links = extract_nodes_or_links($subtxt,$attributes_links);
		$extraction["filtered_links"] = $filtered_links;
	}
	// environment
	$scale = extract_end_begin($txt,"\ncurrent_filter","scale = ");
	$extraction["scale"] = $scale;
	$filter = extract_end_begin($txt,"\nmax_filter","current_filter = ");
	$extraction["filter"] = $filter;
	$filter_range = explode("max_filter = ",$txt);
	$filter_range = $filter_range[1];
	$extraction["filter_range"] = $filter_range;
	return $extraction;
}

function extract_end_begin($txt,$end,$begin){
	$extraction = explode($end,$txt);
	if (count($extraction)>1){
		$extraction = $extraction[0];
		$extraction = explode($begin,$extraction);
		if (count($extraction)>1){
			$extraction = $extraction[1];
			return $extraction;
		}
		echo "warning 2: '$begin' not found in string.";
		return;
	}
	echo "warning 1: '$end' not found in string.";
	return;
}

function extract_nodes_or_links($txt,$attributes){
	$nodes_0 = explode("\n\n",$txt);
	$nodes = array();
	foreach($nodes_0 as $value){
		$new_node = array();	
		// Extacting attributes
		foreach($attributes as $at=>$is_array){
			$attribute_0 = explode("$at = ",$value);
			if (count($attribute_0)>1){
				$attribute_1 = explode("\n",$attribute_0[1]);
				$new_node[$at] = $attribute_1[0];
			}
		}
		array_push($nodes,$new_node);
	}
	return $nodes;
}

// WRITE FUNCTIONS___________________________________________________

function write_array($file, $name, $letter, $variable, $attributes) {
	fputs($file, $name . " = [];\n" );
	if (isset($variable)){
	 	foreach ($variable as $element) {
	 		fputs($file,"var $letter = {};\n");
	 		foreach($attributes as $at => $type){
	 			write_attribute($file, $element, $letter, $at, $type);
	 		}
	 		fputs($file,$name . ".push($letter);\n");
	 	}
	}
};

function write_attribute($file, $element, $letter, $attribute_name, $type){
	if ($type[0]){
		// Le cas d'un array de string n'existe pas => on ne le traite pas.
		fputs($file, $letter . "." . $attribute_name . " = [" . $element[$attribute_name] . "];\n");
	}
	else {
		if ($element[$attribute_name]!='') {
			if ($type[1]){
				fputs($file, $letter . "." . $attribute_name . " = \"" . $element[$attribute_name] . "\";\n");
			}
			else {
				fputs($file, $letter . "." . $attribute_name . " = " . $element[$attribute_name] . ";\n");
			}
		}
	}
}

function write_environment($file,$scale,$filter,$filter_range){
	// Scale
	fputs($file, "scale.domain([0," . $scale . "]);\n");
	fputs($file, "document.scale_info.scale.textContent = ". $scale . ";\n");
	fputs($file, "redraw_nodes_and_links();\n");
	// Filter
	fputs($file, "current_filter = " . $filter . ";\n");
	fputs($file, "document.getElementById(\"current_filter\").textContent = " . $filter . ";\n");
	fputs($file, "document.getElementById(\"filter_id\").max = " . $filter_range . ";\n");
	fputs($file, "document.getElementById(\"filter_id\").value = " . $filter . ";\n");
	fputs($file, "set_nodes_names();\n");
	fputs($file, "update_filtered_nodes_names();\n");
}

?>