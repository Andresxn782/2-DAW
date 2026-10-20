<?php

include "VelnoraJSON.php";

$velnoraJSON = new VelnoraJSON();


// ================================
// GUARDAR JSON
// ================================

$datos = [
    "nombre" => "Andres",
    "apellidos" => "Ruiz Torres",
    "profesion" => "Profesor",
    "tecnologias" => [
        "PHP",
        "Python",
        "JavaScript",
        "SQL"
    ]
];

$velnoraJSON->guardarJSON("datos.json", $datos);


// ================================
// CARGAR JSON
// ================================

$datosCargados = $velnoraJSON->cargarJSON("datos.json");

print_r($datosCargados);

?>