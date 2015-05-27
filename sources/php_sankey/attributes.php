<?php

$attributes_nodes = array(
	"color" => array(0,1,1,1), // 1st element = is_array ; 2nd element = is_string ; 3rd element = is_mandatory ; 4th element = use_when_extracting_layout
	"id" => array(0,0,1,1),
	"name" => array(0,1,1,1),
	"orientation" => array(0,1,1,1),
	"x" => array(0,0,1,1),
	"y" => array(0,0,1,1),
	"x_filter_pos" => array(0,0,0,1),
	"y_filter_pos" => array(0,0,0,1),
	"input_links" => array(1,0,1,1), // can be used in layout if all the links are the same
	"output_links" => array(1,0,1,1), // can be used in layout if all the links are the same
	"input_offsets" => array(1,0,1,0),
	"output_offsets" => array(1,0,1,0),
	"total_input_offset" => array(0,0,1,0),
	"total_output_offset" => array(0,0,1,0),
	"x_label" => array(0,0,0,1),
	"y_label" => array(0,0,0,1),
	"merged_name" => array(0,1,0,1),
	"merged" => array(0,0,0,1) 
);
$attributes_links = array(
	"color" => array(0,1,1,1),
	"id" => array(0,0,1,1),
	"source" => array(0,0,1,1),
	"target" => array(0,0,1,1),
	"source_name" => array(0,1,1,1),
	"target_name" => array(0,1,1,1),
	"value" => array(0,0,1,0),
	"sd_value" => array(0,0,0,0),
	"x_label" => array(0,0,0,1),
	"y_label" => array(0,0,0,1),
	"x_center" => array(0,0,0,1),
	"left_horiz_shift" => array(0,0,0,1),
	"right_horiz_shift" => array(0,0,0,1),
	"vert_shift" => array(0,0,0,1),
	"x_sd_label" => array(0,0,0,1),
	"y_sd_label" => array(0,0,0,1)
);

?>
