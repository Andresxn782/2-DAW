<?php
	// CONTROLADOR: lee la petición, comprueba permisos, llama al modelo y prepara lo que pinta la vista
	class VelnoraControlador {

		private VelnoraModelo $modelo;

		// Inyección de dependencias: el modelo me llega desde fuera
		public function __construct(VelnoraModelo $modelo){
			$this->modelo = $modelo;
		}

		public function estaConectado(): bool {
			return isset($_SESSION['llave']);
		}

		public function login(string $usuario, string $contrasena): bool {
			$fila = $this->modelo->autentica($usuario, $contrasena);
			if(!$fila){
				return false;
			}
			session_regenerate_id(true);
			$_SESSION['llave'] = "velnora";
			$_SESSION['nombre'] = $fila['nombre'];
			$_SESSION['rol'] = $fila['rol'];
			return true;
		}

		public function logout(): void {
			$_SESSION = [];
			setcookie(session_name(), "", time() - 3600, "/");
			session_destroy();
		}

		public function tablasPermitidas(): array {
			return $this->modelo->dameTablasRol($_SESSION['rol'] ?? '');
		}

		// La tabla de la URL solo vale si está entre las del rol
		public function tablaSolicitada(): string {
			$permitidas = $this->tablasPermitidas();
			$tabla = $_GET['tabla'] ?? '';
			return in_array($tabla, $permitidas, true) ? $tabla : ($permitidas[0] ?? 'socios');
		}

		public function procesaCrud(string $tabla): array {
			$accion = $_GET['accion'] ?? 'listar';
			$pk = $this->modelo->dameClavePrimaria($tabla);

			if(isset($_GET['eliminar']) && $pk){
				$this->modelo->eliminaDatos($tabla, $pk, $_GET['eliminar']);
				$this->redirige($tabla, "baja");
			}
			if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['insertar'])){
				$this->modelo->insertaDatos($tabla, $_POST);
				$this->redirige($tabla, "alta");
			}
			if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar']) && $pk){
				$this->modelo->actualizaDatos($tabla, $pk, $_POST[$pk] ?? null, $_POST);
				$this->redirige($tabla, "cambio");
			}

			return [
				'tabla' => $tabla,
				'accion' => $accion,
				'campos' => array_column($this->modelo->dameCampos($tabla), 'name'),
				'pk' => $pk,
				'registro' => ($accion === 'editar' && isset($_GET['id']) && $pk) ? $this->modelo->dameRegistro($tabla, $pk, $_GET['id']) : [],
				'datos' => $accion === 'listar' ? $this->modelo->dameDatos($tabla) : [],
				'ok' => $_GET['ok'] ?? ''
			];
		}

		private function redirige(string $tabla, string $ok): never {
			header("Location: index.php?tabla=".urlencode($tabla)."&ok=".$ok);
			exit();
		}
	}
?>
