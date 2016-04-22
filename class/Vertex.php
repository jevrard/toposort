<?php

/* Classe Vertex : sommet d'un graphe */
class Vertex {

	private $name;

	public function Vertex($name) {
		$this->name = (string) $name;
	}

	/**
	 * Compare le sommet avec un autre
	 * @param Vertex $vertex
	 * @return bool
	 */
	public function equals(Vertex $vertex) {
		return $this->name == $vertex->name;
	}

	/**
	 * Compare le nom du sommet avec le pattern
	 * @param string $pattern
	 * @return bool
	 */
	public function matchPattern($pattern) {
		return preg_match($pattern,$this->name);
	}

	/**
	 * Récupère la partie variable de $name correspondant à la règle générique
	 * @param string $string : règle générique d'un makefile
	 * @param string $pattern : pattern de la règle
	 * @return string : partie variable de la règle générique
	 */
	public function getVariablePart($pattern) {
		if(!preg_match("/^.*%.*$/", $pattern)) return "";
		$pattern = explode("%", $pattern);
		$pattern[0] = "/^".$pattern[0]."/";
		$pattern[1] = "/".$pattern[1]."$/";
		return preg_replace($pattern, "", $this->name);
	}

	/**
	 * Exporte l'objet en tableau
	 * @return array
	 */
	public function toArray() {
		return array(
			'name' => $this->name
		);
	}

	public function __toString() {
		return "vertex($this->name)";
	}
}

?>
