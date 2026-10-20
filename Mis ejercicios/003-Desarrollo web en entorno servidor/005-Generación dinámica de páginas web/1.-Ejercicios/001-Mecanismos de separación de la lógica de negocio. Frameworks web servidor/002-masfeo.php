<!-- Ahora además el HTML está dentro de cadenas de PHP: imposible de mantener -->
<table border=1>
	<?php
		$db=new SQLite3('socios.db');
		$info = $db->query("SELECT * FROM socios;");
		while($fila = $info->fetchArray(SQLITE3_ASSOC)){
			echo '<tr>
				<td>'.$fila['nombre'].'</td>
				<td>'.$fila['apellidos'].'</td>
				<td>'.$fila['email'].'</td>
				<td>'.$fila['plan'].'</td>
			</tr>';
		}
	?>
</table>
