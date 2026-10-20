<?php

include "vista.php";
include "modelo.php";
include "controlador.php";

$clientes = VelnoraModelo::dameDatosEstatico(
    "clientes.db",
    "clientes"
);

$json = coseJSON($clientes);

$vista = new VelnoraVista($json);
$vista->pintaTabla();

?>