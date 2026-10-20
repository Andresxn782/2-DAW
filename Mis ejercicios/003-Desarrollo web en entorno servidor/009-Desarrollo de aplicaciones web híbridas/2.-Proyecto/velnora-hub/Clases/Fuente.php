<?php
	/**
	 * Todas las fuentes de información del hub cumplen este contrato.
	 * Así api.php las trata igual, vengan de una base de datos, de un CSV o de internet.
	 */
	interface Fuente {
		/** Nombre que se usa en api.php?fuente=... */
		public function nombre(): string;

		/** Devuelve los datos ya preparados para el cliente. */
		public function obtener(): array;
	}
?>
