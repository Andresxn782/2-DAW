<?php
	$db = new PDO('sqlite:gimnasio.db');
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$planes = ["Mensual", "Trimestral", "Anual"];
	$mensaje = "";

	// UPDATE con consulta preparada
	if($_SERVER['REQUEST_METHOD'] === 'POST'){
		$nombre = trim($_POST['nombre'] ?? '');
		$plan = in_array($_POST['plan'] ?? '', $planes) ? $_POST['plan'] : 'Mensual';
		$consulta = $db->prepare("UPDATE socios SET nombre = :nombre, plan = :plan WHERE id = :id");
		$consulta->execute([':nombre' => $nombre, ':plan' => $plan, ':id' => (int)$_POST['id']]);
		$mensaje = "Filas actualizadas: ".$consulta->rowCount();
	}

	$id = (int)($_GET['id'] ?? $_POST['id'] ?? 1);
	$consulta = $db->prepare("SELECT * FROM socios WHERE id = :id");
	$consulta->execute([':id' => $id]);
	$socio = $consulta->fetch(PDO::FETCH_ASSOC);
	$socios = $db->query("SELECT id, nombre FROM socios ORDER BY id")->fetchAll(PDO::FETCH_KEY_PAIR);
?>
<!doctype html>
<html lang="es">
	<head><meta charset="UTF-8"><title>Editar socio</title>
	<style>body{font-family:Arial, sans-serif;margin:30px;} label{display:block;margin-top:10px;} .ok{color:green;}</style></head>
	<body>
		<p>
			<?php foreach($socios as $sid => $snombre){ ?>
				<a href="?id=<?php echo $sid; ?>"><?php echo htmlspecialchars($snombre); ?></a> ·
			<?php } ?>
		</p>
		<?php if($mensaje){ ?><p class="ok"><?php echo $mensaje; ?></p><?php } ?>
		<?php if($socio){ ?>
			<form method="POST">
				<input type="hidden" name="id" value="<?php echo $socio['id']; ?>">
				<label>Nombre</label>
				<input type="text" name="nombre" value="<?php echo htmlspecialchars($socio['nombre']); ?>" required>
				<label>Plan</label>
				<select name="plan">
					<?php foreach($planes as $p){ ?>
						<option <?php echo $p == $socio['plan'] ? "selected" : ""; ?>><?php echo $p; ?></option>
					<?php } ?>
				</select>
				<p><button>Guardar</button></p>
			</form>
		<?php }else{ ?>
			<p>Ese socio no existe.</p>
		<?php } ?>
	</body>
</html>
