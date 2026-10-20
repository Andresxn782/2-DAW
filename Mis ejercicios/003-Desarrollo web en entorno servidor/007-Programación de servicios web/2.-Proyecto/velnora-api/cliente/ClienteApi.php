<?php
	/**
	 * Cliente de la API de Velnora hecho con cURL.
	 * Guarda la última petición y respuesta para poder enseñarlas en pantalla.
	 */
	class ClienteApi {
		private string $base;
		private string $clave;
		public array $ultima = [];

		public function __construct(string $base, string $clave) {
			$this->base = $base;
			$this->clave = $clave;
		}

		/**
		 * Hace una petición y devuelve ["codigo" => int, "datos" => array|null].
		 * $ruta es del estilo "/clases/3/reservas".
		 */
		public function pedir(string $metodo, string $ruta, ?array $cuerpo = null, array $parametros = []): array {
			$url = $this->base."?ruta=".rawurlencode($ruta);
			if (count($parametros) > 0) {
				$url .= "&".http_build_query($parametros);
			}
			$cabeceras = ["X-API-Key: ".$this->clave, "Accept: application/json"];

			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_TIMEOUT, 5);
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $metodo);
			if ($cuerpo !== null) {
				curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($cuerpo, JSON_UNESCAPED_UNICODE));
				$cabeceras[] = "Content-Type: application/json";
			}
			curl_setopt($curl, CURLOPT_HTTPHEADER, $cabeceras);
			$texto = curl_exec($curl);
			$codigo = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			$error = curl_error($curl);
			curl_close($curl);

			$this->ultima = [
				"metodo" => $metodo,
				"url" => $url,
				"cuerpo" => $cuerpo,
				"codigo" => $codigo,
				"respuesta" => $texto === false ? "Sin respuesta: ".$error : $texto
			];
			return ["codigo" => $codigo, "datos" => $texto === false ? null : json_decode($texto, true)];
		}
	}
?>
