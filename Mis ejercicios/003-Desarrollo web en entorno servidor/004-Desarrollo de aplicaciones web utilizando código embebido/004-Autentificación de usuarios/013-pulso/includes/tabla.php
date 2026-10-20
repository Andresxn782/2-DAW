<?php
	$db = new SQLite3('gimnasio.db');
	$info = $db->query("PRAGMA table_info(".$tabla.")");
	$campos = [];
	$pk = null;
	while($c = $info->fetchArray(SQLITE3_ASSOC)){
		$campos[] = $c['name'];
		if($c['pk'] == 1){
			$pk = $c['name'];
		}
	}

	// Borrado
	if(isset($_GET['eliminar'])){
		$consulta = $db->prepare("DELETE FROM ".$tabla." WHERE ".$pk." = :id");
		$consulta->bindValue(':id', $_GET['eliminar']);
		$consulta->execute();
		header("Location:?tabla=".urlencode($tabla)."&ok=baja");
		exit();
	}

	$mensajes = ["alta" => "Registro añadido.", "cambio" => "Cambios guardados.", "baja" => "Registro eliminado."];
	if(isset($_GET['ok']) && isset($mensajes[$_GET['ok']])){
		echo "<div class='aviso-ok'>".$mensajes[$_GET['ok']]."</div>";
	}

	$result = $db->query("SELECT * FROM ".$tabla);
?>
<div class="caja tabla-contenedor">
	<table>
		<thead>
			<tr>
				<?php foreach($campos as $c){ echo "<th>".htmlspecialchars($c)."</th>"; } ?>
				<th>Acciones</th>
			</tr>
		</thead>
		<tbody>
			<?php while($fila = $result->fetchArray(SQLITE3_ASSOC)){ ?>
			<tr>
				<?php foreach($campos as $c){ ?><td><?php echo htmlspecialchars($fila[$c] ?? ''); ?></td><?php } ?>
				<td class="acciones">
					<?php $id = urlencode($fila[$pk]); ?>
					<a href="?tabla=<?php echo urlencode($tabla); ?>&accion=editar&id=<?php echo $id; ?>">Editar</a>
					<a class="eliminar" onclick="return confirm('¿Seguro que quieres borrar este registro?')" href="?tabla=<?php echo urlencode($tabla); ?>&eliminar=<?php echo $id; ?>">Borrar</a>
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
<?php $db->close(); ?>
