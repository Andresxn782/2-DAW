<?php
	// Se incluye al principio de todas las páginas
	session_start();
	$config = require __DIR__."/../config.php";
	require_once __DIR__."/../Clases/RepositorioPDO.php";
	require_once __DIR__."/../Clases/RepositorioJSON.php";
	require_once __DIR__."/../Clases/Exportador.php";

	/** Crea el repositorio que diga la configuración (patrón factoría). */
	function crearRepositorio(array $config): RepositorioEntrenos {
		switch($config['almacen']){
			case 'mysql':
				$m = $config['mysql'];
				return new RepositorioPDO(new PDO($m['dsn'], $m['usuario'], $m['contrasena']));
			case 'json':
				return new RepositorioJSON($config['json']);
			default:
				return new RepositorioPDO(new PDO('sqlite:'.$config['sqlite']));
		}
	}

	try{
		$repositorio = crearRepositorio($config);
	}catch(PDOException $e){
		http_response_code(500);
		die("<p>No se puede conectar con el almacén '".htmlspecialchars($config['almacen'])."'. Revisa config.php.</p>");
	}

	// La primera vez cargo unos entrenos de ejemplo
	if($repositorio->contar([]) === 0){
		[$ejemplo] = Exportador::leerCSV(__DIR__."/../ejemplo.csv");
		$repositorio->importar($ejemplo);
	}

	// Filtros que llegan por GET, siempre limpios
	$filtros = [
		'socio' => trim($_GET['socio'] ?? ''),
		'tipo' => in_array($_GET['tipo'] ?? '', Entreno::TIPOS, true) ? $_GET['tipo'] : '',
		'desde' => preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['desde'] ?? '') ? $_GET['desde'] : '',
		'hasta' => preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['hasta'] ?? '') ? $_GET['hasta'] : ''
	];

	function e($texto){
		return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
	}

	// Token para que los formularios que cambian datos solo puedan venir de esta web
	if(empty($_SESSION['token'])){
		$_SESSION['token'] = bin2hex(random_bytes(16));
	}
	function tokenValido(){
		return hash_equals($_SESSION['token'], $_POST['token'] ?? '');
	}

	function aviso($texto){
		$_SESSION['aviso'] = $texto;
	}
?>
