<?php
	// Genera el repositorio de vídeos de Velnora TV (entrenos.json).
	// Uso:  php generar.php
	// mt_srand con semilla fija: siempre salen los mismos datos.
	mt_srand(2026);

	$categorias = [
		"Fuerza" => ["Full body", "Tren superior", "Piernas de acero", "Espalda fuerte", "Empuje y tracción"],
		"Cardio" => ["HIIT exprés", "Quemagrasa", "Intervalos en cinta", "Comba y saltos", "Cardio sin impacto"],
		"Yoga" => ["Saludo al sol", "Yoga para corredores", "Flexibilidad total", "Yoga restaurativo", "Equilibrio y respiración"],
		"Crossfit" => ["WOD de iniciación", "AMRAP 20", "EMOM de potencia", "Chipper Velnora", "Halterofilia básica"],
		"Pilates" => ["Core de pilates", "Pilates con fitball", "Suelo pélvico", "Pilates postural", "Pilates intenso"],
		"Movilidad" => ["Movilidad de cadera", "Hombros sanos", "Estiramientos de oficina", "Rodillas fuertes", "Calentamiento completo"]
	];
	$niveles = ["Iniciación", "Medio", "Avanzado"];
	$monitores = ["Andres Ruiz", "Marta Lozano", "Iván Rey", "Lucía Martín"];
	$frases = [
		"Una sesión pensada para hacer en casa sin apenas material.",
		"Trabajo por bloques con descansos cortos y mucha intensidad.",
		"Ideal para empezar la semana con energía.",
		"Explicamos cada ejercicio paso a paso para hacerlo con buena técnica.",
		"Termina con una vuelta a la calma y estiramientos."
	];

	$entrenos = [];
	$id = 0;
	foreach ($categorias as $categoria => $titulos) {
		foreach ($titulos as $titulo) {
			// Dos versiones de cada título: una normal y otra con número de parte
			foreach ([1, 2] as $parte) {
				$id++;
				$entrenos[] = [
					"id" => $id,
					"titulo" => $parte === 1 ? $titulo : $titulo." II",
					"categoria" => $categoria,
					"nivel" => $niveles[mt_rand(0, 2)],
					"minutos" => [15, 20, 25, 30, 40, 45, 60][mt_rand(0, 6)],
					"monitor" => $monitores[mt_rand(0, 3)],
					"descripcion" => $frases[mt_rand(0, 4)],
					"publicado" => date("Y-m-d", mktime(0, 0, 0, 1 + mt_rand(0, 9), 1 + mt_rand(0, 27), 2026))
				];
			}
		}
	}

	// Los más recientes primero
	usort($entrenos, function ($a, $b) {
		return [$b['publicado'], $a['id']] <=> [$a['publicado'], $b['id']];
	});

	file_put_contents(__DIR__."/entrenos.json", json_encode(["entrenos" => $entrenos], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
	echo "Generados ".count($entrenos)." vídeos en entrenos.json\n";
?>
