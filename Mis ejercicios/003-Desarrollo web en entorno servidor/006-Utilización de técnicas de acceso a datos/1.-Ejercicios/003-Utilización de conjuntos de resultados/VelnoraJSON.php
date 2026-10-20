<?php
	/**
	 * Guarda y carga datos en archivos JSON.
	 */
	class VelnoraJSON {

		/** Guarda el array en el archivo, con formato legible y tildes sin escapar. */
		public function guardarJSON(string $archivo, array $datos): bool {
			$json = json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
			return file_put_contents($archivo, $json, LOCK_EX) !== false;
		}

		/** Carga el archivo. Si no existe o está mal formado devuelve un array vacío. */
		public function cargarJSON(string $archivo): array {
			if(!file_exists($archivo)){
				return [];
			}
			$datos = json_decode(file_get_contents($archivo), true);
			return is_array($datos) ? $datos : [];
		}

		/** Añade un elemento a una lista guardada en el archivo. */
		public function anadir(string $archivo, array $elemento): bool {
			$lista = $this->cargarJSON($archivo);
			$lista[] = $elemento;
			return $this->guardarJSON($archivo, $lista);
		}
	}
?>
