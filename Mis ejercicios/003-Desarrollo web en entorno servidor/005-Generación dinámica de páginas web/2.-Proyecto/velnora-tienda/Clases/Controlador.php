<?php
	/**
	 * Controlador frontal: recibe la ruta y llama a la acción que corresponde.
	 */
	class Controlador {

		private Producto $productos;
		private Carrito $carrito;
		private Vista $vista;
		private array $config;

		/** Rutas públicas y rutas de administración => método */
		private const RUTAS = [
			'catalogo' => 'catalogo',
			'producto' => 'producto',
			'carrito' => 'carrito',
			'login' => 'login',
			'salir' => 'salir',
			'admin' => 'admin',
			'editar' => 'editar',
			'borrar' => 'borrar'
		];
		private const PRIVADAS = ['admin', 'editar', 'borrar'];

		public function __construct(Producto $productos, Carrito $carrito, Vista $vista, array $config){
			$this->productos = $productos;
			$this->carrito = $carrito;
			$this->vista = $vista;
			$this->config = $config;
		}

		public function atender(string $ruta): void {
			$metodo = self::RUTAS[$ruta] ?? 'catalogo';
			if(in_array($ruta, self::PRIVADAS, true) && !$this->esAdmin()){
				$this->redirige('login');
			}
			$this->$metodo();
		}

		private function esAdmin(): bool {
			return ($_SESSION['admin'] ?? false) === true;
		}

		private function redirige(string $ruta, array $parametros = []): never {
			header("Location: ".$this->vista->url($ruta, $parametros));
			exit();
		}

		/** Guarda un aviso para mostrarlo en la siguiente página. */
		private function aviso(string $texto, string $tipo = 'ok'): void {
			$_SESSION['aviso'] = [$texto, $tipo];
		}

		private function catalogo(): void {
			$texto = trim($_GET['q'] ?? '');
			$categoria = $_GET['categoria'] ?? '';
			// Añadir al carrito desde el catálogo
			if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['anadir'])){
				$ok = $this->carrito->anadir((int)$_POST['anadir']);
				$this->aviso($ok ? "Añadido al carrito." : "No quedan más unidades de ese producto.", $ok ? 'ok' : 'error');
				$this->redirige('catalogo', array_filter(['q' => $texto, 'categoria' => $categoria]));
			}
			$this->vista->mostrar('catalogo', [
				'productos' => $this->productos->filtrar($texto, $categoria),
				'texto' => $texto,
				'categoria' => $categoria,
				'categorias' => Producto::CATEGORIAS,
				'carrito' => $this->carrito
			]);
		}

		private function producto(): void {
			$producto = $this->productos->buscar((int)($_GET['id'] ?? 0));
			if(!$producto){
				http_response_code(404);
				$this->vista->mostrar('error', ['mensaje' => "Ese producto no existe.", 'carrito' => $this->carrito]);
				return;
			}
			if($_SERVER['REQUEST_METHOD'] === 'POST'){
				$ok = $this->carrito->anadir($producto['id'], (int)($_POST['cantidad'] ?? 1));
				$this->aviso($ok ? "Añadido al carrito." : "No hay tantas unidades.", $ok ? 'ok' : 'error');
				$this->redirige('producto', ['id' => $producto['id']]);
			}
			$this->vista->mostrar('producto', ['producto' => $producto, 'carrito' => $this->carrito]);
		}

		private function carrito(): void {
			if($_SERVER['REQUEST_METHOD'] === 'POST'){
				if(isset($_POST['vaciar'])){
					$this->carrito->vaciar();
				}else{
					foreach($_POST['cantidad'] ?? [] as $id => $cantidad){
						$this->carrito->cambiar((int)$id, (int)$cantidad);
					}
				}
				$this->aviso("Carrito actualizado.");
				$this->redirige('carrito');
			}
			$this->vista->mostrar('carrito', ['carrito' => $this->carrito]);
		}

		private function login(): void {
			$error = false;
			if($_SERVER['REQUEST_METHOD'] === 'POST'){
				if(($_POST['usuario'] ?? '') === $this->config['admin_usuario'] && password_verify($_POST['contrasena'] ?? '', $this->config['admin_hash'])){
					session_regenerate_id(true);
					$_SESSION['admin'] = true;
					$this->redirige('admin');
				}
				$error = true;
			}
			$this->vista->mostrar('login', ['error' => $error, 'carrito' => $this->carrito]);
		}

		private function salir(): void {
			unset($_SESSION['admin']);
			$this->redirige('catalogo');
		}

		private function admin(): void {
			$this->vista->mostrar('admin', ['productos' => $this->productos->todos(), 'carrito' => $this->carrito]);
		}

		/** Alta (sin id) o edición (con id) con el mismo formulario. */
		private function editar(): void {
			$id = (int)($_GET['id'] ?? 0);
			$producto = $id ? $this->productos->buscar($id) : null;
			$valores = $producto ?? ['nombre' => '', 'categoria' => '', 'descripcion' => '', 'precio' => '', 'stock' => 0, 'destacado' => 0];
			$errores = [];
			if($_SERVER['REQUEST_METHOD'] === 'POST'){
				[$errores, $valores] = $this->productos->validar($_POST);
				if(!$errores){
					if($producto){
						$this->productos->actualizar($id, $valores);
						$this->aviso("Producto guardado.");
					}else{
						$this->productos->insertar($valores);
						$this->aviso("Producto creado.");
					}
					$this->redirige('admin');
				}
			}
			$this->vista->mostrar('formulario', [
				'campos' => $this->productos->campos(),
				'valores' => $valores,
				'errores' => $errores,
				'editando' => $producto !== null,
				'id' => $id,
				'carrito' => $this->carrito
			]);
		}

		private function borrar(): void {
			if($_SERVER['REQUEST_METHOD'] === 'POST'){
				$this->productos->eliminar((int)($_POST['id'] ?? 0));
				$this->aviso("Producto borrado.");
			}
			$this->redirige('admin');
		}
	}
?>
