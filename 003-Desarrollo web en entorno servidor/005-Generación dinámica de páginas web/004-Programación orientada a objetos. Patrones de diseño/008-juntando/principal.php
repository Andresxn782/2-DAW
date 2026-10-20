<?php
	include "vista.php";
  include "modelo.php";
  include "controlador.php";
  
  $basededatos = new VelnoraModelo("clientes.db");
  $clientes = $basededatos->dameDatos("clientes");
  $json = coseJSON($clientes);
  $vista = new VelnoraVista($json);
  $vista->pintaTabla();

?>