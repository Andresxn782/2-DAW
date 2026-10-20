<p><a href="<?= $vista->url('catalogo') ?>">← Volver al catálogo</a></p>
<article class="caja ficha">
	<span class="categoria"><?= $vista->e($producto['categoria']) ?></span>
	<h1><?= $vista->e($producto['nombre']) ?></h1>
	<p><?= $vista->e($producto['descripcion']) ?></p>
	<p class="precio"><?= $vista->precio($carrito->conIva($producto['precio'])) ?> <small>IVA incluido</small></p>
	<p><?= $producto['stock'] > 0 ? $producto['stock']." unidades disponibles" : "Agotado" ?></p>
	<?php if($producto['stock'] > 0){ ?>
		<form method="POST" class="en-linea">
			<input type="number" name="cantidad" value="1" min="1" max="<?= (int)$producto['stock'] ?>">
			<button>Añadir al carrito</button>
		</form>
	<?php } ?>
</article>
