<?php
	// El servidor saca todos los ejercicios de la base de datos
	$db = new PDO("sqlite:".__DIR__."/ejercicios.db");

	$resultado = $db->query("SELECT nombre, grupo FROM ejercicios ORDER BY grupo, nombre");

	$grupoAnterior = "";
	while ($fila = $resultado->fetch(PDO::FETCH_ASSOC)) {
		if ($fila['grupo'] !== $grupoAnterior) {
			echo "<h3>".htmlspecialchars($fila['grupo'])."</h3>";
			$grupoAnterior = $fila['grupo'];
		}
		echo htmlspecialchars($fila['nombre'])."<br>";
	}
?>
