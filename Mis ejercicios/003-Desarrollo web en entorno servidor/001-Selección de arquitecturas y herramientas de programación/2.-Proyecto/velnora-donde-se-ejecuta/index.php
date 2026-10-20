<?php
	// Todo lo que hay en este bloque se ejecuta en el SERVIDOR, antes de enviar la página
	date_default_timezone_set('Europe/Madrid');

	// Horario del gimnasio: lo guardo una sola vez y la página se genera sola
	$horario = [
		"Lunes" => "07:00 - 23:00",
		"Martes" => "07:00 - 23:00",
		"Miércoles" => "07:00 - 23:00",
		"Jueves" => "07:00 - 23:00",
		"Viernes" => "07:00 - 22:00",
		"Sábado" => "09:00 - 20:00",
		"Domingo" => "09:00 - 14:00"
	];
	$dias = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];
	$hoy = $dias[date('w')];
?>
<!doctype html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Velnora · ¿Dónde se ejecuta?</title>
		<style>
			body{margin:0;font-family:Verdana, sans-serif;background:#f1f5f9;color:#0f172a;}
			header{background:#0f766e;color:white;padding:20px 30px;}
			header h1{margin:0;font-size:22px;}
			main{max-width:900px;margin:20px auto;padding:0 16px;display:grid;grid-template-columns:1fr 1fr;gap:20px;}
			.caja{background:white;border-radius:10px;padding:20px;box-shadow:0 2px 6px rgba(0,0,0,0.1);}
			.servidor{border-top:5px solid #0f766e;}
			.cliente{border-top:5px solid #f59e0b;}
			.ancha{grid-column:1 / 3;}
			table{width:100%;border-collapse:collapse;}
			td{padding:6px;border-bottom:1px solid #e2e8f0;}
			.hoy{background:#ccfbf1;font-weight:bold;}
			.grande{font-size:28px;font-weight:bold;}
			@media (max-width:700px){main{grid-template-columns:1fr;}.ancha{grid-column:auto;}}
		</style>
	</head>
	<body>
		<header>
			<h1>Gimnasio Velnora · ¿Dónde se ejecuta cada cosa?</h1>
		</header>
		<main>
			<div class="caja servidor">
				<h2>Generado en el servidor (PHP)</h2>
				<p>Hora del servidor al crear la página:</p>
				<p class="grande"><?php echo date('H:i:s'); ?></p>
				<p>Esta hora no cambia hasta que recargues la página, porque PHP ya terminó de ejecutarse.</p>
			</div>
			<div class="caja cliente">
				<h2>Ejecutado en el cliente (JavaScript)</h2>
				<p>Hora de tu navegador, en directo:</p>
				<p class="grande" id="reloj">--:--:--</p>
				<p>Esta hora se actualiza sola cada segundo sin pedir nada al servidor.</p>
			</div>
			<div class="caja servidor ancha">
				<h2>Horario generado dinámicamente</h2>
				<table>
					<?php foreach($horario as $dia => $horas){ ?>
						<tr class="<?php echo ($dia == $hoy) ? 'hoy' : ''; ?>">
							<td><?php echo $dia; ?></td>
							<td><?php echo $horas; ?></td>
						</tr>
					<?php } ?>
				</table>
				<p>La fila marcada es hoy (<?php echo $hoy; ?>). La decide el servidor, no está escrita a mano en el HTML.</p>
			</div>
			<div class="caja servidor ancha">
				<h2>Quién ha atendido la petición</h2>
				<table>
					<tr><td>Servidor web</td><td><?php echo htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'desconocido'); ?></td></tr>
					<tr><td>Versión de PHP</td><td><?php echo phpversion(); ?></td></tr>
					<tr><td>Interfaz con el servidor</td><td><?php echo php_sapi_name(); ?></td></tr>
					<tr><td>Archivo ejecutado</td><td><?php echo htmlspecialchars($_SERVER['SCRIPT_NAME']); ?></td></tr>
					<tr><td>Tu navegador</td><td><?php echo htmlspecialchars($_SERVER['HTTP_USER_AGENT'] ?? 'desconocido'); ?></td></tr>
				</table>
			</div>
		</main>
		<script>
			// Esto lo ejecuta el NAVEGADOR: el servidor solo lo envía como texto
			function actualizarReloj(){
				document.getElementById("reloj").textContent = new Date().toLocaleTimeString("es-ES");
			}
			actualizarReloj();
			setInterval(actualizarReloj, 1000);
		</script>
	</body>
</html>
