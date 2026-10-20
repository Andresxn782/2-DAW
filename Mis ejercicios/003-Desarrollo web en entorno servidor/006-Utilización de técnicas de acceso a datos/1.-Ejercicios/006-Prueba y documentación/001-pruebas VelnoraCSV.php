<?php
	require_once __DIR__."/../002-Recuperación y edición de información/003-minimotor/VelnoraCSV.php";

	$carpeta = sys_get_temp_dir()."/velnora_pruebas_".uniqid();
	$db = new VelnoraCSV($carpeta);
	$bien = 0;
	$mal = 0;
	function comprobar($que, $resultado){
		global $bien, $mal;
		$resultado ? $bien++ : $mal++;
		echo ($resultado ? "OK     " : "FALLO  ").$que.PHP_EOL;
	}

	comprobar("Crea la base de datos", $db->crearBaseDatos("prueba") && is_dir($carpeta."/prueba"));
	comprobar("Crea la tabla", $db->crearTabla("prueba", "socios", ["id", "nombre", "nota"]));
	comprobar("No sobrescribe una tabla que ya existe", $db->crearTabla("prueba", "socios", ["otra"]) === false);
	comprobar("Lee las columnas", $db->columnas("prueba", "socios") === ["id", "nombre", "nota"]);

	$db->insertar("prueba", "socios", ["id" => 1, "nombre" => "Lucía", "nota" => "Tiene \"bono\", y descuento"]);
	$db->insertar("prueba", "socios", ["id" => 2, "nombre" => "Carlos", "nota" => "Línea 1\nLínea 2"]);
	$db->insertar("prueba", "socios", ["id" => 3, "nombre" => "Nerea"]);
	$filas = $db->listar("prueba", "socios");
	comprobar("Inserta 3 filas", count($filas) === 3);
	comprobar("Respeta comas y comillas", $filas[0]['nota'] === "Tiene \"bono\", y descuento");
	comprobar("Respeta saltos de línea", $filas[1]['nota'] === "Línea 1\nLínea 2");
	comprobar("Una columna que falta se guarda vacía", $filas[2]['nota'] === "");

	comprobar("Busca por columna", count($db->buscar("prueba", "socios", "nombre", "Carlos")) === 1);
	comprobar("Actualiza 1 fila", $db->actualizar("prueba", "socios", "id", 3, ["nota" => "Nueva"]) === 1);
	comprobar("El cambio se guarda", $db->buscar("prueba", "socios", "id", 3)[0]['nota'] === "Nueva");
	comprobar("Actualizar algo que no existe cambia 0", $db->actualizar("prueba", "socios", "id", 99, ["nota" => "x"]) === 0);
	comprobar("Elimina 1 fila", $db->eliminar("prueba", "socios", "id", 1) === 1);
	comprobar("Quedan 2", count($db->listar("prueba", "socios")) === 2);
	comprobar("Lista las tablas", $db->tablas("prueba") === ["socios"]);

	try{
		$db->listar("prueba", "../../etc/passwd");
		comprobar("Rechaza nombres con ../", false);
	}catch(InvalidArgumentException $e){
		comprobar("Rechaza nombres con ../", true);
	}

	comprobar("Borra la tabla", $db->borrarTabla("prueba", "socios") && $db->tablas("prueba") === []);
	rmdir($carpeta."/prueba");
	rmdir($carpeta);

	echo PHP_EOL.$bien." correctas, ".$mal." fallos".PHP_EOL;
	exit($mal ? 1 : 0);
?>
