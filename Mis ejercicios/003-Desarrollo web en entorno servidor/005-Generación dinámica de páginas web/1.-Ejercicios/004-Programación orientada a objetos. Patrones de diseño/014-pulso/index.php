<?php
	session_start();
	include "includes/principio.php";
?>
<?php if(isset($_SESSION['llave'])){ ?>
<?php
	// Tablas que puede ver el rol del usuario
	$db = new SQLite3('gimnasio.db');
	$consulta = $db->prepare("SELECT tabla FROM roles WHERE idrol = :rol");
	$consulta->bindValue(':rol', $_SESSION['rol']);
	$result = $consulta->execute();
	$tablas = [];
	while($fila = $result->fetchArray(SQLITE3_ASSOC)){
		$tablas[] = $fila['tabla'];
	}
	$db->close();

	// Tabla y acción pedidas, siempre validadas
	$tabla = (isset($_GET['tabla']) && in_array($_GET['tabla'], $tablas)) ? $_GET['tabla'] : $tablas[0];
	$accion = isset($_GET['accion']) ? $_GET['accion'] : "listar";
?>
<header>
	<div class="marca"><strong>PULSO</strong><span>Gimnasio Velnora</span></div>
	<div class="usuario"><span><?php echo htmlspecialchars($_SESSION['nombre']); ?></span><a href="matarsesion.php">Cerrar sesión</a></div>
</header>
<main>
	<nav>
		<div class="nav-titulo">Gestión</div>
		<?php include "includes/navegacion.php"; ?>
	</nav>
	<section>
		<div class="titulo-pagina">
			<div>
				<h1><?php echo htmlspecialchars($tabla); ?></h1>
				<p>Registros de <?php echo htmlspecialchars($tabla); ?> del gimnasio.</p>
			</div>
			<?php if($accion == "listar"){ ?>
				<a class="boton boton-primario" href="?tabla=<?php echo urlencode($tabla); ?>&accion=nuevo">+ Añadir</a>
			<?php }else{ ?>
				<a class="boton" href="?tabla=<?php echo urlencode($tabla); ?>">Volver al listado</a>
			<?php } ?>
		</div>
		<?php
			if($accion == "nuevo" || $accion == "editar"){
				include "includes/formulario.php";
			}else{
				include "includes/tabla.php";
			}
		?>
	</section>
</main>
<?php }else{ include "includes/login.php"; } ?>
<?php include "includes/final.php"; ?>
