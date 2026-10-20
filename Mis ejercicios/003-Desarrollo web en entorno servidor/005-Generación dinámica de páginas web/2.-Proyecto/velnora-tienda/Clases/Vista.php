<?php
	/**
	 * Vista: carga una plantilla de la carpeta plantillas/ dentro del diseño común.
	 */
	class Vista {

		private array $config;

		public function __construct(array $config){
			$this->config = $config;
		}

		/**
		 * @param string $plantilla Nombre del archivo en plantillas/ sin .php
		 * @param array  $datos     Variables que podrá usar la plantilla
		 */
		public function mostrar(string $plantilla, array $datos = []): void {
			$config = $this->config;
			$vista = $this;
			extract($datos);
			// Primero genero el contenido y luego lo meto en el diseño
			ob_start();
			include __DIR__."/../plantillas/".$plantilla.".php";
			$contenido = ob_get_clean();
			include __DIR__."/../plantillas/diseno.php";
		}

		/** Escapa un texto para ponerlo en el HTML. */
		public function e(mixed $texto): string {
			return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
		}

		public function precio(float $cantidad): string {
			return number_format($cantidad, 2, ',', '.')." ".$this->config['moneda'];
		}

		/** Enlace a una ruta de la aplicación con sus parámetros. */
		public function url(string $ruta, array $parametros = []): string {
			return "index.php?".http_build_query(['ruta' => $ruta] + $parametros);
		}

		/**
		 * Genera un control de formulario según su definición en el modelo.
		 * Es el mismo método para cualquier modelo: el formulario se construye solo.
		 */
		public function control(string $nombre, array $definicion, mixed $valor, ?string $error): string {
			[$etiqueta, $tipo] = $definicion;
			$id = "campo_".$nombre;
			$html = "<div class='campo".($error ? " con-error" : "")."'>";
			if($tipo === 'checkbox'){
				$html .= "<label class='check'><input type='checkbox' id='".$id."' name='".$nombre."' value='1'".($valor ? " checked" : "")."> ".$this->e($etiqueta)."</label>";
			}else{
				$html .= "<label for='".$id."'>".$this->e($etiqueta)."</label>";
				if($tipo === 'select'){
					$html .= "<select id='".$id."' name='".$nombre."'><option value=''>-- Elige --</option>";
					foreach($definicion[2] as $opcion){
						$html .= "<option".($opcion === $valor ? " selected" : "").">".$this->e($opcion)."</option>";
					}
					$html .= "</select>";
				}elseif($tipo === 'textarea'){
					$html .= "<textarea id='".$id."' name='".$nombre."' rows='3'>".$this->e($valor)."</textarea>";
				}else{
					$paso = $tipo === 'number' ? " step='any' min='0'" : "";
					$html .= "<input type='".$tipo."'".$paso." id='".$id."' name='".$nombre."' value='".$this->e($valor)."'>";
				}
			}
			if($error){
				$html .= "<small>".$this->e($error)."</small>";
			}
			return $html."</div>";
		}
	}
?>
