<div class="titulo">
	<h1>Productos</h1>
	<a class="boton" href="<?= $vista->url('editar') ?>">+ Nuevo producto</a>
</div>
<div class="caja">
	<table>
		<tr><th>Nombre</th><th>Categoría</th><th>Precio sin IVA</th><th>Stock</th><th></th></tr>
		<?php foreach($productos as $p){ ?>
			<tr>
				<td><?= $vista->e($p['nombre']) ?><?= $p['destacado'] ? " ★" : "" ?></td>
				<td><?= $vista->e($p['categoria']) ?></td>
				<td><?= $vista->precio($p['precio']) ?></td>
				<td class="<?= $p['stock'] == 0 ? 'agotado' : '' ?>"><?= $p['stock'] ?></td>
				<td class="acciones">
					<a href="<?= $vista->url('editar', ['id' => $p['id']]) ?>">Editar</a>
					<form method="POST" action="<?= $vista->url('borrar') ?>" onsubmit="return confirm('¿Borrar este producto?')">
						<input type="hidden" name="id" value="<?= $p['id'] ?>">
						<button class="enlace">Borrar</button>
					</form>
				</td>
			</tr>
		<?php } ?>
	</table>
</div>
