<?php
	include "includes/config.php";
	include "includes/funciones.php";
	exigirRol("socio");

	$yo = $_SESSION['usuario']['id'];
	$mensaje = "";

	// Reservar o cancelar (siempre por POST y con token)
	if($_SERVER['REQUEST_METHOD'] == "POST" && tokenValido()){
		$clase = (int)($_POST['clase'] ?? 0);
		if(isset($_POST['reservar'])){
			if(plazasLibres($db, $clase) > 0){
				$consulta = $db->prepare("INSERT OR IGNORE INTO reservas (usuario, clase) VALUES (:u, :c)");
				$consulta->bindValue(':u', $yo);
				$consulta->bindValue(':c', $clase);
				$consulta->execute();
				$mensaje = "Reserva hecha.";
			}else{
				$mensaje = "Esa clase está completa.";
			}
		}else if(isset($_POST['cancelar'])){
			$consulta = $db->prepare("DELETE FROM reservas WHERE usuario = :u AND clase = :c");
			$consulta->bindValue(':u', $yo);
			$consulta->bindValue(':c', $clase);
			$consulta->execute();
			$mensaje = "Reserva cancelada.";
		}
		// Guardo el mensaje en la sesión y redirijo, así F5 no repite la acción
		$_SESSION['mensaje'] = $mensaje;
		header("Location: clases.php");
		exit();
	}

	$consulta = $db->prepare("SELECT c.*, u.nombre AS monitor_nombre,
		(SELECT COUNT(*) FROM reservas r WHERE r.clase = c.id) AS ocupadas,
		(SELECT COUNT(*) FROM reservas r WHERE r.clase = c.id AND r.usuario = :yo) AS mia
		FROM clases c LEFT JOIN usuarios u ON u.id = c.monitor ORDER BY c.id");
	$consulta->bindValue(':yo', $yo);
	$clases = $consulta->execute();

	$mensaje = $_SESSION['mensaje'] ?? "";
	unset($_SESSION['mensaje']);
	include "includes/cabecera.php";
?>
<h1>Clases de esta semana</h1>
<?php if($mensaje){ ?><div class="aviso ok"><?php echo e($mensaje); ?></div><?php } ?>
<div class="rejilla">
	<?php while($c = $clases->fetchArray(SQLITE3_ASSOC)){ $libres = $c['plazas'] - $c['ocupadas']; ?>
		<div class="caja clase <?php echo $c['mia'] ? 'reservada' : ''; ?>">
			<h2><?php echo e($c['nombre']); ?></h2>
			<p><?php echo e($c['dia']); ?> a las <?php echo e($c['hora']); ?> · <?php echo e($c['monitor_nombre']); ?></p>
			<p class="plazas"><?php echo $libres; ?> de <?php echo $c['plazas']; ?> plazas libres</p>
			<form method="POST">
				<input type="hidden" name="token" value="<?php echo token(); ?>">
				<input type="hidden" name="clase" value="<?php echo $c['id']; ?>">
				<?php if($c['mia']){ ?>
					<button class="secundario" name="cancelar">Cancelar reserva</button>
				<?php }else if($libres > 0){ ?>
					<button name="reservar">Reservar</button>
				<?php }else{ ?>
					<button disabled>Completa</button>
				<?php } ?>
			</form>
		</div>
	<?php } ?>
</div>
<?php include "includes/pie.php"; ?>
