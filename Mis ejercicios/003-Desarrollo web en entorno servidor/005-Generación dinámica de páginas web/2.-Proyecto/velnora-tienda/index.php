<?php
	/**
	 * Velnora Tienda · punto de entrada único (front controller).
	 * Autor: Andres Ruiz
	 */
	$config = require __DIR__."/config.php";

	ini_set('display_errors', $config['depuracion'] ? '1' : '0');
	error_reporting(E_ALL);

	session_start();

	require_once __DIR__."/Clases/BaseDatos.php";
	require_once __DIR__."/Clases/Producto.php";
	require_once __DIR__."/Clases/Carrito.php";
	require_once __DIR__."/Clases/Vista.php";
	require_once __DIR__."/Clases/Controlador.php";

	$productos = new Producto(BaseDatos::conexion($config['basededatos']));
	$carrito = new Carrito($productos, $config);
	$vista = new Vista($config);
	$controlador = new Controlador($productos, $carrito, $vista, $config);

	$controlador->atender($_GET['ruta'] ?? 'catalogo');
?>
