<?php
	/**
	 * Mini motor de base de datos con archivos CSV.
	 * - Una base de datos es una carpeta.
	 * - Una tabla es un archivo .csv cuya primera fila son las columnas.
	 * Al escribir bloqueo el archivo (flock) para que dos peticiones a la vez no lo estropeen.
	 */
	class VelnoraCSV {

		private string $rutaBase;

		public function __construct(string $rutaBase = "datos"){
			$this->rutaBase = rtrim($rutaBase, "/");
		}

		/** Solo letras, números y guion bajo: así nadie puede salir de la carpeta con ../ */
		private function nombre(string $nombre): string {
			if(!preg_match('/^[A-Za-z0-9_]+$/', $nombre)){
				throw new InvalidArgumentException("Nombre no válido: ".$nombre);
			}
			return $nombre;
		}

		private function rutaTabla(string $basedatos, string $tabla): string {
			return $this->rutaBase."/".$this->nombre($basedatos)."/".$this->nombre($tabla).".csv";
		}

		// ---------- BASES DE DATOS Y TABLAS ----------

		/** CREATE DATABASE */
		public function crearBaseDatos(string $nombre): bool {
			$ruta = $this->rutaBase."/".$this->nombre($nombre);
			return is_dir($ruta) || mkdir($ruta, 0777, true);
		}

		/** CREATE TABLE. Si ya existe no la toca (como CREATE TABLE IF NOT EXISTS). */
		public function crearTabla(string $basedatos, string $tabla, array $columnas): bool {
			$this->crearBaseDatos($basedatos);
			$ruta = $this->rutaTabla($basedatos, $tabla);
			if(file_exists($ruta)){
				return false;
			}
			$this->escribir($ruta, $columnas, []);
			return true;
		}

		/** DROP TABLE */
		public function borrarTabla(string $basedatos, string $tabla): bool {
			$ruta = $this->rutaTabla($basedatos, $tabla);
			return file_exists($ruta) && unlink($ruta);
		}

		/** SHOW TABLES */
		public function tablas(string $basedatos): array {
			$archivos = glob($this->rutaBase."/".$this->nombre($basedatos)."/*.csv") ?: [];
			return array_map(fn($a) => basename($a, ".csv"), $archivos);
		}

		/** Columnas de la tabla (la primera fila del CSV). */
		public function columnas(string $basedatos, string $tabla): array {
			$ruta = $this->rutaTabla($basedatos, $tabla);
			if(!file_exists($ruta)){
				return [];
			}
			$archivo = fopen($ruta, "r");
			$cabeceras = fgetcsv($archivo, null, ",", "\"", "\\") ?: [];
			fclose($archivo);
			return $cabeceras;
		}

		// ---------- LECTURA ----------

		/** SELECT * FROM tabla */
		public function listar(string $basedatos, string $tabla): array {
			$ruta = $this->rutaTabla($basedatos, $tabla);
			if(!file_exists($ruta)){
				return [];
			}
			$archivo = fopen($ruta, "r");
			flock($archivo, LOCK_SH);
			$cabeceras = fgetcsv($archivo, null, ",", "\"", "\\") ?: [];
			$conjunto = [];
			while(($fila = fgetcsv($archivo, null, ",", "\"", "\\")) !== false){
				if(count($fila) === count($cabeceras)){
					$conjunto[] = array_combine($cabeceras, $fila);
				}
			}
			flock($archivo, LOCK_UN);
			fclose($archivo);
			return $conjunto;
		}

		/** SELECT * FROM tabla WHERE columna = valor */
		public function buscar(string $basedatos, string $tabla, string $columna, mixed $valor): array {
			return array_values(array_filter(
				$this->listar($basedatos, $tabla),
				fn($fila) => isset($fila[$columna]) && $fila[$columna] == $valor
			));
		}

		// ---------- ESCRITURA ----------

		/** INSERT INTO tabla. Las columnas que falten se guardan vacías. */
		public function insertar(string $basedatos, string $tabla, array $datos): bool {
			$ruta = $this->rutaTabla($basedatos, $tabla);
			if(!file_exists($ruta)){
				return false;
			}
			$fila = [];
			foreach($this->columnas($basedatos, $tabla) as $columna){
				$fila[] = $datos[$columna] ?? "";
			}
			$archivo = fopen($ruta, "a");
			flock($archivo, LOCK_EX);
			fputcsv($archivo, $fila, ",", "\"", "\\");
			flock($archivo, LOCK_UN);
			fclose($archivo);
			return true;
		}

		/** UPDATE tabla SET ... WHERE columna = valor. @return int filas cambiadas */
		public function actualizar(string $basedatos, string $tabla, string $columna, mixed $valor, array $nuevosDatos): int {
			$cambiadas = 0;
			$datos = $this->listar($basedatos, $tabla);
			foreach($datos as &$fila){
				if(isset($fila[$columna]) && $fila[$columna] == $valor){
					foreach($nuevosDatos as $campo => $nuevoValor){
						if(array_key_exists($campo, $fila)){
							$fila[$campo] = $nuevoValor;
						}
					}
					$cambiadas++;
				}
			}
			unset($fila);
			$this->escribir($this->rutaTabla($basedatos, $tabla), $this->columnas($basedatos, $tabla), $datos);
			return $cambiadas;
		}

		/** DELETE FROM tabla WHERE columna = valor. @return int filas borradas */
		public function eliminar(string $basedatos, string $tabla, string $columna, mixed $valor): int {
			$datos = $this->listar($basedatos, $tabla);
			$quedan = array_filter($datos, fn($fila) => !isset($fila[$columna]) || $fila[$columna] != $valor);
			$this->escribir($this->rutaTabla($basedatos, $tabla), $this->columnas($basedatos, $tabla), $quedan);
			return count($datos) - count($quedan);
		}

		/** Reescribe la tabla entera con las filas indicadas. */
		private function escribir(string $ruta, array $cabeceras, array $filas): void {
			$archivo = fopen($ruta, "c");
			flock($archivo, LOCK_EX);
			ftruncate($archivo, 0);
			fputcsv($archivo, $cabeceras, ",", "\"", "\\");
			foreach($filas as $fila){
				$ordenada = [];
				foreach($cabeceras as $columna){
					$ordenada[] = $fila[$columna] ?? "";
				}
				fputcsv($archivo, $ordenada, ",", "\"", "\\");
			}
			fflush($archivo);
			flock($archivo, LOCK_UN);
			fclose($archivo);
		}
	}
?>
