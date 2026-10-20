<?php
	/**
	 * Convierte una lista de entrenos a CSV, JSON o XML.
	 */
	class Exportador {

		/** @param Entreno[] $entrenos */
		public static function csv(array $entrenos): string {
			$salida = fopen("php://temp", "r+");
			fputcsv($salida, ["socio", "fecha", "tipo", "minutos", "intensidad", "notas"], ",", "\"", "\\");
			foreach($entrenos as $e){
				fputcsv($salida, array_values($e->aArray()), ",", "\"", "\\");
			}
			rewind($salida);
			$texto = stream_get_contents($salida);
			fclose($salida);
			return $texto;
		}

		/** @param Entreno[] $entrenos */
		public static function json(array $entrenos): string {
			$lista = array_map(fn($e) => $e->aArray() + ['calorias' => $e->calorias()], $entrenos);
			return json_encode(['gimnasio' => 'Velnora', 'exportado' => date('c'), 'entrenos' => $lista], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		}

		/** @param Entreno[] $entrenos */
		public static function xml(array $entrenos): string {
			$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><entrenos gimnasio="Velnora"/>');
			foreach($entrenos as $e){
				$nodo = $xml->addChild('entreno');
				$nodo->addAttribute('fecha', $e->fecha);
				foreach(['socio', 'tipo', 'minutos', 'intensidad', 'notas'] as $campo){
					// htmlspecialchars porque addChild no escapa el &
					$nodo->addChild($campo, htmlspecialchars((string)$e->$campo, ENT_XML1));
				}
				$nodo->addChild('calorias', (string)$e->calorias());
			}
			return $xml->asXML();
		}

		/**
		 * Lee un CSV con cabeceras (socio, fecha, tipo, minutos, intensidad, notas).
		 * @return array [Entreno[] válidos, string[] errores con su número de línea]
		 */
		public static function leerCSV(string $ruta): array {
			$archivo = fopen($ruta, "r");
			$cabeceras = array_map(fn($c) => strtolower(trim($c, " \xEF\xBB\xBF")), fgetcsv($archivo, null, ",", "\"", "\\") ?: []);
			$validos = [];
			$errores = [];
			$linea = 1;
			while(($fila = fgetcsv($archivo, null, ",", "\"", "\\")) !== false){
				$linea++;
				if(count($fila) !== count($cabeceras)){
					$errores[] = "Línea ".$linea.": número de columnas incorrecto.";
					continue;
				}
				$entreno = Entreno::desdeArray(array_combine($cabeceras, $fila));
				$fallos = $entreno->validar();
				if($fallos){
					$errores[] = "Línea ".$linea.": ".implode(" ", $fallos);
				}else{
					$validos[] = $entreno;
				}
			}
			fclose($archivo);
			return [$validos, $errores];
		}
	}
?>
