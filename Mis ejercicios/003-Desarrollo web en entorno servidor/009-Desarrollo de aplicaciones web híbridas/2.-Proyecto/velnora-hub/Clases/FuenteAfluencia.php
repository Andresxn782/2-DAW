<?php
	/**
	 * Datos masivos: los accesos del torno de las últimas cuatro semanas (CSV).
	 * Lee el archivo fila a fila con un generador y saca la afluencia media
	 * de cada hora para el día de la semana de hoy.
	 */
	class FuenteAfluencia implements Fuente {
		private string $archivo;
		private int $ahora;

		public function __construct(string $carpetaDatos, ?int $ahora = null) {
			$this->archivo = $carpetaDatos."/accesos.csv";
			$this->ahora = $ahora ?? time();
			if (!file_exists($this->archivo)) {
				self::generar($this->archivo, $this->ahora);
			}
		}

		public function nombre(): string {
			return "afluencia";
		}

		/** Crea un CSV de ejemplo con 28 días de accesos hasta ayer. */
		public static function generar(string $archivo, int $hasta): void {
			mt_srand(28);
			$pesos = [6 => 3, 7 => 9, 8 => 10, 9 => 6, 10 => 4, 11 => 3, 12 => 3, 13 => 4, 14 => 3, 15 => 3, 16 => 4, 17 => 7, 18 => 11, 19 => 12, 20 => 10, 21 => 5, 22 => 2];
			$horas = [];
			foreach ($pesos as $hora => $peso) {
				$horas = array_merge($horas, array_fill(0, $peso, $hora));
			}
			$puntero = fopen($archivo, "w");
			fwrite($puntero, "fecha;hora;minutos;socio\n");
			for ($d = 28; $d >= 1; $d--) {
				$dia = strtotime("-".$d." days", $hasta);
				$visitas = (int)(900 * [1 => 1.1, 1.05, 1, 1, 0.85, 0.6, 0.45][(int)date("N", $dia)] * mt_rand(90, 110) / 100);
				for ($v = 0; $v < $visitas; $v++) {
					fwrite($puntero, date("Y-m-d", $dia).";".sprintf("%02d:%02d", $horas[mt_rand(0, count($horas) - 1)], mt_rand(0, 59)).";".mt_rand(30, 110).";".mt_rand(1, 1500)."\n");
				}
			}
			fclose($puntero);
		}

		private function filas(): Generator {
			$puntero = fopen($this->archivo, "r");
			$cabecera = fgetcsv($puntero, 0, ";", '"', "");
			while (($fila = fgetcsv($puntero, 0, ";", '"', "")) !== false) {
				if (count($fila) === count($cabecera)) {
					yield array_combine($cabecera, $fila);
				}
			}
			fclose($puntero);
		}

		public function obtener(): array {
			$diaSemana = (int)date("N", $this->ahora);
			$porHora = array_fill(6, 17, 0);
			$fechas = [];
			$total = 0;
			foreach ($this->filas() as $fila) {
				$total++;
				if ((int)date("N", strtotime($fila['fecha'])) !== $diaSemana) {
					continue;
				}
				$fechas[$fila['fecha']] = true;
				$hora = (int)substr($fila['hora'], 0, 2);
				if (isset($porHora[$hora])) {
					$porHora[$hora]++;
				}
			}
			$semanas = max(1, count($fechas));
			$medias = [];
			foreach ($porHora as $hora => $visitas) {
				$medias[$hora] = round($visitas / $semanas, 1);
			}

			// Comparo la hora actual con la más llena del día
			$horaActual = (int)date("G", $this->ahora);
			$maximo = max($medias);
			$ahora = $medias[$horaActual] ?? 0;
			$nivel = "cerrado";
			if (isset($medias[$horaActual])) {
				$proporcion = $ahora / $maximo;
				$nivel = $proporcion > 0.75 ? "lleno" : ($proporcion > 0.4 ? "normal" : "tranquilo");
			}
			// Mejor hora que queda hoy (la más tranquila de 9 a 21 a partir de ahora)
			$mejor = null;
			foreach ($medias as $hora => $media) {
				if ($hora > $horaActual && $hora >= 9 && $hora <= 21 && ($mejor === null || $media < $medias[$mejor])) {
					$mejor = $hora;
				}
			}

			return [
				"dia" => FuenteClases::DIAS[$diaSemana],
				"registros_leidos" => $total,
				"semanas" => $semanas,
				"horas" => array_keys($medias),
				"medias" => array_values($medias),
				"hora_actual" => $horaActual,
				"ahora" => $nivel,
				"mejor_hora_restante" => $mejor
			];
		}
	}
?>
