<?php
	// Genera accesos.csv: cada fila es una visita al gimnasio.
	// fecha;hora;minutos;socio;plan
	// Simula horas punta (7-9 y 18-21), menos gente el fin de semana,
	// el pico de enero (propósitos de año nuevo) y la bajada de agosto.
	// Uso:  php "002-generar accesos.php"
	if (PHP_SAPI !== "cli") {
		exit("Ejecútalo en la consola: php \"002-generar accesos.php\"");
	}
	mt_srand(2026);
	set_time_limit(0);

	$planes = ["Musculación", "Spinning", "Crossfit", "Yoga", "Pilates", "Natación"];
	$pesoPlanes = [40, 15, 15, 10, 10, 10];
	$totalSocios = 1800;

	// Plan de cada socio, repartido según los pesos, y meses en los que está de alta:
	// muchos se apuntan en enero y un tercio se da de baja a los pocos meses
	$planSocio = [];
	$altaSocio = [];
	$bajaSocio = [];
	for ($socio = 1; $socio <= $totalSocios; $socio++) {
		$altaSocio[$socio] = mt_rand(1, 100) <= 45 ? 1 : mt_rand(1, 9);
		$bajaSocio[$socio] = mt_rand(1, 100) <= 33 ? $altaSocio[$socio] + mt_rand(2, 4) : 13;
		$tirada = mt_rand(1, 100);
		$acumulado = 0;
		foreach ($pesoPlanes as $i => $peso) {
			$acumulado += $peso;
			if ($tirada <= $acumulado) {
				$planSocio[$socio] = $planes[$i];
				break;
			}
		}
	}

	// Cuánta gente viene a cada hora (6 a 22) en un día normal
	$pesoHoras = [6 => 3, 7 => 9, 8 => 10, 9 => 6, 10 => 4, 11 => 3, 12 => 3, 13 => 4, 14 => 3, 15 => 3, 16 => 4, 17 => 7, 18 => 11, 19 => 12, 20 => 10, 21 => 5, 22 => 2];
	$horas = [];
	foreach ($pesoHoras as $hora => $peso) {
		$horas = array_merge($horas, array_fill(0, $peso, $hora));
	}
	$factorMes = [1 => 1.35, 2 => 1.2, 3 => 1.1, 4 => 1.0, 5 => 1.0, 6 => 0.9, 7 => 0.75, 8 => 0.55, 9 => 1.1];
	$factorDia = [1 => 1.1, 2 => 1.05, 3 => 1.0, 4 => 1.0, 5 => 0.85, 6 => 0.6, 7 => 0.45];	// lunes a domingo

	$archivo = fopen(__DIR__."/accesos.csv", "w");
	fwrite($archivo, "fecha;hora;minutos;socio;plan\n");
	$filas = 0;
	$dia = new DateTime("2026-01-01");
	$fin = new DateTime("2026-09-30");
	while ($dia <= $fin) {
		$mes = (int)$dia->format("n");
		// Socios de alta ese mes (lo recalculo cada día, pero es barato)
		$activos = [];
		for ($socio = 1; $socio <= $totalSocios; $socio++) {
			if ($altaSocio[$socio] <= $mes && $mes < $bajaSocio[$socio]) {
				$activos[] = $socio;
			}
		}
		$visitas = (int)round(1000 * $factorMes[$mes] * $factorDia[(int)$dia->format("N")] * mt_rand(90, 110) / 100);
		for ($v = 0; $v < $visitas; $v++) {
			$socio = $activos[mt_rand(0, count($activos) - 1)];
			$hora = $horas[mt_rand(0, count($horas) - 1)];
			fwrite($archivo, $dia->format("Y-m-d").";".sprintf("%02d:%02d", $hora, mt_rand(0, 59)).";".mt_rand(30, 110).";".$socio.";".$planSocio[$socio]."\n");
			$filas++;
		}
		$dia->modify("+1 day");
	}
	fclose($archivo);
	echo "Generadas ".number_format($filas, 0, ",", ".")." visitas en accesos.csv (".round(filesize(__DIR__."/accesos.csv") / 1048576, 1)." MB)\n";
?>
