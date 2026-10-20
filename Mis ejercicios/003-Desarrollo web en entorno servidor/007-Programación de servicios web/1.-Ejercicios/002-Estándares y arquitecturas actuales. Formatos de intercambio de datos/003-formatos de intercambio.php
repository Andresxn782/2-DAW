<?php
	// Los mismos datos en tres formatos de intercambio.
	// ?formato=json (por defecto), ?formato=xml o ?formato=csv
	$clases = [
		["id" => 1, "nombre" => "Spinning", "dia" => "Lunes", "hora" => "18:00", "plazas" => 20],
		["id" => 2, "nombre" => "Crossfit", "dia" => "Martes", "hora" => "19:30", "plazas" => 12],
		["id" => 3, "nombre" => "Pilates", "dia" => "Jueves", "hora" => "10:00", "plazas" => 15]
	];

	$formato = $_GET['formato'] ?? "json";

	if ($formato === "xml") {
		header("Content-Type: application/xml; charset=UTF-8");
		$xml = new SimpleXMLElement('<clases gimnasio="Velnora"/>');
		foreach ($clases as $clase) {
			$nodo = $xml->addChild("clase");
			$nodo->addAttribute("id", (string)$clase['id']);
			$nodo->addChild("nombre", $clase['nombre']);
			$nodo->addChild("dia", $clase['dia']);
			$nodo->addChild("hora", $clase['hora']);
			$nodo->addChild("plazas", (string)$clase['plazas']);
		}
		echo $xml->asXML();
	} else if ($formato === "csv") {
		header("Content-Type: text/csv; charset=UTF-8");
		$salida = fopen("php://output", "w");
		fputcsv($salida, array_keys($clases[0]), ",", '"', "");
		foreach ($clases as $clase) {
			fputcsv($salida, $clase, ",", '"', "");
		}
		fclose($salida);
	} else {
		header("Content-Type: application/json; charset=UTF-8");
		echo json_encode(["gimnasio" => "Velnora", "clases" => $clases], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	}
?>
