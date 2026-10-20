<?php
	/**
	 * Envía las respuestas de la API con su código de estado
	 * en JSON (por defecto) o en XML si el cliente lo pide.
	 */
	class Respuesta {
		private string $formato;

		public function __construct() {
			// El cliente elige el formato con ?formato=xml o con la cabecera Accept
			$aceptado = $_SERVER['HTTP_ACCEPT'] ?? "";
			if (($_GET['formato'] ?? "") === "xml" || (str_contains($aceptado, "application/xml") && !str_contains($aceptado, "application/json"))) {
				$this->formato = "xml";
			} else {
				$this->formato = "json";
			}
		}

		public function enviar(int $codigo, array $datos): void {
			http_response_code($codigo);
			if ($this->formato === "xml") {
				header("Content-Type: application/xml; charset=UTF-8");
				$xml = new SimpleXMLElement('<respuesta/>');
				$this->arrayAXml($datos, $xml);
				echo $xml->asXML();
			} else {
				header("Content-Type: application/json; charset=UTF-8");
				echo json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
			}
		}

		public function error(int $codigo, string $mensaje, array $errores = []): void {
			$datos = ["error" => true, "mensaje" => $mensaje];
			if (count($errores) > 0) {
				$datos["errores"] = $errores;
			}
			$this->enviar($codigo, $datos);
		}

		// Las listas (claves 0, 1, 2...) se convierten en etiquetas <elemento>
		private function arrayAXml(array $datos, SimpleXMLElement $nodo): void {
			foreach ($datos as $clave => $valor) {
				$etiqueta = is_int($clave) ? "elemento" : $clave;
				if (is_array($valor)) {
					$this->arrayAXml($valor, $nodo->addChild($etiqueta));
				} else {
					if (is_bool($valor)) {
						$valor = $valor ? "true" : "false";
					}
					$nodo->addChild($etiqueta, htmlspecialchars((string)$valor, ENT_XML1));
				}
			}
		}
	}
?>
