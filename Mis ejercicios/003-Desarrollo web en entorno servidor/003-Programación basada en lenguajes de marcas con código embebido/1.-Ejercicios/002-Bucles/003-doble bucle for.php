<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Plan de 12 semanas</title>
		<style>
			body{background:#0f172a;color:#e2e8f0;font-family:Verdana, sans-serif;padding:20px;}
			h2{color:#2dd4bf;font-size:16px;margin:20px 0 6px 0;}
			main{display:grid;grid-template-columns:repeat(7, 1fr);gap:6px;}
			.dia{background:#1e293b;border-radius:6px;height:40px;padding:6px;}
		</style>
	</head>
	<body>
		<?php
			// Bucle de fuera: las semanas del plan
			for($semana = 1;$semana <= 12;$semana++){
		?>
		<h2>Semana <?php echo $semana; ?></h2>
		<main>
			<?php
				// Bucle de dentro: los 7 días de cada semana
				for($dia = 1;$dia <= 7;$dia++){
					echo "<div class='dia'>".$dia."</div>";
				}
			?>
		</main>
		<?php
			}
		?>
	</body>
</html>
