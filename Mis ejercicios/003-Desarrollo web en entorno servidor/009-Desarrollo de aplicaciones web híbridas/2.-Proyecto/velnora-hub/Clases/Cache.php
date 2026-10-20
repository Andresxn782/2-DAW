<?php
	/**
	 * Caché en archivos JSON con caducidad.
	 * Evita repetir cálculos pesados (el CSV) y llamadas a terceros (la AEMET).
	 */
	class Cache {
		private string $carpeta;

		public function __construct(string $carpeta) {
			$this->carpeta = $carpeta;
			if (!is_dir($carpeta)) {
				mkdir($carpeta, 0777, true);
			}
		}

		private function ruta(string $clave): string {
			return $this->carpeta."/".preg_replace('/[^a-z0-9_-]/i', "_", $clave).".json";
		}

		/**
		 * Si hay una copia de menos de $segundos la devuelve; si no, ejecuta $calcular y la guarda.
		 * Devuelve ["datos" => ..., "desde_cache" => bool, "guardado" => fecha].
		 */
		public function recordar(string $clave, int $segundos, callable $calcular): array {
			$ruta = $this->ruta($clave);
			clearstatcache(true, $ruta);
			if (file_exists($ruta) && time() - filemtime($ruta) < $segundos) {
				return ["datos" => json_decode(file_get_contents($ruta), true), "desde_cache" => true, "guardado" => date("c", filemtime($ruta))];
			}
			$datos = $calcular();
			file_put_contents($ruta, json_encode($datos, JSON_UNESCAPED_UNICODE), LOCK_EX);
			return ["datos" => $datos, "desde_cache" => false, "guardado" => date("c")];
		}

		public function vaciar(): int {
			$archivos = glob($this->carpeta."/*.json");
			array_map("unlink", $archivos);
			return count($archivos);
		}
	}
?>
