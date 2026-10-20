<?php
	include "includes/config.php";
	include "includes/funciones.php";

	// Si ya hay sesión, cada rol va a su página
	if(usuarioActual()){
		header("Location: ".($_SESSION['usuario']['rol'] == "monitor" ? "monitor.php" : "clases.php"));
		exit();
	}

	$error = "";
	if($_SERVER['REQUEST_METHOD'] == "POST"){
		$usuario = comprobarCredenciales($db, trim($_POST['usuario'] ?? ""), $_POST['contrasena'] ?? "");
		if($usuario){
			session_regenerate_id(true);			// Evito la fijación de sesión
			$_SESSION['usuario'] = $usuario;
			// Cookie para recordar el nombre de usuario 30 días (nunca la contraseña)
			if(isset($_POST['recordar'])){
				setcookie("recordar", $usuario['usuario'], time() + 60*60*24*30, "", "", false, true);
			}else{
				setcookie("recordar", "", time() - 3600);
			}
			header("Location: index.php");
			exit();
		}
		$error = "Usuario o contraseña incorrectos.";
		error_log("Login fallido para '".($_POST['usuario'] ?? "")."' desde ".$_SERVER['REMOTE_ADDR']);
	}

	$recordado = $_COOKIE['recordar'] ?? "";
	include "includes/cabecera.php";
?>
<div class="caja estrecha">
	<h1>Entrar</h1>
	<?php if($error){ ?><div class="aviso error"><?php echo $error; ?></div><?php } ?>
	<?php if(isset($_GET['registrado'])){ ?><div class="aviso ok">Cuenta creada. Ya puedes entrar.</div><?php } ?>
	<?php if(isset($_GET['salir'])){ ?><div class="aviso ok">Has cerrado la sesión.</div><?php } ?>
	<form method="POST">
		<label>Usuario</label>
		<input type="text" name="usuario" value="<?php echo e($recordado); ?>" required autocomplete="username">
		<label>Contraseña</label>
		<input type="password" name="contrasena" required autocomplete="current-password">
		<label class="check"><input type="checkbox" name="recordar" <?php echo $recordado ? "checked" : ""; ?>> Recordar mi usuario</label>
		<button type="submit">Entrar</button>
	</form>
	<p>¿No tienes cuenta? <a href="registro.php">Hazte socio</a></p>
</div>
<?php include "includes/pie.php"; ?>
