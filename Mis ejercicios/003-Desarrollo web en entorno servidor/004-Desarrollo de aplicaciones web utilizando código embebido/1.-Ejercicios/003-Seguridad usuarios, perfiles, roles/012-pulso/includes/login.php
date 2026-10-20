<div class="login-pantalla">
	<div class="login-caja">
		<div class="login-logo">P</div>
		<h1>PULSO</h1>
		<p>Gestión del gimnasio Velnora</p>
		<form method="POST" action="login.php">
			<?php if(isset($_GET['error'])){ ?><div class="aviso-error">Usuario o contraseña incorrectos.</div><?php } ?>
			<label>Usuario</label>
			<input type="text" name="usuario" autocomplete="username" required>
			<label>Contraseña</label>
			<input type="password" name="contrasena" autocomplete="current-password" required>
			<input class="boton boton-primario boton-login" type="submit" value="Entrar">
		</form>
	</div>
</div>
