<?php
	// Abre comunidad.db y, si todavía no existe, la crea con los datos de demostración
	require_once __DIR__."/003-datos de demostracion.php";

	function abrirComunidad() {
		$ruta = __DIR__."/comunidad.db";
		if (!file_exists($ruta)) {
			crearComunidad($ruta);
		}
		$db = new PDO("sqlite:".$ruta);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		$db->exec("PRAGMA foreign_keys = ON");
		return $db;
	}

	// "hace 5 minutos", "hace 3 días"...
	function haceCuanto($fecha) {
		$segundos = time() - strtotime($fecha);
		if ($segundos < 60) {
			return "ahora mismo";
		}
		$unidades = [86400 => "día", 3600 => "hora", 60 => "minuto"];
		foreach ($unidades as $tamano => $nombre) {
			if ($segundos >= $tamano) {
				$cantidad = intdiv($segundos, $tamano);
				return "hace ".$cantidad." ".$nombre.($cantidad > 1 ? "s" : "");
			}
		}
	}
?>
