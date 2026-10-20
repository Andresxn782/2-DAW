<?php
	// Punto de entrada único de la API (controlador frontal).
	// La ruta se puede pasar de dos formas:
	//   api/index.php/clases/3
	//   api/index.php?ruta=/clases/3
	require __DIR__."/../Clases/BaseDatos.php";
	require __DIR__."/../Clases/Respuesta.php";
	require __DIR__."/../Clases/ServicioClases.php";
	$config = require __DIR__."/../config.php";

	$respuesta = new Respuesta();

	// Cabeceras para que la API se pueda usar desde JavaScript de otra web
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
	header("Access-Control-Allow-Headers: Content-Type, X-API-Key, Accept");

	$metodo = $_SERVER['REQUEST_METHOD'];
	if ($metodo === "OPTIONS") {
		// Petición previa del navegador (CORS): basta con las cabeceras
		http_response_code(204);
		exit;
	}

	$ruta = $_GET['ruta'] ?? ($_SERVER['PATH_INFO'] ?? "/");
	$partes = array_values(array_filter(explode("/", $ruta), "strlen"));

	// Ruta pública para saber si el servicio está vivo
	if ($partes === ["estado"] && $metodo === "GET") {
		$respuesta->enviar(200, ["servicio" => "API de clases de Velnora", "version" => "1.0", "hora" => date('c')]);
		exit;
	}

	// Autenticación con la cabecera X-API-Key
	$clave = $_SERVER['HTTP_X_API_KEY'] ?? "";
	$cliente = null;
	foreach ($config['claves'] as $claveValida => $datos) {
		if (hash_equals($claveValida, $clave)) {
			$cliente = $datos;
		}
	}
	if ($cliente === null) {
		$respuesta->error(401, "Falta la cabecera X-API-Key o la clave no es válida");
		exit;
	}

	// Leo el cuerpo: JSON o, si no, un formulario normal
	$cuerpo = [];
	if ($metodo === "POST" || $metodo === "PUT") {
		$texto = file_get_contents("php://input");
		$tipo = $_SERVER['CONTENT_TYPE'] ?? "";
		if (str_contains($tipo, "application/json")) {
			$cuerpo = json_decode($texto, true);
			if (!is_array($cuerpo)) {
				$respuesta->error(400, "El cuerpo no es un JSON válido");
				exit;
			}
		} else if ($metodo === "POST") {
			$cuerpo = $_POST;
		} else {
			parse_str($texto, $cuerpo);
		}
	}

	// Comprueba que el cliente tiene permiso de administración
	function exigirAdmin($cliente) {
		if ($cliente['permiso'] !== "admin") {
			throw new ErrorApi(403, "La clave de ".$cliente['nombre']." no puede gestionar clases");
		}
	}

	try {
		$servicio = new ServicioClases(BaseDatos::conectar($config['base_datos']), $config['dias']);
		$recurso = $partes[0] ?? "";
		$id = isset($partes[1]) ? filter_var($partes[1], FILTER_VALIDATE_INT) : null;
		$subrecurso = $partes[2] ?? null;
		if ($id === false || count($partes) > 3) {
			throw new ErrorApi(404, "Ruta no encontrada: ".$ruta);
		}

		// Tabla de rutas: recurso + si lleva id + subrecurso => métodos permitidos
		if ($recurso === "clases" && $id === null) {
			if ($metodo === "GET") {
				$clases = $servicio->listarClases($_GET);
				$respuesta->enviar(200, ["total" => count($clases), "clases" => $clases]);
			} else if ($metodo === "POST") {
				exigirAdmin($cliente);
				$clase = $servicio->crearClase($cuerpo);
				// 201 Created y la dirección del recurso nuevo
				header("Location: index.php/clases/".$clase['id']);
				$respuesta->enviar(201, $clase);
			} else {
				throw new ErrorApi(405, "Método no permitido en /clases");
			}
		} else if ($recurso === "clases" && $subrecurso === null) {
			if ($metodo === "GET") {
				$respuesta->enviar(200, $servicio->obtenerClase($id));
			} else if ($metodo === "PUT") {
				exigirAdmin($cliente);
				$respuesta->enviar(200, $servicio->modificarClase($id, $cuerpo));
			} else if ($metodo === "DELETE") {
				exigirAdmin($cliente);
				$respuesta->enviar(200, $servicio->borrarClase($id));
			} else {
				throw new ErrorApi(405, "Método no permitido en /clases/".$id);
			}
		} else if ($recurso === "clases" && $subrecurso === "reservas") {
			if ($metodo === "GET") {
				$respuesta->enviar(200, $servicio->reservasDeClase($id));
			} else if ($metodo === "POST") {
				$respuesta->enviar(201, $servicio->reservar($id, $cuerpo));
			} else {
				throw new ErrorApi(405, "Método no permitido en /clases/".$id."/reservas");
			}
		} else if ($recurso === "reservas" && $id !== null && $subrecurso === null) {
			if ($metodo === "DELETE") {
				$respuesta->enviar(200, $servicio->cancelarReserva($id));
			} else {
				throw new ErrorApi(405, "Método no permitido en /reservas/".$id);
			}
		} else if ($recurso === "socios" && $id === null) {
			if ($metodo === "GET") {
				$socios = $servicio->listarSocios();
				$respuesta->enviar(200, ["total" => count($socios), "socios" => $socios]);
			} else {
				throw new ErrorApi(405, "Método no permitido en /socios");
			}
		} else {
			throw new ErrorApi(404, "Ruta no encontrada: ".$ruta);
		}
	} catch (ErrorApi $error) {
		if ($error->getCode() === 405) {
			header("Allow: GET, POST, PUT, DELETE");
		}
		$respuesta->error($error->getCode(), $error->getMessage(), $error->errores);
	} catch (Exception $error) {
		// No enseño el error interno al cliente; lo dejo en el registro de PHP
		error_log("API Velnora: ".$error->getMessage());
		$respuesta->error(500, "Error interno del servidor");
	}
?>
