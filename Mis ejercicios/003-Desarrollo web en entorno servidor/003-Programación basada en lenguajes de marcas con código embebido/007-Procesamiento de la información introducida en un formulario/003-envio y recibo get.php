<?php
	// Solo muestro el saludo si ya me han enviado el nombre
	if(isset($_GET['nombre'])){
		echo "Recibido por GET: bienvenido, ".$_GET['nombre'];
	}
?>
<form action="?" method="GET">
	<input type="text" name="nombre" placeholder="Tu nombre">
	<input type="submit" value="Apuntarme">
</form>
