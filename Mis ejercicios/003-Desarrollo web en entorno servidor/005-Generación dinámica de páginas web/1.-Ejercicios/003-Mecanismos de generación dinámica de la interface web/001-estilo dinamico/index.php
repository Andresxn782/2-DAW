<?php include "config.php"; ?>
<!doctype html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<title><?php echo $nombre; ?></title>
		<style>
			:root{
				--colorcorporativo:<?php echo $color; ?>;
			}
			html,body{padding:0;margin:0;width:100%;height:100%;font-family:Arial, sans-serif;}
			body{display:flex;flex-direction:column;}
			header{background:var(--colorcorporativo);color:white;padding:15px;font-size:20px;}
			main{display:flex;flex:1;}
			nav{background:var(--colorcorporativo);opacity:0.85;color:white;flex:1;padding:15px;}
			nav a{display:block;color:white;margin-bottom:8px;}
			section{flex:5;padding:20px;}
		</style>
	</head>
	<body>
		<header><?php echo $nombre; ?> | gimnasio</header>
		<main>
			<nav>
				<a href="#">Socios</a>
				<a href="#">Clases</a>
				<a href="#">Material</a>
			</nav>
			<section>
				<h1>Bienvenido</h1>
				<p>El color corporativo (<?php echo $color; ?>) sale de config.php.</p>
			</section>
		</main>
	</body>
</html>
