<?php
	// Saco la lista de tablas de la base de datos y hago un botón para cada una
	$db = new SQLite3('gimnasio.db');
	$result = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name != 'sqlite_sequence'");
	$tablas = [];
	while ($fila = $result->fetchArray(SQLITE3_ASSOC)) {
		$tablas[] = $fila['name'];
		echo "<a href='?tabla=".$fila['name']."'><button>".$fila['name']."</button></a> ";
	}
	$db->close();

	// Solo acepto una tabla que exista de verdad; si no, enseño socios
	$tabla = (isset($_GET['tabla']) && in_array($_GET['tabla'], $tablas)) ? $_GET['tabla'] : "socios";
?>
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
