<?php
	// Pruebas de la clase Cuota. Ejecutar: php "002-pruebas de la cuota.php"
	include "001-cuota documentada.php";

	$pasadas = 0;
	$falladas = 0;

	/**
	 * Muestra OK o FALLO según la condición y lleva la cuenta.
	 */
	function comprobar(string $descripcion, bool $condicion): void {
		global $pasadas, $falladas;
		if($condicion){
			$pasadas++;
			echo "OK     ".$descripcion.PHP_EOL;
		}else{
			$falladas++;
			echo "FALLO  ".$descripcion.PHP_EOL;
		}
	}

	$normal = new Cuota(30, 1);
	comprobar("Sin descuentos solo se suma el IVA", $normal->mensual() === 36.30);
	comprobar("Sin descuentos el descuento es 0", $normal->descuento() == 0);

	$estudiante = new Cuota(30, 1, true);
	comprobar("Estudiante tiene un 15 %", $estudiante->descuento() === 0.15);

	$anual = new Cuota(30, 12, true);
	comprobar("Estudiante con 12 meses suma un 25 %", abs($anual->descuento() - 0.25) < 0.0001);
	comprobar("El total son 12 cuotas", $anual->total() === round($anual->mensual() * 12, 2));

	$semestral = new Cuota(30, 6);
	comprobar("Con 6 meses hay un 5 %", $semestral->descuento() === 0.05);

	// Casos que deben dar error
	try{
		new Cuota(0, 3);
		comprobar("Precio 0 da error", false);
	}catch(InvalidArgumentException $e){
		comprobar("Precio 0 da error", true);
	}
	try{
		new Cuota(30, 30);
		comprobar("30 meses da error", false);
	}catch(InvalidArgumentException $e){
		comprobar("30 meses da error", true);
	}

	echo PHP_EOL.$pasadas." pruebas correctas, ".$falladas." fallos".PHP_EOL;
?>
