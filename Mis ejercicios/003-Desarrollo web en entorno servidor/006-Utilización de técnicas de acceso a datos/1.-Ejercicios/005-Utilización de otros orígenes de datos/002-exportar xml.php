<?php
	// Leo de SQLite y escribo un documento XML con DOMDocument
	$db = new PDO('sqlite:socios.db');

	$xml = new DOMDocument("1.0", "UTF-8");
	$xml->formatOutput = true;
	$raiz = $xml->createElement("socios");
	$raiz->setAttribute("gimnasio", "Velnora");
	$raiz->setAttribute("generado", date("c"));
	$xml->appendChild($raiz);

	foreach($db->query("SELECT * FROM socios ORDER BY id", PDO::FETCH_ASSOC) as $fila){
		$socio = $xml->createElement("socio");
		$socio->setAttribute("id", $fila['id']);
		foreach(["nombre", "apellidos", "email", "plan"] as $campo){
			// createTextNode escapa los caracteres especiales (&, <...)
			$elemento = $xml->createElement($campo);
			$elemento->appendChild($xml->createTextNode($fila[$campo]));
			$socio->appendChild($elemento);
		}
		$raiz->appendChild($socio);
	}

	$xml->save("socios.xml");
	header("Content-Type: application/xml; charset=utf-8");
	echo $xml->saveXML();
?>
