<form class="login" method="POST" action="login.php">
	<h2>Acceso a Pulso</h2>
	<?php if(isset($_GET['error'])){ echo "<p class='error'>Usuario o contraseña incorrectos</p>"; } ?>
	<input type="text" name="usuario" placeholder="Usuario" required>
	<input type="password" name="contrasena" placeholder="Contraseña" required>
	<input type="submit" value="Entrar">
</form>
