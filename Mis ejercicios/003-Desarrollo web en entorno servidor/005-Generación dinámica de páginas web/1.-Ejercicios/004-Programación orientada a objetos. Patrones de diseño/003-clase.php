<?php
	// Meto las funciones dentro de una clase: ahora son métodos
	class VelnoraModelo{
		public function dameDatos($tabla){
			$db = new PDO('sqlite:socios.db');
			$info = $db->query("SELECT * FROM ".$tabla);
			return $info->fetchAll(PDO::FETCH_ASSOC);
		}

		public function insertaDatos($tabla, $datos){
			$db = new PDO('sqlite:socios.db');
			$db->query("INSERT INTO ".$tabla." VALUES(".$datos.")");
			return 0;
		}

		public function eliminaDatos($tabla, $id){
			$db = new PDO('sqlite:socios.db');
			$db->query("DELETE FROM ".$tabla." WHERE id = ".$id);
			return 0;
		}
	}

	$modelo = new VelnoraModelo();
	echo count($modelo->dameDatos("material"))." piezas de material";
?>
