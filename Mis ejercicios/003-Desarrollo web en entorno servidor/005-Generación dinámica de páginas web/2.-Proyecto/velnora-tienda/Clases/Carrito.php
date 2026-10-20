<?php
	/**
	 * Carrito guardado en la sesión: id del producto => cantidad.
	 */
	class Carrito {

		private Producto $productos;
		private array $config;

		public function __construct(Producto $productos, array $config){
			$this->productos = $productos;
			$this->config = $config;
			if(!isset($_SESSION['carrito'])){
				$_SESSION['carrito'] = [];
			}
		}

		/** Añade unidades sin pasar del stock. @return bool false si no hay stock. */
		public function anadir(int $id, int $cantidad = 1): bool {
			$producto = $this->productos->buscar($id);
			if(!$producto){
				return false;
			}
			$nueva = ($_SESSION['carrito'][$id] ?? 0) + max(1, $cantidad);
			if($nueva > $producto['stock']){
				return false;
			}
			$_SESSION['carrito'][$id] = $nueva;
			return true;
		}

		/** Cambia la cantidad. 0 lo quita del carrito. */
		public function cambiar(int $id, int $cantidad): void {
			$producto = $this->productos->buscar($id);
			if($cantidad <= 0 || !$producto){
				unset($_SESSION['carrito'][$id]);
				return;
			}
			$_SESSION['carrito'][$id] = min($cantidad, (int)$producto['stock']);
		}

		public function vaciar(): void {
			$_SESSION['carrito'] = [];
		}

		public function unidades(): int {
			return array_sum($_SESSION['carrito']);
		}

		/** @return array Líneas con el producto, la cantidad y el subtotal con IVA. */
		public function lineas(): array {
			$lineas = [];
			foreach($_SESSION['carrito'] as $id => $cantidad){
				$producto = $this->productos->buscar((int)$id);
				if($producto){
					$precio = $this->conIva($producto['precio']);
					$lineas[] = ['producto' => $producto, 'cantidad' => $cantidad, 'precio' => $precio, 'subtotal' => round($precio * $cantidad, 2)];
				}
			}
			return $lineas;
		}

		/** @return array subtotal, envío y total. */
		public function resumen(): array {
			$subtotal = array_sum(array_column($this->lineas(), 'subtotal'));
			$envio = ($subtotal == 0 || $subtotal >= $this->config['envio_gratis_desde']) ? 0 : $this->config['gastos_envio'];
			return ['subtotal' => round($subtotal, 2), 'envio' => $envio, 'total' => round($subtotal + $envio, 2)];
		}

		public function conIva(float $precio): float {
			return round($precio * (1 + $this->config['iva']), 2);
		}
	}
?>
