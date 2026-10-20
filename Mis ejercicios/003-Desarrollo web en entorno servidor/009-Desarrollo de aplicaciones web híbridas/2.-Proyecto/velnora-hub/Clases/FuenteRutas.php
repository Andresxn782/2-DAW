<?php
	/**
	 * Información geográfica en GeoJSON (el formato estándar de mapas).
	 * Calcula la distancia de cada ruta y la dibuja como un trazado SVG,
	 * sin depender de un servicio de mapas.
	 */
	class FuenteRutas implements Fuente {
		private string $archivo;

		public function __construct(string $carpetaDatos) {
			$this->archivo = $carpetaDatos."/rutas.geojson";
		}

		public function nombre(): string {
			return "rutas";
		}

		/** Distancia en km entre dos puntos [longitud, latitud] (fórmula del haversine). */
		public static function distancia(array $a, array $b): float {
			$radio = 6371;
			$dLat = deg2rad($b[1] - $a[1]);
			$dLon = deg2rad($b[0] - $a[0]);
			$h = sin($dLat / 2) ** 2 + cos(deg2rad($a[1])) * cos(deg2rad($b[1])) * sin($dLon / 2) ** 2;
			return 2 * $radio * asin(sqrt($h));
		}

		/** Convierte las coordenadas en puntos de un dibujo de 200 x 140. */
		public static function trazado(array $coordenadas): string {
			$longitudes = array_column($coordenadas, 0);
			$latitudes = array_column($coordenadas, 1);
			$minLon = min($longitudes);
			$maxLat = max($latitudes);
			// Un grado de longitud mide menos que uno de latitud: corrijo con el coseno
			$factor = cos(deg2rad(array_sum($latitudes) / count($latitudes)));
			$ancho = max(max($longitudes) - $minLon, 0.0001) * $factor;
			$alto = max($maxLat - min($latitudes), 0.0001);
			$escala = min(180 / $ancho, 120 / $alto);
			$puntos = [];
			foreach ($coordenadas as [$lon, $lat]) {
				$puntos[] = round(10 + ($lon - $minLon) * $factor * $escala, 1).",".round(10 + ($maxLat - $lat) * $escala, 1);
			}
			return implode(" ", $puntos);
		}

		public function obtener(): array {
			$geojson = json_decode(file_get_contents($this->archivo), true);
			$rutas = [];
			foreach ($geojson['features'] as $ruta) {
				$puntos = $ruta['geometry']['coordinates'];
				$km = 0;
				for ($i = 1; $i < count($puntos); $i++) {
					$km += self::distancia($puntos[$i - 1], $puntos[$i]);
				}
				$rutas[] = [
					"nombre" => $ruta['properties']['nombre'],
					"nivel" => $ruta['properties']['nivel'],
					"superficie" => $ruta['properties']['superficie'],
					"km" => round($km, 2),
					"circular" => $puntos[0] === end($puntos),
					"trazado" => self::trazado($puntos)
				];
			}
			return ["rutas" => $rutas];
		}
	}
?>
