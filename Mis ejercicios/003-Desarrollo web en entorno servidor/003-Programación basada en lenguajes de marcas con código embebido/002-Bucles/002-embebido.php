<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Reto 30 días</title>
		<style>
			body{background:#0f172a;color:#e2e8f0;font-family:Verdana, sans-serif;padding:20px;}
			h1{color:#2dd4bf;}
			main{display:grid;grid-template-columns:repeat(6, 1fr);gap:8px;}
			.dia{background:#1e293b;border-radius:8px;height:60px;padding:8px;border-left:4px solid #2dd4bf;}
		</style>
	</head>
	<body>
		<h1>Reto Velnora 30 días</h1>
		<main>
			<?php
				for($dia = 1;$dia <= 30;$dia++){
					echo "<div class='dia'>Día ".$dia."</div>";
				}
			?>
		</main>
	</body>
</html>
