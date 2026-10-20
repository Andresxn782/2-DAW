<?php
	/**
	 * Calcula el precio final de una cuota aplicando un descuento.
	 * Uso un comentario multilínea (docstring) para documentar la función:
	 * qué hace, qué recibe y qué devuelve.
	 *
	 * @param float $precio    precio de la cuota
	 * @param int   $descuento porcentaje de descuento
	 * @return float precio con el descuento aplicado
	 */
	function precioFinal($precio, $descuento){
		// Comentario de una línea: solo para aclarar una línea concreta
		return $precio - ($precio * $descuento / 100);
	}

	echo precioFinal(40, 15)." €";

	/*
		Resumen:
		- Multilínea: docstrings y explicar funciones o métodos.
		- Una línea: notas locales, solo donde de verdad aporten.
	*/
?>
