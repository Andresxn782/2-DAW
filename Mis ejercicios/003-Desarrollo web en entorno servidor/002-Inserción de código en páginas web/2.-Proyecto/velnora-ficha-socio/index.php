<?php
	// Directiva: en este archivo PHP no convierte tipos al llamar a las funciones.
	// Si paso un string donde espero un float, da error en vez de convertirlo solo.
	declare(strict_types=1);

	// Variable global: la usa calcularCuota() con la palabra global
	$ivaGimnasio = 0.21;

	function calcularCuota(float $precioBase, int $meses, bool $estudiante): float {
		global $ivaGimnasio;						// Ámbito global: traigo la variable de fuera
		$descuento = 0;								// Ámbito local: solo existe aquí dentro
		if($estudiante){
			$descuento += 0.15;						// Operador de asignación combinado
		}
		if($meses >= 12){
			$descuento += 0.10;
		}elseif($meses >= 6){
			$descuento += 0.05;
		}
		$sinIva = $precioBase * (1 - $descuento);
		return round($sinIva * (1 + $ivaGimnasio), 2);
	}

	function contarVisitaFicha(): int {
		static $visitas = 0;						// Ámbito static: conserva el valor entre llamadas
		$visitas++;
		return $visitas;
	}

	// Directivas en tiempo de ejecución: mostrar todos los errores mientras desarrollo
	ini_set('display_errors', '1');
	error_reporting(E_ALL);

	// Datos de los socios (todavía sin base de datos)
	$socios = [
		1 => ["nombre" => "Lucía Martín", "edad" => "24", "plan" => "Musculación", "precio" => "39.90", "meses" => 12, "estudiante" => true, "lesion" => null],
		2 => ["nombre" => "Carlos Pérez", "edad" => "41", "plan" => "Spinning", "precio" => "29.50", "meses" => 3, "estudiante" => false, "lesion" => "Rodilla"],
		3 => ["nombre" => "Nerea Gil", "edad" => "19", "plan" => "Crossfit", "precio" => "45", "meses" => 6, "estudiante" => true, "lesion" => null]
	];

	// Conversión explícita: lo que llega por la URL siempre es un string
	$id = isset($_GET['socio']) ? (int)$_GET['socio'] : 1;
	if(!isset($socios[$id])){
		$id = 1;
	}
	$socio = $socios[$id];

	// Conversiones explícitas a los tipos que necesito
	$nombre = (string)$socio['nombre'];
	$edad = intval($socio['edad']);
	$precio = (float)$socio['precio'];
	$meses = $socio['meses'];
	$estudiante = (bool)$socio['estudiante'];
	settype($meses, "integer");

	// Conversión implícita: PHP convierte "45" a número él solo al sumar
	$precioConMatricula = $socio['precio'] + 15;

	// Operadores
	$cuota = calcularCuota($precio, $meses, $estudiante);
	$totalPeriodo = $cuota * $meses;
	$ahorro = ($precio * 1.21 * $meses) - $totalPeriodo;
	$esJoven = $edad < 25;
	$tieneDescuento = $estudiante || $meses >= 6;
	$puedeHacerImpacto = !($socio['lesion'] !== null && $socio['plan'] == "Crossfit");
	$resto = $meses % 3;

	$visitas = 0;
	for($i = 0; $i < 3; $i++){
		$visitas = contarVisitaFicha();
	}
?>
<!doctype html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Ficha de <?= htmlspecialchars($nombre) ?> · Velnora</title>
		<style>
			body{margin:0;font-family:"Trebuchet MS", sans-serif;background:#111827;color:#f9fafb;}
			.tarjeta{max-width:760px;margin:30px auto;background:#1f2937;border-radius:14px;overflow:hidden;}
			.cabecera{background:linear-gradient(90deg,#7c3aed,#db2777);padding:25px;}
			.cabecera h1{margin:0;}
			.cuerpo{padding:25px;display:grid;grid-template-columns:1fr 1fr;gap:15px;}
			.dato{background:#374151;border-radius:8px;padding:12px;}
			.dato span{display:block;font-size:12px;color:#9ca3af;text-transform:uppercase;}
			.dato strong{font-size:20px;}
			.ancho{grid-column:1 / 3;}
			nav{text-align:center;padding:15px;}
			nav a{color:#f9a8d4;margin:0 10px;}
			code{background:#111827;padding:2px 5px;border-radius:4px;}
			table{width:100%;border-collapse:collapse;font-size:14px;}
			td{padding:6px;border-bottom:1px solid #4b5563;}
			@media (max-width:600px){.cuerpo{grid-template-columns:1fr;}.ancho{grid-column:auto;}}
		</style>
	</head>
	<body>
		<div class="tarjeta">
			<div class="cabecera">
				<h1><?= htmlspecialchars($nombre) ?></h1>
				<p>Socio nº <?= $id ?> · Plan <?= $socio['plan'] ?></p>
			</div>
			<div class="cuerpo">
				<div class="dato"><span>Edad</span><strong><?= $edad ?> años</strong></div>
				<div class="dato"><span>Precio base</span><strong><?= number_format($precio, 2, ',', '.') ?> €</strong></div>
				<div class="dato"><span>Cuota mensual con IVA</span><strong><?= number_format($cuota, 2, ',', '.') ?> €</strong></div>
				<div class="dato"><span>Total <?= $meses ?> meses</span><strong><?= number_format($totalPeriodo, 2, ',', '.') ?> €</strong></div>
				<div class="dato"><span>Ahorro por descuentos</span><strong><?= number_format($ahorro, 2, ',', '.') ?> €</strong></div>
				<div class="dato"><span>Primer mes con matrícula</span><strong><?= $precioConMatricula ?> €</strong></div>
				<div class="dato ancho">
					<span>Comprobaciones con operadores</span>
					<table>
						<tr><td><code>$edad &lt; 25</code></td><td><?= $esJoven ? "Sí, tarifa joven" : "No" ?></td></tr>
						<tr><td><code>$estudiante || $meses &gt;= 6</code></td><td><?= $tieneDescuento ? "Tiene descuento" : "Sin descuento" ?></td></tr>
						<tr><td><code>!($lesion !== null &amp;&amp; plan == Crossfit)</code></td><td><?= $puedeHacerImpacto ? "Puede hacer ejercicios de impacto" : "Evitar impacto por lesión" ?></td></tr>
						<tr><td><code>$meses % 3</code></td><td><?= $resto ?> (meses que sobran de trimestres completos)</td></tr>
						<tr><td><code>"45" + 15</code> (implícita)</td><td><?= gettype($precioConMatricula) ?></td></tr>
						<tr><td>Lesión</td><td><?= $socio['lesion'] ?? "Ninguna" ?> (<?= gettype($socio['lesion']) ?>)</td></tr>
						<tr><td>Variable <code>static</code> tras 3 llamadas</td><td><?= $visitas ?></td></tr>
					</table>
				</div>
				<div class="dato ancho">
					<span>Tipos de cada dato (var_dump)</span>
					<pre><?php var_dump($nombre, $edad, $precio, $estudiante, $socio['lesion']); ?></pre>
				</div>
			</div>
			<nav>
				<?php foreach($socios as $numero => $s){ ?>
					<a href="?socio=<?= $numero ?>"><?= $s['nombre'] ?></a>
				<?php } ?>
			</nav>
		</div>
	</body>
</html>
