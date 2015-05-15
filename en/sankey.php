<?php

session_start();
$_SESSION['language'] = array('en','EN');

if (is_null($_SESSION["access"])){
	$_SESSION["bug"]="bug";
	$url = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	$url = str_replace('sources/php_sankey/sankey.php',$_SESSION['language'][0].'/start.php',$url);
	echo "<meta http-equiv='refresh' content='0; url=http://$url'>";
}

require("root.php");
require($to_main_dir . "header.php");
print_header('sankey',$_SESSION['language']);

?>

	<div id="herowrap">

		<div class="container">

			<div class="row">
					<div class="span3 offset1"><h3>Sankey</h3></div>
					<div class="span3.5"><br><br><a href="#save_diagram_modal" role="button" class="btn" data-toggle="modal">Save diagram</a></div>
					<div class="span1.5"><br><br><a href="#myModal" role="button" class="btn" data-toggle="modal">Tutorial</a></div>
			</div><!-- /row -->

		</div><!-- /container -->

	</div><!-- /herowrap -->
	
    <script src="<?php echo $to_main_dir; ?>sources/js/jquery-2.0.3.min.js" type="text/javascript"></script>
    <script src="<?php echo $to_main_dir; ?>sources/js/d3.v3.min.js" type="text/javascript"></script>
    <style type="text/css">
.selected_node {
	stroke: black;
}
.selected_link {
	fill: none;
	stroke-opacity: 1;
}
.label {
	font: 11 px "arial";
}
.node {
	stroke: none;
	fill-opacity: 0.9;
}
.link {
	fill: none;
	stroke-opacity: 0.9;
}​
    </style>

  
<body>
<div class="container">
<form name="node_info" style="margin-bottom:0px">
<div class="row">
<div class="span2"><b>NODES</b></div>
<div class="span0.5">Name:</div>
<div class="span2.5"><textarea name="name" rows="1" cols="15" style="height:24px; width:150px; font-size:13px; padding-top:2px; padding-bottom:2px; padding-left:2px; padding-right:2px;"></textarea></div>
<div class="span4">Orientation:&nbsp;&nbsp;<input type="radio" name="orientation" value="vertical">&nbsp;Vertical</input>
&nbsp;<input type="radio" name="orientation" value="horizontal">&nbsp;Horizontal</input></div>
<div class="span0.5">Color:</div>
<div class="span2.5"><textarea name="color" rows="1" cols="8" style="height:24px; width:100px;font-size:13px; padding-top:2px; padding-bottom:2px; padding-left:2px; padding-right:2px;"></textarea></div>
</form>
</div>
<div class="offset2">
<button onclick="add_new_node()">Add</button>
<button onclick="update_node()">Update</button>
<button onclick="delete_node()">Delete</button>
</div>

<hr>

