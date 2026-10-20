<?php
	require_once __DIR__."/Modelo.php";

	/**
	 * Productos de la tienda.
	 */
	class Producto extends Modelo {

		public const CATEGORIAS = ["Nutrición", "Accesorios", "Ropa"];

		protected function tabla(): string {
			return "productos";
		}

		public function campos(): array {
			return [
				'nombre' => ['Nombre', 'text'],
				'categoria' => ['Categoría', 'select', self::CATEGORIAS],
				'descripcion' => ['Descripción', 'textarea'],
				'precio' => ['Precio (sin IVA)', 'number'],
				'stock' => ['Unidades en stock', 'number'],
				'destacado' => ['Destacado en portada', 'checkbox']
			];
		}

		/**
		 * Busca por texto y categoría. Los dos filtros son opcionales.
		 * @return array
		 */
		public function filtrar(string $texto = '', string $categoria = ''): array {
			$sql = "SELECT * FROM productos WHERE 1 = 1";
			$valores = [];
			if($texto !== ''){
				$sql .= " AND (nombre LIKE :texto OR descripcion LIKE :texto)";
				$valores[':texto'] = '%'.$texto.'%';
			}
			if(in_array($categoria, self::CATEGORIAS, true)){
				$sql .= " AND categoria = :categoria";
				$valores[':categoria'] = $categoria;
			}
			$consulta = $this->bd->prepare($sql." ORDER BY destacado DESC, nombre");
			$consulta->execute($valores);
			return $consulta->fetchAll();
		}

		/**
		 * Comprueba los datos de un formulario de producto.
		 * @return array Lista de errores (vacía si todo está bien) y datos limpios.
		 */
		public function validar(array $entrada): array {
			$errores = [];
			$datos = [
				'nombre' => trim($entrada['nombre'] ?? ''),
				'categoria' => $entrada['categoria'] ?? '',
				'descripcion' => trim($entrada['descripcion'] ?? ''),
				'precio' => str_replace(',', '.', $entrada['precio'] ?? ''),
				'stock' => $entrada['stock'] ?? '',
				'destacado' => isset($entrada['destacado']) ? 1 : 0
			];
			if($datos['nombre'] === '' || mb_strlen($datos['nombre']) > 80){
				$errores['nombre'] = "El nombre es obligatorio (máximo 80 caracteres).";
			}
			if(!in_array($datos['categoria'], self::CATEGORIAS, true)){
				$errores['categoria'] = "Elige una categoría.";
			}
			if(!is_numeric($datos['precio']) || $datos['precio'] <= 0){
				$errores['precio'] = "El precio debe ser un número mayor que 0.";
			}
			if(filter_var($datos['stock'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) === false){
				$errores['stock'] = "El stock debe ser un número entero, 0 o más.";
			}
			$datos['precio'] = round((float)$datos['precio'], 2);
			$datos['stock'] = (int)$datos['stock'];
			return [$errores, $datos];
		}
	}
?>
