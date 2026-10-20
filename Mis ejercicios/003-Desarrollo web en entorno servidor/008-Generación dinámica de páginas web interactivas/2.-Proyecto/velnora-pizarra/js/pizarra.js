// Parte de cliente de la pizarra de Velnora.
// Cada 2 segundos pide el estado a api.php y cambia solo lo que haya cambiado.

let pizarra = document.querySelector("#pizarra")
let estado = {
	token: pizarra.dataset.token,
	socio: pizarra.dataset.socio,
	wod: pizarra.dataset.wod * 1,
	tipo: pizarra.dataset.tipo,
	animosDesde: pizarra.dataset.animosDesde * 1
}
let temporizador = null
let indicador = document.querySelector("#directo")

// ---------- Comunicación con el servidor ----------

// Envía un formulario por POST con el token y devuelve el JSON (lanza error si no es 2xx)
function enviar(accion, datos){
	return fetch("api.php?accion=" + accion, {
		method: "POST",
		headers: { "X-Token": estado.token },
		body: datos
	}).then(function(respuesta){
		return respuesta.json().then(function(json){
			if(!respuesta.ok){
				let error = new Error(json.error || "Error " + respuesta.status)
				error.datos = json
				throw error
			}
			return json
		})
	})
}

function refrescar(){
	clearTimeout(temporizador)
	fetch("api.php?accion=estado&wod=" + estado.wod + "&animos_desde=" + estado.animosDesde)
	.then(function(respuesta){
		if(respuesta.status === 401){
			// La sesión ha caducado: vuelvo a la entrada
			location.reload()
		}
		return respuesta.json()
	})
	.then(function(datos){
		if(datos.wod_nuevo){
			cambiarWod(datos.wod)
		}
		pintarRanking(datos.ranking)
		datos.animos.forEach(pintarAnimo)
		indicador.classList.remove("caido")
		indicador.textContent = "en directo"
	})
	.catch(function(){
		indicador.classList.add("caido")
		indicador.textContent = "sin conexión"
	})
	.finally(function(){
		temporizador = setTimeout(refrescar, 2000)
	})
}

// ---------- Cambios en la página ----------

// Un monitor ha publicado otro WOD: cambio textos, el campo del resultado y vacío ánimos
function cambiarWod(wod){
	estado.wod = wod.id
	estado.tipo = wod.tipo
	estado.animosDesde = 0
	document.querySelector("#wodTitulo").textContent = wod.titulo
	document.querySelector("#wodDescripcion").textContent = wod.descripcion
	document.querySelector("#wodMonitor").textContent = wod.monitor
	document.querySelector("#wodTipo").textContent = wod.tipo === "tiempo" ? "Por tiempo: gana el más rápido" : "Por repeticiones: gana quien haga más"
	let campo = document.querySelector("#valor")
	campo.value = ""
	campo.placeholder = wod.tipo === "tiempo" ? "6:45" : "120"
	campo.inputMode = wod.tipo === "tiempo" ? "text" : "numeric"
	document.querySelector("#etiquetaValor").textContent = wod.tipo === "tiempo" ? "Tiempo (min:seg)" : "Repeticiones"
	document.querySelector("#formResultado").escalado.checked = false
	document.querySelector("#borrarResultado").hidden = true
	document.querySelector("#animos").replaceChildren()
	document.querySelector("#ranking").replaceChildren()
	let aviso = document.querySelector(".wod")
	aviso.classList.add("destello")
	setTimeout(function(){ aviso.classList.remove("destello") }, 1500)
}

// Actualiza la tabla reutilizando las filas que ya existen (una por socio)
function pintarRanking(ranking){
	let cuerpo = document.querySelector("#ranking")
	let filasAntes = {}
	cuerpo.querySelectorAll("tr").forEach(function(fila, indice){
		filasAntes[fila.dataset.socio] = { fila: fila, posicion: indice + 1, marca: fila.querySelector(".marca").textContent }
	})

	ranking.forEach(function(dato){
		let antes = filasAntes[dato.socio]
		let fila = antes ? antes.fila : crearFila(dato.socio)
		let marca = dato.texto + (dato.escalado ? " escalado" : "")
		fila.querySelector(".posicion").textContent = dato.posicion
		if(!antes || antes.marca !== marca){
			let celda = fila.querySelector(".marca")
			celda.textContent = dato.texto
			if(dato.escalado){
				let etiqueta = document.createElement("span")
				etiqueta.className = "escalado"
				etiqueta.textContent = "escalado"
				celda.append(" ", etiqueta)
			}
		}
		// Destaco la fila si es nueva, si ha cambiado la marca o si ha subido puestos
		if(antes && (antes.marca !== marca || dato.posicion < antes.posicion) || (!antes && Object.keys(filasAntes).length > 0)){
			destacar(fila)
		}
		// appendChild de una fila que ya está la mueve: así quedan en el orden nuevo
		cuerpo.appendChild(fila)
		delete filasAntes[dato.socio]
	})

	// Las que ya no vienen (resultado borrado) desaparecen
	for(let socio in filasAntes){
		filasAntes[socio].fila.remove()
	}
	document.querySelector("#sinResultados").hidden = ranking.length > 0
}

