<?php
include_once 'Vertex.php';

/* Classe Arrow : arc orienté d'un sommet x vers un sommet y d'un graphe */
class Arrow {

	private $tail;
	private $head;

	public function Arrow(Vertex $x, Vertex $y) {
		$this->tail = $x;
		$this->head = $y;
	}

	/**
	 * Test si un sommet est la queue de la flêche
	 * @param Vertex $vertex
	 * @return bool
	 */
	public function isTail(Vertex $vertex) {
		return $this->tail->equals($vertex);
	}

	/**
	 * Test si un sommet est la tête de la flêche
	 * @param Vertex $vertex
	 * @return bool
	 */
	public function isHead(Vertex $vertex) {
		return $this->head->equals($vertex);
	}

	/**
	 * Test si les deux sommets sont contenus dans le tableau
	 * @param array of Vertex $vertex
	 * @return bool
	 */
	public function verticesAreInArray($vertexArray) {
		return in_array($this->tail, $vertexArray) && in_array($this->head, $vertexArray);
	}

	/**
	 * Exporte l'objet en tableau ; le tableau retour contiendra les indices de $tail et $head dans $vertexArray
	 * Cette fonction permettra de pouvoir encoder proprement le DAG en JSON pour l'affichage en JS
	 * @param array of Vertex $vertex
	 * @return array | throw
	 */
	public function toArray($vertexArray) {
		if(($tailKey = array_search($this->tail, $vertexArray)) === false) throw new Exception("Error : $this->tail is missing in the array !");
		if(($headKey = array_search($this->head, $vertexArray)) === false) throw new Exception("Error : $this->head is missing in the array !");
		return array(
			'tail' => $tailKey,
			'head' => $headKey,
		);
	}

	public function __toString() {
		return "arrow($this->tail,$this->head)";
	}
}

?>
