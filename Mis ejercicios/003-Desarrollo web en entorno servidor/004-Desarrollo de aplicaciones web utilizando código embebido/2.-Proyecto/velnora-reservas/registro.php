<?php
	include "includes/config.php";
	include "includes/funciones.php";

	$errores = [];
	if($_SERVER['REQUEST_METHOD'] == "POST"){
		$usuario = trim($_POST['usuario'] ?? "");
		$nombre = trim($_POST['nombre'] ?? "");
		$contrasena = $_POST['contrasena'] ?? "";

		if(!preg_match('/^[a-z0-9_]{3,20}$/', $usuario)){
			$errores[] = "El usuario debe tener de 3 a 20 letras minúsculas, números o _.";
		}
		if($nombre == ""){
			$errores[] = "Escribe tu nombre.";
		}
		if(strlen($contrasena) < 8){
			$errores[] = "La contraseña debe tener al menos 8 caracteres.";
		}
		if($contrasena !== ($_POST['repetir'] ?? "")){
			$errores[] = "Las contraseñas no coinciden.";
		}

		if(count($errores) == 0){
			$consulta = $db->prepare("INSERT INTO usuarios (usuario, contrasena, nombre, rol) VALUES (:u, :c, :n, 'socio')");
			$consulta->bindValue(':u', $usuario);
			$consulta->bindValue(':c', password_hash($contrasena, PASSWORD_DEFAULT));	// Nunca guardo la contraseña en claro
			$consulta->bindValue(':n', $nombre);
			if(@$consulta->execute()){
				header("Location: index.php?registrado=1");
				exit();
			}
			$errores[] = "Ese usuario ya existe.";
		}
	}
	include "includes/cabecera.php";
?>
<div class="caja estrecha">
	<h1>Hazte socio</h1>
	<?php foreach($errores as $error){ ?><div class="aviso error"><?php echo $error; ?></div><?php } ?>
	<form method="POST">
		<label>Nombre completo</label>
		<input type="text" name="nombre" value="<?php echo e($_POST['nombre'] ?? ""); ?>" required>
		<label>Usuario</label>
		<input type="text" name="usuario" value="<?php echo e($_POST['usuario'] ?? ""); ?>" required>
		<label>Contraseña</label>
		<input type="password" name="contrasena" required autocomplete="new-password">
		<label>Repite la contraseña</label>
		<input type="password" name="repetir" required autocomplete="new-password">
		<button type="submit">Crear cuenta</button>
	</form>
	<p><a href="index.php">Volver</a></p>
</div>
<?php include "includes/pie.php"; ?>
