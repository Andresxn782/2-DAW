<?php
	// 018: junto la estructura con estilo (017) y el panel de tablas (015)

	$db = new SQLite3('gimnasio.db');

	// 1.-Lista de tablas para el menú
	$result = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name != 'sqlite_sequence'");
	$tablas = [];
	while ($fila = $result->fetchArray(SQLITE3_ASSOC)) {
		$tablas[] = $fila['name'];
	}

	// 2.-Tabla elegida (solo si existe)
	$tabla = (isset($_GET['tabla']) && in_array($_GET['tabla'], $tablas)) ? $_GET['tabla'] : "socios";

	// 3.-Columnas de esa tabla, sin el identificador
	$result = $db->query("PRAGMA table_info(".$tabla.")");
	$campos = [];
	while ($column = $result->fetchArray(SQLITE3_ASSOC)) {
		if($column['pk'] == 1){
			continue;
		}
		$campos[] = $column['name'];
	}

	// 4.-Si me han enviado el formulario, inserto la fila
	$mensaje = "";
	if(isset($_POST['insertar'])){
		$huecos = array_fill(0, count($campos), "?");
		$consulta = $db->prepare("INSERT INTO ".$tabla." (".implode(",", $campos).") VALUES (".implode(",", $huecos).")");
		foreach($campos as $posicion => $campo){
			$consulta->bindValue($posicion + 1, trim($_POST[$campo]));
		}
		$consulta->execute();
		$mensaje = "Registro guardado en ".$tabla;
	}
?>
<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Panel Velnora</title>
		<style>
			*{
				box-sizing:border-box;
			}
			body{
				margin:0;
				font-family:Verdana, sans-serif;
				background:#0f172a;
				color:#e2e8f0;
			}
			header{
				height:60px;
				background:#020617;
				display:flex;
				align-items:center;
				padding:0 20px;
				border-bottom:3px solid #2dd4bf;
			}
			header h1{
				margin:0;
				font-size:20px;
				color:#2dd4bf;
				letter-spacing:2px;
			}
			main{
				display:flex;
				min-height:calc(100vh - 100px);
			}
			nav{
				width:220px;
				background:#1e293b;
				padding:15px;
			}
			section{
				flex:1;
				margin:20px;
				padding:25px;
				background:#1e293b;
				border-radius:14px;
			}
			footer{
				height:40px;
				text-align:center;
				font-size:12px;
				color:#64748b;
			}
			nav a{
				display:block;
				margin-bottom:6px;
			}
			nav button{
				width:100%;
				padding:10px;
				text-align:left;
				background:transparent;
				color:#e2e8f0;
				border:0;
				border-radius:8px;
				cursor:pointer;
				text-transform:capitalize;
			}
			nav button:hover, nav .activa button{
				background:#134e4a;
				color:#5eead4;
			}
			h2{
				margin-top:0;
				text-transform:capitalize;
			}
			form{
				display:grid;
				grid-template-columns:repeat(auto-fill, minmax(180px, 1fr));
				gap:10px;
			}
			input{
				padding:10px;
				border-radius:8px;
				border:1px solid #334155;
				background:#0f172a;
				color:#e2e8f0;
			}
			input[type=submit]{
				background:#2dd4bf;
				color:#0f172a;
				font-weight:bold;
				cursor:pointer;
			}
			.mensaje{
				background:#134e4a;
				padding:10px;
				border-radius:8px;
			}
			table{
				margin-top:25px;
				border-collapse:collapse;
				width:100%;
			}
			th{
				text-align:left;
				color:#2dd4bf;
				border-bottom:2px solid #2dd4bf;
			}
			th, td{
				padding:10px;
			}
			tr:nth-child(even) td{
				background:#0f172a;
			}
		</style>
	</head>
	<body>
		<header>
			<h1>VELNORA · GIMNASIO</h1>
		</header>
		<main>
			<nav>
				<?php
					foreach($tablas as $nombre){
						$clase = ($nombre == $tabla) ? "activa" : "";
						echo "<a class='".$clase."' href='?tabla=".$nombre."'><button>".$nombre."</button></a>";
					}
				?>
			</nav>
			<section>
				<h2><?php echo $tabla; ?></h2>

				<?php if($mensaje != ""){ echo "<p class='mensaje'>".$mensaje."</p>"; } ?>

				<form action="?tabla=<?php echo $tabla; ?>" method="POST">
					<?php
						foreach($campos as $campo){
							echo "<input type='text' name='".$campo."' placeholder='".$campo."' required>";
						}
					?>
					<input type="submit" name="insertar" value="Guardar">
				</form>

				<table>
					<?php
						$result = $db->query("SELECT * FROM ".$tabla);
						$primera = true;
						while ($fila = $result->fetchArray(SQLITE3_ASSOC)) {
							if($primera){
								echo "<tr>";
								foreach(array_keys($fila) as $columna){
									echo "<th>".$columna."</th>";
								}
								echo "</tr>";
								$primera = false;
							}
							echo "<tr>";
							foreach ($fila as $valor) {
								echo "<td>".htmlspecialchars($valor)."</td>";
							}
							echo "</tr>";
						}
						$db->close();
					?>
				</table>
			</section>
		</main>
		<footer>
			Panel de gestión · Andres Ruiz
		</footer>
	</body>
</html>
