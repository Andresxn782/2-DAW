<?php
	/**
	 * Una sesión de entrenamiento de un socio.
	 * PDO puede crear objetos de esta clase directamente con FETCH_CLASS.
	 */
	class Entreno {

		public const TIPOS = ["Fuerza", "Cardio", "Clase dirigida", "Natación", "Movilidad"];

		public $id = null;
		public $socio = "";
		public $fecha = "";
		public $tipo = "";
		public $minutos = 0;
		public $intensidad = 3;
		public $notas = "";

		/** Crea un entreno a partir de un array (formulario, CSV o JSON). */
		public static function desdeArray(array $datos): Entreno {
			$e = new Entreno();
			$e->id = isset($datos['id']) && $datos['id'] !== '' ? (int)$datos['id'] : null;
			$e->socio = trim((string)($datos['socio'] ?? ''));
			$e->fecha = trim((string)($datos['fecha'] ?? ''));
			$e->tipo = trim((string)($datos['tipo'] ?? ''));
			$e->minutos = (int)($datos['minutos'] ?? 0);
			$e->intensidad = (int)($datos['intensidad'] ?? 3);
			$e->notas = trim((string)($datos['notas'] ?? ''));
			return $e;
		}

		/** @return array campo => mensaje de error. Vacío si es correcto. */
		public function validar(): array {
			$errores = [];
			if($this->socio === '' || mb_strlen($this->socio) > 40){
				$errores['socio'] = "Escribe el nombre del socio (máximo 40 caracteres).";
			}
			$fecha = DateTime::createFromFormat('Y-m-d', $this->fecha);
			if(!$fecha || $fecha->format('Y-m-d') !== $this->fecha){
				$errores['fecha'] = "La fecha debe tener el formato AAAA-MM-DD.";
			}elseif($this->fecha > date('Y-m-d')){
				$errores['fecha'] = "No se puede apuntar un entreno en el futuro.";
			}
			if(!in_array($this->tipo, self::TIPOS, true)){
				$errores['tipo'] = "Elige un tipo de entreno.";
			}
			if($this->minutos < 5 || $this->minutos > 300){
				$errores['minutos'] = "La duración va de 5 a 300 minutos.";
			}
			if($this->intensidad < 1 || $this->intensidad > 5){
				$errores['intensidad'] = "La intensidad va de 1 a 5.";
			}
			return $errores;
		}

		/** Calorías aproximadas: minutos × factor del tipo × intensidad. */
		public function calorias(): int {
			$factor = ["Fuerza" => 1.6, "Cardio" => 2.2, "Clase dirigida" => 2.0, "Natación" => 2.4, "Movilidad" => 0.9];
			return (int)round($this->minutos * ($factor[$this->tipo] ?? 1.5) * $this->intensidad);
		}

		/** @return array Los datos para guardar (sin el id). */
		public function aArray(): array {
			return [
				'socio' => $this->socio,
				'fecha' => $this->fecha,
				'tipo' => $this->tipo,
				'minutos' => (int)$this->minutos,
				'intensidad' => (int)$this->intensidad,
				'notas' => $this->notas
			];
		}
	}
?>
