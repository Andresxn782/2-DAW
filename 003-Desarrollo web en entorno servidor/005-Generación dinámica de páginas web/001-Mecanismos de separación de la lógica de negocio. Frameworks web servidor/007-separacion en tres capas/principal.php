<?php
	include "modelo.php";
  include "vista.php";
  include "controlador.php";
  
  $datos = dameDatos("clientes");
  $json = coseJson($datos);
  
  $vista = new VelnoraVista($json);
  $vista->pintaFormulario();
  
?>