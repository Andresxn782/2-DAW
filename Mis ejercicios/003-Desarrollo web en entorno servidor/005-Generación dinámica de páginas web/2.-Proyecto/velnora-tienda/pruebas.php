<?php
	/**
	 * Pruebas de los modelos de la tienda. Ejecutar: php pruebas.php
	 * Usan una base de datos en memoria: no tocan tienda.db.
	 */
	if(php_sapi_name() !== 'cli'){
		die("Ejecuta las pruebas desde la terminal: php pruebas.php");
	}
	require_once __DIR__."/Clases/BaseDatos.php";
	require_once __DIR__."/Clases/Producto.php";
	require_once __DIR__."/Clases/Carrito.php";

	$config = require __DIR__."/config.php";
	$_SESSION = [];
	$bien = 0;
	$mal = 0;
	function comprobar(string $que, bool $resultado): void {
		global $bien, $mal;
		$resultado ? $bien++ : $mal++;
		echo ($resultado ? "  OK    " : "  FALLO ").$que.PHP_EOL;
	}

	$productos = new Producto(BaseDatos::conexion(':memory:'));
	comprobar("Singleton: siempre la misma conexión", BaseDatos::conexion() === BaseDatos::conexion());
	comprobar("La base de datos nueva trae 10 productos", count($productos->todos()) === 10);
	comprobar("Filtrar por categoría Ropa da 3", count($productos->filtrar('', 'Ropa')) === 3);
	comprobar("Buscar 'proteína' encuentra algo", count($productos->filtrar('proteína')) >= 1);
	comprobar("Una categoría inventada no filtra", count($productos->filtrar('', 'Coches')) === 10);

	[$errores, $datos] = $productos->validar(['nombre' => '', 'categoria' => 'Coches', 'precio' => '-3', 'stock' => '2.5']);
	comprobar("Validar detecta los 4 errores", count($errores) === 4);
	[$errores, $datos] = $productos->validar(['nombre' => 'Toalla', 'categoria' => 'Accesorios', 'precio' => '8,50', 'stock' => '12']);
	comprobar("Validar acepta datos correctos y la coma decimal", !$errores && $datos['precio'] === 8.5);

	$id = $productos->insertar($datos + ['id' => 999, 'trampa' => 'x']);
	comprobar("Insertar ignora campos que no son del modelo", $id === 11);
	$productos->actualizar($id, ['stock' => 1]);
	comprobar("Actualizar cambia el stock", (int)$productos->buscar($id)['stock'] === 1);

	$carrito = new Carrito($productos, $config);
	comprobar("Añadir un producto con stock", $carrito->anadir($id));
	comprobar("No deja pasar del stock", $carrito->anadir($id) === false);
	comprobar("Un producto agotado no se añade", $carrito->anadir(10) === false);
	comprobar("El precio lleva IVA", $carrito->conIva(10) === 12.1);
	comprobar("Pedido pequeño paga envío", $carrito->resumen()['envio'] == $config['gastos_envio']);
	$carrito->anadir(1, 2);
	comprobar("Desde 50 € el envío es gratis", $carrito->resumen()['envio'] == 0);
	$carrito->cambiar(1, 0);
	comprobar("Cantidad 0 quita el producto", $carrito->unidades() === 1);

	$productos->eliminar($id);
	comprobar("Eliminar borra el producto", $productos->buscar($id) === null);

	echo PHP_EOL.$bien." correctas, ".$mal." fallos".PHP_EOL;
	exit($mal ? 1 : 0);
?>
