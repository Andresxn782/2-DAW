<?php
	// Horario de Velnora. Devuelve las clases de un día en HTML o en JSON.
	//   clases.php?dia=Lunes&formato=html
	//   clases.php?dia=Lunes&formato=json
	$horario = [
		["dia" => "Lunes", "hora" => "07:30", "nombre" => "Running", "monitor" => "Andres Ruiz", "plazas" => 20],
		["dia" => "Lunes", "hora" => "18:00", "nombre" => "Spinning", "monitor" => "Andres Ruiz", "plazas" => 18],
		["dia" => "Lunes", "hora" => "19:00", "nombre" => "Yoga", "monitor" => "Marta Lozano", "plazas" => 0],
		["dia" => "Martes", "hora" => "19:30", "nombre" => "Crossfit", "monitor" => "Andres Ruiz", "plazas" => 4],
		["dia" => "Martes", "hora" => "20:30", "nombre" => "Boxeo", "monitor" => "Iván Rey", "plazas" => 10],
		["dia" => "Miércoles", "hora" => "09:00", "nombre" => "Pilates", "monitor" => "Marta Lozano", "plazas" => 6],
		["dia" => "Jueves", "hora" => "18:00", "nombre" => "Spinning", "monitor" => "Andres Ruiz", "plazas" => 12],
		["dia" => "Jueves", "hora" => "19:30", "nombre" => "Crossfit", "monitor" => "Andres Ruiz", "plazas" => 0],
		["dia" => "Viernes", "hora" => "20:00", "nombre" => "Boxeo", "monitor" => "Iván Rey", "plazas" => 8]
	];

	$dia = $_GET['dia'] ?? "Lunes";
	$clases = array_values(array_filter($horario, function ($clase) use ($dia) {
		return $clase['dia'] === $dia;
	}));

	if (($_GET['formato'] ?? "html") === "json") {
		header("Content-Type: application/json; charset=UTF-8");
		echo json_encode($clases, JSON_UNESCAPED_UNICODE);
		exit;
	}

	// Formato HTML: el servidor manda el trozo de página ya construido
	if (count($clases) === 0) {
		echo "<p>No hay clases el ".htmlspecialchars($dia).".</p>";
	}
	foreach ($clases as $clase) {
		$completa = $clase['plazas'] === 0;
		echo '<div class="clase'.($completa ? ' completa' : '').'">';
		echo '<strong>'.$clase['hora'].'</strong> '.htmlspecialchars($clase['nombre']);
		echo ' <em>con '.htmlspecialchars($clase['monitor']).'</em> · ';
		echo $completa ? "Completa" : $clase['plazas']." plazas";
		echo '</div>';
	}
?>
