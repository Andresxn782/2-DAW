<?php
	// Carátula SVG de un vídeo: color según la categoría y el título encima.
	$datos = json_decode(file_get_contents(__DIR__."/entrenos.json"), true);
	$id = (int)($_GET['id'] ?? 0);
	$entreno = null;
	foreach ($datos['entrenos'] as $fila) {
		if ($fila['id'] === $id) {
			$entreno = $fila;
		}
	}
	if ($entreno === null) {
		http_response_code(404);
		exit;
	}

	$colores = [
		"Fuerza" => ["#7f1d1d", "#ef4444"], "Cardio" => ["#7c2d12", "#f97316"], "Yoga" => ["#134e4a", "#2dd4bf"],
		"Crossfit" => ["#1e1b4b", "#818cf8"], "Pilates" => ["#4a044e", "#e879f9"], "Movilidad" => ["#14532d", "#4ade80"]
	];
	[$oscuro, $claro] = $colores[$entreno['categoria']] ?? ["#111827", "#9ca3af"];

	// Parto el título en líneas de unos 14 caracteres
	$lineas = explode("\n", wordwrap($entreno['titulo'], 14, "\n", true));
	$texto = "";
	foreach ($lineas as $i => $linea) {
		$texto .= '<text x="20" y="'.(250 + $i * 34).'" font-size="30" font-weight="800" fill="white">'.htmlspecialchars($linea, ENT_XML1).'</text>';
	}

	// Las carátulas no cambian: el navegador las puede guardar un día
	header("Content-Type: image/svg+xml");
	header("Cache-Control: public, max-age=86400");
	echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 240 360" width="240" height="360">
		<defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="'.$claro.'"/><stop offset="1" stop-color="'.$oscuro.'"/></linearGradient></defs>
		<rect width="240" height="360" fill="url(#g)"/>
		<circle cx="190" cy="70" r="'.(30 + $id % 5 * 8).'" fill="white" opacity="0.15"/>
		<text x="20" y="40" font-family="Arial" font-size="14" font-weight="bold" fill="white" opacity="0.85">VELNORA TV</text>
		<g font-family="Arial">'.$texto.'</g>
		<text x="20" y="340" font-family="Arial" font-size="15" fill="white" opacity="0.85">'.$entreno['minutos'].' min · '.htmlspecialchars($entreno['nivel'], ENT_XML1).'</text>
	</svg>';
?>
