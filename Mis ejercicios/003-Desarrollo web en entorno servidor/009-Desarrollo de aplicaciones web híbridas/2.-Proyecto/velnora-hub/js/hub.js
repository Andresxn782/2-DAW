// Velnora Hub: cada tarjeta pide su fuente a api.php por separado.

function pedir(fuente){
	return fetch("api.php?fuente=" + fuente).then(function(respuesta){
		return respuesta.json().then(function(datos){
			if(!respuesta.ok){
				throw new Error(datos.error || "Error " + respuesta.status)
			}
			return datos
		})
	})
}

// Rellena una tarjeta: si la fuente falla, solo esa tarjeta muestra el error
function tarjeta(id, fuente, pintar){
	let caja = document.getElementById(id)
	return pedir(fuente)
	.then(function(respuesta){
		caja.querySelector(".contenido").replaceChildren()
		pintar(respuesta.datos, caja.querySelector(".contenido"))
		let origen = caja.querySelector(".origen")
		let hora = new Date(respuesta.guardado).toLocaleTimeString("es-ES", { hour: "2-digit", minute: "2-digit" })
		origen.textContent = origen.textContent + (respuesta.desde_cache ? " · en caché desde las " : " · actualizado a las ") + hora
		return respuesta.datos
	})
	.catch(function(error){
		let aviso = document.createElement("p")
		aviso.className = "error"
		aviso.textContent = error.message
		caja.querySelector(".contenido").replaceChildren(aviso)
	})
	.finally(function(){
		caja.removeAttribute("aria-busy")
	})
}

function crear(etiqueta, texto, clase){
	let elemento = document.createElement(etiqueta)
	if(texto !== undefined){
		elemento.textContent = texto
	}
	if(clase){
		elemento.className = clase
	}
	return elemento
}

tarjeta("tarjetaTiempo", "tiempo", function(datos, contenido){
	let hoy = datos.dias[0]
	contenido.append(
		crear("p", hoy.entrenar_fuera ? "Sí, hoy se puede entrenar al aire libre." : "Hoy mejor bajo techo.", hoy.entrenar_fuera ? "veredicto si" : "veredicto no")
	)
	let lista = crear("ul", undefined, "lista")
	let nombres = ["Hoy", "Mañana", "Pasado"]
	datos.dias.forEach(function(dia, i){
		lista.append(crear("li", nombres[i] + ": " + dia.minima + "º / " + dia.maxima + "º · lluvia " + dia.lluvia + "%" + (dia.cielo ? " · " + dia.cielo : "")))
	})
	contenido.append(lista)
	document.querySelector("#tarjetaTiempo .origen").textContent = "Fuente: " + datos.origen + " · " + datos.municipio
	// Si hoy hace bueno, marco las clases al aire libre
	document.querySelectorAll("#tarjetaClases .aire-libre").forEach(function(clase){
		clase.classList.add(hoy.entrenar_fuera ? "buen-tiempo" : "mal-tiempo")
		clase.title = hoy.entrenar_fuera ? "Hace bueno para esta clase" : "Puede que esta clase se pase a interior"
	})
})

tarjeta("tarjetaAfluencia", "afluencia", function(datos, contenido){
	let textos = { lleno: "Ahora mismo suele estar lleno.", normal: "Ahora mismo hay un ambiente normal.", tranquilo: "Ahora mismo suele estar tranquilo.", cerrado: "Ahora mismo está cerrado (abrimos de 6 a 23)." }
	contenido.append(crear("p", textos[datos.ahora], "veredicto " + datos.ahora))
	if(datos.mejor_hora_restante !== null){
		contenido.append(crear("p", "La hora más tranquila que queda hoy: " + datos.mejor_hora_restante + ":00."))
	}
	document.querySelector("#tarjetaAfluencia .origen").textContent = "Fuente: " + datos.registros_leidos.toLocaleString("es-ES") + " accesos del torno (media de " + datos.semanas + " " + datos.dia.toLowerCase() + "s)"
	if(window.Chart){
		new Chart(document.querySelector("#graficaAfluencia"), {
			type: "bar",
			data: {
				labels: datos.horas.map(function(h){ return h + "h" }),
				datasets: [{
					data: datos.medias,
					// La hora actual en naranja
					backgroundColor: datos.horas.map(function(h){ return h === datos.hora_actual ? "#ea580c" : "#16a34a" })
				}]
			},
			options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, title: { display: true, text: "personas de media" } } } }
		})
	}
})

tarjeta("tarjetaRutas", "rutas", function(datos, contenido){
	let plantilla = document.querySelector("#plantillaRuta")
	let colores = { "Fácil": "#16a34a", "Medio": "#ea580c", "Difícil": "#dc2626" }
	datos.rutas.forEach(function(ruta){
		let copia = plantilla.content.cloneNode(true)
		let linea = copia.querySelector("polyline")
		linea.setAttribute("points", ruta.trazado)
		linea.setAttribute("stroke", colores[ruta.nivel] || "#2563eb")
		copia.querySelector("svg").setAttribute("aria-label", "Trazado de " + ruta.nombre)
		copia.querySelector("h3").textContent = ruta.nombre
		copia.querySelector("p").textContent = ruta.km.toLocaleString("es-ES") + " km · " + ruta.nivel + " · " + ruta.superficie + (ruta.circular ? " · circular" : "")
		contenido.appendChild(copia)
	})
	document.querySelector("#tarjetaRutas .origen").textContent = "Fuente: rutas.geojson (distancias calculadas con las coordenadas)"
})

// Los vídeos dependen del tiempo, pero la API ya se encarga de juntarlos
tarjeta("tarjetaVideos", "videos", function(datos, contenido){
	contenido.append(crear("p", datos.motivo))
	let fila = crear("div", undefined, "videos")
	let plantilla = document.querySelector("#plantillaVideo")
	datos.videos.forEach(function(video){
		let copia = plantilla.content.cloneNode(true)
		copia.querySelector("img").src = "miniatura.php?id=" + video.id
		copia.querySelector("img").alt = video.titulo
		copia.querySelector("h3").textContent = video.titulo
		copia.querySelector("p").textContent = video.categoria + " · " + video.minutos + " min · " + video.monitor
		fila.appendChild(copia)
	})
	contenido.append(fila)
	document.querySelector("#tarjetaVideos .origen").textContent = "Fuente: repositorio de Velnora TV (JSON) + predicción de hoy"
})
