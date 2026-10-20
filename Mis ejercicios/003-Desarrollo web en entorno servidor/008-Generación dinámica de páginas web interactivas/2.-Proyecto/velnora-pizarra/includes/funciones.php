<?php
	// Funciones comunes de la página y de la API.
	session_start();
	require_once __DIR__."/bd.php";
	require_once __DIR__."/valores.php";

	// Clave que tienen que saber los monitores para entrar como tales
	const PIN_MONITOR = "velnora";

	function usuario() {
		return $_SESSION['usuario'] ?? null;
	}

	function esMonitor() {
		return (usuario()['rol'] ?? "") === "monitor";
	}

	function token() {
		if (empty($_SESSION['token'])) {
			$_SESSION['token'] = bin2hex(random_bytes(16));
		}
		return $_SESSION['token'];
	}

	function e($texto) {
		return htmlspecialchars((string)$texto);
	}

	/** El WOD activo es el último que ha publicado un monitor. */
	function wodActual(PDO $db) {
		return $db->query("SELECT * FROM wods ORDER BY id DESC LIMIT 1")->fetch() ?: null;
	}

	/**
	 * Clasificación del WOD: primero los que lo hicieron completo (RX)
	 * y después los escalados. Tiempo: menos es mejor. Repeticiones: más es mejor.
	 */
	function ranking(PDO $db, array $wod) {
		$orden = $wod['tipo'] === "tiempo" ? "ASC" : "DESC";
		$consulta = $db->prepare("SELECT socio, valor, escalado, actualizado FROM resultados
			WHERE wod_id = ? ORDER BY escalado ASC, valor ".$orden.", actualizado ASC");
		$consulta->execute([$wod['id']]);
		$filas = [];
		$posicion = 0;
		foreach ($consulta->fetchAll() as $fila) {
			$posicion++;
			$filas[] = [
				"posicion" => $posicion,
				"socio" => $fila['socio'],
				"valor" => (int)$fila['valor'],
				"texto" => formatearValor((int)$fila['valor'], $wod['tipo']),
				"escalado" => (bool)$fila['escalado'],
				"actualizado" => $fila['actualizado']
			];
		}
		return $filas;
	}

	function animos(PDO $db, array $wod, int $desde = 0) {
		$consulta = $db->prepare("SELECT id, socio, texto, substr(fecha, 12, 5) AS hora FROM animos
			WHERE wod_id = ? AND id > ? ORDER BY id LIMIT 200");
		$consulta->execute([$wod['id'], $desde]);
		$animos = $consulta->fetchAll();
		foreach ($animos as $i => $animo) {
			$animos[$i]['id'] = (int)$animo['id'];
		}
		return $animos;
	}
?>
