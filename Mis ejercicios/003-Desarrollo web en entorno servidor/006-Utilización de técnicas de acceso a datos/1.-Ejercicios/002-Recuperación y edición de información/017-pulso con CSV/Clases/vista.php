<?php
	// VISTA: solo genera HTML con lo que le pasa el controlador. No conoce la base de datos.
	class VelnoraVista {

		private array $config;

		// La vista recibe la configuración para saber nombre, logo y color
		public function __construct(array $config = []){
			$this->config = $config;
		}

		protected function h(mixed $valor): string {
			return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
		}

		public function principio(): void { ?>
<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>Pulso · Velnora</title>
	<link rel="stylesheet" href="css/estilo.css">
	<?php $this->estilosExtra(); ?>
</head>
<body>
<?php
		}

		// El color corporativo sustituye al de estilo.css
		protected function estilosExtra(): void {
			$color = $this->h($this->config['color'] ?? '#2dd4bf');
			echo "<style>:root{--acento:".$color.";--colorcorporativo:".$color."}</style>";
		}

		protected function nombreApp(): string { return $this->config['nombre'] ?? "PULSO"; }
		protected function subtitulo(): string { return $this->config['subtitulo'] ?? "Gimnasio Velnora"; }
		protected function logo(): string { return $this->config['logo'] ?? "P"; }

		public function final(): void {
			echo "</body>\n</html>";
		}

		public function login(bool $error): void { ?>
<div class="login-pantalla">
	<div class="login-caja">
		<div class="login-logo"><?= $this->h($this->logo()) ?></div>
		<h1><?= $this->h($this->nombreApp()) ?></h1>
		<p><?= $this->h($this->subtitulo()) ?></p>
		<form method="POST" action="login.php">
			<?php if($error){ ?><div class="aviso-error">Usuario o contraseña incorrectos.</div><?php } ?>
			<label>Usuario</label>
			<input type="text" name="usuario" autocomplete="username" required>
			<label>Contraseña</label>
			<input type="password" name="contrasena" autocomplete="current-password" required>
			<input class="boton boton-primario boton-login" type="submit" value="Entrar">
		</form>
	</div>
</div>
<?php
		}

		public function abrePanel(string $nombre, array $tablas, array $estado): void {
			$tabla = $estado['tabla']; ?>
<header>
	<div class="marca"><strong><?= $this->h($this->nombreApp()) ?></strong><span><?= $this->h($this->subtitulo()) ?></span></div>
	<div class="usuario"><span><?= $this->h($nombre) ?></span><a href="matarsesion.php">Cerrar sesión</a></div>
</header>
<main>
	<nav>
		<div class="nav-titulo">Gestión</div>
		<?php foreach($tablas as $t){ ?>
			<a class="nav-link<?= $t === $tabla ? ' activa' : '' ?>" href="?tabla=<?= urlencode($t) ?>"><?= $this->h($t) ?></a>
		<?php } ?>
	</nav>
	<section>
		<div class="titulo-pagina">
			<div>
				<h1><?= $this->h($tabla) ?></h1>
				<p>Registros de <?= $this->h($tabla) ?> del gimnasio.</p>
			</div>
			<?php if($estado['accion'] === 'listar'){ ?>
				<a class="boton boton-primario" href="?tabla=<?= urlencode($tabla) ?>&accion=nuevo">+ Añadir</a>
			<?php }else{ ?>
				<a class="boton" href="?tabla=<?= urlencode($tabla) ?>">Volver al listado</a>
			<?php } ?>
		</div>
<?php
			$mensajes = ["alta" => "Registro añadido.", "cambio" => "Cambios guardados.", "baja" => "Registro eliminado."];
			if(isset($mensajes[$estado['ok']])){
				echo "<div class='aviso-ok'>".$mensajes[$estado['ok']]."</div>";
			}
		}

		public function cierraPanel(): void {
			echo "\t</section>\n</main>\n";
		}

		public function pintaTabla(array $estado): void {
			$tabla = urlencode($estado['tabla']); ?>
<div class="caja tabla-contenedor">
	<table>
		<thead>
			<tr><?php foreach($estado['campos'] as $c){ ?><th><?= $this->h($c) ?></th><?php } ?><th>Acciones</th></tr>
		</thead>
		<tbody>
			<?php foreach($estado['datos'] as $fila){ $id = urlencode((string)$fila[$estado['pk']]); ?>
			<tr>
				<?php foreach($estado['campos'] as $c){ ?><td><?= $this->h($fila[$c] ?? '') ?></td><?php } ?>
				<td class="acciones">
					<a href="?tabla=<?= $tabla ?>&accion=editar&id=<?= $id ?>">Editar</a>
					<a class="eliminar" onclick="return confirm('¿Seguro que quieres borrar este registro?')" href="?tabla=<?= $tabla ?>&eliminar=<?= $id ?>">Borrar</a>
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
<?php
		}

		// Formulario generado dinámicamente con las columnas de la tabla
		public function pintaFormulario(array $estado): void {
			$editando = $estado['accion'] === 'editar'; ?>
<div class="caja formulario-caja">
	<h2><?= $editando ? "Editar registro" : "Nuevo registro" ?></h2>
	<form method="POST">
		<?php foreach($estado['campos'] as $n){ ?>
			<?php if($n === $estado['pk'] && !$editando){ continue; } ?>
			<div class="campo">
				<label><?= $this->h($n) ?></label>
				<input type="<?= $this->tipoCampo($n) ?>" step="any" name="<?= $this->h($n) ?>" value="<?= $this->h($estado['registro'][$n] ?? '') ?>" <?= $n === $estado['pk'] ? "readonly" : "required" ?>>
			</div>
		<?php } ?>
		<input class="boton boton-primario" type="submit" name="<?= $editando ? 'actualizar' : 'insertar' ?>" value="<?= $editando ? 'Guardar cambios' : 'Añadir' ?>">
	</form>
</div>
<?php
		}

		// Elijo el tipo de input por el nombre de la columna
		private function tipoCampo(string $campo): string {
			if(str_contains($campo, "email")){ return "email"; }
			if(str_contains($campo, "fecha")){ return "date"; }
			if(in_array($campo, ["precio", "importe", "meses", "unidades"]) || str_ends_with($campo, "_id")){ return "number"; }
			return "text";
		}
	}
?>
