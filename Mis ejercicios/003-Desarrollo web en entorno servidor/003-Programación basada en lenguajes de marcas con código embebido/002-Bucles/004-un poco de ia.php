<!doctype html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Calendario de entrenamientos 2026</title>
	<style>
		*{box-sizing:border-box;}
		body{
			margin:0;
			padding:30px;
			background:#0f172a;
			font-family:Verdana, sans-serif;
			color:#e2e8f0;
		}
		h1{text-align:center;color:#2dd4bf;margin-bottom:10px;}
		.leyenda{text-align:center;margin-bottom:30px;font-size:13px;}
		.leyenda span{display:inline-block;padding:3px 10px;border-radius:20px;margin:0 5px;}
		.calendario{
			display:grid;
			grid-template-columns:repeat(4, 1fr);
			gap:20px;
			max-width:1300px;
			margin:auto;
		}
		.mes{background:#1e293b;border-radius:14px;padding:15px;}
		.mes h2{text-align:center;margin:0 0 10px 0;font-size:18px;}
		.dias-semana, .dias{display:grid;grid-template-columns:repeat(7, 1fr);gap:3px;}
		.nombre-dia{text-align:center;font-size:11px;color:#94a3b8;padding:4px 0;}
		.dia{
			min-height:34px;
			border-radius:6px;
			display:flex;
			align-items:center;
			justify-content:center;
			font-size:13px;
		}
		.entreno{background:#134e4a;color:#5eead4;}
		.descanso{color:#64748b;}
		.hoy{outline:2px solid #f59e0b;font-weight:bold;}
		@media(max-width:1000px){.calendario{grid-template-columns:repeat(2, 1fr);}}
		@media(max-width:600px){.calendario{grid-template-columns:1fr;}}
	</style>
</head>
<body>

<h1>Calendario de entrenamientos 2026</h1>
<p class="leyenda">
	<span class="entreno">Lunes, miércoles y viernes: entreno</span>
	<span class="descanso">Fin de semana: descanso</span>
	<span class="hoy">Hoy</span>
</p>

<div class="calendario">
<?php
$anyo = 2026;
$nombresMeses = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
$hoy = date("Y-m-d");

for($mes = 1; $mes <= 12; $mes++){
	// Cuántos días tiene el mes y en qué día de la semana empieza (1 = lunes, 7 = domingo)
	$diasMes = date("t", mktime(0, 0, 0, $mes, 1, $anyo));
	$primerDia = date("N", mktime(0, 0, 0, $mes, 1, $anyo));

	echo "<section class='mes'>";
	echo "<h2>".$nombresMeses[$mes - 1]."</h2>";
	echo "<div class='dias-semana'>";
	foreach(["L","M","X","J","V","S","D"] as $letra){
		echo "<div class='nombre-dia'>".$letra."</div>";
	}
	echo "</div>";
	echo "<div class='dias'>";

	// Huecos vacíos hasta el día 1
	for($i = 1; $i < $primerDia; $i++){
		echo "<div></div>";
	}

	for($dia = 1; $dia <= $diasMes; $dia++){
		$diaSemana = date("N", mktime(0, 0, 0, $mes, $dia, $anyo));
		$clase = "dia";
		if($diaSemana == 1 || $diaSemana == 3 || $diaSemana == 5){
			$clase .= " entreno";
		}else if($diaSemana >= 6){
			$clase .= " descanso";
		}
		if(sprintf("%04d-%02d-%02d", $anyo, $mes, $dia) == $hoy){
			$clase .= " hoy";
		}
		echo "<div class='$clase'>$dia</div>";
	}

	echo "</div>";
	echo "</section>";
}
?>
</div>

</body>
</html>
