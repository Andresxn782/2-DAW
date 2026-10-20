<?php
	/*
		Planificador de entrenamiento · Gimnasio Velnora
		Autor: Andres Ruiz
		El socio rellena el formulario y la página le genera un plan semanal,
		que además queda guardado en planes.db.
	*/

	// Catálogo de ejercicios: array multidimensional objetivo -> grupo -> ejercicios
	$catalogo = [
		"fuerza" => [
			"Pierna" => ["Sentadilla", "Peso muerto rumano", "Prensa"],
			"Empuje" => ["Press banca", "Press militar", "Fondos"],
			"Tirón" => ["Dominadas", "Remo con barra", "Curl de bíceps"]
		],
		"resistencia" => [
			"Cardio" => ["Carrera continua", "Remo ergómetro", "Bicicleta"],
			"Circuito" => ["Burpees", "Comba", "Kettlebell swing"],
			"Core" => ["Plancha", "Mountain climbers", "Rueda abdominal"]
		],
		"movilidad" => [
			"Cadera" => ["Sentadilla profunda", "Estiramiento de psoas", "90/90"],
			"Hombro" => ["Rotaciones con banda", "Dislocaciones", "Colgarse de barra"],
			"Columna" => ["Gato-camello", "Rotación torácica", "Puente de glúteo"]
		]
	];
	$diasSemana = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"];

	/**
	 * Devuelve las series y repeticiones según el nivel del socio.
	 */
	function seriesPorNivel($nivel){
		switch($nivel){
			case "principiante":
				return "3 x 10";
			case "intermedio":
				return "4 x 8";
			case "avanzado":
				return "5 x 5";
			default:
				return "3 x 12";
		}
	}

	/**
	 * Reparte los grupos del objetivo entre los días elegidos.
	 * Devuelve un array día -> [grupo, ejercicios].
	 */
	function generarPlan($catalogo, $objetivo, $dias){
		$grupos = array_keys($catalogo[$objetivo]);
		$plan = [];
		for($i = 0; $i < count($dias); $i++){
			$grupo = $grupos[$i % count($grupos)];		// Voy rotando los grupos
			$plan[$dias[$i]] = [$grupo, $catalogo[$objetivo][$grupo]];
		}
		return $plan;
	}

	// Quita espacios y caracteres peligrosos de lo que escribe el usuario
	function limpiar($texto){
		return htmlspecialchars(trim($texto));
	}

	// Base de datos: la creo si no existe
	$db = new SQLite3('planes.db');
	$db->exec("CREATE TABLE IF NOT EXISTS planes (
		id INTEGER PRIMARY KEY AUTOINCREMENT,
		nombre TEXT, objetivo TEXT, nivel TEXT, dias TEXT, fecha TEXT
	)");

	$errores = [];
	$plan = null;

	// Solo proceso el formulario si llega por POST
	if($_SERVER['REQUEST_METHOD'] == "POST"){
		$nombre = trim($_POST['nombre'] ?? "");
		$objetivo = $_POST['objetivo'] ?? "";
		$nivel = $_POST['nivel'] ?? "";
		$dias = $_POST['dias'] ?? [];

		// Validación con if / else if
		if($nombre == ""){
			$errores[] = "Escribe tu nombre.";
		}else if(strlen($nombre) > 40){
			$errores[] = "El nombre es demasiado largo.";
		}
		if(!isset($catalogo[$objetivo])){
			$errores[] = "Elige un objetivo.";
		}
		if(!in_array($nivel, ["principiante", "intermedio", "avanzado"])){
			$errores[] = "Elige un nivel.";
		}
		// Solo acepto días que estén en mi lista
		$dias = array_values(array_intersect($diasSemana, (array)$dias));
		if(count($dias) == 0){
			$errores[] = "Marca al menos un día.";
		}else if(count($dias) > 6){
			$errores[] = "Deja al menos un día de descanso.";
		}

		if(count($errores) == 0){
			$plan = generarPlan($catalogo, $objetivo, $dias);
			$series = seriesPorNivel($nivel);

			$consulta = $db->prepare("INSERT INTO planes (nombre, objetivo, nivel, dias, fecha) VALUES (:nombre, :objetivo, :nivel, :dias, :fecha)");
			$consulta->bindValue(':nombre', $nombre);
			$consulta->bindValue(':objetivo', $objetivo);
			$consulta->bindValue(':nivel', $nivel);
			$consulta->bindValue(':dias', implode(", ", $dias));
			$consulta->bindValue(':fecha', date('Y-m-d H:i'));
			$consulta->execute();
		}
	}

	// Filtro del historial por GET
	$filtro = $_GET['filtro'] ?? "todos";
	if(isset($catalogo[$filtro])){
		$consulta = $db->prepare("SELECT * FROM planes WHERE objetivo = :objetivo ORDER BY id DESC");
		$consulta->bindValue(':objetivo', $filtro);
		$historial = $consulta->execute();
	}else{
		$filtro = "todos";
		$historial = $db->query("SELECT * FROM planes ORDER BY id DESC");
	}
