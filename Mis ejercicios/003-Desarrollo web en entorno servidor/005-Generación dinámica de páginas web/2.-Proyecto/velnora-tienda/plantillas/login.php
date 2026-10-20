<div class="caja estrecha">
	<h1>Acceso de administración</h1>
	<?php if($error){ ?><div class="aviso error">Usuario o contraseña incorrectos.</div><?php } ?>
	<form method="POST">
		<label>Usuario</label>
		<input type="text" name="usuario" required autocomplete="username">
		<label>Contraseña</label>
		<input type="password" name="contrasena" required autocomplete="current-password">
		<button>Entrar</button>
	</form>
</div>
