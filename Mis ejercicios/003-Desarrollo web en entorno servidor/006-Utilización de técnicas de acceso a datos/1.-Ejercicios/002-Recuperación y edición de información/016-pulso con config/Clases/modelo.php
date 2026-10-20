<?php
	// MODELO: todo el acceso a gimnasio.db. No escribe HTML ni lee $_GET o $_POST.
	class VelnoraModelo {

		private PDO $bd;

		public function __construct(string $basededatos){
			$this->bd = new PDO('sqlite:'.$basededatos);
			$this->bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->bd->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		}

		// Los nombres de tabla y columna no se pueden preparar, así que los valido
		private function identificador(string $nombre): string {
			if(!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $nombre)){
				throw new InvalidArgumentException("Identificador no válido");
			}
			return '"'.$nombre.'"';
		}

		public function autentica(string $usuario, string $contrasena): ?array {
			$consulta = $this->bd->prepare("SELECT * FROM usuarios WHERE usuario = :usuario");
			$consulta->execute([':usuario' => $usuario]);
			$fila = $consulta->fetch();
			if($fila && password_verify($contrasena, $fila['contrasena'])){
				unset($fila['contrasena']);
				return $fila;
			}
			return null;
		}

		public function dameTablasRol(mixed $rol): array {
			$consulta = $this->bd->prepare("SELECT tabla FROM roles WHERE idrol = :rol");
			$consulta->execute([':rol' => $rol]);
			return array_column($consulta->fetchAll(), 'tabla');
		}

		public function dameCampos(string $tabla): array {
			return $this->bd->query("PRAGMA table_info(".$this->identificador($tabla).")")->fetchAll();
		}

		public function dameClavePrimaria(string $tabla): ?string {
			foreach($this->dameCampos($tabla) as $campo){
				if((int)$campo['pk'] === 1){
					return $campo['name'];
				}
			}
			return null;
		}

		public function dameDatos(string $tabla): array {
			return $this->bd->query("SELECT * FROM ".$this->identificador($tabla))->fetchAll();
		}

		public function dameRegistro(string $tabla, string $pk, mixed $id): array {
			$consulta = $this->bd->prepare("SELECT * FROM ".$this->identificador($tabla)." WHERE ".$this->identificador($pk)." = :id");
			$consulta->execute([':id' => $id]);
			return $consulta->fetch() ?: [];
		}

		// Solo deja pasar campos que existen en la tabla
		private function filtraCampos(string $tabla, array $datos, ?string $pk): array {
			$permitidos = array_column($this->dameCampos($tabla), 'name');
			$filtrados = [];
			foreach($datos as $campo => $valor){
				if($campo !== $pk && in_array($campo, $permitidos, true)){
					$filtrados[$campo] = trim((string)$valor);
				}
			}
			return $filtrados;
		}

		public function insertaDatos(string $tabla, array $datos): void {
			$filtrados = $this->filtraCampos($tabla, $datos, $this->dameClavePrimaria($tabla));
			if(!$filtrados){
				return;
			}
			$columnas = array_map(fn($c) => $this->identificador($c), array_keys($filtrados));
			$huecos = array_map(fn($c) => ':'.$c, array_keys($filtrados));
			$consulta = $this->bd->prepare("INSERT INTO ".$this->identificador($tabla)." (".implode(",", $columnas).") VALUES (".implode(",", $huecos).")");
			$consulta->execute(array_combine($huecos, array_values($filtrados)));
		}

		public function actualizaDatos(string $tabla, string $pk, mixed $id, array $datos): void {
			$filtrados = $this->filtraCampos($tabla, $datos, $pk);
			if(!$filtrados){
				return;
			}
			$sets = array_map(fn($c) => $this->identificador($c)." = :".$c, array_keys($filtrados));
			$consulta = $this->bd->prepare("UPDATE ".$this->identificador($tabla)." SET ".implode(",", $sets)." WHERE ".$this->identificador($pk)." = :clave_primaria");
			$valores = [];
			foreach($filtrados as $campo => $valor){
				$valores[':'.$campo] = $valor;
			}
			$valores[':clave_primaria'] = $id;
			$consulta->execute($valores);
		}

		public function eliminaDatos(string $tabla, string $pk, mixed $id): void {
			$consulta = $this->bd->prepare("DELETE FROM ".$this->identificador($tabla)." WHERE ".$this->identificador($pk)." = :id");
			$consulta->execute([':id' => $id]);
		}
	}
?>
