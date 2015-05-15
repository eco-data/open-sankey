<?php
	session_start();
	unset($_SESSION['layout_path']);
	unset($_SESSION["access"]);
	unset($_SESSION["sankey_type"]);
	unset($_SESSION["use_path"]);
	unset($_SESSION["supply_path"]);
	unset($_SESSION["file_path"]);
	unset($_SESSION["charset"]);
	
	require("root.php");
	require($to_main_dir . "header.php");
	print_header("eco-data.fr","tools");
	$error_message = array(
		"diagram" => "Fichier invalide",
		"supply" => "Fichier Ressources invalide",
		"use" => "Fichier Emplois invalide",
		"charset" => "Encodage non défini"
	);
?>

	

	<div id="herowrap">

		<div class="container">

			<div class="span12">

				<h3>Outil Sankey</h3>

			</div><!-- /span12 -->

		</div><!-- /container -->

	</div><!-- /herowrap -->

<br>

<!-- Container -->
<div class="container">	
<a href="http://www.eco-data.fr/tools/sankey/manual_sankey.php">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tracez un nouveau diagramme manuellement</a>
	<br><hr>
	OU&nbsp;&nbsp;&nbsp;&nbsp;Chargez un diagramme précédemment sauvegardé :
	<form action="http://www.eco-data.fr/tools/sankey/upload_diagram.php" method="post" enctype="multipart/form-data">
		<div class="span2">Diagramme <input type="file" name="diagram_file"></div>
		<div class="span2">Incertitudes ressources <input type="file" name="uncertainties_file_supply" ></div>
		<div class="span2">Incertitudes emplois <input type="file" name="uncertainties_file_use" ></div>
		<div class="span1"></div>
		<input type="submit" name="submit" value="Charger">
	</form>
	<?php if ($_SESSION["error"] == "diagram") { echo "<div style='color:red'>".$error_message["diagram"]."</div>"; }?>
	<br>
	<hr>
	<div>
		OU&nbsp;&nbsp;&nbsp;&nbsp;Chargez les tables ressources et emplois correspondant au diagramme pour obtenir une première représentation automatiquement.<br><br>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#myModal" role="button" class="btn" data-toggle="modal">Afficher l'exemple et télécharger les fichiers associés.</a><br><br>
		<div class="row">
		<form action="http://www.eco-data.fr/tools/sankey/upload_supply_and_use_tables.php" method="post" enctype="multipart/form-data">
			<div class="span2">Ressources <input type="file" name="supply_file"></div>
			<div class="span2">Emplois <input type="file" name="use_file" ></div>
			<div class="span2">Layout <input type="file" name="layout_file" ></div>
			<div class="span2.5" ><span id="t1" rel="tooltip" title="pour le rendu des accents">Encodage :</span><br>
				&nbsp;&nbsp;&nbsp;<input type="radio" name="charset" value="utf-8"> UTF-8<br>
				&nbsp;&nbsp;&nbsp;<input type="radio" name="charset" value="iso-8859-1"><span id="t2" rel="tooltip" title="export Excel sous Windows"> Latin-1</span><br>
			&nbsp;&nbsp;&nbsp;<input type="radio" name="charset" value="macintosh"><span id="t3" rel="tooltip" title="export Excel sous MacOS X"> MacOS-Roman</span>
			</div>
			<div class="span2"><input type="submit" name="submit_all" value="Charger"></div>
		</form>
		</div>
		<?php if ($_SESSION["error"] && $_SESSION["error"] != "diagram") { echo "<div style='color:red'>".$error_message[$_SESSION["error"]]."</div>"; }?>
	</div>
</div><!-- End of container -->
<br>


<?php
	require($to_main_dir . "footer.php");
	print_footer();
?>

<script>
	$("#t1, #t2, #t3").tooltip({"placement":"right"});
</script>

<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Diagrammes de Sankey automatiques</h3>
  </div>
  <div class="modal-body">
    <p>Téléchargez l'exemple de fichier csv <b>Ressources (Supply)</b> : <a href="examples/example_supply_semicolon.csv">séparateur point-virgule</a> OU <a href="examples/example_supply.csv">séparateur virgule</a>.</p>
    <p>Télécharger l'exemple de fichier csv <b>Emplois (Use)</b> : <a href="examples/example_use_semicolon.csv">séparateur point-virgule</a> OU <a href="examples/example_use.csv">séparateur virgule</a>.</p>
    <p>Le diagramme tracé automatiquement à partir de ces deux fichiers (encodés en Latin-1) est le suivant :
    	<img src="examples/1.png"></img>
    </p>	
	<p>On peut facilement le réorganiser à la main pour obtenir :
		<img src="examples/2.png"></img>
	</p>
    <p>Enfin, la fonction "Agréger les flux" permet d'obtenir après un arrangement manuel :
    	<img src="examples/3.png"></img>
    </p>
    <p>FORMAT COMPATIBLE :<br>
    	- fichiers csv avec les mêmes colonnes que les fichiers de l'exemple,<br>
    	- séparateur ',' ou ';',<br>
    	- encodages UTF-8, Latin-1 (export excel sous windows), MacOS-Roman (export excel sous MacOS X).
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Fermer</button>
  </div>
</div>