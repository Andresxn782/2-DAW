<?php
	/**
	 * Pruebas del diario. Ejecutar: php pruebas.php
	 * Las mismas pruebas se pasan a los dos repositorios (SQLite en memoria y JSON temporal)
	 * para comprobar que se comportan igual. Si MySQL de XAMPP está arrancado, también a MySQL.
	 */
	if(php_sapi_name() !== 'cli'){
		die("Ejecuta las pruebas desde la terminal: php pruebas.php");
	}
	require_once __DIR__."/Clases/RepositorioPDO.php";
	require_once __DIR__."/Clases/RepositorioJSON.php";
	require_once __DIR__."/Clases/Exportador.php";

	$bien = 0;
	$mal = 0;
	function comprobar(string $que, bool $resultado): void {
		global $bien, $mal;
		$resultado ? $bien++ : $mal++;
		echo ($resultado ? "  OK    " : "  FALLO ").$que.PHP_EOL;
	}

	function entreno(string $socio, string $fecha, string $tipo, int $minutos): Entreno {
		return Entreno::desdeArray(['socio' => $socio, 'fecha' => $fecha, 'tipo' => $tipo, 'minutos' => $minutos, 'intensidad' => 3]);
	}

	function probarRepositorio(string $nombre, RepositorioEntrenos $r): void {
		echo PHP_EOL."Repositorio ".$nombre.PHP_EOL;
		$r->importar([
			entreno("Lucía", "2026-10-01", "Fuerza", 60),
			entreno("Lucía", "2026-10-03", "Cardio", 30),
			entreno("Carlos", "2026-10-02", "Fuerza", 45),
			entreno("Carlos", "2026-10-05", "Natación", 50),
			entreno("Nerea", "2026-10-04", "Fuerza", 90)
		]);
		comprobar("Importa 5 entrenos", $r->contar([]) === 5);
		comprobar("Filtra por socio", $r->contar(['socio' => 'Lucía']) === 2);
		comprobar("Filtra por tipo y fechas", $r->contar(['tipo' => 'Fuerza', 'desde' => '2026-10-02', 'hasta' => '2026-10-04']) === 2);
		$pagina = $r->buscar([], 1, 2);
		comprobar("Pagina y ordena por fecha descendente", count($pagina) === 2 && $pagina[0]->fecha === "2026-10-05");
		comprobar("La página 3 tiene el último", count($r->buscar([], 3, 2)) === 1);
		comprobar("Devuelve objetos Entreno", $pagina[0] instanceof Entreno);

		$e = $r->estadisticas([]);
		comprobar("Suma los minutos", $e['minutos'] === 275);
		comprobar("Minutos por tipo, de más a menos", array_keys($e['por_tipo'])[0] === "Fuerza" && $e['por_tipo']['Fuerza'] === 195);
		comprobar("Sesiones por socio", $e['por_socio']['Carlos'] === 2);

		$id = $r->guardar(entreno("Andres", "2026-10-06", "Movilidad", 20));
		comprobar("Guardar uno nuevo devuelve su id", $id > 0 && $r->obtener($id)->socio === "Andres");
		$nuevo = $r->obtener($id);
		$nuevo->minutos = 25;
		$r->guardar($nuevo);
		comprobar("Guardar con id actualiza", (int)$r->obtener($id)->minutos === 25 && $r->contar([]) === 6);
		comprobar("Lista de socios sin repetir", $r->socios() === ["Andres", "Carlos", "Lucía", "Nerea"]);
		comprobar("Elimina", $r->eliminar($id) && $r->obtener($id) === null);
		comprobar("Eliminar lo que no existe devuelve false", $r->eliminar(9999) === false);
	}

	probarRepositorio("SQLite", new RepositorioPDO(new PDO('sqlite::memory:')));

	$json = sys_get_temp_dir()."/diario_prueba_".uniqid().".json";
	probarRepositorio("JSON", new RepositorioJSON($json));
	unlink($json);

	try{
		$mysql = new PDO('mysql:host=localhost;dbname=velnora;charset=utf8mb4', 'root', '');
		// Una tabla temporal tapa a la de verdad mientras dura la conexión: no toco los datos reales
		echo PHP_EOL."(MySQL disponible: pruebo sobre una tabla temporal)".PHP_EOL;
		$mysql->exec("CREATE TEMPORARY TABLE entrenos (id INT AUTO_INCREMENT PRIMARY KEY, socio VARCHAR(40), fecha DATE, tipo VARCHAR(20), minutos INT, intensidad INT, notas TEXT)");
		probarRepositorio("MySQL", new RepositorioPDO($mysql));
	}catch(PDOException $e){
		echo PHP_EOL."(MySQL no está arrancado: me salto sus pruebas)".PHP_EOL;
	}

	echo PHP_EOL."Validación y exportación".PHP_EOL;
	$malo = Entreno::desdeArray(['socio' => '', 'fecha' => '2026-02-30', 'tipo' => 'Golf', 'minutos' => 2, 'intensidad' => 7]);
	comprobar("Detecta 5 errores (incluida una fecha que no existe)", count($malo->validar()) === 5);
	comprobar("Una fecha futura no vale", isset(entreno("A", date('Y-m-d', strtotime('+1 day')), "Fuerza", 60)->validar()['fecha']));
	comprobar("Calcula calorías", entreno("A", "2026-10-01", "Cardio", 60)->calorias() === 396);
	$lista = [Entreno::desdeArray(['socio' => 'Ana & Co', 'fecha' => '2026-10-01', 'tipo' => 'Fuerza', 'minutos' => 60, 'intensidad' => 3, 'notas' => 'Press, "banca"'])];
	comprobar("El XML escapa el &", simplexml_load_string(Exportador::xml($lista))->entreno->socio == "Ana & Co");
	$csv = sys_get_temp_dir()."/diario_".uniqid().".csv";
	file_put_contents($csv, Exportador::csv($lista));
	[$leidos, $errores] = Exportador::leerCSV($csv);
	unlink($csv);
	comprobar("Exportar a CSV y volver a leerlo da lo mismo", !$errores && $leidos[0]->notas === 'Press, "banca"');

	echo PHP_EOL.$bien." correctas, ".$mal." fallos".PHP_EOL;
	exit($mal ? 1 : 0);
?>
