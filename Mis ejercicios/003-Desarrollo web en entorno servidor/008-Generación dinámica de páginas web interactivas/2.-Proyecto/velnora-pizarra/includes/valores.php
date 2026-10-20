<?php
	// Conversión y validación de las marcas. No usa sesión ni base de datos,
	// así se puede probar directamente desde pruebas.php.

	/** 412 segundos -> "6:52"; las repeticiones se quedan como están. */
	function formatearValor($valor, $tipo) {
		if ($tipo === "tiempo") {
			return intdiv((int)$valor, 60).":".str_pad((string)($valor % 60), 2, "0", STR_PAD_LEFT);
		}
		return $valor." reps";
	}

	/**
	 * Convierte lo que escribe el socio en un número, o devuelve un texto de error.
	 * Tiempo: "m:ss" entre 0:30 y 59:59. Repeticiones: entero entre 1 y 999.
	 */
	function interpretarValor($texto, $tipo) {
		$texto = trim((string)$texto);
		if ($tipo === "tiempo") {
			if (!preg_match('/^(\d{1,2}):([0-5]\d)$/', $texto, $partes)) {
				return "Escribe el tiempo como minutos:segundos, por ejemplo 6:45";
			}
			$segundos = $partes[1] * 60 + $partes[2];
			if ($segundos < 30) {
				return "Menos de 30 segundos no es posible";
			}
			return $segundos;
		}
		if (!preg_match('/^\d{1,3}$/', $texto) || (int)$texto < 1) {
			return "Escribe las repeticiones, entre 1 y 999";
		}
		return (int)$texto;
	}
?>
