<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<style>
			body{background:#0f172a;color:#e2e8f0;font-family:Verdana, sans-serif;padding:20px;}
			table{border-collapse:collapse;width:100%;font-size:13px;}
			td{border-bottom:1px solid #334155;padding:6px;word-break:break-all;}
			td:first-child{color:#2dd4bf;width:30%;}
		</style>
	</head>
	<body>
		<h1>Lo que el servidor sabe de mí</h1>
		<table>
			<?php
				foreach($_SERVER as $clave => $valor){
					echo "<tr><td>".$clave."</td><td>".$valor."</td></tr>";
				}
			?>
		</table>
	</body>
</html>
