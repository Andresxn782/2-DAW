<h1>Catálogo</h1>
<form class="filtros" method="GET">
	<input type="hidden" name="ruta" value="catalogo">
	<input type="search" name="q" value="<?= $vista->e($texto) ?>" placeholder="Buscar producto">
	<select name="categoria" onchange="this.form.submit()">
		<option value="">Todas las categorías</option>
		<?php foreach($categorias as $c){ ?>
			<option <?= $c === $categoria ? "selected" : "" ?>><?= $vista->e($c) ?></option>
		<?php } ?>
	</select>
	<button>Buscar</button>
</form>
<?php if(!$productos){ ?>
	<p>No hay productos que coincidan.</p>
<?php } ?>
<div class="rejilla">
	<?php foreach($productos as $p){ ?>
		<article class="tarjeta<?= $p['destacado'] ? ' destacado' : '' ?>">
			<span class="categoria"><?= $vista->e($p['categoria']) ?></span>
			<h2><a href="<?= $vista->url('producto', ['id' => $p['id']]) ?>"><?= $vista->e($p['nombre']) ?></a></h2>
			<p class="precio"><?= $vista->precio($carrito->conIva($p['precio'])) ?></p>
			<form method="POST">
				<?php if($p['stock'] > 0){ ?>
					<button name="anadir" value="<?= $p['id'] ?>">Añadir al carrito</button>
				<?php }else{ ?>
					<button disabled>Agotado</button>
				<?php } ?>
			</form>
		</article>
	<?php } ?>
</div>
