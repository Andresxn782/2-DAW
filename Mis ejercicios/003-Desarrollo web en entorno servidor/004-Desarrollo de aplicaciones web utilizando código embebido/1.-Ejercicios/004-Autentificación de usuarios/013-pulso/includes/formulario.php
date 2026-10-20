<?php
	$db = new SQLite3('gimnasio.db');

	// Columnas de la tabla y cuál es la clave primaria
	$info = $db->query("PRAGMA table_info(".$tabla.")");
	$campos = [];
	$pk = null;
	while($c = $info->fetchArray(SQLITE3_ASSOC)){
		$campos[] = $c['name'];
		if($c['pk'] == 1){
			$pk = $c['name'];
		}
	}

	$editando = ($accion == "editar");
	$datos = [];

	// Si edito, cargo los datos actuales del registro
	if($editando && isset($_GET['id'])){
		$consulta = $db->prepare("SELECT * FROM ".$tabla." WHERE ".$pk." = :id");
		$consulta->bindValue(':id', $_GET['id']);
		$datos = $consulta->execute()->fetchArray(SQLITE3_ASSOC) ?: [];
	}

	// Alta
	if(isset($_POST['insertar'])){
		$columnas = [];
		foreach($campos as $n){
			if($n != $pk){
				$columnas[] = $n;
			}
		}
		$consulta = $db->prepare("INSERT INTO ".$tabla." (".implode(",", $columnas).") VALUES (:".implode(",:", $columnas).")");
		foreach($columnas as $n){
			$consulta->bindValue(':'.$n, trim($_POST[$n] ?? ''));
		}
		$consulta->execute();
		header("Location:?tabla=".urlencode($tabla)."&ok=alta");
		exit();
	}

	// Modificación
	if(isset($_POST['actualizar'])){
		$sets = [];
		foreach($campos as $n){
			if($n != $pk){
				$sets[] = $n." = :".$n;
			}
		}
		$consulta = $db->prepare("UPDATE ".$tabla." SET ".implode(",", $sets)." WHERE ".$pk." = :pk");
		foreach($campos as $n){
			if($n != $pk){
				$consulta->bindValue(':'.$n, trim($_POST[$n] ?? ''));
			}
		}
		$consulta->bindValue(':pk', $_POST[$pk]);
		$consulta->execute();
		header("Location:?tabla=".urlencode($tabla)."&ok=cambio");
		exit();
	}
?>
<div class="caja formulario-caja">
	<h2><?php echo $editando ? "Editar registro" : "Nuevo registro"; ?></h2>
	<form method="POST">
		<?php foreach($campos as $n){ ?>
			<?php if($n == $pk && !$editando){ continue; } ?>
			<div class="campo">
				<label><?php echo htmlspecialchars($n); ?></label>
				<input type="text" name="<?php echo htmlspecialchars($n); ?>" value="<?php echo htmlspecialchars($datos[$n] ?? ''); ?>" <?php echo ($n == $pk) ? "readonly" : "required"; ?>>
			</div>
		<?php } ?>
		<input class="boton boton-primario" type="submit" name="<?php echo $editando ? 'actualizar' : 'insertar'; ?>" value="<?php echo $editando ? 'Guardar cambios' : 'Añadir'; ?>">
	</form>
</div>
<?php $db->close(); ?>
