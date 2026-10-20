<?php
	// SimpleXML convierte el XML en objetos que se recorren con foreach
	$horario = simplexml_load_file("clases.xml");
	if($horario === false){
		die("No se ha podido leer clases.xml");
	}

	echo "<h1>Horario ".$horario['gimnasio']." ".$horario['temporada']."</h1>";
	echo "<table border='1' cellpadding='6'><tr><th>Clase</th><th>Sala</th><th>Día</th><th>Hora</th><th>Monitor</th></tr>";
	foreach($horario->clase as $clase){
		// Los atributos se leen como array y los hijos como propiedades
		echo "<tr><td>".$clase->nombre."</td><td>".$clase['sala']."</td><td>".$clase->dia."</td><td>".$clase->hora."</td><td>".$clase->monitor."</td></tr>";
	}
	echo "</table>";

	// XPath: es como un WHERE para XML
	$monitor = $_GET['monitor'] ?? "Ainhoa Ferrer";
	echo "<h2>Clases de ".htmlspecialchars($monitor)."</h2><ul>";
	$encontradas = $horario->xpath("//clase[monitor=".json_encode($monitor)."]");
	foreach($encontradas as $clase){
		echo "<li>".$clase->nombre." (".$clase->dia.")</li>";
	}
	echo "</ul>";

	$plazas = 0;
	foreach($horario->xpath("//clase/plazas") as $p){
		$plazas += (int)$p;
	}
	echo "<p>Plazas totales a la semana: ".$plazas."</p>";
?>
