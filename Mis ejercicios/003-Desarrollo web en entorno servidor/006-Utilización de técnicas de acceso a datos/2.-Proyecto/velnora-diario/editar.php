<?php
	include "includes/inicio.php";

	$id = (int)($_GET['id'] ?? 0);
	$entreno = $id ? $repositorio->obtener($id) : null;
	if($id && !$entreno){
		aviso("Ese entreno no existe.");
		header("Location: index.php");
		exit();
	}
	if(!$entreno){
		$entreno = Entreno::desdeArray(['fecha' => date('Y-m-d'), 'minutos' => 60, 'intensidad' => 3]);
	}
	$errores = [];

	if($_SERVER['REQUEST_METHOD'] === 'POST' && tokenValido()){
		$entreno = Entreno::desdeArray(['id' => $id ?: null] + $_POST);
		$errores = $entreno->validar();
		if(!$errores){
			$repositorio->guardar($entreno);
			aviso($id ? "Entreno actualizado." : "Entreno apuntado.");
			header("Location: index.php");
			exit();
		}
	}

	include "includes/cabecera.php";
?>
<div class="caja estrecha">
	<h1><?= $id ? "Editar entreno" : "Apuntar entreno" ?></h1>
	<form method="POST">
		<input type="hidden" name="token" value="<?= e($_SESSION['token']) ?>">
		<label>Socio</label>
		<input type="text" name="socio" value="<?= e($entreno->socio) ?>" list="socios" required>
		<datalist id="socios"><?php foreach($repositorio->socios() as $s){ ?><option value="<?= e($s) ?>"><?php } ?></datalist>
		<?php if(isset($errores['socio'])){ ?><small><?= e($errores['socio']) ?></small><?php } ?>

		<label>Fecha</label>
		<input type="date" name="fecha" value="<?= e($entreno->fecha) ?>" max="<?= date('Y-m-d') ?>" required>
		<?php if(isset($errores['fecha'])){ ?><small><?= e($errores['fecha']) ?></small><?php } ?>

		<label>Tipo</label>
		<select name="tipo">
			<option value="">-- Elige --</option>
			<?php foreach(Entreno::TIPOS as $t){ ?><option <?= $t === $entreno->tipo ? "selected" : "" ?>><?= e($t) ?></option><?php } ?>
		</select>
		<?php if(isset($errores['tipo'])){ ?><small><?= e($errores['tipo']) ?></small><?php } ?>

		<label>Minutos</label>
		<input type="number" name="minutos" min="5" max="300" value="<?= (int)$entreno->minutos ?>">
		<?php if(isset($errores['minutos'])){ ?><small><?= e($errores['minutos']) ?></small><?php } ?>

		<label>Intensidad (1 a 5)</label>
		<input type="range" name="intensidad" min="1" max="5" value="<?= (int)$entreno->intensidad ?>">
		<?php if(isset($errores['intensidad'])){ ?><small><?= e($errores['intensidad']) ?></small><?php } ?>

		<label>Notas</label>
		<textarea name="notas" rows="3"><?= e($entreno->notas) ?></textarea>

		<button><?= $id ? "Guardar cambios" : "Apuntar" ?></button>
		<a href="index.php">Cancelar</a>
	</form>
</div>
<?php include "includes/pie.php"; ?>
