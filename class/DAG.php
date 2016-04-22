<?php
include_once 'Arrow.php';

/* Classe DAG : graphe acyclique orienté (Directed Acyclic Graph) */
class DAG {

	/** array $vertices : liste des sommets du graphe */
	private $vertices = array();
	/** array of Arrow $arrows : liste des arcs orientés du graphe */
	private $arrows = array();

	public function DAG() {
	}

	/**
	 * Remplie le graphe avec les valeurs du fichier XML
	 * @param string $xmlPath
	 */
	public function createFromXml($xmlPath) {
		if(!file_exists($xmlPath)) throw new Exception("Fichier xml non touvé !");
		$dom = new DomDocument('1.0', 'utf-8');
		$dom->load($xmlPath);
		if(!$dom->validate()) throw new Exception("Fichier xml non validé par le DTD !");

		$xpath = new DOMXpath($dom);
		$vertices = $xpath->query("//vertex");
		foreach($vertices as $vertex)
			$this->addVertex(new Vertex($vertex->getAttribute("name")));

		$arrows = $xpath->query("//arrow");
		foreach($arrows as $arrow)
			$this->addArrow(new Arrow(new Vertex($arrow->getAttribute("tail")), new Vertex($arrow->getAttribute("head"))));
	}

	/**
	 * Remplie le graphe avec les valeurs du fichier makefile
	 * @param string $makefilePath
	 */
	public function createFromMakefile($makefilePath) {
		if(!file_exists($makefilePath)) throw new Exception("Fichier makefile non touvé !");
		$file = fopen($makefilePath, "r");
		while (!feof($file)) {
			$line = fgets($file);
			if(!preg_match("/^.+:.*$/", $line) || preg_match("/^#/", $line)) continue; // continue car la ligne est une commande ou un commentaire

			/* Différents patterns pour $line
			 * [1] ab :
			 * [2] ab : cd ...
			 * [3] a%b :
			 * [4] a%b : (cd|c%d)+
			 */
			$line = preg_split("/:/",$line); // sépare la cible des dépendances
			foreach($line as $key => $value) $line[$key] = trim($value); // enlève les espaces inutiles

			$target = $line[0];
			if(strlen($target) == 0) throw new Exception("Erreur dans le fichier $makefilePath !"); // $target ne doit pas être vide
			$dependencies = array_filter(explode(" ", $line[1])); // éclate et garde seulement les dépendances non vides

			if(preg_match("/^.*%.*$/", $target)) { // traitement des cas [3] et [4]

				$pattern = "/^".preg_replace(["/\./","/%/"],["\.",".+"],$target)."$/"; // crée la regex de $target
				$targets = $this->getMatchPatternVertices($pattern); // récupère tout les sommets existants qui correspondent à la règle générique
				if(count($targets) == 0) throw new Exception("Erreur dans le fichier $makefilePath !"); // impossible de créer un sommet pour une règle générique
				if(count($dependencies) == 0)  continue; // on est dans le cas [3], les cibles sont déjà des sommets et il n'y a pas de dépendances

				foreach($targets as $vertex) { // relie chaque cible à ses dépendances
					$variablePart = $vertex->getVariablePart($target); // récupère la partie correspondante au '%'

					// ajoute une Arrow de chaque dépendance à $vertex (qui est une target)
					foreach($dependencies as $dep) {
						if(preg_match("/^.*%.*$/", $dep)) // si $dep contient une partie variable '%'
							$dep = preg_replace("/%/", $variablePart, $dep); // le '%' est remplacé par la partie variable de la règle
						$newVertex = new Vertex($dep);
						try {
							$this->addVertex($newVertex);
							$this->addArrow(new Arrow($newVertex, $vertex));
						} catch (Exception $e) {} // ne rien faire si le sommet existe déjà
					}
				}

			} else { // traitement des cas [1] et [2]

				try {
					$this->addVertex(new Vertex($target));
				} catch (Exception $e) {} // ne rien faire si le sommet existe déjà
				if(count($dependencies) != 0) { // on est dans le cas [2] sinon le cas [1]
					foreach($dependencies as $dep) {
						if(preg_match("/^.*%.*$/", $dep)) throw new Exception("Erreur dans le fichier $makefilePath !"); // cas impossible
						try {
							$this->addVertex(new Vertex($dep));
							$this->addArrow(new Arrow(new Vertex($dep), new Vertex($target)));
						} catch (Exception $e) {} // ne rien faire si le sommet existe déjà
					}
				}
			}

		}
		fclose($file);
	}

