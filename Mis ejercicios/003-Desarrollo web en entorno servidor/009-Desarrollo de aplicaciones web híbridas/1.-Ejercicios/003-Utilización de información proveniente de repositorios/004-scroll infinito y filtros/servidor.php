<?php
	// Catálogo con filtros y paginación.
	//   servidor.php?pagina=0&categoria=Yoga&nivel=Medio&q=cadera
	// Devuelve {"total", "pagina", "hayMas", "categorias", "entrenos"}
	$porPagina = 12;
	$pagina = max(0, (int)($_GET['pagina'] ?? 0));
	$categoria = $_GET['categoria'] ?? "";
	$nivel = $_GET['nivel'] ?? "";
	$busqueda = mb_strtolower(trim($_GET['q'] ?? ""));

	$datos = json_decode(file_get_contents(__DIR__."/../repositorio/entrenos.json"), true);
	$todos = $datos["entrenos"];

	$filtrados = array_values(array_filter($todos, function ($entreno) use ($categoria, $nivel, $busqueda) {
		if ($categoria !== "" && $entreno['categoria'] !== $categoria) {
			return false;
		}
		if ($nivel !== "" && $entreno['nivel'] !== $nivel) {
			return false;
		}
		if ($busqueda !== "" && !str_contains(mb_strtolower($entreno['titulo']." ".$entreno['monitor']), $busqueda)) {
			return false;
		}
		return true;
	}));

	header("Content-Type: application/json; charset=UTF-8");
	echo json_encode([
		"total" => count($filtrados),
		"pagina" => $pagina,
		"hayMas" => ($pagina + 1) * $porPagina < count($filtrados),
		// Las categorías salen del propio repositorio, no están escritas a mano
		"categorias" => array_values(array_unique(array_column($todos, "categoria"))),
		"entrenos" => array_slice($filtrados, $pagina * $porPagina, $porPagina)
	], JSON_UNESCAPED_UNICODE);
?>