?>
<!doctype html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Planificador · Velnora</title>
		<style>
			*{box-sizing:border-box;}
			body{margin:0;font-family:Arial, sans-serif;background:#fff7ed;color:#1c1917;}
			header{background:#ea580c;color:white;padding:20px 30px;}
			header h1{margin:0;font-size:24px;}
			main{max-width:1000px;margin:20px auto;padding:0 16px;}
			.caja{background:white;border-radius:12px;padding:20px;margin-bottom:20px;box-shadow:0 2px 8px rgba(0,0,0,0.08);}
			label{display:block;font-weight:bold;margin:12px 0 5px;}
			input[type=text], select{width:100%;padding:9px;border:1px solid #d6d3d1;border-radius:6px;}
			.dias{display:flex;flex-wrap:wrap;gap:10px;}
			.dias label{font-weight:normal;margin:0;}
			button{margin-top:15px;background:#ea580c;color:white;border:none;padding:10px 20px;border-radius:6px;font-size:16px;cursor:pointer;}
			.error{background:#fee2e2;color:#991b1b;padding:10px;border-radius:6px;margin-bottom:8px;}
			.plan{display:grid;grid-template-columns:repeat(auto-fill, minmax(200px, 1fr));gap:12px;}
			.dia{border:2px solid #fdba74;border-radius:10px;padding:12px;}
			.dia h3{margin:0 0 5px;color:#c2410c;}
			table{width:100%;border-collapse:collapse;}
			th, td{text-align:left;padding:8px;border-bottom:1px solid #e7e5e4;}
			.filtros a{margin-right:10px;color:#c2410c;}
			.filtros a.activo{font-weight:bold;}
		</style>
	</head>
	<body>
		<header>
			<h1>Gimnasio Velnora · Planificador de entrenamiento</h1>
		</header>
		<main>
			<div class="caja">
				<h2>Crea tu plan semanal</h2>
				<?php foreach($errores as $error){ ?>
					<div class="error"><?php echo $error; ?></div>
				<?php } ?>
				<form method="POST">
					<label>Nombre</label>
					<input type="text" name="nombre" value="<?php echo limpiar($_POST['nombre'] ?? ""); ?>">
					<label>Objetivo</label>
					<select name="objetivo">
						<option value="">-- Elige --</option>
						<?php foreach($catalogo as $clave => $grupos){ ?>
							<option value="<?php echo $clave; ?>" <?php if(($_POST['objetivo'] ?? "") == $clave){ echo "selected"; } ?>><?php echo ucfirst($clave); ?></option>
						<?php } ?>
					</select>
					<label>Nivel</label>
					<select name="nivel">
						<option value="principiante">Principiante</option>
						<option value="intermedio" <?php if(($_POST['nivel'] ?? "") == "intermedio"){ echo "selected"; } ?>>Intermedio</option>
						<option value="avanzado" <?php if(($_POST['nivel'] ?? "") == "avanzado"){ echo "selected"; } ?>>Avanzado</option>
					</select>
					<label>Días disponibles</label>
					<div class="dias">
						<?php foreach($diasSemana as $dia){ ?>
							<label><input type="checkbox" name="dias[]" value="<?php echo $dia; ?>" <?php if(in_array($dia, $_POST['dias'] ?? [])){ echo "checked"; } ?>> <?php echo $dia; ?></label>
						<?php } ?>
					</div>
					<button type="submit">Generar plan</button>
				</form>
			</div>

			<?php if($plan != null){ ?>
			<div class="caja">
				<h2>Plan de <?php echo limpiar($nombre); ?> (<?php echo $series; ?>)</h2>
				<p>
					<?php
						// Consejo distinto según el objetivo
						if($objetivo == "fuerza"){
							echo "Descansa 2 minutos entre series y sube el peso poco a poco.";
						}else if($objetivo == "resistencia"){
							echo "Mantén un ritmo en el que puedas hablar y bebe agua cada 15 minutos.";
						}else{
							echo "Haz cada movimiento despacio y sin dolor.";
						}
					?>
				</p>
				<div class="plan">
					<?php foreach($plan as $dia => $sesion){ ?>
						<div class="dia">
							<h3><?php echo $dia; ?></h3>
							<strong><?php echo $sesion[0]; ?></strong>
							<ol>
								<?php foreach($sesion[1] as $ejercicio){ ?>
									<li><?php echo $ejercicio; ?></li>
								<?php } ?>
							</ol>
						</div>
					<?php } ?>
				</div>
			</div>
			<?php } ?>

			<div class="caja">
				<h2>Historial de planes</h2>
				<p class="filtros">
					Filtrar:
					<a class="<?php echo $filtro == "todos" ? "activo" : ""; ?>" href="?filtro=todos">Todos</a>
					<?php foreach(array_keys($catalogo) as $clave){ ?>
						<a class="<?php echo $filtro == $clave ? "activo" : ""; ?>" href="?filtro=<?php echo $clave; ?>"><?php echo ucfirst($clave); ?></a>
					<?php } ?>
				</p>
				<table>
					<tr><th>Fecha</th><th>Socio</th><th>Objetivo</th><th>Nivel</th><th>Días</th></tr>
					<?php while($fila = $historial->fetchArray(SQLITE3_ASSOC)){ ?>
						<tr>
							<td><?php echo $fila['fecha']; ?></td>
							<td><?php echo limpiar($fila['nombre']); ?></td>
							<td><?php echo ucfirst($fila['objetivo']); ?></td>
							<td><?php echo ucfirst($fila['nivel']); ?></td>
							<td><?php echo $fila['dias']; ?></td>
						</tr>
					<?php } ?>
				</table>
			</div>
		</main>
	</body>
</html>
<?php $db->close(); ?>
