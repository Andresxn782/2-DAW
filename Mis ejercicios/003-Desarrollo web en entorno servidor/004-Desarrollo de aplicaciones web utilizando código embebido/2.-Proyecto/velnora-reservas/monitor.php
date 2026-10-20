<?php
	include "includes/config.php";
	include "includes/funciones.php";
	exigirRol("monitor");

	$yo = $_SESSION['usuario']['id'];
	$errores = [];
	$dias = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];

	// Crear una clase nueva
	if($_SERVER['REQUEST_METHOD'] == "POST" && tokenValido()){
		$nombre = trim($_POST['nombre'] ?? "");
		$dia = $_POST['dia'] ?? "";
		$hora = $_POST['hora'] ?? "";
		$plazas = (int)($_POST['plazas'] ?? 0);
		if($nombre == ""){ $errores[] = "Pon un nombre a la clase."; }
		if(!in_array($dia, $dias)){ $errores[] = "Elige un día."; }
		if(!preg_match('/^\d{2}:\d{2}$/', $hora)){ $errores[] = "La hora debe ser HH:MM."; }
		if($plazas < 1 || $plazas > 40){ $errores[] = "Las plazas van de 1 a 40."; }
		if(count($errores) == 0){
			$consulta = $db->prepare("INSERT INTO clases (nombre, dia, hora, plazas, monitor) VALUES (:n, :d, :h, :p, :m)");
			$consulta->bindValue(':n', $nombre);
			$consulta->bindValue(':d', $dia);
			$consulta->bindValue(':h', $hora);
			$consulta->bindValue(':p', $plazas);
			$consulta->bindValue(':m', $yo);
			$consulta->execute();
			header("Location: monitor.php");
			exit();
		}
	}

	$consulta = $db->prepare("SELECT * FROM clases WHERE monitor = :yo ORDER BY id");
	$consulta->bindValue(':yo', $yo);
	$clases = $consulta->execute();

	$apuntados = $db->prepare("SELECT u.nombre FROM reservas r JOIN usuarios u ON u.id = r.usuario WHERE r.clase = :c ORDER BY u.nombre");
	include "includes/cabecera.php";
?>
<h1>Mis clases</h1>
<div class="rejilla">
	<?php while($c = $clases->fetchArray(SQLITE3_ASSOC)){ ?>
		<div class="caja clase">
			<h2><?php echo e($c['nombre']); ?></h2>
			<p><?php echo e($c['dia']); ?> a las <?php echo e($c['hora']); ?> · <?php echo $c['plazas']; ?> plazas</p>
			<strong>Apuntados:</strong>
			<ul>
				<?php
					$apuntados->bindValue(':c', $c['id']);
					$lista = $apuntados->execute();
					$hay = false;
					while($a = $lista->fetchArray(SQLITE3_ASSOC)){
						$hay = true;
						echo "<li>".e($a['nombre'])."</li>";
					}
					$apuntados->reset();
					if(!$hay){ echo "<li>Nadie todavía</li>"; }
				?>
			</ul>
		</div>
	<?php } ?>
</div>
<div class="caja estrecha">
	<h2>Nueva clase</h2>
	<?php foreach($errores as $error){ ?><div class="aviso error"><?php echo $error; ?></div><?php } ?>
	<form method="POST">
		<input type="hidden" name="token" value="<?php echo token(); ?>">
		<label>Nombre</label>
		<input type="text" name="nombre" required>
		<label>Día</label>
		<select name="dia"><?php foreach($dias as $d){ echo "<option>".$d."</option>"; } ?></select>
		<label>Hora</label>
		<input type="time" name="hora" required>
		<label>Plazas</label>
		<input type="number" name="plazas" min="1" max="40" value="10" required>
		<button type="submit">Crear clase</button>
	</form>
</div>
<?php include "includes/pie.php"; ?>
