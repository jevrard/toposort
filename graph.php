<?php
require_once 'class/DAG.php';
$count = 0; // permet de numéroter les balises

/**
 * Etudie chaque graphe XML du dossier graphs
 */
function graph_process() {
  $graphDir = "graphs/";
  $graphPaths = preg_grep("/^.*\.xml$/", scandir($graphDir));
  global $count;
  foreach ($graphPaths as $xmlPath) {
    $count++;
    echo "\t<h3>~ Graphe <i>$xmlPath</i></h3>\n";
    writeFile($graphDir.$xmlPath);
    $dag = new DAG();
    try {
      $dag->createFromXml($graphDir.$xmlPath);
      writeGraph($dag);
      writeToposort($dag);
    } catch (Exception $e) {
      writeException($e);
    }
  }
}

/**
 * Etudie chaque fichier makefile du dossier makefiles
 */
function makefile_process() {
  $makefileDir = "makefiles/";
  $makefilePaths = preg_grep("/^makefile\d*$/", scandir($makefileDir));
  global $count;
  foreach ($makefilePaths as $makefilePath) {
    $count++;
    echo "\t<h3>~ Fichier <i>$makefilePath</i></h3>\n";
    writeFile($makefileDir.$makefilePath);
    $dag = new DAG();
    try {
      $dag->createFromMakefile($makefileDir.$makefilePath);
      writeGraph($dag);
      writeToposort($dag);
    } catch (Exception $e) {
      writeException($e);
    }
  }
}

/**
  * Affiche dans une balise le fichier source à traiter
  * @param string filePath
  */
function writeFile($filePath) {
  $out = file_get_contents($filePath);
  echo "\t<p class='file'>".preg_replace(["/</","/>/","/\n/"], ["&lt;","&gt;","<br/>\n"],$out)."</p>\n";
}

/**
  * Ecrit la balise dessinant le graphe $dag
  * @param DAG $dag
  */
function writeGraph(DAG $dag) {
  global $count;
  $json = json_encode($dag->toArray(), JSON_PRETTY_PRINT);
  if(!json_last_error()) {
    echo "\t<div id='chart$count'></div>";
    echo "\t<script>draw_graph($json,'#chart$count');</script>\n";
  }
}

/**
  * Ecrit la balise du tri topologique de $dag
  * @param DAG $dag
  */
function writeToposort(DAG $dag) {
  echo "\t<p class='toposort'><b>Tri topologique arbitraire associé au makefile : </b><i>";
  foreach ($dag->toposort() as $vertex) echo $vertex['name']." ; ";
  echo "</i></p>\n";
}

/**
  * Affiche dans une balise l'erreur survenue lors du traitement
  * @param Exception $e
  */
function writeException(Exception $e) {
  echo "\t<p class='error'>Exception catched : ".$e->getMessage()."</br>";
  echo "Le graphe ne peut donc pas être créé. Vérifier votre fichier !</p>\n";
}
?>
