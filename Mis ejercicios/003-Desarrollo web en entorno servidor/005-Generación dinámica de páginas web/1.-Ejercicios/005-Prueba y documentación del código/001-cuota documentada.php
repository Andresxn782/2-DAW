<?php
	/**
	 * Calcula la cuota de un socio del gimnasio Velnora.
	 *
	 * @author Andres Ruiz
	 */
	class Cuota {

		/** IVA que se aplica a todas las cuotas. */
		public const IVA = 0.21;

		/** @var float Precio mensual del plan sin IVA. */
		private float $precioBase;

		/** @var int Meses contratados. */
		private int $meses;

		/** @var bool Si el socio es estudiante. */
		private bool $estudiante;

		/**
		 * @param float $precioBase Precio mensual del plan sin IVA. Debe ser mayor que 0.
		 * @param int   $meses      Meses contratados, de 1 a 24.
		 * @param bool  $estudiante Si tiene carné de estudiante.
		 * @throws InvalidArgumentException Si el precio o los meses no son válidos.
		 */
		public function __construct(float $precioBase, int $meses, bool $estudiante = false){
			if($precioBase <= 0){
				throw new InvalidArgumentException("El precio debe ser mayor que 0");
			}
			if($meses < 1 || $meses > 24){
				throw new InvalidArgumentException("Los meses van de 1 a 24");
			}
			$this->precioBase = $precioBase;
			$this->meses = $meses;
			$this->estudiante = $estudiante;
		}

		/**
		 * Descuento total en tanto por uno.
		 * Estudiante 15 %, contrato de 12 meses o más 10 %, de 6 a 11 meses 5 %.
		 *
		 * @return float Por ejemplo, 0.25 para un 25 %.
		 */
		public function descuento(): float {
			$descuento = $this->estudiante ? 0.15 : 0;
			if($this->meses >= 12){
				$descuento += 0.10;
			}elseif($this->meses >= 6){
				$descuento += 0.05;
			}
			return $descuento;
		}

		/**
		 * Cuota mensual con descuento e IVA, redondeada a céntimos.
		 *
		 * @return float
		 */
		public function mensual(): float {
			return round($this->precioBase * (1 - $this->descuento()) * (1 + self::IVA), 2);
		}

		/**
		 * Lo que paga en total por todos los meses contratados.
		 *
		 * @return float
		 */
		public function total(): float {
			return round($this->mensual() * $this->meses, 2);
		}
	}

	// Ejemplo de uso solo si abro este archivo directamente
	if(basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)){
		$cuota = new Cuota(39.90, 12, true);
		echo "Cuota mensual: ".$cuota->mensual()." € · Total: ".$cuota->total()." €";
	}
?>
