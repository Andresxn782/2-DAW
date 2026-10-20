<?php
	// Software B: otro programa que consume la API del Software A.
	// La URL se calcula a partir de esta misma página, así funciona
	// tanto en XAMPP como con php -S.
	$protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
	$carpeta = dirname(dirname($_SERVER['SCRIPT_NAME']));
	$url = $protocolo."://".$_SERVER['HTTP_HOST'].str_replace(" ", "%20", $carpeta)."/SoftwareA/api.php";

	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_TIMEOUT, 5);
	$respuesta = curl_exec($curl);
	$codigo = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	curl_close($curl);

	// Interpreto el contrato: espero un JSON con estos campos
	$monitor = json_decode((string)$respuesta, true);
?>
<!doctype html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Software B - Velnora</title>
</head>
<body>
	<h1>Ficha que me ha devuelto el Software A</h1>
	<p>URL llamada: <code><?= htmlspecialchars($url) ?></code> · código HTTP <?= $codigo ?></p>
	<?php if ($monitor === null) { ?>
		<p>No he podido leer la respuesta.</p>
	<?php } else { ?>
		<p><strong><?= htmlspecialchars($monitor['nombre']." ".$monitor['apellidos']) ?></strong> (<?= htmlspecialchars($monitor['email']) ?>)</p>
		<p>Da estas clases:</p>
		<ul>
			<?php foreach ($monitor['clases'] as $clase) { ?>
				<li><?= htmlspecialchars($clase) ?></li>
			<?php } ?>
		</ul>
	<?php } ?>
	<h2>Respuesta en bruto</h2>
	<pre><?= htmlspecialchars((string)$respuesta) ?></pre>
</body>
</html>
