<?php
	// La primera carga la hace el servidor (con include), así la página
	// ya sale con el lunes aunque JavaScript no funcione.
	$dias = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes"];
?>
<!doctype html>
<html lang="es">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Horario - Velnora</title>
		<style>
			body{font-family:Arial, sans-serif;max-width:700px;margin:20px auto;padding:0 16px;}
			nav{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:12px;}
			nav button{border:1px solid #16a34a;background:white;color:#16a34a;padding:6px 12px;border-radius:20px;cursor:pointer;}
			nav button.activo{background:#16a34a;color:white;}
			.clase{padding:10px;border-left:4px solid #16a34a;background:#f0fdf4;margin-bottom:8px;}
			.clase.completa{border-color:#b91c1c;background:#fef2f2;}
			section{min-height:120px;}
			label{display:block;margin-bottom:12px;}
		</style>
	</head>
	<body>
		<h1>Horario de clases</h1>
		<label><input type="checkbox" id="usarJson"> Pedir JSON y construir el HTML en el navegador</label>
		<nav>
			<?php foreach ($dias as $dia) { ?>
				<button class="<?= $dia === "Lunes" ? "activo" : "" ?>" data-dia="<?= $dia ?>"><?= $dia ?></button>
			<?php } ?>
		</nav>
		<section id="clases">
			<?php $_GET['dia'] = "Lunes"; include "clases.php"; ?>
		</section>
		<p id="origen">Contenido generado por el servidor al cargar la página.</p>

		<!-- Plantilla que se clona por cada clase cuando el servidor manda JSON -->
		<template id="plantillaClase">
			<div class="clase"><strong class="hora"></strong> <span class="nombre"></span> <em class="monitor"></em> · <span class="plazas"></span></div>
		</template>

		<script>
			let seccion = document.querySelector("#clases")
			let origen = document.querySelector("#origen")

			document.querySelectorAll("nav button").forEach(function(boton){
				boton.onclick = function(){
					// Cambio qué botón está marcado
					document.querySelector("nav .activo").classList.remove("activo")
					boton.classList.add("activo")
					let dia = encodeURIComponent(boton.dataset.dia)
					if(document.querySelector("#usarJson").checked){
						cargarJson(dia)
					} else {
						cargarHtml(dia)
					}
				}
			})

			// Opción 1: el servidor manda HTML y lo pego tal cual
			function cargarHtml(dia){
				fetch("clases.php?formato=html&dia=" + dia)
				.then(function(resultado){ return resultado.text() })
				.then(function(html){
					seccion.innerHTML = html
					origen.textContent = "HTML recibido del servidor y colocado con innerHTML."
				})
			}

			// Opción 2: el servidor manda datos y yo construyo los elementos
			function cargarJson(dia){
				fetch("clases.php?formato=json&dia=" + dia)
				.then(function(resultado){ return resultado.json() })
				.then(function(clases){
					seccion.replaceChildren()
					let plantilla = document.querySelector("#plantillaClase")
					clases.forEach(function(clase){
						let copia = plantilla.content.cloneNode(true)
						copia.querySelector(".hora").textContent = clase.hora
						copia.querySelector(".nombre").textContent = clase.nombre
						copia.querySelector(".monitor").textContent = "con " + clase.monitor
						copia.querySelector(".plazas").textContent = clase.plazas === 0 ? "Completa" : clase.plazas + " plazas"
						if(clase.plazas === 0){
							copia.querySelector(".clase").classList.add("completa")
						}
						seccion.appendChild(copia)
					})
					if(clases.length === 0){
						seccion.textContent = "No hay clases ese día."
					}
					origen.textContent = "JSON recibido del servidor; " + clases.length + " elementos creados con la plantilla."
				})
			}
		</script>
	</body>
</html>
