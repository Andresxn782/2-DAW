<?php
	// Funciones que generan controles HTML a partir de un array.
	// Si el valor enviado coincide, el control sale ya marcado.

	function desplegable($nombre, $opciones, $elegido = ""){
		$html = "<select name='".htmlspecialchars($nombre)."'>";
		foreach($opciones as $valor => $texto){
			$marcado = ((string)$valor === (string)$elegido) ? " selected" : "";
			$html .= "<option value='".htmlspecialchars($valor)."'".$marcado.">".htmlspecialchars($texto)."</option>";
		}
		return $html."</select>";
	}

	function casillas($nombre, $opciones, $elegidos = []){
		$html = "";
		foreach($opciones as $valor => $texto){
			$marcado = in_array($valor, $elegidos) ? " checked" : "";
			$html .= "<label><input type='checkbox' name='".htmlspecialchars($nombre)."[]' value='".htmlspecialchars($valor)."'".$marcado."> ".htmlspecialchars($texto)."</label> ";
		}
		return $html;
	}

	function radios($nombre, $opciones, $elegido = ""){
		$html = "";
		foreach($opciones as $valor => $texto){
			$marcado = ((string)$valor === (string)$elegido) ? " checked" : "";
			$html .= "<label><input type='radio' name='".htmlspecialchars($nombre)."' value='".htmlspecialchars($valor)."'".$marcado."> ".htmlspecialchars($texto)."</label> ";
		}
		return $html;
	}
?>
