<?php
	/**
	 * Conexión única a la base de datos (patrón Singleton).
	 * Da igual cuántas veces se pida: siempre devuelve el mismo objeto PDO.
	 */
	class BaseDatos {

		private static ?PDO $conexion = null;

		/** No se puede hacer new BaseDatos(): se usa BaseDatos::conexion(). */
		private function __construct(){}

		/**
		 * @param string $ruta Ruta al archivo SQLite (solo se usa la primera vez).
		 * @return PDO
		 */
		public static function conexion(string $ruta = ''): PDO {
			if(self::$conexion === null){
				self::$conexion = new PDO('sqlite:'.$ruta);
				self::$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				self::$conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
				self::prepara(self::$conexion);
			}
			return self::$conexion;
		}

		/** Para las pruebas: permite empezar con una conexión nueva. */
		public static function reinicia(): void {
			self::$conexion = null;
		}

		/** Crea la tabla y los productos de ejemplo si la base de datos está vacía. */
		private static function prepara(PDO $bd): void {
			$bd->exec("CREATE TABLE IF NOT EXISTS productos (
				id INTEGER PRIMARY KEY AUTOINCREMENT,
				nombre TEXT NOT NULL,
				categoria TEXT NOT NULL,
				descripcion TEXT,
				precio REAL NOT NULL,
				stock INTEGER NOT NULL DEFAULT 0,
				destacado INTEGER NOT NULL DEFAULT 0
			)");
			if($bd->query("SELECT COUNT(*) FROM productos")->fetchColumn() > 0){
				return;
			}
			$productos = [
				["Proteína whey 1 kg", "Nutrición", "Proteína de suero sabor chocolate, 33 raciones.", 29.90, 25, 1],
				["Creatina monohidrato 300 g", "Nutrición", "Creatina pura micronizada.", 19.50, 40, 0],
				["Barritas de avena (12 uds.)", "Nutrición", "Barritas con 15 g de proteína cada una.", 14.95, 30, 0],
				["Guantes de entrenamiento", "Accesorios", "Guantes con muñequera, tallas S a XL.", 17.90, 18, 1],
				["Cinturón lumbar", "Accesorios", "Cinturón de cuero para sentadilla y peso muerto.", 34.00, 8, 0],
				["Comba de velocidad", "Accesorios", "Cable de acero con rodamientos.", 9.90, 50, 0],
				["Botella 1 litro Velnora", "Accesorios", "Botella sin BPA con el logo del gimnasio.", 7.50, 60, 1],
				["Camiseta técnica", "Ropa", "Tejido transpirable, logo Velnora.", 15.00, 35, 0],
				["Mallas de entrenamiento", "Ropa", "Mallas largas con bolsillo lateral.", 24.90, 20, 0],
				["Sudadera Velnora", "Ropa", "Sudadera con capucha, edición 2026.", 32.00, 0, 1]
			];
			$consulta = $bd->prepare("INSERT INTO productos (nombre, categoria, descripcion, precio, stock, destacado) VALUES (?, ?, ?, ?, ?, ?)");
			foreach($productos as $p){
				$consulta->execute($p);
			}
		}
	}
?>
