<?php
	include "includes/inicio.php";

	$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
	// El borrado de verdad solo se hace por POST, con token, después de confirmar
	if($_SERVER['REQUEST_METHOD'] === 'POST' && tokenValido()){
		aviso($repositorio->eliminar($id) ? "Entreno borrado." : "Ese entreno ya no existía.");
		header("Location: index.php");
		exit();
	}
	$entreno = $repositorio->obtener($id);
	if(!$entreno){
		header("Location: index.php");
		exit();
	}
	include "includes/cabecera.php";
?>
<div class="caja estrecha">
	<h1>¿Borrar este entreno?</h1>
	<p><?= e($entreno->socio) ?> · <?= e($entreno->tipo) ?> · <?= e($entreno->fecha) ?> · <?= (int)$entreno->minutos ?> minutos</p>
	<form method="POST">
		<input type="hidden" name="token" value="<?= e($_SESSION['token']) ?>">
		<input type="hidden" name="id" value="<?= (int)$entreno->id ?>">
		<button class="peligro">Sí, borrar</button>
		<a href="index.php">Cancelar</a>
	</form>
</div>
<?php include "includes/pie.php"; ?>
