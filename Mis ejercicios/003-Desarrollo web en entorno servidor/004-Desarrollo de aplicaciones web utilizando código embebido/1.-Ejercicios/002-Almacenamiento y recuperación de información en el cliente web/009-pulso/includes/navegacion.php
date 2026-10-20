<?php
	// Un botón por cada tabla de la base de datos
	foreach($tablas as $nombre){
		echo "<a href='?tabla=".$nombre."'><button>".$nombre."</button></a>";
	}
?>
