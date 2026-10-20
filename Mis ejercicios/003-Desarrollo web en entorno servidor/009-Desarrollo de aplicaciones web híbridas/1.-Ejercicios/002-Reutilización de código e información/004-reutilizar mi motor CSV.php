<?php
	// Reutilización de código: no copio la clase, la incluyo desde la unidad 6.
	// Si mejoro VelnoraCSV allí, este ejercicio se beneficia sin tocarlo.
	require __DIR__."/../../../006-Utilización de técnicas de acceso a datos/1.-Ejercicios/002-Recuperación y edición de información/003-minimotor/VelnoraCSV.php";

	// Trabajo en una carpeta propia para no tocar los datos de la unidad 6
	$motor = new VelnoraCSV(__DIR__."/datos");
	$motor->crearBaseDatos("reutilizado");
	$motor->borrarTabla("reutilizado", "suplementos");
	$motor->crearTabla("reutilizado", "suplementos", ["id", "nombre", "precio"]);
	$motor->insertar("reutilizado", "suplementos", ["id" => 1, "nombre" => "Proteína de suero", "precio" => "29.90"]);
	$motor->insertar("reutilizado", "suplementos", ["id" => 2, "nombre" => "Creatina", "precio" => "19.50"]);
	$motor->insertar("reutilizado", "suplementos", ["id" => 3, "nombre" => "Barritas de avena", "precio" => "12.00"]);

	echo "<h1>Suplementos (guardados con VelnoraCSV de la unidad 6)</h1><ul>";
	foreach ($motor->listar("reutilizado", "suplementos") as $fila) {
		echo "<li>".htmlspecialchars($fila['nombre'])." · ".$fila['precio']." €</li>";
	}
	echo "</ul>";
?>
