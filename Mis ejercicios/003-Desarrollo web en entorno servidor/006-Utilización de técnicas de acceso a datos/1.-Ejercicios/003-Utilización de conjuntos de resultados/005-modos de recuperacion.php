<?php
	// Un mismo conjunto de resultados se puede recuperar de muchas formas con PDO
	$db = new PDO('sqlite:socios.db');
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT id, nombre, apellidos, plan FROM socios WHERE plan = :plan";

	echo "<h2>1. fetch() en un bucle, como array asociativo</h2>";
	$consulta = $db->prepare($sql);
	$consulta->execute([':plan' => 'Crossfit']);
	while($fila = $consulta->fetch(PDO::FETCH_ASSOC)){
		echo $fila['nombre']." ".$fila['apellidos']."<br>";
	}

	echo "<h2>2. fetchAll(): todo de golpe en un array</h2>";
	$consulta->execute([':plan' => 'Yoga']);
	$todos = $consulta->fetchAll(PDO::FETCH_ASSOC);
	echo count($todos)." socios de yoga: ".implode(", ", array_column($todos, 'nombre'))."<br>";

	echo "<h2>3. FETCH_OBJ: cada fila es un objeto</h2>";
	$consulta->execute([':plan' => 'Spinning']);
	foreach($consulta->fetchAll(PDO::FETCH_OBJ) as $socio){
		echo $socio->nombre." (id ".$socio->id.")<br>";
	}

	echo "<h2>4. FETCH_CLASS: cada fila es un objeto de mi clase</h2>";
	class Socio {
		public $id;
		public $nombre;
		public $apellidos;
		public $plan;
		public function nombreCompleto(){
			return $this->nombre." ".$this->apellidos;
		}
		public function iniciales(){
			return mb_substr($this->nombre, 0, 1).mb_substr($this->apellidos, 0, 1);
		}
	}
	$consulta->execute([':plan' => 'Musculación']);
	foreach($consulta->fetchAll(PDO::FETCH_CLASS, 'Socio') as $socio){
		echo $socio->iniciales()." · ".$socio->nombreCompleto()."<br>";
	}

	echo "<h2>5. FETCH_KEY_PAIR: id => nombre (ideal para un desplegable)</h2>";
	$pares = $db->query("SELECT id, nombre FROM socios ORDER BY nombre LIMIT 5")->fetchAll(PDO::FETCH_KEY_PAIR);
	print_r($pares);

	echo "<h2>6. FETCH_COLUMN: una sola columna</h2>";
	echo implode(", ", $db->query("SELECT DISTINCT plan FROM socios ORDER BY plan")->fetchAll(PDO::FETCH_COLUMN));

	echo "<h2>7. fetchColumn(): un único valor</h2>";
	echo "Total de socios: ".$db->query("SELECT COUNT(*) FROM socios")->fetchColumn();
?>
