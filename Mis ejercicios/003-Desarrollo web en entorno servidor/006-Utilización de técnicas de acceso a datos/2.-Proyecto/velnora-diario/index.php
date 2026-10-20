<?php
	include "includes/inicio.php";

	$pagina = max(1, (int)($_GET['pagina'] ?? 1));
	$total = $repositorio->contar($filtros);
	$paginas = max(1, (int)ceil($total / $config['por_pagina']));
	$pagina = min($pagina, $paginas);
	$entrenos = $repositorio->buscar($filtros, $pagina, $config['por_pagina']);
	$estadisticas = $repositorio->estadisticas($filtros);
	$maxTipo = $estadisticas['por_tipo'] ? max($estadisticas['por_tipo']) : 1;
	$consulta = http_build_query(array_filter($filtros));

	include "includes/cabecera.php";
?>
<form class="filtros caja" method="GET">
	<label>Socio
		<select name="socio">
			<option value="">Todos</option>
			<?php foreach($repositorio->socios() as $s){ ?><option <?= $s === $filtros['socio'] ? "selected" : "" ?>><?= e($s) ?></option><?php } ?>
		</select>
	</label>
	<label>Tipo
		<select name="tipo">
			<option value="">Todos</option>
			<?php foreach(Entreno::TIPOS as $t){ ?><option <?= $t === $filtros['tipo'] ? "selected" : "" ?>><?= e($t) ?></option><?php } ?>
		</select>
	</label>
	<label>Desde <input type="date" name="desde" value="<?= e($filtros['desde']) ?>"></label>
	<label>Hasta <input type="date" name="hasta" value="<?= e($filtros['hasta']) ?>"></label>
	<button>Filtrar</button>
	<a href="index.php">Quitar filtros</a>
</form>

<div class="resumen">
	<div class="caja dato"><span>Sesiones</span><strong><?= $estadisticas['sesiones'] ?></strong></div>
	<div class="caja dato"><span>Horas</span><strong><?= number_format($estadisticas['minutos'] / 60, 1, ',', '.') ?></strong></div>
	<div class="caja dato"><span>Media por sesión</span><strong><?= $estadisticas['sesiones'] ? round($estadisticas['minutos'] / $estadisticas['sesiones']) : 0 ?> min</strong></div>
	<div class="caja dato"><span>Socio más constante</span><strong><?= e(array_key_first($estadisticas['por_socio']) ?? '-') ?></strong></div>
</div>

<div class="dos">
	<div class="caja">
		<div class="titulo">
			<h2>Entrenos (<?= $total ?>)</h2>
			<span class="exportar">Exportar:
				<a href="exportar.php?formato=csv&<?= e($consulta) ?>">CSV</a>
				<a href="exportar.php?formato=json&<?= e($consulta) ?>">JSON</a>
				<a href="exportar.php?formato=xml&<?= e($consulta) ?>">XML</a>
			</span>
		</div>
		<table>
			<tr><th>Fecha</th><th>Socio</th><th>Tipo</th><th>Min</th><th>Int.</th><th>kcal</th><th></th></tr>
			<?php foreach($entrenos as $en){ ?>
				<tr>
					<td><?= e(date('d/m/Y', strtotime($en->fecha))) ?></td>
					<td><?= e($en->socio) ?></td>
					<td><span class="etiqueta"><?= e($en->tipo) ?></span></td>
					<td><?= (int)$en->minutos ?></td>
					<td><?= str_repeat("●", (int)$en->intensidad) ?></td>
					<td><?= $en->calorias() ?></td>
					<td class="acciones"><a href="editar.php?id=<?= (int)$en->id ?>">Editar</a> <a href="borrar.php?id=<?= (int)$en->id ?>">Borrar</a></td>
				</tr>
			<?php } ?>
			<?php if(!$entrenos){ ?><tr><td colspan="7">No hay entrenos con esos filtros.</td></tr><?php } ?>
		</table>
		<?php if($paginas > 1){ ?>
			<p class="paginas">
				<?php for($p = 1; $p <= $paginas; $p++){ ?>
					<?php if($p === $pagina){ ?><strong><?= $p ?></strong><?php }else{ ?><a href="?<?= e($consulta) ?>&pagina=<?= $p ?>"><?= $p ?></a><?php } ?>
				<?php } ?>
			</p>
		<?php } ?>
	</div>
	<div class="caja">
		<h2>Minutos por tipo</h2>
		<?php foreach($estadisticas['por_tipo'] as $tipo => $minutos){ ?>
			<div class="barra-fila">
				<span><?= e($tipo) ?></span>
				<div class="barra" style="width:<?= round($minutos / $maxTipo * 100) ?>%"><?= $minutos ?></div>
			</div>
		<?php } ?>
	</div>
</div>
<?php include "includes/pie.php"; ?>
