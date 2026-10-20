<?php
	// Un botón por cada tabla que permite el rol del usuario
	foreach($tablas as $nombre){
		echo "<a href='?tabla=".$nombre."'><button>".$nombre."</button></a>";
	}
?>
