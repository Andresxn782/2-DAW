<?php $aviso = $_SESSION['aviso'] ?? null; unset($_SESSION['aviso']); ?>
<!doctype html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?= $vista->e($config['nombre']) ?></title>
		<link rel="stylesheet" href="css/estilo.css">
		<style>:root{--marca:<?= $vista->e($config['color']) ?>;}</style>
	</head>
	<body>
		<header>
			<a class="logo" href="<?= $vista->url('catalogo') ?>"><?= $vista->e($config['nombre']) ?></a>
			<span class="eslogan"><?= $vista->e($config['eslogan']) ?></span>
			<nav>
				<a href="<?= $vista->url('catalogo') ?>">Catálogo</a>
				<a href="<?= $vista->url('carrito') ?>">Carrito (<?= $carrito->unidades() ?>)</a>
				<?php if($_SESSION['admin'] ?? false){ ?>
					<a href="<?= $vista->url('admin') ?>">Administrar</a>
					<a href="<?= $vista->url('salir') ?>">Salir</a>
				<?php }else{ ?>
					<a href="<?= $vista->url('login') ?>">Acceso tienda</a>
				<?php } ?>
			</nav>
		</header>
		<main>
			<?php if($aviso){ ?><div class="aviso <?= $vista->e($aviso[1]) ?>"><?= $vista->e($aviso[0]) ?></div><?php } ?>
			<?= $contenido ?>
		</main>
		<footer>Gimnasio Velnora · Proyecto de la unidad 5 de Andres Ruiz</footer>
	</body>
</html>
