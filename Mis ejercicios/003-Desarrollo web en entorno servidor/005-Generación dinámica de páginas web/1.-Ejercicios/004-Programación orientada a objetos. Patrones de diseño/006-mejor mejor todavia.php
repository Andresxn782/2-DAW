<?php
	// Ahora la base de datos llega como parámetro: la clase sirve para cualquier archivo .db
	class VelnoraModelo{
		public $bd;

		public function __construct($basededatos){
			$this->bd = new PDO('sqlite:'.$basededatos);		// En PHP se concatena con punto, no con +
		}

		public function dameDatos($tabla){
			$info = $this->bd->query("SELECT * FROM ".$tabla);
			return $info->fetchAll(PDO::FETCH_ASSOC);
		}

		public function insertaDatos($tabla, $datos){
			$this->bd->query("INSERT INTO ".$tabla." VALUES(".$datos.")");
			return 0;
		}

		public function eliminaDatos($tabla, $id){
			$this->bd->query("DELETE FROM ".$tabla." WHERE id = ".$id);
			return 0;
		}
	}

	$modelo = new VelnoraModelo("socios.db");
	echo count($modelo->dameDatos("socios"))." socios";
?>
