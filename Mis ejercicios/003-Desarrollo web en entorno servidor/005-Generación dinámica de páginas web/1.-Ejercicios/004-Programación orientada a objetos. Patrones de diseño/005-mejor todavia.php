<?php
	// Con un constructor, la conexión se abre una sola vez al crear el objeto
	class VelnoraModelo{
		public $bd;

		public function __construct(){
			$this->bd = new PDO('sqlite:socios.db');
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

	$modelo = new VelnoraModelo();
	echo count($modelo->dameDatos("socios"))." socios";
?>
