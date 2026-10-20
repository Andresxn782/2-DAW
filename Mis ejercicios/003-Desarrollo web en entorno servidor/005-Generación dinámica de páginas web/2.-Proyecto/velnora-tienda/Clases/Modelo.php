<?php
	/**
	 * Modelo base: CRUD genérico para una tabla.
	 * Las clases hijas solo indican su tabla y sus campos (herencia).
	 */
	abstract class Modelo {

		protected PDO $bd;

		/** Nombre de la tabla en la base de datos. */
		abstract protected function tabla(): string;

		/**
		 * Campos editables: nombre => [etiqueta, tipo de control, opciones].
		 * La vista los usa para generar el formulario dinámicamente.
		 */
		abstract public function campos(): array;

		public function __construct(PDO $bd){
			$this->bd = $bd;
		}

		/** @return array Todas las filas. */
		public function todos(): array {
			return $this->bd->query("SELECT * FROM ".$this->tabla()." ORDER BY id")->fetchAll();
		}

		/** @return array|null La fila con ese id, o null si no existe. */
		public function buscar(int $id): ?array {
			$consulta = $this->bd->prepare("SELECT * FROM ".$this->tabla()." WHERE id = :id");
			$consulta->execute([':id' => $id]);
			return $consulta->fetch() ?: null;
		}

		/** @return int Id de la fila nueva. */
		public function insertar(array $datos): int {
			$datos = $this->soloCampos($datos);
			$columnas = array_keys($datos);
			$consulta = $this->bd->prepare("INSERT INTO ".$this->tabla()." (".implode(",", $columnas).") VALUES (:".implode(",:", $columnas).")");
			$consulta->execute($datos);
			return (int)$this->bd->lastInsertId();
		}

		public function actualizar(int $id, array $datos): void {
			$datos = $this->soloCampos($datos);
			$sets = array_map(fn($c) => $c." = :".$c, array_keys($datos));
			$datos['id'] = $id;
			$consulta = $this->bd->prepare("UPDATE ".$this->tabla()." SET ".implode(",", $sets)." WHERE id = :id");
			$consulta->execute($datos);
		}

		public function eliminar(int $id): void {
			$consulta = $this->bd->prepare("DELETE FROM ".$this->tabla()." WHERE id = :id");
			$consulta->execute([':id' => $id]);
		}

		/** Quita cualquier dato que no sea un campo del modelo. */
		protected function soloCampos(array $datos): array {
			return array_intersect_key($datos, $this->campos());
		}
	}
?>
