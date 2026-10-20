<h2><?php echo $tabla; ?></h2>

<form action="?tabla=<?php echo $tabla; ?>" method="POST">
	<?php
		$db = new SQLite3('gimnasio.db');
		$result = $db->query("PRAGMA table_info(".$tabla.")");
		$campos = [];
		while ($column = $result->fetchArray(SQLITE3_ASSOC)) {
			if($column['pk'] == 1){
				continue;
			}
			$campos[] = $column['name'];
			echo "<input type='text' name='".$column['name']."' placeholder='".$column['name']."' required>";
		}

		if(isset($_POST['insertar'])){
			$huecos = array_fill(0, count($campos), "?");
			$consulta = $db->prepare("INSERT INTO ".$tabla." (".implode(",", $campos).") VALUES (".implode(",", $huecos).")");
			foreach($campos as $posicion => $campo){
				$consulta->bindValue($posicion + 1, trim($_POST[$campo]));
			}
			$consulta->execute();
		}
		$db->close();
	?>
	<input type="submit" name="insertar" value="Guardar">
</form>
