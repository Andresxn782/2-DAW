<?php $aviso = $_SESSION['aviso'] ?? ''; unset($_SESSION['aviso']); ?>
<!doctype html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Diario de entrenamiento · Velnora</title>
		<link rel="stylesheet" href="css/estilo.css">
	</head>
	<body>
		<header>
			<a class="logo" href="index.php">Velnora <span>diario de entrenamiento</span></a>
			<nav>
				<a href="index.php">Entrenos</a>
				<a href="editar.php">+ Apuntar entreno</a>
				<a href="importar.php">Importar CSV</a>
			</nav>
			<span class="almacen">Datos en: <?= e(strtoupper($config['almacen'])) ?></span>
		</header>
		<main>
			<?php if($aviso){ ?><div class="aviso"><?= e($aviso) ?></div><?php } ?>
