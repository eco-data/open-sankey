<?php
	session_start();
	$_SESSION['language'] = array('en','EN');
	unset($_SESSION['layout_path']);
	unset($_SESSION["access"]);
	unset($_SESSION["sankey_type"]);
	unset($_SESSION["use_path"]);
	unset($_SESSION["supply_path"]);
	unset($_SESSION["file_path"]);
	unset($_SESSION["charset"]);
	
	require('root.php'); // set variable '$to_main_dir'
	require($to_main_dir.'header.php');
	print_header('sankey',$_SESSION['language']);
	
	$error_message = array(
		"diagram" => "Invalid file",
		"supply" => "Invalid supply file",
		"use" => "Invalid use file",
		"charset" => "Undefined encoding"
	);
?>

	<div id="herowrap">
		<div class="container">
			<div class="span12">
				<h3>Sankey</h3>
			</div><!-- /span12 -->
		</div><!-- /container -->
	</div><!-- /herowrap -->
<br>

<!-- Container -->
<div class="container">	
<a href="<?php echo $to_main_dir.'sources/php_sankey/manual_sankey.php' ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Draw a new diagram manually</a>
	<br><hr>
	OR&nbsp;&nbsp;&nbsp;&nbsp;Load a previously saved diagram:
	<form action="<?php echo $to_main_dir.'sources/php_sankey/upload_diagram.php' ?>" method="post" enctype="multipart/form-data">
		<div class="span2">Diagram <input type="file" name="diagram_file"></div>
		<div class="span2">Supply uncertainties <input type="file" name="uncertainties_file_supply" ></div>
		<div class="span2">Use uncertainties <input type="file" name="uncertainties_file_use" ></div>
		<div class="span1"></div>
		<input type="submit" name="submit" value="Load">
	</form>
	<?php if ($_SESSION["error"] == "diagram") {echo "<div style='color:red'>".$error_message["diagram"]."</div>";} ?>
	<br>
	<hr>
	<div>
		OR&nbsp;&nbsp;&nbsp;&nbsp;Load the supply and use tables corresponding to the diagram in order to obtain a first representation automatically.<br><br>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#myModal" role="button" class="btn" data-toggle="modal">Display the example and download associated files.</a><br><br>
		<div class="row">
		<form action="<?php echo $to_main_dir.'sources/php_sankey/upload_supply_and_use_tables.php' ?>" method="post" enctype="multipart/form-data">
			<div class="span2">Supply <input type="file" name="supply_file"></div>
			<div class="span2">Use <input type="file" name="use_file" ></div>
			<div class="span2">Layout <input type="file" name="layout_file" ></div>
			<div class="span2.5" ><span id="t1" rel="tooltip" title="pour le rendu des accents">Encoding:</span><br>
				&nbsp;&nbsp;&nbsp;<input type="radio" name="charset" value="utf-8"> UTF-8<br>
				&nbsp;&nbsp;&nbsp;<input type="radio" name="charset" value="iso-8859-1"><span id="t2" rel="tooltip" title="export Excel sous Windows"> Latin-1</span><br>
			&nbsp;&nbsp;&nbsp;<input type="radio" name="charset" value="macintosh"><span id="t3" rel="tooltip" title="export Excel sous MacOS X"> MacOS-Roman</span>
			</div>
			<div class="span2"><input type="submit" name="submit_all" value="Load"></div>
		</form>
		</div>
		<?php if ($_SESSION["error"] && $_SESSION["error"] != "diagram") { echo "<div style='color:red'>".$error_message[$_SESSION["error"]]."</div>"; }?>
	</div>
</div><!-- End of container -->
<br>

<?php
	require($to_main_dir . 'footer.php');
	print_footer();
?>

<script>
	$("#t1, #t2, #t3").tooltip({"placement":"right"});
</script>

<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Automatic Sankey diagrams</h3>
  </div>
  <div class="modal-body">
    <p>Download the sample csv <b>Supply</b> file: <a href="<?php echo  $to_main_dir.'sources/'?>examples/example_supply_semicolon.csv">semicolon separator</a> OR <a href="<?php echo  $to_main_dir.'sources/'?>examples/example_supply.csv">comma separator</a>.</p>
    <p>Download the sample csv <b>Use</b> file: <a href="<?php echo  $to_main_dir.'sources/'?>examples/example_use_semicolon.csv">semicolon separator</a> OR <a href="<?php echo  $to_main_dir.'sources/'?>examples/example_use.csv">comma separator</a>.</p>
    <p>The diagram drawn automatically based on these two files (encoded in Latin-1) is the following:
    	<img src="<?php echo  $to_main_dir.'sources/'?>examples/1.png"></img>
    </p>	
	<p>It can be easily reorganized manually in order to obtain:
		<img src="<?php echo  $to_main_dir.'sources/'?>examples/2.png"></img>
	</p>
    <p>Finally, the "Aggregate flows" function makes it possible to obtain (after manual reorganization):
    	<img src="<?php echo  $to_main_dir.'sources/'?>examples/3.png"></img>
    </p>
    <p>COMPATIBLE FORMATS:<br>
    	- csv files with the first 4 row and colum headers identical to the sample files,<br>
    	- ',' or ';' separator,<br>
    	- UTF-8, Latin-1 (excel export in Windows), MacOS-Roman (excel export in MacOS X).
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>