<?php
	// La tabla ya no está escrita a fuego: cambiando esta variable sirve para cualquiera
	$tabla = "clases";
?>
<form action="?" method="POST">
	<?php
		$db = new SQLite3('gimnasio.db');
		$result = $db->query("PRAGMA table_info(".$tabla.")");
		$campos = [];
		while ($column = $result->fetchArray(SQLITE3_ASSOC)) {
			if($column['pk'] == 1){
				continue;
			}
			$campos[] = $column['name'];
			echo "<input type='text' name='".$column['name']."' placeholder='".$column['name']."'><br>";
		}
		if(isset($_POST['insertar'])){
			$huecos = array_fill(0, count($campos), "?");
			$consulta = $db->prepare("INSERT INTO ".$tabla." (".implode(",", $campos).") VALUES (".implode(",", $huecos).")");
			foreach($campos as $posicion => $campo){
				$consulta->bindValue($posicion + 1, $_POST[$campo]);
			}
			$consulta->execute();
		}
		$db->close();
	?>
	<input type="submit" name="insertar" value="Guardar">
</form>
<table border="1">
	<?php
		$db = new SQLite3('gimnasio.db');
		$result = $db->query("SELECT * FROM ".$tabla);
		while ($fila = $result->fetchArray(SQLITE3_ASSOC)) {
			echo "<tr>";
			foreach ($fila as $valor) {
				echo "<td>".htmlspecialchars($valor)."</td>";
			}
			echo "</tr>";
		}
		$db->close();
	?>
</table>
