<table>
	<?php
		$db = new SQLite3('gimnasio.db');
		$result = $db->query("SELECT * FROM ".$tabla);
		$primera = true;
		while ($fila = $result->fetchArray(SQLITE3_ASSOC)) {
			if($primera){
				echo "<tr>";
				foreach(array_keys($fila) as $columna){
					echo "<th>".$columna."</th>";
				}
				echo "</tr>";
				$primera = false;
			}
			echo "<tr>";
			foreach ($fila as $valor) {
				echo "<td>".htmlspecialchars($valor)."</td>";
			}
			echo "</tr>";
		}
		$db->close();
	?>
</table>
