<form action="?" method="POST">
	<?php
		$db = new SQLite3('gimnasio.db');
		$result = $db->query("PRAGMA table_info(socios)");

		$campos = [];
		while ($column = $result->fetchArray(SQLITE3_ASSOC)) {
			if($column['pk'] == 1){
				continue;
			}
			$campos[] = $column['name'];
			echo "<input type='text' name='".$column['name']."' placeholder='".$column['name']."'><br>";
		}

		if(isset($_POST['insertar'])){
			// Uso una consulta preparada: los ? se rellenan con lo que escribe el usuario
			// y así nadie puede colar SQL dentro de un campo
			$huecos = array_fill(0, count($campos), "?");
			$sql = "INSERT INTO socios (".implode(",", $campos).") VALUES (".implode(",", $huecos).")";
			$consulta = $db->prepare($sql);
			foreach($campos as $posicion => $campo){
				$consulta->bindValue($posicion + 1, $_POST[$campo]);
			}
			$consulta->execute();
			echo "<p>Socio guardado</p>";
		}

		$db->close();
	?>
	<input type="submit" name="insertar" value="Guardar socio">
</form>
