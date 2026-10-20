<?php
	// Conexión a la base de datos de la pizarra.
	// La primera vez crea las tablas y un WOD de ejemplo con algunos resultados.
	function conectar() {
		$ruta = getenv("VELNORA_PIZARRA_BD") ?: __DIR__."/../datos/pizarra.db";
		$nueva = !file_exists($ruta);
		$db = new PDO("sqlite:".$ruta);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		if ($nueva) {
			$db->exec("
				CREATE TABLE wods (
					id INTEGER PRIMARY KEY AUTOINCREMENT,
					titulo TEXT NOT NULL,
					descripcion TEXT NOT NULL,
					tipo TEXT NOT NULL CHECK (tipo IN ('tiempo', 'repeticiones')),
					monitor TEXT NOT NULL,
					creado TEXT NOT NULL DEFAULT (datetime('now', 'localtime'))
				);
				CREATE TABLE resultados (
					id INTEGER PRIMARY KEY AUTOINCREMENT,
					wod_id INTEGER NOT NULL REFERENCES wods(id),
					socio TEXT NOT NULL,
					valor INTEGER NOT NULL,
					escalado INTEGER NOT NULL DEFAULT 0,
					actualizado TEXT NOT NULL DEFAULT (datetime('now', 'localtime')),
					UNIQUE (wod_id, socio)
				);
				CREATE TABLE animos (
					id INTEGER PRIMARY KEY AUTOINCREMENT,
					wod_id INTEGER NOT NULL REFERENCES wods(id),
					socio TEXT NOT NULL,
					texto TEXT NOT NULL,
					fecha TEXT NOT NULL DEFAULT (datetime('now', 'localtime'))
				);
			");
			$db->exec("INSERT INTO wods (titulo, descripcion, tipo, monitor) VALUES
				('Fran a la Velnora', '21-15-9 de thrusters (40 kg) y dominadas. Lo antes posible.', 'tiempo', 'Andres Ruiz')");
			$db->exec("INSERT INTO resultados (wod_id, socio, valor, escalado) VALUES
				(1, 'Nerea', 412, 0), (1, 'Carlos', 365, 0), (1, 'Marcos', 298, 1)");
			$db->exec("INSERT INTO animos (wod_id, socio, texto) VALUES
				(1, 'Andres Ruiz', '¡Hoy toca sufrir! Recordad calentar hombros.'),
				(1, 'Nerea', 'Las dominadas se me han atragantado')");
		}
		return $db;
	}
?>
