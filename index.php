<!--   PROJET À OUVRIR SUR UN SERVEUR POUR POUVOIR INTERPRÉTER LES SCRIPTS PHP   -->
<!-- Pour le confort de lisibilité, il est conseillé d'ouvrir ce fichier dans un navigateur internet -->
<!-- Il est recommandé d'avoir les droits 755 sur les dossiers et 644 sur les fichiers du projet pour éviter tout problème de lecture -->

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8"/>

  <title>Tri topologique</title>
  <link rel="stylesheet" type="text/css" href="style.css"/>

  <!-- import d3.js library -->
  <script src="lib/d3_v3.js" type="text/javascript"></script>
  <script src="graph.js" type="text/javascript"></script>
</head>

<body>
  <h1>Tri topologique d'un graphe acyclique orienté</h1>
  <h4><i>Justine Evrard - L3 Informatique - 2016</i></h4>

  <blockquote>
  <p>Ce projet de <b>fondements de mathématiques de l’informatique</b> est un programme permettant de fournir un tri topologique d’un graphe acyclique orienté. Celui-ci montre aussi comment cette méthode est appliquée à la commande <code>make</code>.</p>
  </blockquote>


  <h2># Description technique du programme</h2>
  <p>Ce projet a été écrit avec les pricipaux langages du web : PHP orienté objet, JavaScript et HTML/CSS. Le premier pour décrire la mécanique du programme, le second pour dessiner les graphes et les derniers pour écrire ce rapport et afficher les exemples.</p>

  <h3>~ Contenu</h3>
  <p>Ce projet contient les dossiers et fichiers suivants :</p>
  <ul>
    <li><b>class</b> :
      <ul>
        <li><i>Arrow.php</i></li>
        <li><i>DAG.php</i></li>
        <li><i>Vertex.php</i></li>
      </ul>
    </li>
    <li><b>graphs</b> :
      <ul>
        <li><i>graph.dtd</i></li>
        <li><i>graph1.xml</i></li>
        <li><i>graph2.xml</i></li>
        <li><i>graph3.xml</i></li>
        <li><i>graph4.xml</i></li>
        <li><i>graph5.xml</i></li>
      </ul>
    </li>
    <li><b>lib</b> :
      <ul>
        <li><i>d3_v3.js</i></li>
      </ul>
    </li>
    <li><b>makefiles</b> :
      <ul>
        <li><i>makefile1</i></li>
        <li><i>makefile2</i></li>
        <li><i>makefile3</i></li>
        <li><i>makefile4</i></li>
      </ul>
    </li>
    <li><i>graph.js</i></li>
    <li><i>graph.php</i></li>
    <li><i>index.php</i></li>
    <li><i>style.css</i></li>
  </ul>

  <h3>~ Explications</h3>
  <p>Le fichier <b>index.php</b> de cette présentation n'a pas grand intérêt à être ouvert en dehors d'un navigateur. En effet, l'objectif de ce fichier est de présenter graphiquement ce projet. Le traitement fait pour les exemples est importé depuis le fichier <b>graph.php</b> qui construit chaque exemple en créant des objets <code>DAG</code> et en appelant des fonction PHP et Javascript du programme. Chaque exemple est automatiquement créé pour chaque fichier contenu dans les dossiers <b>graphs</b> et <b>makefiles</b>. Ainsi, il suffit d'ajouter vos propres fichiers dans ces dossiers pour les traiter comme exemple. <i>Attention aux droits d'accès en lecture des ces fichiers !</i></p>
  <p>Les classes <code>Arrow</code>, <code>DAG</code> et <code>Vertex</code>, écrites en PHP, représentent respectivement une flêche, un graphe acyclique orienté et un sommet. Un objet <code>DAG</code> est ainsi composé d’un tableau de <code>Vertex</code> et d’un autre de <code>Arrow</code>.</p>
  <p>Le fichier <b>graph.js</b> contient une fonction <code>draw_graph(graph,divId)</code> qui dessine le graphe <code>graph</code> dans la division HTML comportant l’identifiant <code>divId</code>. Elle utilise la bibliothèque JavaScript <i><a href="https://d3js.org/">D3 Data-Driven Documents</a></i> qui correspond au fichier <b>d3_v3.js</b> et qui permet de manipuler graphiquement des données. Le graphe en paramètre doit être un objet JSON encodé depuis PHP à partir de la classe <code>DAG</code> (voir le fichier <b>graph.php</b>). Les graphes sont générés grâce à un layout qui place les sommets de façon aléatoire. Ainsi, certaines flêches risquent de se croiser. Mais ces sommets ne sont pas fixes et ils peuvent donc être déplacés afin de rendre les graphes plus lisibles.</p>

  <?php require_once 'graph.php'; ?>

  <h2># Exemples de traitement des graphes</h2>
  <p>Ici, les objets <code>DAG</code> sont créer à partir d'un fichier XML validé par le fichier <b>graph.dtd</b>.</p>

  <?php graph_process(); ?>

  <h2># Exemples de traitement des makefiles</h2>
  <p>Pour simplifier le processus, on admettra que les makefiles ne contiennent pas de variables, c'est-à-dire, par exemple, qu'ils ont déjà été <i>parsés</i> une fois pour remplacer tout appel à une variable par la valeur de celle-ci. Ainsi, ces fichiers sont tous de la forme suivante :<br/><code>target: dependencies ...<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;commands<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;...</code></br>Pour faire un parallèle avec un graphe orienté acyclique, chaque <code>target</code> est un sommet qui a pour prédécesseurs les sommets <code>dependencies</code>. De ce fait, le tri topologique de ce graphe correspond à l'ordre dans lequel il faut exécuter les commandes associées à chaque <code>target</code> (ces commandes ne sont pas enregistrées dans notre classe <code>DAG</code>).</p>
  <p>Ici, les objets <code>DAG</code> sont créer à partir d'un fichier makefile qui sera <i>parsé</i>. La fonction <code>createFromMakefile($makefilePath)</code> correspondante est assez lourde et peut ne pas être évidente à comprendre au premier coup d'oeil mais les explications sont présentes dans le code.</p>
  <?php makefile_process(); ?>

  <br/>
</body>
</html>