<form name="link_info" style="margin-bottom:0px">
<div class="row">
<div class="span2"><b>LINKS</b></div>
<div class="span0.5">Source (#):</div>
<div class="span1"><textarea name="source" rows="1" cols="2" style="height:24px; width:30px; font-size:13px; padding-top:2px; padding-bottom:2px; padding-left:2px; padding-right:2px;"></textarea></div>
<div class="span0.5">Target (#):</div>
<div class="span1"><textarea name="target" rows="1" cols="2" style="height:24px; width:30px; font-size:13px; padding-top:2px; padding-bottom:2px; padding-left:2px; padding-right:2px;"></textarea></div>
<div class="span0.5">Value:</div>
<div class="span1"><textarea name="value" rows="1" cols="5" style="height:24px; width:60px; font-size:13px; padding-top:2px; padding-bottom:2px; padding-left:2px; padding-right:2px;"></textarea></div>
<div class="span0.5">Color:</div>
<div class="span1"><textarea name="color" rows="1" cols="8" style="height:24px; width:100px; font-size:13px; padding-top:2px; padding-bottom:2px; padding-left:2px; padding-right:2px;"></textarea></div>
</form>
</div>
<div class="offset2">
<button onclick="add_new_link()">Add</button>
<button onclick="update_link()">Update</button>
<button onclick="delete_link()">Delete</button>
</div>

<hr>

<div class="row">
<form name="scale_info" style="margin-bottom:0px">
<div class="span2"><b>SETTINGS</b></div>
<div class="span0.5">Scale (value for 100px):</div>
<div class="span1"><textarea name="scale" rows="1" cols="5" style="height:24px; width:60px; font-size:13px; padding-top:2px; padding-bottom:2px; padding-left:2px; padding-right:2px;">100</textarea></div>
</form>
<div class="span1.5"><button onclick="update_scale()">Update</button></div>
<div class="span0.5">Filter:</div> 
<div class="span1.5"><input id="filter_id" style="width:100px" type="range" min="0" max="1000" value="0"></div>
<div class="span1" id="current_filter" style="width:60px; font-size:13px;">0</div>
<?php
if ($_SESSION["sankey_type"] != "manual_sankey") {
	echo "
		<div class='span1'>Agréger les flux : </div>
		<div class='span0.5'><input type='checkbox' id='aggregation_info' value='aggregate'></input></div>
		";
}
?>
</div> <!-- End of row -->

<hr>

</div> <!-- End of container -->

<script src="<?php echo $to_main_dir; ?>sources/js_sankey/clone.js" type="text/javascript"></script>
<script>var to_main_dir = '<?php echo $to_main_dir ?>';</script>
<script src="<?php echo $to_main_dir; ?>sources/js_sankey/manual_sankey.js" type="text/javascript"></script>

<?php

if ($_SESSION["sankey_type"] == "saved_diagram") {
	echo "<script src='" . $to_main_dir . "sources/user_sankey/" . $_SESSION["file_path"] . "' type='text/javascript'></script>";
}

elseif ($_SESSION["sankey_type"] == "auto_sankey") {
	echo "<script src='" . $to_main_dir . "sources/user_sankey/" . $_SESSION["file_path"] . "' type='text/javascript'></script>";
	if (!isset($_SESSION["layout_path"])){
		echo "<script src='" . $to_main_dir . "sources/js_sankey/auto_sankey.js' type='text/javascript'></script>";
	}
}

?>

<a id="download_link" style="visibility:hidden" href="<?php echo $to_main_dir; ?>sources/php_sankey/my_file.php"></a>

</body>

<?php
require($to_main_dir . "footer.php");
print_footer();
?>

<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">User guide</h3>
  </div>
  <div class="modal-body">
  	<p>A sankey diagram is composed of nodes connected to each other with links. A link has a unique origine (source) node and a unique destination (target) node. Nodes can be vertical or horizontal:
  		<img src="<?php echo $to_main_dir . 'sources/examples/orientation.png' ?>"></img>
  	</p>
  	<p>In the current version, it is not possible to create a link between two horizontal nodes.</p>
    <p><b>To create your first diagram manually:</b></p>
    <ul>
    	<li>In the NODES part, fill out the fields "Name", "Orientation" and "Color" and validate by pressing "Add". The "Color" field must contain an html color (for instance "red", "steelblue" or "#019abf"). The node is created in the central part. You can move it by drag and drop. This node has the id "0" (which is displayed on mouseover after a few seconds).</li>
    	<li>Create a 2nd node identically. This node has the id "1".</li>
    	<li>In the LINKS part, fill out the fields "Source" with "0", "Target" with "1", "Value" with a number and "Color" and then validate by clicking on "Add".</li>
    	<li>If elements are superposed, use drag and drop to move them.</li>
    	<li>If you want, you can ajust the scale (the larger the number in the field the smaller the links) and validate by clicking on "Update".</li>
    </ul>
   <p><b>Other available functions:</b></p> 
   <ul>
   		<li><b>Modify a node or a link.</b> Click on the node or link. The information associated to the objet appear in the fields. You can modify them and validate by clicking "Update".</li>
    	<li><b>Delete a node or a link.</b> Click on the node or link and on "Delete".</li>
    	<li><b>Modify label position.</b> Maintain the "alt" keyboard key pressed and move the label.</li>
    	<li><b>Go to a new line in a label.</b> Type "&lt;br&gt;" in the field "Name" where you want the line feed.</li>
    	<li><b>Change the order of a node's input or output links</b> Place the cursor on the link you wish to move close enough to the node. Drag it up or down: the order changes when you go beyond the border with the neighbour link. To facilitate the operation with thin links, you can zoom in with your browser.</li>
    	<img src="<?php echo $to_main_dir . 'sources/examples/links_position.png' ?>"></img>
    	 <li><b>Translate the center of of link horizontally</b> (between two vertical nodes). Look for the center by moving the mouse over it and drag it left or right.
    	<img src="<?php echo $to_main_dir . 'sources/examples/links_center.png' ?>"></img></li>
    	<li><b>Save your work.</b> Click on the "Save diagram" button and enter a title to download all the info associated to your diagram. Keep this file and load it on your next sankey session via the interface "Load a previously saved diagram".</li>
    </ul>
    <p><b>Thanks for citing the name "Open Sankey" if you use this tool in a paper!</b></p>	
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<!-- Modal -->
<div id="save_diagram_modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Choose a title for your diagram</h3>
  </div>
  <div class="modal-body">
  	<input id="diagram_title" type="textarea"></input>		
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
    <button id="save_layout_button" type="button" class="btn btn-primary">Save</button>
  </div>
</div>