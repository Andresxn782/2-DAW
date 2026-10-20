<?php
	// La cookie se guarda en el navegador del usuario durante 30 días
	setcookie("socio", "Andres Ruiz", time() + 60*60*24*30);
	setcookie("tema", "oscuro", time() + 60*60*24*30);
	echo "Cookies guardadas";
?>
