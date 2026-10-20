<?php
	/**
	 * Repositorio JSON de Velnora TV (unidad 9, apartado 003).
	 * Recomienda vídeos: si hoy no se puede entrenar fuera, de interior y cortos.
	 */
	class FuenteVideos implements Fuente {
		private string $archivo;
		private bool $entrenarFuera;

		public function __construct(string $carpetaDatos, bool $entrenarFuera) {
			$this->archivo = $carpetaDatos."/entrenos.json";
			$this->entrenarFuera = $entrenarFuera;
		}

		public function nombre(): string {
			return "videos";
		}

		public function obtener(): array {
			$entrenos = json_decode(file_get_contents($this->archivo), true)['entrenos'];
			$categorias = $this->entrenarFuera ? ["Movilidad", "Yoga"] : ["Fuerza", "Cardio", "Crossfit"];
			$elegidos = array_values(array_filter($entrenos, function ($video) use ($categorias) {
				return in_array($video['categoria'], $categorias, true) && $video['minutos'] <= 30;
			}));
			// Cambia cada día pero es la misma durante todo el día
			mt_srand((int)date("Ymd"));
			shuffle($elegidos);
			return [
				"motivo" => $this->entrenarFuera ? "Hoy hace bueno: sal a correr y completa con movilidad o yoga." : "Hoy mejor bajo techo: entrenos cortos para casa.",
				"videos" => array_slice($elegidos, 0, 3)
			];
		}
	}
?>
