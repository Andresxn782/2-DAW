<?php
	if(isset($_POST['nombre'])){
		echo "Recibido por POST: bienvenido, ".$_POST['nombre'];
	}
?>
<form action="?" method="POST">
	<input type="text" name="nombre" placeholder="Tu nombre">
	<input type="submit" value="Apuntarme">
</form>
