<?php
	include "includes/config.php";
	include "includes/funciones.php";
	exigirLogin();

	// El tema se guarda en el navegador del usuario con una cookie de un año
	if(isset($_POST['tema']) && in_array($_POST['tema'], ["claro", "oscuro"])){
		setcookie("tema", $_POST['tema'], time() + 60*60*24*365);
		header("Location: preferencias.php?guardado=1");
		exit();
	}
	include "includes/cabecera.php";
?>
<div class="caja estrecha">
	<h1>Preferencias</h1>
	<?php if(isset($_GET['guardado'])){ ?><div class="aviso ok">Preferencia guardada en tu navegador.</div><?php } ?>
	<form method="POST">
		<label class="check"><input type="radio" name="tema" value="claro" <?php echo tema() == "claro" ? "checked" : ""; ?>> Tema claro</label>
		<label class="check"><input type="radio" name="tema" value="oscuro" <?php echo tema() == "oscuro" ? "checked" : ""; ?>> Tema oscuro</label>
		<button type="submit">Guardar</button>
	</form>
	<h2>Qué guarda esta web</h2>
	<table>
		<tr><th>Dónde</th><th>Qué</th><th>Valor ahora</th></tr>
		<tr><td>Sesión (servidor)</td><td>Tu usuario y rol</td><td><?php echo e($_SESSION['usuario']['usuario']); ?> / <?php echo e($_SESSION['usuario']['rol']); ?></td></tr>
		<tr><td>Cookie <code>PHPSESSID</code></td><td>Solo el identificador de la sesión</td><td><?php echo e(substr(session_id(), 0, 8)); ?>…</td></tr>
		<tr><td>Cookie <code>tema</code></td><td>Tema elegido</td><td><?php echo e($_COOKIE['tema'] ?? "(sin elegir)"); ?></td></tr>
		<tr><td>Cookie <code>recordar</code></td><td>Usuario para el login</td><td><?php echo e($_COOKIE['recordar'] ?? "(no)"); ?></td></tr>
	</table>
</div>
<?php include "includes/pie.php"; ?>
