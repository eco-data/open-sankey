<?php

session_start();
$_SESSION['language'] = array('fr','FR');

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
					<div class="span3.5"><br><br><a href="#save_diagram_modal" role="button" class="btn" data-toggle="modal">Sauvegarder le diagramme</a></div>
					<div class="span1.5"><br><br><a href="#myModal" role="button" class="btn" data-toggle="modal">Tutoriel</a></div>
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
<div class="span2"><b>NOEUDS</b></div>
<div class="span0.5">Nom :</div>
<div class="span2.5"><textarea name="name" rows="1" cols="15" style="height:24px; width:150px; font-size:13px; padding-top:2px; padding-bottom:2px; padding-left:2px; padding-right:2px;"></textarea></div>
<div class="span4">Orientation:&nbsp;&nbsp;<input type="radio" name="orientation" value="vertical">&nbsp;Vertical</input>
&nbsp;<input type="radio" name="orientation" value="horizontal">&nbsp;Horizontal</input></div>
<div class="span0.5">Couleur :</div>
<div class="span2.5"><textarea name="color" rows="1" cols="8" style="height:24px; width:100px;font-size:13px; padding-top:2px; padding-bottom:2px; padding-left:2px; padding-right:2px;"></textarea></div>
</form>
</div>
<div class="offset2">
<button onclick="add_new_node()">Ajouter</button>
<button onclick="update_node()">Mettre à jour</button>
<button onclick="delete_node()">Supprimer</button>
</div>

<hr>

<form name="link_info" style="margin-bottom:0px">
<div class="row">
<div class="span2"><b>LIENS</b></div>
<div class="span0.5">Source (n°) :</div>
<div class="span1"><textarea name="source" rows="1" cols="2" style="height:24px; width:30px; font-size:13px; padding-top:2px; padding-bottom:2px; padding-left:2px; padding-right:2px;"></textarea></div>
<div class="span0.5">Cible (n°) :</div>
<div class="span1"><textarea name="target" rows="1" cols="2" style="height:24px; width:30px; font-size:13px; padding-top:2px; padding-bottom:2px; padding-left:2px; padding-right:2px;"></textarea></div>
<div class="span0.5">Valeur :</div>
<div class="span1"><textarea name="value" rows="1" cols="5" style="height:24px; width:60px; font-size:13px; padding-top:2px; padding-bottom:2px; padding-left:2px; padding-right:2px;"></textarea></div>
<div class="span0.5">Couleur :</div>
<div class="span1"><textarea name="color" rows="1" cols="8" style="height:24px; width:100px; font-size:13px; padding-top:2px; padding-bottom:2px; padding-left:2px; padding-right:2px;"></textarea></div>
</form>
</div>
<div class="offset2">
<button onclick="add_new_link()">Ajouter</button>
<button onclick="update_link()">Mettre à jour</button>
<button onclick="delete_link()">Supprimer</button>
</div>

<hr>

