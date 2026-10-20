<?php
	/**
	 * Configuración de la tienda. Todo lo que puede cambiar sin tocar el código está aquí.
	 */
	return [
		'nombre' => 'Velnora Tienda',
		'eslogan' => 'Material y nutrición para entrenar mejor',
		'color' => '#16a34a',
		'moneda' => '€',
		'iva' => 0.21,
		'envio_gratis_desde' => 50,
		'gastos_envio' => 4.95,
		'basededatos' => __DIR__.'/tienda.db',
		// Acceso de administración: usuario admin, contraseña velnora2026 (guardada con password_hash)
		'admin_usuario' => 'admin',
		'admin_hash' => '$2y$10$d8IQTRYkJ7Lg6WyXIJU.ue2XOUwTTDYC.vJ20tGPbimonNbYul2Mu',
		'depuracion' => true
	];
?>
