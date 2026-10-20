<?php
	/**
	 * Analiza el CSV de accesos del torno.
	 * Lee el archivo fila a fila (generador) y calcula todos los indicadores en una pasada.
	 */
	class AnalizadorAccesos {
		private string $archivo;
		private string $carpetaCache;

		public function __construct(string $archivo, ?string $carpetaCache = null) {
			$this->archivo = $archivo;
			$this->carpetaCache = $carpetaCache ?? __DIR__."/cache";
		}

		/**
		 * Devuelve las filas una a una como arrays asociativos.
		 * Con yield nunca hay más de una fila en memoria.
		 */
		public function filas(): Generator {
			$puntero = fopen($this->archivo, "r");
			if ($puntero === false) {
				throw new RuntimeException("No puedo abrir ".$this->archivo);
			}
			$cabecera = fgetcsv($puntero, 0, ";", '"', "");
			while (($fila = fgetcsv($puntero, 0, ";", '"', "")) !== false) {
				if (count($fila) !== count($cabecera)) {
					continue;	// fila rota: la salto
				}
				yield array_combine($cabecera, $fila);
			}
			fclose($puntero);
		}

		/** Indicadores, usando la caché si el CSV no ha cambiado. */
		public function indicadores(): array {
			$firma = md5($this->archivo.filemtime($this->archivo).filesize($this->archivo));
			$cache = $this->carpetaCache."/indicadores-".$firma.".json";
			if (file_exists($cache)) {
				$datos = json_decode(file_get_contents($cache), true);
				$datos['desde_cache'] = true;
				return $datos;
			}
			$inicio = microtime(true);
			$datos = $this->calcular();
			$datos['segundos_calculo'] = round(microtime(true) - $inicio, 2);
			if (!is_dir($this->carpetaCache)) {
				mkdir($this->carpetaCache, 0777, true);
			}
			file_put_contents($cache, json_encode($datos, JSON_UNESCAPED_UNICODE));
			$datos['desde_cache'] = false;
			return $datos;
		}

		/** Una sola pasada por el archivo. */
		public function calcular(): array {
			$diasSemana = ["", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"];
			$total = 0;
			$minutosTotales = 0;
			$calor = [];		// [día de la semana][hora] => visitas
			$porMes = [];		// "2026-01" => visitas
			$sociosMes = [];	// "2026-01" => [socio => true]
			$porPlan = [];		// plan => [visitas, minutos]
			$porFecha = [];		// "2026-01-05" => visitas
			$socios = [];

			foreach ($this->filas() as $fila) {
				$total++;
				$minutos = (int)$fila['minutos'];
				$minutosTotales += $minutos;
				$diaSemana = (int)date("N", strtotime($fila['fecha']));
				$hora = (int)substr($fila['hora'], 0, 2);
				$mes = substr($fila['fecha'], 0, 7);

				$calor[$diaSemana][$hora] = ($calor[$diaSemana][$hora] ?? 0) + 1;
				$porMes[$mes] = ($porMes[$mes] ?? 0) + 1;
				$sociosMes[$mes][$fila['socio']] = true;
				$porPlan[$fila['plan']][0] = ($porPlan[$fila['plan']][0] ?? 0) + 1;
				$porPlan[$fila['plan']][1] = ($porPlan[$fila['plan']][1] ?? 0) + $minutos;
				$porFecha[$fila['fecha']] = ($porFecha[$fila['fecha']] ?? 0) + 1;
				$socios[$fila['socio']] = true;
			}
			if ($total === 0) {
				throw new RuntimeException("El archivo no tiene datos");
			}

			// Mapa de calor: media de visitas por día de la semana y hora
			$numeroDias = [];
			foreach (array_keys($porFecha) as $fecha) {
				$diaSemana = (int)date("N", strtotime($fecha));
				$numeroDias[$diaSemana] = ($numeroDias[$diaSemana] ?? 0) + 1;
			}
			$mapa = [];
			$punta = ["dia" => "", "hora" => 0, "media" => 0];
			$valle = ["dia" => "", "hora" => 0, "media" => PHP_INT_MAX];
			for ($d = 1; $d <= 7; $d++) {
				$fila = [];
				for ($h = 6; $h <= 22; $h++) {
					$media = isset($numeroDias[$d]) ? round(($calor[$d][$h] ?? 0) / $numeroDias[$d], 1) : 0;
					$fila[] = $media;
					if ($media > $punta['media']) {
						$punta = ["dia" => $diasSemana[$d], "hora" => $h, "media" => $media];
					}
					// El valle lo busco solo de lunes a viernes, de 9 a 21: horas en las que tiene sentido ir
					if ($d <= 5 && $h >= 9 && $h <= 21 && $media < $valle['media']) {
						$valle = ["dia" => $diasSemana[$d], "hora" => $h, "media" => $media];
					}
				}
				$mapa[] = ["dia" => $diasSemana[$d], "valores" => $fila];
			}

			ksort($porMes);
			$meses = [];
			foreach ($porMes as $mes => $visitas) {
				$meses[] = ["mes" => $mes, "visitas" => $visitas, "socios_activos" => count($sociosMes[$mes])];
			}

			$planes = [];
			foreach ($porPlan as $plan => [$visitas, $minutos]) {
				$planes[] = ["plan" => $plan, "visitas" => $visitas, "minutos_medios" => round($minutos / $visitas, 1)];
			}
			usort($planes, function ($a, $b) { return $b['visitas'] <=> $a['visitas']; });

			arsort($porFecha);
			$diaRecord = array_key_first($porFecha);

			return [
				"total_visitas" => $total,
				"socios_distintos" => count($socios),
				"dias" => count($porFecha),
				"media_diaria" => round($total / count($porFecha)),
				"minutos_medios" => round($minutosTotales / $total, 1),
				"dia_record" => ["fecha" => $diaRecord, "visitas" => $porFecha[$diaRecord]],
				"hora_punta" => $punta,
				"mejor_hora" => $valle,
				"horas" => range(6, 22),
				"mapa_calor" => $mapa,
				"meses" => $meses,
				"planes" => $planes
			];
		}
	}
?>