<div class="row">
<form name="scale_info" style="margin-bottom:0px">
<div class="span2"><b>REGLAGES</b></div>
<div class="span0.5">Echelle (valeur pour 100px):</div>
<div class="span1"><textarea name="scale" rows="1" cols="5" style="height:24px; width:60px; font-size:13px; padding-top:2px; padding-bottom:2px; padding-left:2px; padding-right:2px;">100</textarea></div>
</form>
<div class="span1.5"><button onclick="update_scale()">Mettre à jour</button></div>
<div class="span0.5">Filtre :</div> 
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
<script src="<?php 
	if (strpos($_SERVER['HTTP_USER_AGENT'],'Chrome')!==false) {echo $to_main_dir . 'sources/js_sankey/manual_sankey_chrome.js';} else {echo $to_main_dir . 'sources/js_sankey/manual_sankey.js';} ?>" type="text/javascript"></script>

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
    <h3 id="myModalLabel">Guide d'utilisation de l'outil Sankey</h3>
  </div>
  <div class="modal-body">
  	<p>Un diagramme de Sankey est constitué de noeuds reliés entre eux par des liens. Un lien a un unique noeud d'origine (source) et de destination (cible). Les noeuds peuvent être verticaux ou horizontaux :
  		<img src="<?php echo $to_main_dir . 'sources/examples/orientation.png' ?>"></img>
  	</p>
  	<p>Il est impossible de créer un lien entre deux noeuds horizontaux.</p>
    <p><b>Pour créer votre premier diagramme à la main :</b></p>
    <ul>
    	<li>Dans la partie NOEUDS, remplissez les champs "Nom", "Orientation" et "Couleur" et validez en appuyant sur "Ajouter". Le champ "Couleur" doit contenir une couleur html (par exemple "red", "steelblue" ou "#019abf"). Le noeud est créé dans la partie centrale. Vous pouvez le déplacer par glisser/déposer. Ce noeud possède l'identifiant "0" (visible en laissant la souris dessus pendant quelques secondes).</li>
    	<li>Créez un 2e noeud de la même façon. Ce noeud possède l'identifiant "1".</li>
    	<li>Dans la partie LIENS, remplissez le champ "Source" avec "0", "Cible" avec "1", "Valeur" avec un nombre et "Couleur" puis validez en cliquant sur "Ajouter".</li>
    	<li>Si les figures sont superposées, déplacez-les par glisser/déposer.</li>
    	<li>Eventuellement, ajustez l'échelle (plus le chiffre indiqué est grand plus les liens seront fins) et validez en appuyant sur "Mettre à jour".</li>
    </ul>
   <p><b>Autres fonctionnalités disponibles :</b></p> 
   <ul>
   		<li><b>Modifier un noeud ou un lien.</b> Cliquez sur le noeud ou le lien. Les informations relatives à l'objet apparaissent dans les champs. Vous pouvez les modifier puis valider en cliquant sur "Mettre à jour".</li>
    	<li><b>Supprimer un noeud ou un lien.</b> Cliquer sur le noeud ou le lien puis cliquez sur "supprimer".</li>
    	<li><b>Modifier la position des labels.</b> Maintenez la touche "alt" du clavier enfoncée et déplacez le label.</li>
    	<li><b>Aller à la ligne dans le nom d'un noeud.</b> Tapez "&lt;br&gt;" dans le champ "Nom" à l'emplacement du retour à la ligne.</li>
    	<li><b>Changer l'ordre des liens entrant ou sortant d'un noeud.</b> Placez-vous sur le lien que vous souhaitez déplacer et suffisamment proche du noeud concerné. Faites glisser le lien vers le haut ou vers le bas : l'ordre change lorsque vous dépassez la frontière avec le lien voisin. Pour faciliter l'opération quand les liens sont très fins, vous pouvez zoomer avec votre navigateur.</li>
    	<img src="<?php echo $to_main_dir . 'sources/examples/links_position.png' ?>"></img>
    	 <li><b>Translater horizontalement le centre d'un lien</b> (entre deux noeuds verticaux). Repérez le centre en passant la souris dessus puis glissez-le vers la gauche ou la droite.
    	<img src="<?php echo $to_main_dir . 'sources/examples/links_center.png' ?>"></img></li>
    	<li><b>Sauvegarder votre travail.</b> Cliquez sur le bouton "Sauvegarder le diagramme" et entrez un titre pour télécharger les informations relatives à votre diagramme. Conservez ce fichier et chargez-le à votre prochaine visite sur la page de l'outil Sankey via l'interface "Charger un diagramme précédemment sauvegardé".</li>
    </ul>
    <p>Un <b>flux de recyclage</b> est un lien entre deux noeuds verticaux, lorsque le noeud cible se situe avant le noeud source. La transformation en flux de recyclage se fait automatiquement dès que cette condition est satisfaite comme illustré par la figure ci-dessous :
    	<img src="<?php echo $to_main_dir . 'sources/examples/normal_to_recycling.png' ?>"></img>
    </p>
    <p>En utilisant les 3 poignées disponibles, il est possible de modifier la forme du flux de recyclage (les distances qui changeront sont affichées en bleu) :
    	<img src="<?php echo $to_main_dir . 'sources/examples/vert_handle.png' ?>"></img>
    	<img src="<?php echo $to_main_dir . 'sources/examples/left_horiz_handle.png' ?>"></img>
    	<img src="<?php echo $to_main_dir . 'sources/examples/right_horiz_handle.png' ?>"></img>
    </p>
    <p><b>Merci de citer le nom "Open Sankey" si vous utilisez l'outil dans une publication !</b></p>	
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Fermer</button>
  </div>
</div>

<!-- Modal -->
<div id="save_diagram_modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Choisissez un titre pour votre diagramme</h3>
  </div>
  <div class="modal-body">
  	<input id="diagram_title" type="textarea"></input>		
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Fermer</button>
    <button id="save_layout_button" type="button" class="btn btn-primary">Sauvegarder</button>
  </div>
</div>