<?php
	// Software B: consume la API de socios enviando la API Key.
	// Hago dos llamadas: una con la clave buena y otra con una clave falsa,
	// para ver los dos códigos de estado (200 y 401).

	function llamarApi($url, $clave) {
		// Inicializo la conexión
		$curl = curl_init($url);
		// Quiero que me devuelva la respuesta en vez de pintarla
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 5);
		// Envío la API Key en la cabecera /////////// IMPORTANTE
		curl_setopt($curl, CURLOPT_HTTPHEADER, ["X-API-Key: ".$clave]);
		// Ejecuto la petición
		$respuesta = curl_exec($curl);
		$codigo = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		// Cierro la conexión
		curl_close($curl);
		return ["codigo" => $codigo, "datos" => json_decode((string)$respuesta, true)];
	}

	$protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
	$carpeta = dirname(dirname($_SERVER['SCRIPT_NAME']));
	$url = $protocolo."://".$_SERVER['HTTP_HOST'].str_replace(" ", "%20", $carpeta)."/SoftwareA/api.php";

	$plan = $_GET['plan'] ?? "";
	$urlLista = $plan === "" ? $url : $url."?plan=".urlencode($plan);

	$buena = llamarApi($urlLista, "velnora_2026_clave_segura");
	$mala = llamarApi($url, "clave_inventada");
?>
<!doctype html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Socios desde la API - Velnora</title>
	<style>
		body{font-family:Arial, sans-serif;max-width:800px;margin:30px auto;}
		table{border-collapse:collapse;width:100%;}
		th, td{border-bottom:1px solid #ddd;padding:6px;text-align:left;}
		.ok{color:#16a34a;}
		.mal{color:#b91c1c;}
	</style>
</head>
<body>
	<h1>Socios de Velnora (vía API)</h1>
	<form>
		<select name="plan">
			<option value="">Todos los planes</option>
			<?php foreach (["Musculación", "Spinning", "Crossfit", "Yoga", "Pilates", "Natación"] as $opcion) { ?>
				<option <?= $opcion === $plan ? "selected" : "" ?>><?= $opcion ?></option>
			<?php } ?>
		</select>
		<button>Filtrar</button>
	</form>

	<h2 class="ok">Con la clave correcta: código <?= $buena['codigo'] ?></h2>
	<?php if ($buena['codigo'] === 200) { ?>
		<p><?= $buena['datos']['total'] ?> socios</p>
		<table>
			<tr><th>Id</th><th>Nombre</th><th>Email</th><th>Plan</th></tr>
			<?php foreach ($buena['datos']['socios'] as $socio) { ?>
				<tr>
					<td><?= $socio['id'] ?></td>
					<td><?= htmlspecialchars($socio['nombre']." ".$socio['apellidos']) ?></td>
					<td><?= htmlspecialchars($socio['email']) ?></td>
					<td><?= htmlspecialchars($socio['plan']) ?></td>
				</tr>
			<?php } ?>
		</table>
	<?php } ?>

	<h2 class="mal">Con una clave falsa: código <?= $mala['codigo'] ?></h2>
	<p><?= htmlspecialchars($mala['datos']['mensaje'] ?? "Sin respuesta") ?></p>
</body>
</html>
