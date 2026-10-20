<?php
	// $tablas ya viene calculado en index.php según el rol
	foreach($tablas as $t){
		$clase = ($t == $tabla) ? "nav-link activa" : "nav-link";
		echo "<a class='".$clase."' href='?tabla=".urlencode($t)."'>".htmlspecialchars($t)."</a>";
	}
?>
