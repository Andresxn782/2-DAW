<?php
	include "includes/inicio.php";

	$errores = [];
	$importados = null;
	if($_SERVER['REQUEST_METHOD'] === 'POST' && tokenValido()){
		$archivo = $_FILES['csv'] ?? null;
		if(!$archivo || $archivo['error'] !== UPLOAD_ERR_OK){
			$errores[] = "No se ha recibido ningún archivo.";
		}elseif($archivo['size'] > 1024 * 1024){
			$errores[] = "El archivo pasa de 1 MB.";
		}else{
			[$validos, $errores] = Exportador::leerCSV($archivo['tmp_name']);
			// Si hay alguna línea mala no importo nada: así no queda a medias
			if(!$errores && $validos){
				$importados = $repositorio->importar($validos);
				aviso($importados." entrenos importados.");
				header("Location: index.php");
				exit();
			}elseif(!$validos && !$errores){
				$errores[] = "El archivo no tiene filas.";
			}
		}
	}
	include "includes/cabecera.php";
?>
<div class="caja estrecha">
	<h1>Importar entrenos desde CSV</h1>
	<p>El archivo debe tener la cabecera <code>socio,fecha,tipo,minutos,intensidad,notas</code>. Puedes usar <a href="ejemplo.csv">ejemplo.csv</a> como plantilla, o el CSV que da "Exportar".</p>
	<?php if($errores){ ?>
		<div class="aviso error"><strong>No se ha importado nada:</strong><br><?= implode("<br>", array_map('e', $errores)) ?></div>
	<?php } ?>
	<form method="POST" enctype="multipart/form-data">
		<input type="hidden" name="token" value="<?= e($_SESSION['token']) ?>">
		<input type="file" name="csv" accept=".csv,text/csv" required>
		<button>Importar</button>
	</form>
</div>
<?php include "includes/pie.php"; ?>
