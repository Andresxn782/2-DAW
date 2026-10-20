<div class="caja estrecha">
	<h1><?= $editando ? "Editar producto" : "Nuevo producto" ?></h1>
	<form method="POST" novalidate>
		<?php
			// Cada control se genera a partir de la definición de campos del modelo
			foreach($campos as $nombre => $definicion){
				echo $vista->control($nombre, $definicion, $valores[$nombre] ?? '', $errores[$nombre] ?? null);
			}
		?>
		<button><?= $editando ? "Guardar cambios" : "Crear producto" ?></button>
		<a href="<?= $vista->url('admin') ?>">Cancelar</a>
	</form>
</div>
