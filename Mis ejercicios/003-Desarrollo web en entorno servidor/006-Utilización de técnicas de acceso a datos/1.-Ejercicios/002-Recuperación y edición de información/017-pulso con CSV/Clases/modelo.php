<?php
	require_once __DIR__."/VelnoraCSV.php";

	// MODELO en versión CSV: tiene los mismos métodos que el de SQLite,
	// así que el controlador y la vista funcionan sin cambiar ni una línea.
	class VelnoraModelo {

		private VelnoraCSV $bd;
		private string $basededatos;
		private array $clavesPrimarias;

		public function __construct(string $rutaDatos, string $basededatos, array $clavesPrimarias){
			$this->bd = new VelnoraCSV($rutaDatos);
			$this->basededatos = $basededatos;
			$this->clavesPrimarias = $clavesPrimarias;
		}

		public function autentica(string $usuario, string $contrasena): ?array {
			foreach($this->bd->buscar($this->basededatos, "usuarios", "usuario", $usuario) as $fila){
				if(password_verify($contrasena, $fila['contrasena'])){
					unset($fila['contrasena']);
					return $fila;
				}
			}
			return null;
		}

		public function dameTablasRol(mixed $rol): array {
			return array_values(array_unique(array_column($this->bd->buscar($this->basededatos, "roles", "idrol", $rol), "tabla")));
		}

		// Imito el formato de PRAGMA table_info para que la vista no note la diferencia
		public function dameCampos(string $tabla): array {
			$pk = $this->dameClavePrimaria($tabla);
			return array_map(fn($n) => ['name' => $n, 'pk' => $n === $pk ? 1 : 0], $this->bd->columnas($this->basededatos, $tabla));
		}

		public function dameClavePrimaria(string $tabla): ?string {
			return $this->clavesPrimarias[$tabla] ?? null;
		}

		public function dameDatos(string $tabla): array {
			return $this->bd->listar($this->basededatos, $tabla);
		}

		public function dameRegistro(string $tabla, string $pk, mixed $id): array {
			return $this->bd->buscar($this->basededatos, $tabla, $pk, $id)[0] ?? [];
		}

		// En CSV no hay AUTOINCREMENT: calculo el siguiente id a mano
		private function siguienteId(string $tabla, string $pk): int {
			$ids = array_map('intval', array_column($this->dameDatos($tabla), $pk));
			return $ids ? max($ids) + 1 : 1;
		}

		public function insertaDatos(string $tabla, array $datos): void {
			$pk = $this->dameClavePrimaria($tabla);
			$fila = [];
			foreach($this->bd->columnas($this->basededatos, $tabla) as $columna){
				$fila[$columna] = trim((string)($datos[$columna] ?? ''));
			}
			if($pk){
				$fila[$pk] = $this->siguienteId($tabla, $pk);
			}
			$this->bd->insertar($this->basededatos, $tabla, $fila);
		}

		public function actualizaDatos(string $tabla, string $pk, mixed $id, array $datos): void {
			$columnas = $this->bd->columnas($this->basededatos, $tabla);
			$nuevos = [];
			foreach($datos as $campo => $valor){
				if($campo !== $pk && in_array($campo, $columnas, true)){
					$nuevos[$campo] = trim((string)$valor);
				}
			}
			$this->bd->actualizar($this->basededatos, $tabla, $pk, $id, $nuevos);
		}

		public function eliminaDatos(string $tabla, string $pk, mixed $id): void {
			$this->bd->eliminar($this->basededatos, $tabla, $pk, $id);
		}
	}
?>
