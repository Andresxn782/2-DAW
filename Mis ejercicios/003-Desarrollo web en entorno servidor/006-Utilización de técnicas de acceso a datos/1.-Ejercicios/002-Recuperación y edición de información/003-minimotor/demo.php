<?php
	require_once "VelnoraCSV.php";

	// 1.-Conecto con el motor
	$db = new VelnoraCSV("datos");

	// 2.-CREATE DATABASE y CREATE TABLE (empiezo de cero cada vez que se ejecuta la demo)
	$db->crearBaseDatos("gimnasio");
	$db->borrarTabla("gimnasio", "clases");
	$db->crearTabla("gimnasio", "clases", ["id", "nombre", "monitor", "dia", "hora", "plazas"]);
	echo "Base de datos y tabla creadas<br>";

	// 3.-INSERT
	$db->insertar("gimnasio", "clases", ["id" => 1, "nombre" => "Spinning", "monitor" => "Marcos", "dia" => "Lunes", "hora" => "18:00", "plazas" => 12]);
	$db->insertar("gimnasio", "clases", ["id" => 2, "nombre" => "Yoga", "monitor" => "Ainhoa", "dia" => "Martes", "hora" => "10:00", "plazas" => 8]);
	$db->insertar("gimnasio", "clases", ["id" => 3, "nombre" => "Crossfit, nivel 1", "monitor" => "Marcos", "dia" => "Miércoles", "hora" => "19:30", "plazas" => 10]);
	echo "3 clases insertadas (una lleva coma en el nombre para ver que el CSV la entrecomilla)<br>";

	echo "<h2>SELECT * FROM clases</h2><pre>";
	print_r($db->listar("gimnasio", "clases"));
	echo "</pre>";

	echo "<h2>SELECT * FROM clases WHERE monitor = 'Marcos'</h2><pre>";
	print_r(array_column($db->buscar("gimnasio", "clases", "monitor", "Marcos"), "nombre"));
	echo "</pre>";

	// 4.-UPDATE
	$n = $db->actualizar("gimnasio", "clases", "id", 2, ["hora" => "11:00", "plazas" => 10]);
	echo "<h2>UPDATE: ".$n." fila cambiada</h2><pre>";
	print_r($db->buscar("gimnasio", "clases", "id", 2));
	echo "</pre>";

	// 5.-DELETE
	$n = $db->eliminar("gimnasio", "clases", "id", 1);
	echo "<h2>DELETE: ".$n." fila borrada</h2>";
	echo "Quedan: ".implode(", ", array_column($db->listar("gimnasio", "clases"), "nombre"))."<br>";
	echo "Tablas de gimnasio: ".implode(", ", $db->tablas("gimnasio"));
?>
