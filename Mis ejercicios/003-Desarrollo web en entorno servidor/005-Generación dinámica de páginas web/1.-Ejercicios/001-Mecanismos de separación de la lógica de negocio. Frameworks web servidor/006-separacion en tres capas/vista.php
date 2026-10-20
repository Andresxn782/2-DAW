<?php
	// Vista: recibe JSON y pinta una tabla. No sabe nada de bases de datos.
	function pintaTabla($json, $titulo = ""){
		$datos = json_decode($json, true);
		if($titulo != ""){
			echo "<h2>".htmlspecialchars($titulo)."</h2>";
		}
		if(!$datos || count($datos) == 0){
			echo "<p>No hay datos</p>";
			return;
		}
		echo "<table border=\"1\" cellpadding=\"6\">";
		// Cabeceras: las saco de las claves de la primera fila
		echo "<thead><tr>";
		foreach($datos[0] as $clave => $valor){
			echo "<th>".htmlspecialchars($clave)."</th>";
		}
		echo "</tr></thead>";
		// Filas
		echo "<tbody>";
		foreach($datos as $fila){
			echo "<tr>";
			foreach($fila as $valor){
				echo "<td>".htmlspecialchars((string)$valor)."</td>";
			}
			echo "</tr>";
		}
		echo "</tbody>";
		echo "</table>";
	}
?>
