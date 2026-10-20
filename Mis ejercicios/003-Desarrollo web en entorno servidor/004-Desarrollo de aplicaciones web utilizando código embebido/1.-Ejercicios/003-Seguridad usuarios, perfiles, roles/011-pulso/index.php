<?php
	session_start();
	include "includes/principio.php";
?>
<?php if(isset($_SESSION['llave'])){ ?>
<?php
	// Solo las tablas que el rol del usuario puede ver
	$db = new SQLite3('gimnasio.db');
	$consulta = $db->prepare("SELECT tabla FROM roles WHERE idrol = :rol");
	$consulta->bindValue(':rol', $_SESSION['rol']);
	$result = $consulta->execute();
	$tablas = [];
	while ($fila = $result->fetchArray(SQLITE3_ASSOC)) {
		$tablas[] = $fila['tabla'];
	}
	$db->close();

	// Solo acepto una tabla de la lista; si no, la primera
	$tabla = (isset($_GET['tabla']) && in_array($_GET['tabla'], $tablas)) ? $_GET['tabla'] : $tablas[0];
?>
		<header>
			<h1>PULSO · VELNORA</h1>
			<span><?php echo $_SESSION['nombre']; ?> · <a href="matarsesion.php">Cerrar sesión</a></span>
		</header>
		<main>
			<nav>
				<?php include "includes/navegacion.php"; ?>
			</nav>
			<section>
				<?php include "includes/formulario.php"; ?>
				<?php include "includes/tabla.php"; ?>
			</section>
		</main>
<?php }else{ ?>
	<?php include "includes/login.php"; ?>
<?php } ?>
<?php include "includes/final.php"; ?>
