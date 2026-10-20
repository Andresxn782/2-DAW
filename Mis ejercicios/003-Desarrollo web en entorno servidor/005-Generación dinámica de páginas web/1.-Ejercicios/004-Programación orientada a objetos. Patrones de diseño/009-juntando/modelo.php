<?php
	// Versión corregida:
	// - la conexión es privada, nadie de fuera puede tocarla
	// - PDO lanza excepciones cuando algo falla
	// - los valores van con consultas preparadas
	// - el nombre de la tabla se valida, porque no se puede preparar
	class VelnoraModelo {

		private $bd;

		public function __construct($basededatos){
			$this->bd = new PDO('sqlite:'.$basededatos);
			$this->bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}

		private function tablaValida($tabla){
			if(!preg_match('/^[a-z_]+$/', $tabla)){
				throw new InvalidArgumentException("Tabla no válida: ".$tabla);
			}
			return $tabla;
		}

		// Método estático: se usa sin crear el objeto, VelnoraModelo::cuenta(...)
		public static function cuenta($basededatos, $tabla){
			$bd = new PDO('sqlite:'.$basededatos);
			$bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			if(!preg_match('/^[a-z_]+$/', $tabla)){
				throw new InvalidArgumentException("Tabla no válida: ".$tabla);
			}
			return (int)$bd->query("SELECT COUNT(*) FROM ".$tabla)->fetchColumn();
		}

		public static function dameDatosEstatico($basededatos, $tabla){
			$modelo = new VelnoraModelo($basededatos);
			return $modelo->dameDatos($tabla);
		}

		public function dameDatos($tabla){
			$info = $this->bd->query("SELECT * FROM ".$this->tablaValida($tabla));
			return $info->fetchAll(PDO::FETCH_ASSOC);
		}

		// $datos es un array asociativo campo => valor
		public function insertaDatos($tabla, $datos){
			$campos = array_keys($datos);
			$huecos = array_map(fn($c) => ":".$c, $campos);
			$consulta = $this->bd->prepare("INSERT INTO ".$this->tablaValida($tabla)." (".implode(",", $campos).") VALUES (".implode(",", $huecos).")");
			$consulta->execute($datos);
			return $this->bd->lastInsertId();
		}

		public function eliminaDatos($tabla, $id){
			$consulta = $this->bd->prepare("DELETE FROM ".$this->tablaValida($tabla)." WHERE id = :id");
			$consulta->execute([':id' => $id]);
			return $consulta->rowCount();
		}
	}
?>
