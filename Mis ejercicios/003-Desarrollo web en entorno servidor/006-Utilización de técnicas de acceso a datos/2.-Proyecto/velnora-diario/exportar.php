<?php
	include "includes/inicio.php";

	// Exporto todos los entrenos que cumplen los filtros (sin paginar)
	$entrenos = $repositorio->buscar($filtros, 1, PHP_INT_MAX);
	$nombre = "entrenos-velnora-".date('Y-m-d');

	switch($_GET['formato'] ?? 'csv'){
		case 'json':
			header("Content-Type: application/json; charset=utf-8");
			header("Content-Disposition: attachment; filename=".$nombre.".json");
			echo Exportador::json($entrenos);
			break;
		case 'xml':
			header("Content-Type: application/xml; charset=utf-8");
			header("Content-Disposition: attachment; filename=".$nombre.".xml");
			echo Exportador::xml($entrenos);
			break;
		default:
			header("Content-Type: text/csv; charset=utf-8");
			header("Content-Disposition: attachment; filename=".$nombre.".csv");
			echo Exportador::csv($entrenos);
	}
?>
