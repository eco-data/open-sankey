<?php
function print_footer(){
	echo "
	<div id='copyright'>
			<div class='container'>
				<div class='row'>
					<div class='span4'>
						<p>
						<a href='https://github.com/eco-data/open-sankey' style='color:white'>Open Sankey - v1.1</a>
						</p>
					</div>
				</div>
			</div>
		</div>

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script type='text/javascript' src='" . $GLOBALS["to_main_dir"] . "sources/js/jquery-2.0.3.min.js'></script>
    <script type='text/javascript' src='" . $GLOBALS["to_main_dir"] . "sources/js/retina.js'></script>
    <script type='text/javascript' src='" . $GLOBALS["to_main_dir"] . "sources/js/bootstrap.min.js'></script>
    <script type='text/javascript' src='" . $GLOBALS["to_main_dir"] . "sources/js/theme.js'></script>
  </body>
</html>
	";
} // end of echo
?>