function crearFila(socio){
	let fila = document.createElement("tr")
	fila.dataset.socio = socio
	if(socio === estado.socio){
		fila.classList.add("yo")
	}
	;["posicion", "nombre", "marca"].forEach(function(clase){
		let celda = document.createElement("td")
		celda.className = clase
		fila.appendChild(celda)
	})
	fila.querySelector(".nombre").textContent = socio
	return fila
}

function destacar(elemento){
	elemento.classList.remove("cambio")
	// Leer offsetWidth obliga al navegador a reiniciar la animación
	void elemento.offsetWidth
	elemento.classList.add("cambio")
}

function pintarAnimo(animo){
	let parrafo = document.createElement("p")
	parrafo.className = "animo"
	let autor = document.createElement("strong")
	autor.textContent = animo.socio
	let hora = document.createElement("time")
	hora.textContent = animo.hora
	parrafo.append(autor, " " + animo.texto + " ", hora)
	let caja = document.querySelector("#animos")
	caja.appendChild(parrafo)
	caja.scrollTop = caja.scrollHeight
	estado.animosDesde = Math.max(estado.animosDesde, animo.id)
}

// ---------- Formularios ----------

// La misma comprobación que hace el servidor, para avisar antes de enviar
function validarValor(texto){
	texto = texto.trim()
	if(estado.tipo === "tiempo"){
		let partes = texto.match(/^(\d{1,2}):([0-5]\d)$/)
		if(!partes){
			return "Escribe el tiempo como minutos:segundos, por ejemplo 6:45"
		}
		if(partes[1] * 60 + partes[2] * 1 < 30){
			return "Menos de 30 segundos no es posible"
		}
		return ""
	}
	if(!/^\d{1,3}$/.test(texto) || texto * 1 < 1){
		return "Escribe las repeticiones, entre 1 y 999"
	}
	return ""
}

let formResultado = document.querySelector("#formResultado")
let errorValor = document.querySelector("#errorValor")
let mensajeResultado = document.querySelector("#mensajeResultado")

formResultado.valor.addEventListener("input", function(){
	// Mientras escribe solo quito el error si ya está bien
	if(errorValor.textContent !== "" && validarValor(this.value) === ""){
		errorValor.textContent = ""
		this.classList.remove("mal")
	}
})

formResultado.addEventListener("submit", function(evento){
	evento.preventDefault()
	let problema = validarValor(formResultado.valor.value)
	errorValor.textContent = problema
	formResultado.valor.classList.toggle("mal", problema !== "")
	if(problema !== ""){
		return
	}
	enviar("resultado", new FormData(formResultado))
	.then(function(respuesta){
		mensajeResultado.textContent = "Guardado: " + respuesta.texto + ". Vas el " + respuesta.posicion + "º."
		document.querySelector("#borrarResultado").hidden = false
		refrescar()
	})
	.catch(function(error){
		errorValor.textContent = error.message
	})
})

document.querySelector("#borrarResultado").addEventListener("click", function(){
	if(!confirm("¿Quitar tu resultado de la pizarra?")){
		return
	}
	enviar("borrar_resultado", new FormData())
	.then(function(){
		formResultado.reset()
		formResultado.valor.value = ""
		document.querySelector("#borrarResultado").hidden = true
		mensajeResultado.textContent = "Resultado quitado."
		refrescar()
	})
})

let formAnimo = document.querySelector("#formAnimo")
formAnimo.addEventListener("submit", function(evento){
	evento.preventDefault()
	let error = document.querySelector("#errorAnimo")
	let texto = formAnimo.texto.value.trim()
	if(texto === ""){
		error.textContent = "Escribe algo antes de enviar"
		return
	}
	error.textContent = ""
	enviar("animo", new FormData(formAnimo))
	.then(function(){
		formAnimo.reset()
		refrescar()
	})
	.catch(function(fallo){
		error.textContent = fallo.message
	})
})

let formWod = document.querySelector("#formWod")
if(formWod){
	formWod.addEventListener("submit", function(evento){
		evento.preventDefault()
		let error = document.querySelector("#errorWod")
		if(formWod.titulo.value.trim() === "" || formWod.descripcion.value.trim() === ""){
			error.textContent = "Pon título y descripción"
			return
		}
		if(!confirm("Al publicar un WOD nuevo la clasificación empieza de cero. ¿Seguir?")){
			return
		}
		enviar("wod", new FormData(formWod))
		.then(function(){
			formWod.reset()
			error.textContent = ""
			refrescar()
		})
		.catch(function(fallo){
			let detalles = fallo.datos && fallo.datos.errores ? " (" + Object.values(fallo.datos.errores).join(", ") + ")" : ""
			error.textContent = fallo.message + detalles
		})
	})
}

temporizador = setTimeout(refrescar, 2000)
