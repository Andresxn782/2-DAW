<?php
	$db = new PDO('sqlite:gimnasio.db');
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$mensaje = "";

	// El borrado solo se hace por POST (un enlace GET lo podría lanzar cualquiera sin querer)
	if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar'])){
		$id = (int)$_POST['id'];
		$consulta = $db->prepare("SELECT COUNT(*) FROM pagos WHERE socio_id = :id");
		$consulta->execute([':id' => $id]);
		if($consulta->fetchColumn() > 0){
			// Tiene pagos: no lo borro para no perder la contabilidad, lo doy de baja
			$consulta = $db->prepare("UPDATE socios SET activo = 0 WHERE id = :id");
			$consulta->execute([':id' => $id]);
			$mensaje = "El socio tiene pagos, así que lo he dado de baja (borrado lógico).";
		}else{
			$consulta = $db->prepare("DELETE FROM socios WHERE id = :id");
			$consulta->execute([':id' => $id]);
			$mensaje = "Socio eliminado (".$consulta->rowCount()." fila).";
		}
	}

	// Pantalla de confirmación
	$confirmar = null;
	if(isset($_GET['borrar'])){
		$consulta = $db->prepare("SELECT * FROM socios WHERE id = :id");
		$consulta->execute([':id' => (int)$_GET['borrar']]);
		$confirmar = $consulta->fetch(PDO::FETCH_ASSOC);
	}
	$socios = $db->query("SELECT s.*, (SELECT COUNT(*) FROM pagos p WHERE p.socio_id = s.id) AS pagos FROM socios s ORDER BY s.id")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="es">
	<head><meta charset="UTF-8"><title>Eliminar socios</title>
	<style>body{font-family:Arial, sans-serif;margin:30px;} td, th{padding:6px 10px;border-bottom:1px solid #ddd;} .baja{color:#999;text-decoration:line-through;} .aviso{background:#fef3c7;padding:10px;}</style></head>
	<body>
		<?php if($mensaje){ ?><p class="aviso"><?php echo $mensaje; ?></p><?php } ?>
		<?php if($confirmar){ ?>
			<form method="POST" class="aviso">
				¿Seguro que quieres eliminar a <strong><?php echo htmlspecialchars($confirmar['nombre']); ?></strong>?
				<input type="hidden" name="id" value="<?php echo $confirmar['id']; ?>">
				<button name="confirmar" value="1">Sí, eliminar</button>
				<a href="?">Cancelar</a>
			</form>
		<?php } ?>
		<table>
			<tr><th>Socio</th><th>Plan</th><th>Pagos</th><th></th></tr>
			<?php foreach($socios as $s){ ?>
				<tr class="<?php echo $s['activo'] ? '' : 'baja'; ?>">
					<td><?php echo htmlspecialchars($s['nombre']); ?></td>
					<td><?php echo htmlspecialchars($s['plan']); ?></td>
					<td><?php echo $s['pagos']; ?></td>
					<td><?php if($s['activo']){ ?><a href="?borrar=<?php echo $s['id']; ?>">Eliminar</a><?php }else{ ?>De baja<?php } ?></td>
				</tr>
			<?php } ?>
		</table>
	</body>
</html>
