<?php
	// Crea comunidad.db con la estructura del .sql y datos de demostración.
	// Uso: php "003-datos de demostracion.php"   (o abrir cualquier página: se llama sola)
	function crearComunidad($ruta) {
		if (file_exists($ruta)) {
			unlink($ruta);
		}
		$db = new PDO("sqlite:".$ruta);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->exec(file_get_contents(__DIR__."/002-creacion base de datos.sql"));
		mt_srand(9);

		$nombres = ["Lucía", "Carlos", "Nerea", "Marcos", "Sofía", "Hugo", "Alba", "Iván", "Marta", "Pablo", "Irene", "Diego", "Claudia", "Raúl", "Elena", "Sergio", "Noa", "Adrián", "Paula", "Andres"];
		$apellidos = ["Martín", "Pérez", "Gil", "Ibáñez", "Navarro", "Ferrer", "Crespo", "Rey", "Lozano", "Soler", "Vidal", "Campos", "Ruiz", "Torres"];
		$planes = ["Musculación", "Spinning", "Crossfit", "Yoga", "Pilates", "Natación"];
		$textos = [
			"entreno" => ["Hoy pierna a tope: sentadilla 5x5 y zancadas.", "45 minutos de spinning con Andres, ¡qué sudada!", "Sesión de yoga para soltar la espalda.", "WOD terminado en 12:30, mejorando poco a poco.", "Natación técnica: 1500 metros sin parar.", "Día de espalda: dominadas y remo con barra."],
			"logro" => ["¡Nuevo récord en peso muerto: 120 kg!", "Primer mes sin faltar ni un día.", "Ya hago 10 dominadas seguidas.", "He bajado mi 5K de 30 a 26 minutos.", "Primera clase de crossfit completa sin escalar."],
			"pregunta" => ["¿Alguien sabe a qué hora hay menos gente?", "¿Qué calentáis antes de hacer sentadilla?", "¿Recomendáis creatina para empezar?", "¿Alguien se apunta a correr el sábado por el Retiro?"]
		];
		$tiposReaccion = ["fuerza", "fuego", "aplauso"];

		// La misma contraseña para todos: el hash se calcula una vez (es lento a propósito)
		$hash = password_hash("velnora123", PASSWORD_DEFAULT);
		$db->beginTransaction();
		$insertar = $db->prepare("INSERT INTO socios (nombre, apellidos, usuario, email, contrasena, plan) VALUES (?, ?, ?, ?, ?, ?)");
		for ($i = 1; $i <= 40; $i++) {
			$nombre = $nombres[($i - 1) % count($nombres)];
			$apellido = $apellidos[mt_rand(0, count($apellidos) - 1)]." ".$apellidos[mt_rand(0, count($apellidos) - 1)];
			$usuario = strtolower(iconv("UTF-8", "ASCII//TRANSLIT", $nombre)).sprintf("%02d", $i);
			$insertar->execute([$nombre, $apellido, $usuario, $usuario."@velnora.es", $hash, $planes[mt_rand(0, 5)]]);
		}

		// Publicaciones repartidas en los últimos 30 días
		$insertar = $db->prepare("INSERT INTO publicaciones (id_socio, fecha, texto, tipo) VALUES (?, ?, ?, ?)");
		for ($i = 1; $i <= 300; $i++) {
			$tipo = ["entreno", "entreno", "entreno", "logro", "pregunta"][mt_rand(0, 4)];
			$fecha = date("Y-m-d H:i:s", time() - mt_rand(0, 30 * 24 * 3600));
			$insertar->execute([mt_rand(1, 40), $fecha, $textos[$tipo][mt_rand(0, count($textos[$tipo]) - 1)], $tipo]);
		}

		// Reacciones (INSERT OR IGNORE: si sale repetida, se salta)
		$insertar = $db->prepare("INSERT OR IGNORE INTO reacciones (id_publicacion, id_socio, tipo) VALUES (?, ?, ?)");
		for ($i = 1; $i <= 1500; $i++) {
			$insertar->execute([mt_rand(1, 300), mt_rand(1, 40), $tiposReaccion[mt_rand(0, 2)]]);
		}

		// Cada socio sigue a entre 3 y 8 personas
		$insertar = $db->prepare("INSERT OR IGNORE INTO seguimientos (id_seguidor, id_seguido) VALUES (?, ?)");
		for ($socio = 1; $socio <= 40; $socio++) {
			$cuantos = mt_rand(3, 8);
			for ($j = 0; $j < $cuantos; $j++) {
				$otro = mt_rand(1, 40);
				if ($otro !== $socio) {
					$insertar->execute([$socio, $otro]);
				}
			}
		}
		$db->commit();
		return $db;
	}

	// Si se ejecuta directamente (no con include), crea la base de datos y cuenta
	if (realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__) {
		$db = crearComunidad(__DIR__."/comunidad.db");
		foreach (["socios", "publicaciones", "reacciones", "seguimientos"] as $tabla) {
			echo $tabla.": ".$db->query("SELECT COUNT(*) FROM ".$tabla)->fetchColumn().(PHP_SAPI === "cli" ? "\n" : "<br>");
		}
	}
?>
