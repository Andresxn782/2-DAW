<?php
	// Descarga los socios en un CSV que Excel en español abre bien
	$db = new PDO('sqlite:socios.db');
	$socios = $db->query("SELECT id, nombre, apellidos, email, plan FROM socios ORDER BY apellidos", PDO::FETCH_ASSOC);

	header("Content-Type: text/csv; charset=utf-8");
	header("Content-Disposition: attachment; filename=socios-velnora-".date("Y-m-d").".csv");

	$salida = fopen("php://output", "w");
	fwrite($salida, "\xEF\xBB\xBF");		// BOM: así Excel sabe que es UTF-8 y respeta las tildes
	fputcsv($salida, ["Id", "Nombre", "Apellidos", "Email", "Plan"], ";", "\"", "\\");		// Excel en español usa ;
	foreach($socios as $fila){
		fputcsv($salida, $fila, ";", "\"", "\\");
	}
	fclose($salida);
?>
