<?php
	/**
	 * Servicio de un tercero: la predicción XML de la AEMET.
	 * Si no responde a tiempo, uso la copia de ejemplo y lo digo en "origen".
	 */
	class FuenteTiempo implements Fuente {
		private array $config;
		private string $carpetaDatos;

		public function __construct(array $config, string $carpetaDatos) {
			$this->config = $config;
			$this->carpetaDatos = $carpetaDatos;
		}

		public function nombre(): string {
			return "tiempo";
		}

		private function descargar(): ?string {
			if (!function_exists("curl_init")) {
				return null;
			}
			$curl = curl_init($this->config['url']);
			curl_setopt_array($curl, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => $this->config['timeout'], CURLOPT_CONNECTTIMEOUT => 3]);
			$respuesta = curl_exec($curl);
			$codigo = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);
			return ($respuesta !== false && $codigo === 200) ? $respuesta : null;
		}

		public function obtener(): array {
			$origen = "AEMET";
			$texto = $this->descargar();
			$xml = $texto !== null ? @simplexml_load_string($texto) : false;
			if ($xml === false) {
				$xml = simplexml_load_file($this->carpetaDatos."/prediccion-ejemplo.xml");
				$origen = "Copia de ejemplo (la AEMET no ha respondido)";
			}
			return self::interpretar($xml, $origen);
		}

		/** Saca los tres primeros días y decide si se puede entrenar fuera. Pública para probarla. */
		public static function interpretar(SimpleXMLElement $xml, string $origen): array {
			$dias = [];
			foreach ($xml->prediccion->dia as $dia) {
				$lluvia = 0;
				foreach ($dia->prob_precipitacion as $tramo) {
					if ((string)$tramo !== "") {
						$lluvia = max($lluvia, (int)$tramo);
					}
				}
				$maxima = (int)$dia->temperatura->maxima;
				$minima = (int)$dia->temperatura->minima;
				$cielo = "";
				foreach ($dia->estado_cielo as $estado) {
					if ((string)$estado["descripcion"] !== "") {
						$cielo = (string)$estado["descripcion"];
						break;
					}
				}
				$dias[] = [
					"fecha" => (string)$dia["fecha"],
					"maxima" => $maxima,
					"minima" => $minima,
					"lluvia" => $lluvia,
					"cielo" => $cielo,
					"entrenar_fuera" => $lluvia < 40 && $maxima <= 32 && $maxima >= 6
				];
				if (count($dias) === 3) {
					break;
				}
			}
			return [
				"municipio" => (string)$xml->nombre,
				"origen" => $origen,
				"elaborado" => (string)$xml->elaborado,
				"dias" => $dias
			];
		}
	}
?>
