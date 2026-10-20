<?php $lineas = $carrito->lineas(); $resumen = $carrito->resumen(); ?>
<h1>Carrito</h1>
<?php if(!$lineas){ ?>
	<p>El carrito está vacío. <a href="<?= $vista->url('catalogo') ?>">Ver productos</a></p>
<?php }else{ ?>
	<form method="POST" class="caja">
		<table>
			<tr><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th></tr>
			<?php foreach($lineas as $l){ ?>
				<tr>
					<td><?= $vista->e($l['producto']['nombre']) ?></td>
					<td><?= $vista->precio($l['precio']) ?></td>
					<td><input type="number" name="cantidad[<?= $l['producto']['id'] ?>]" value="<?= $l['cantidad'] ?>" min="0" max="<?= (int)$l['producto']['stock'] ?>"></td>
					<td><?= $vista->precio($l['subtotal']) ?></td>
				</tr>
			<?php } ?>
			<tr><td colspan="3">Subtotal</td><td><?= $vista->precio($resumen['subtotal']) ?></td></tr>
			<tr><td colspan="3">Envío<?= $resumen['envio'] == 0 ? " (gratis desde ".$vista->precio($config['envio_gratis_desde']).")" : "" ?></td><td><?= $vista->precio($resumen['envio']) ?></td></tr>
			<tr class="total"><td colspan="3">Total</td><td><?= $vista->precio($resumen['total']) ?></td></tr>
		</table>
		<button>Actualizar cantidades</button>
		<button name="vaciar" value="1" class="secundario">Vaciar carrito</button>
	</form>
<?php } ?>
