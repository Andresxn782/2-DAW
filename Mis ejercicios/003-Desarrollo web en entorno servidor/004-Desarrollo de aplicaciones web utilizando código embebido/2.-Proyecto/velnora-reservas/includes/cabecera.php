<!doctype html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Velnora Reservas</title>
		<link rel="stylesheet" href="css/estilo.css">
	</head>
	<body class="<?php echo tema(); ?>">
		<header>
			<div class="marca">VELNORA <span>reservas</span></div>
			<?php if(usuarioActual()){ ?>
				<nav>
					<?php if($_SESSION['usuario']['rol'] == "monitor"){ ?>
						<a href="monitor.php">Mis clases</a>
					<?php }else{ ?>
						<a href="clases.php">Clases</a>
					<?php } ?>
					<a href="preferencias.php">Preferencias</a>
					<span class="quien"><?php echo e($_SESSION['usuario']['nombre']); ?> (<?php echo e($_SESSION['usuario']['rol']); ?>)</span>
					<a href="logout.php">Salir</a>
				</nav>
			<?php } ?>
		</header>
		<main>