	/**
	 * Retourne les sommets qui correspondent au $pattern
	 * @param string $pattern
	 * @return array of Vertex
	 */
	private function getMatchPatternVertices($pattern) {
		$vertices = array();
		foreach ($this->vertices as $vertex)
			if($vertex->matchPattern($pattern)) $vertices[] = $vertex;
		return $vertices;
	}

	/**
	 * Réalise un tri topologique sur le graphe
	 * @return array of Vertex
	 */
	public function toposort() {
		$toposort = array();
		$dag = clone $this;
		while(!empty($dag->vertices))
			foreach($dag->getStartNodes() as $vertex) {
				$toposort[] = $vertex->toArray();
				$dag->removeVertex($vertex);
			}
		return $toposort;
	}

	/**
	 * Ajoute un sommet au graphe
	 * @param Vertex $vertex
	 * @return void | throw Exception
	 */
	public function addVertex(Vertex $vertex) {
		if(in_array($vertex, $this->vertices)) throw new Exception("Error : $vertex already is in the DAG !");
		$this->vertices[] = $vertex;
	}

	/**
	 * Ajoute une flêche au graphe
	 * @param Arrow $arrow
	 * @return void | throw Exception
	 */
	public function addArrow(Arrow $arrow) {
		if(!$arrow->verticesAreInArray($this->vertices)) throw new Exception("Error : $arrow can't be add in the DAG !");
		$this->arrows[] = $arrow;
	}

	/**
	 * Retire un sommet du graphe
	 * @param Vertex $vertex
	 * @return void | throw Exception
	 */
	public function removeVertex(Vertex $vertex) {
		if(($key = array_search($vertex, $this->vertices)) === false) throw new Exception("Error : $vertex is missing in the DAG !");
		unset($this->vertices[$key]);
		foreach($this->arrows as $arrow)
			if($arrow->isTail($vertex) || $arrow->isHead($vertex))
				$this->removeArrow($arrow);
	}

	/**
	 * Retire une flêche du graphe
	 * @param Arrow $arrow
	 * @return void | throw Exception
	 */
	public function removeArrow(Arrow $arrow) {
		if(($key = array_search($arrow, $this->arrows)) === false) throw new Exception("Error : $arrow is missing in the DAG !");
		unset($this->arrows[$key]);
	}

	/**
	 * @param Vertex $vertex
	 * @return int : nombre des degrés entrant
	 */
	public function indegrees(Vertex $vertex) {
		$count = 0;
		foreach($this->arrows as $arrow)
			if($arrow->isHead($vertex)) $count++;
		return $count;
	}

	/**
	 * Récupère les racines du graphe
	 * @return array of Vertex | throw Exception
	 */
	public function getStartNodes() {
		$startNodes = array();
		foreach($this->vertices as $vertex)
			if($this->indegrees($vertex) == 0) $startNodes[] = $vertex;
		if(empty($startNodes)) throw new Exception("Error : $this is cyclic !");
		return $startNodes;
	}

// namespace TriTopologique;
	/**
	 * Exporte l'objet en tableau
	 * @return array
	 */
	public function toArray() {
		$vertices = array();
		$arrows = array();
		foreach($this->vertices as $vertex) $vertices[] = $vertex->toArray();
		foreach($this->arrows as $arrow) $arrows[] = $arrow->toArray($this->vertices);
		return array(
			'vertices' => $vertices,
			'arrows' => $arrows
		);
	}

	public function __toString() {
		$string = "DAG(\n\t".count($this->vertices)." vertices\n";
		foreach($this->arrows as $arrow) $string .= "\t".$arrow."\n";
		$string .= ")\n";
		return $string;
	}
}

?>
