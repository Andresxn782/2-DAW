<?php
	// Clase que trata un CSV como una tabla
	class VelnoraCSV {

		private $archivo;

		public function __construct($archivo){
			$this->archivo = $archivo;
		}

		// Equivale a SELECT * FROM tabla
		public function listar(){
			$archivo = fopen($this->archivo, "r");
			$cabeceras = fgetcsv($archivo, null, ",", "\"", "\\");
			$conjunto = [];
			while(($fila = fgetcsv($archivo, null, ",", "\"", "\\")) !== false){
				$conjunto[] = array_combine($cabeceras, $fila);		// Uso las cabeceras como claves
			}
			fclose($archivo);
			return $conjunto;
		}

		// Equivale a SELECT * FROM tabla WHERE columna = valor
		public function buscar($columna, $valor){
			$resultados = [];
			foreach($this->listar() as $fila){
				if(isset($fila[$columna]) && $fila[$columna] == $valor){
					$resultados[] = $fila;
				}
			}
			return $resultados;
		}

		// Equivale a SELECT AVG(columna) FROM tabla
		public function media($columna){
			$valores = array_column($this->listar(), $columna);
			return count($valores) ? array_sum($valores) / count($valores) : 0;
		}
	}

	$socios = new VelnoraCSV("datos.csv");

	echo "<pre>";
	print_r($socios->buscar("plan", "Crossfit"));
	echo "</pre>";
	echo "Cuota media: ".number_format($socios->media("cuota"), 2, ',', '.')." €";
?>
