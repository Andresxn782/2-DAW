<?php
	/**
	 * Lógica de la API: clases, reservas y socios.
	 * No sabe nada de HTTP; devuelve arrays y lanza ErrorApi
	 * con el código de estado que corresponde cuando algo va mal.
	 */
	class ErrorApi extends Exception {
		public array $errores;

		public function __construct(int $codigo, string $mensaje, array $errores = []) {
			parent::__construct($mensaje, $codigo);
			$this->errores = $errores;
		}
	}

	class ServicioClases {
		private PDO $pdo;
		private array $dias;

		public function __construct(PDO $pdo, array $dias) {
			$this->pdo = $pdo;
			$this->dias = $dias;
		}

		// Consulta común: cada clase con sus plazas ocupadas y libres
		private function consultaClases(string $donde = ""): string {
			return "SELECT c.id, c.nombre, c.monitor, c.dia, c.hora, c.duracion, c.aforo,
					COUNT(r.id) AS ocupadas, c.aforo - COUNT(r.id) AS libres
				FROM clases c LEFT JOIN reservas r ON r.clase_id = c.id
				".$donde."
				GROUP BY c.id";
		}

		/** Lista de clases, opcionalmente filtrada por día o monitor. */
		public function listarClases(array $filtros): array {
			$condiciones = [];
			$valores = [];
			if (!empty($filtros['dia'])) {
				$condiciones[] = "c.dia = :dia";
				$valores[':dia'] = $filtros['dia'];
			}
			if (!empty($filtros['monitor'])) {
				$condiciones[] = "c.monitor LIKE :monitor";
				$valores[':monitor'] = "%".$filtros['monitor']."%";
			}
			$donde = count($condiciones) > 0 ? "WHERE ".implode(" AND ", $condiciones) : "";
			$consulta = $this->pdo->prepare($this->consultaClases($donde));
			$consulta->execute($valores);
			$clases = array_map([$this, "tipos"], $consulta->fetchAll());

			// Ordeno por día de la semana y hora, no por orden alfabético
			$orden = array_flip($this->dias);
			usort($clases, function ($a, $b) use ($orden) {
				return [$orden[$a['dia']] ?? 9, $a['hora']] <=> [$orden[$b['dia']] ?? 9, $b['hora']];
			});
			return $clases;
		}

		public function obtenerClase(int $id): array {
			$consulta = $this->pdo->prepare($this->consultaClases("WHERE c.id = :id"));
			$consulta->execute([':id' => $id]);
			$clase = $consulta->fetch();
			if ($clase === false) {
				throw new ErrorApi(404, "No existe la clase ".$id);
			}
			return $this->tipos($clase);
		}

		/** Comprueba los datos de una clase. Con $parcial solo mira los campos que vengan (PUT). */
		private function validarClase(array $datos, bool $parcial): array {
			$limpios = [];
			$errores = [];
			$campos = ["nombre", "monitor", "dia", "hora", "duracion", "aforo"];
			foreach ($campos as $campo) {
				if (!array_key_exists($campo, $datos)) {
					if (!$parcial) {
						$errores[$campo] = "Es obligatorio";
					}
					continue;
				}
				$valor = is_string($datos[$campo]) ? trim($datos[$campo]) : $datos[$campo];
				if ($campo === "nombre" || $campo === "monitor") {
					if (!is_string($valor) || $valor === "" || mb_strlen($valor) > 40) {
						$errores[$campo] = "Debe tener entre 1 y 40 caracteres";
					}
				} else if ($campo === "dia") {
					if (!in_array($valor, $this->dias, true)) {
						$errores[$campo] = "Debe ser un día de la semana: ".implode(", ", $this->dias);
					}
				} else if ($campo === "hora") {
					if (!is_string($valor) || !preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]$/', $valor)) {
						$errores[$campo] = "Formato HH:MM";
					}
				} else if ($campo === "duracion") {
					if (filter_var($valor, FILTER_VALIDATE_INT, ["options" => ["min_range" => 15, "max_range" => 180]]) === false) {
						$errores[$campo] = "Entre 15 y 180 minutos";
					}
					$valor = (int)$valor;
				} else if ($campo === "aforo") {
					if (filter_var($valor, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1, "max_range" => 60]]) === false) {
						$errores[$campo] = "Entre 1 y 60 plazas";
					}
					$valor = (int)$valor;
				}
				$limpios[$campo] = $valor;
			}
			if (count($errores) > 0) {
				throw new ErrorApi(422, "Los datos de la clase no son válidos", $errores);
			}
			if ($parcial && count($limpios) === 0) {
				throw new ErrorApi(400, "No se ha enviado ningún campo para modificar");
			}
			return $limpios;
		}

		public function crearClase(array $datos): array {
			$clase = $this->validarClase($datos, false);
			$consulta = $this->pdo->prepare("INSERT INTO clases (nombre, monitor, dia, hora, duracion, aforo)
				VALUES (:nombre, :monitor, :dia, :hora, :duracion, :aforo)");
			$consulta->execute($clase);
			return $this->obtenerClase((int)$this->pdo->lastInsertId());
		}

		public function modificarClase(int $id, array $datos): array {
			$actual = $this->obtenerClase($id);
			$cambios = $this->validarClase($datos, true);
			if (isset($cambios['aforo']) && $cambios['aforo'] < $actual['ocupadas']) {
				throw new ErrorApi(409, "La clase ya tiene ".$actual['ocupadas']." reservas; el aforo no puede ser menor");
			}
			$partes = [];
			foreach (array_keys($cambios) as $campo) {
				$partes[] = $campo." = :".$campo;
			}
			$cambios['id'] = $id;
			$consulta = $this->pdo->prepare("UPDATE clases SET ".implode(", ", $partes)." WHERE id = :id");
			$consulta->execute($cambios);
			return $this->obtenerClase($id);
		}

		/** Borra la clase y sus reservas en una transacción. */
		public function borrarClase(int $id): array {
			$clase = $this->obtenerClase($id);
			$this->pdo->beginTransaction();
			try {
				$this->pdo->prepare("DELETE FROM reservas WHERE clase_id = ?")->execute([$id]);
				$this->pdo->prepare("DELETE FROM clases WHERE id = ?")->execute([$id]);
				$this->pdo->commit();
			} catch (Exception $error) {
				$this->pdo->rollBack();
				throw $error;
			}
			return ["borrada" => $clase['nombre'], "reservas_anuladas" => $clase['ocupadas']];
		}

		public function reservasDeClase(int $id): array {
			$clase = $this->obtenerClase($id);
			$consulta = $this->pdo->prepare("SELECT r.id, r.socio_id, s.nombre AS socio, r.creada
				FROM reservas r JOIN socios s ON s.id = r.socio_id
				WHERE r.clase_id = ? ORDER BY r.id");
			$consulta->execute([$id]);
			$reservas = [];
			foreach ($consulta->fetchAll() as $fila) {
				$fila['id'] = (int)$fila['id'];
				$fila['socio_id'] = (int)$fila['socio_id'];
				$reservas[] = $fila;
			}
			return ["clase" => $clase, "reservas" => $reservas];
		}

		/** Reserva una plaza. 409 si está llena o si el socio ya está apuntado. */
		public function reservar(int $claseId, array $datos): array {
			$socioId = filter_var($datos['socio_id'] ?? null, FILTER_VALIDATE_INT);
			if ($socioId === false) {
				throw new ErrorApi(422, "Falta socio_id o no es un número", ["socio_id" => "Es obligatorio"]);
			}
			$clase = $this->obtenerClase($claseId);
			$this->obtenerSocio($socioId);

			// La comprobación y la inserción van juntas para que dos reservas
			// a la vez no se queden con la última plaza
			$this->pdo->beginTransaction();
			try {
				$ocupadas = $this->pdo->prepare("SELECT COUNT(*) FROM reservas WHERE clase_id = ?");
				$ocupadas->execute([$claseId]);
				if ((int)$ocupadas->fetchColumn() >= $clase['aforo']) {
					throw new ErrorApi(409, "La clase ".$clase['nombre']." está completa");
				}
				$repetida = $this->pdo->prepare("SELECT COUNT(*) FROM reservas WHERE clase_id = ? AND socio_id = ?");
				$repetida->execute([$claseId, $socioId]);
				if ((int)$repetida->fetchColumn() > 0) {
					throw new ErrorApi(409, "Ese socio ya tiene plaza en esta clase");
				}
				$this->pdo->prepare("INSERT INTO reservas (clase_id, socio_id) VALUES (?, ?)")->execute([$claseId, $socioId]);
				$id = (int)$this->pdo->lastInsertId();
				$this->pdo->commit();
			} catch (Exception $error) {
				$this->pdo->rollBack();
				throw $error;
			}
			return ["id" => $id, "clase" => $this->obtenerClase($claseId)];
		}

		public function cancelarReserva(int $id): array {
			$consulta = $this->pdo->prepare("DELETE FROM reservas WHERE id = ?");
			$consulta->execute([$id]);
			if ($consulta->rowCount() === 0) {
				throw new ErrorApi(404, "No existe la reserva ".$id);
			}
			return ["cancelada" => $id];
		}

		public function listarSocios(): array {
			$socios = $this->pdo->query("SELECT id, nombre, email FROM socios ORDER BY nombre")->fetchAll();
			foreach ($socios as &$socio) {
				$socio['id'] = (int)$socio['id'];
			}
			return $socios;
		}

		private function obtenerSocio(int $id): array {
			$consulta = $this->pdo->prepare("SELECT id, nombre, email FROM socios WHERE id = ?");
			$consulta->execute([$id]);
			$socio = $consulta->fetch();
			if ($socio === false) {
				throw new ErrorApi(404, "No existe el socio ".$id);
			}
			return $socio;
		}

		// SQLite devuelve los números como texto; los paso a enteros para el JSON
		private function tipos(array $clase): array {
			foreach (["id", "duracion", "aforo", "ocupadas", "libres"] as $campo) {
				$clase[$campo] = (int)$clase[$campo];
			}
			return $clase;
		}
	}
?>
