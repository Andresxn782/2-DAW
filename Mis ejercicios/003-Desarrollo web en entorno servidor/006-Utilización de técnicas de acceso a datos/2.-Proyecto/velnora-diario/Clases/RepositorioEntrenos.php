<?php
	/**
	 * Lo que tiene que saber hacer cualquier almacén de entrenos (patrón Repositorio).
	 * Las páginas solo usan estos métodos, así que da igual dónde estén los datos.
	 *
	 * $filtros admite: socio, tipo, desde, hasta (todos opcionales).
	 */
	interface RepositorioEntrenos {

		/** @return Entreno[] Una página de resultados, del más reciente al más antiguo. */
		public function buscar(array $filtros, int $pagina, int $porPagina): array;

		/** Total de entrenos que cumplen los filtros (para paginar). */
		public function contar(array $filtros): int;

		public function obtener(int $id): ?Entreno;

		/** Inserta si no tiene id y actualiza si lo tiene. Devuelve el id. */
		public function guardar(Entreno $entreno): int;

		/** @return bool true si se ha borrado algo. */
		public function eliminar(int $id): bool;

		/**
		 * Resumen de los entrenos filtrados.
		 * @return array ['sesiones' => int, 'minutos' => int, 'por_tipo' => [tipo => minutos], 'por_socio' => [socio => sesiones]]
		 */
		public function estadisticas(array $filtros): array;

		/** @return string[] Nombres de socio distintos, ordenados. */
		public function socios(): array;

		/**
		 * Guarda varios entrenos de golpe. Si uno falla no se guarda ninguno.
		 * @return int Cuántos se han guardado.
		 */
		public function importar(array $entrenos): int;
	}
?>
