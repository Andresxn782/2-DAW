<?php
	var_dump($_REQUEST);
?>
<form action="?" method="POST">
	<input type="text" name="nombre" placeholder="Nombre">
	<input type="text" name="apellidos" placeholder="Apellidos">
	<input type="text" name="dni" placeholder="DNI">
	<input type="date" name="fecha_nacimiento">
	<input type="email" name="email" placeholder="Email">
	<input type="tel" name="telefono" placeholder="Teléfono">
	<input type="number" name="peso" placeholder="Peso (kg)">
	<input type="number" name="altura" placeholder="Altura (cm)">
	<select name="plan">
		<option>Mensual</option>
		<option>Trimestral</option>
		<option>Anual</option>
	</select>
	<select name="objetivo">
		<option>Perder peso</option>
		<option>Ganar músculo</option>
		<option>Mejorar resistencia</option>
	</select>
	<input type="time" name="horario" title="Hora a la que prefieres entrenar">
	<input type="color" name="color_taquilla" title="Color de tu taquilla">
	<label><input type="checkbox" name="entrenador_personal"> Quiero entrenador personal</label>
	<input type="submit" value="Dar de alta">
</form>
