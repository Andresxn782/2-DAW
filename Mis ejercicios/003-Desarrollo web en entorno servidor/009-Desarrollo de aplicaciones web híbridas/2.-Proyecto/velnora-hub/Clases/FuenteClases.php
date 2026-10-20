<?php
	/**
	 * Repositorio propio: el horario de clases en SQLite.
	 * Devuelve las clases de hoy que aún no han empezado y las de mañana.
	 */
	class FuenteClases implements Fuente {
		private PDO $db;
		private int $ahora;
		const DIAS = ["", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"];

		public function __construct(string $carpetaDatos, ?int $ahora = null) {
			$this->ahora = $ahora ?? time();
			$ruta = $carpetaDatos."/velnora.db";
			$nueva = !file_exists($ruta);
			$this->db = new PDO("sqlite:".$ruta);
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
			if ($nueva) {
				$this->crear();
			}
		}

		public function nombre(): string {
			return "clases";
		}

		private function crear(): void {
			$this->db->exec("CREATE TABLE clases (id INTEGER PRIMARY KEY AUTOINCREMENT, nombre TEXT, monitor TEXT, dia INTEGER, hora TEXT, sala TEXT, aire_libre INTEGER DEFAULT 0)");
			$clases = [
				["Running", "Andres Ruiz", "07:30", "Retiro", 1], ["Spinning", "Andres Ruiz", "18:00", "Sala 1", 0],
				["Yoga", "Marta Lozano", "19:00", "Sala 2", 0], ["Crossfit", "Andres Ruiz", "19:30", "Box", 0],
				["Pilates", "Marta Lozano", "10:00", "Sala 2", 0], ["Boxeo", "Iván Rey", "20:30", "Sala 1", 0]
			];
			$insertar = $this->db->prepare("INSERT INTO clases (nombre, monitor, dia, hora, sala, aire_libre) VALUES (?, ?, ?, ?, ?, ?)");
			// Cada día de la semana tiene cuatro de las seis clases, cambiando cuáles
			for ($dia = 1; $dia <= 7; $dia++) {
				for ($i = 0; $i < 4; $i++) {
					[$nombre, $monitor, $hora, $sala, $fuera] = $clases[($dia + $i) % count($clases)];
					$insertar->execute([$nombre, $monitor, $dia, $hora, $sala, $fuera]);
				}
			}
		}

		public function obtener(): array {
			$hoy = (int)date("N", $this->ahora);
			$manana = $hoy % 7 + 1;
			$consulta = $this->db->prepare("SELECT nombre, monitor, hora, sala, aire_libre FROM clases WHERE dia = ? ORDER BY hora");
			$resultado = [];
			foreach (["hoy" => $hoy, "manana" => $manana] as $clave => $dia) {
				$consulta->execute([$dia]);
				$lista = [];
				foreach ($consulta->fetchAll() as $clase) {
					// De hoy solo enseño las que no han empezado
					if ($clave === "hoy" && $clase['hora'] < date("H:i", $this->ahora)) {
						continue;
					}
					$clase['aire_libre'] = (bool)$clase['aire_libre'];
					$lista[] = $clase;
				}
				$resultado[$clave] = ["dia" => self::DIAS[$dia], "clases" => $lista];
			}
			return $resultado;
		}
	}
?>
