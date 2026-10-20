<?php
	// La vista pasa a ser una clase: guarda los datos y sabe pintarlos de varias formas
	class VelnoraVista {

		private $json;
		private $datos;

		public function __construct($json){
			$this->json = $json;
			$this->datos = json_decode($json, true);
			if(!is_array($this->datos)){
				$this->datos = [];
			}
		}

		// Tabla con todos los registros
		public function pintaTabla(){
			if(count($this->datos) == 0){
				echo "<p>No hay datos</p>";
				return;
			}
			echo "<table border='1' cellpadding='6'>";
			echo "<thead><tr>";
			foreach($this->datos[0] as $clave => $valor){
				echo "<th>".htmlspecialchars($clave)."</th>";
			}
			echo "</tr></thead><tbody>";
			foreach($this->datos as $fila){
				echo "<tr>";
				foreach($fila as $valor){
					echo "<td>".htmlspecialchars((string)$valor)."</td>";
				}
				echo "</tr>";
			}
			echo "</tbody></table>";
		}

		// Formulario generado con los campos de la primera fila (menos el id)
		public function pintaFormulario($action = "", $method = "POST"){
			if(count($this->datos) == 0){
				echo "<p>No hay estructura para generar el formulario</p>";
				return;
			}
			echo "<form action='".htmlspecialchars($action)."' method='".htmlspecialchars($method)."'>";
			foreach($this->datos[0] as $campo => $valor){
				if($campo == "id"){
					continue;
				}
				// Elijo el tipo de input según el valor de ejemplo
				$tipo = is_numeric($valor) ? "number" : "text";
				if(str_contains($campo, "email")){
					$tipo = "email";
				}
				echo "<p>";
				echo "<label for='".htmlspecialchars($campo)."'>".htmlspecialchars(ucfirst($campo))."</label><br>";
				echo "<input type='".$tipo."' step='any' id='".htmlspecialchars($campo)."' name='".htmlspecialchars($campo)."'>";
				echo "</p>";
			}
			echo "<input type='submit' value='Guardar'>";
			echo "</form>";
		}

		// Tarjetas: misma información, otra presentación
		public function pintaTarjetas($campoTitulo){
			echo "<div style='display:flex;flex-wrap:wrap;gap:10px'>";
			foreach($this->datos as $fila){
				echo "<div style='border:1px solid #0f766e;border-radius:8px;padding:10px;width:200px'>";
				echo "<strong>".htmlspecialchars($fila[$campoTitulo] ?? "")."</strong><br>";
				foreach($fila as $campo => $valor){
					if($campo != $campoTitulo && $campo != "id"){
						echo "<small>".htmlspecialchars($campo).": ".htmlspecialchars((string)$valor)."</small><br>";
					}
				}
				echo "</div>";
			}
			echo "</div>";
		}
	}
?>
