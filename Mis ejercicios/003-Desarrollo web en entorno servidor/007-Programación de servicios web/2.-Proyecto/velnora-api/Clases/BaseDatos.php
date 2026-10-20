<?php
	/**
	 * Abre la base de datos SQLite y, si está vacía, crea las tablas
	 * y mete unos datos de ejemplo para poder probar la API.
	 */
	class BaseDatos {
		public static function conectar(string $ruta): PDO {
			$carpeta = dirname($ruta);
			if (!is_dir($carpeta)) {
				mkdir($carpeta, 0777, true);
			}
			$pdo = new PDO("sqlite:".$ruta);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
			$pdo->exec("PRAGMA foreign_keys = ON");

			$existe = $pdo->query("SELECT COUNT(*) FROM sqlite_master WHERE name = 'clases'")->fetchColumn();
			if ((int)$existe === 0) {
				self::crearTablas($pdo);
				self::datosDeEjemplo($pdo);
			}
			return $pdo;
		}

		private static function crearTablas(PDO $pdo): void {
			$pdo->exec("
				CREATE TABLE socios (
					id INTEGER PRIMARY KEY AUTOINCREMENT,
					nombre TEXT NOT NULL,
					email TEXT NOT NULL UNIQUE
				);
				CREATE TABLE clases (
					id INTEGER PRIMARY KEY AUTOINCREMENT,
					nombre TEXT NOT NULL,
					monitor TEXT NOT NULL,
					dia TEXT NOT NULL,
					hora TEXT NOT NULL,
					duracion INTEGER NOT NULL,
					aforo INTEGER NOT NULL
				);
				CREATE TABLE reservas (
					id INTEGER PRIMARY KEY AUTOINCREMENT,
					clase_id INTEGER NOT NULL REFERENCES clases(id) ON DELETE CASCADE,
					socio_id INTEGER NOT NULL REFERENCES socios(id),
					creada TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
					UNIQUE (clase_id, socio_id)
				);
			");
		}

		private static function datosDeEjemplo(PDO $pdo): void {
			$socios = [
				["Lucía Martín", "lucia.martin@velnora.es"],
				["Carlos Pérez", "carlos.perez@velnora.es"],
				["Nerea Gil", "nerea.gil@velnora.es"],
				["Marcos Ibáñez", "marcos.ibanez@velnora.es"],
				["Sofía Navarro", "sofia.navarro@velnora.es"],
				["Hugo Ferrer", "hugo.ferrer@velnora.es"]
			];
			$consulta = $pdo->prepare("INSERT INTO socios (nombre, email) VALUES (?, ?)");
			foreach ($socios as $socio) {
				$consulta->execute($socio);
			}

			$clases = [
				["Spinning", "Andres Ruiz", "Lunes", "18:00", 45, 20],
				["Crossfit", "Andres Ruiz", "Martes", "19:30", 60, 12],
				["Yoga", "Marta Lozano", "Miércoles", "09:00", 60, 15],
				["Pilates", "Marta Lozano", "Jueves", "10:00", 50, 3],
				["Boxeo", "Iván Rey", "Viernes", "20:00", 60, 10],
				["Running", "Andres Ruiz", "Sábado", "08:30", 75, 25]
			];
			$consulta = $pdo->prepare("INSERT INTO clases (nombre, monitor, dia, hora, duracion, aforo) VALUES (?, ?, ?, ?, ?, ?)");
			foreach ($clases as $clase) {
				$consulta->execute($clase);
			}

			// Unas reservas para que se vean plazas ocupadas (Pilates casi llena)
			$reservas = [[1, 1], [1, 2], [2, 3], [4, 1], [4, 4], [6, 5]];
			$consulta = $pdo->prepare("INSERT INTO reservas (clase_id, socio_id) VALUES (?, ?)");
			foreach ($reservas as $reserva) {
				$consulta->execute($reserva);
			}
		}
	}
?>
