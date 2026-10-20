<?php
	require_once __DIR__."/Entreno.php";
	require_once __DIR__."/RepositorioEntrenos.php";

	/**
	 * Repositorio sobre PDO. La misma clase sirve para SQLite y para MySQL:
	 * solo cambia la conexión que recibe y un par de detalles de cada motor.
	 */
	class RepositorioPDO implements RepositorioEntrenos {

		private PDO $bd;

		public function __construct(PDO $bd){
			$this->bd = $bd;
			$this->bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->crearTabla();
		}

		private function crearTabla(): void {
			$mysql = $this->bd->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql';
			$id = $mysql ? "INT AUTO_INCREMENT PRIMARY KEY" : "INTEGER PRIMARY KEY AUTOINCREMENT";
			$this->bd->exec("CREATE TABLE IF NOT EXISTS entrenos (
				id ".$id.",
				socio VARCHAR(40) NOT NULL,
				fecha DATE NOT NULL,
				tipo VARCHAR(20) NOT NULL,
				minutos INTEGER NOT NULL,
				intensidad INTEGER NOT NULL,
				notas TEXT
			)");
		}

		/** Construye el WHERE con marcadores; los valores nunca van dentro del SQL. */
		private function donde(array $filtros, array &$valores): string {
			$condiciones = [];
			if(($filtros['socio'] ?? '') !== ''){
				$condiciones[] = "socio = :socio";
				$valores[':socio'] = $filtros['socio'];
			}
			if(($filtros['tipo'] ?? '') !== ''){
				$condiciones[] = "tipo = :tipo";
				$valores[':tipo'] = $filtros['tipo'];
			}
			if(($filtros['desde'] ?? '') !== ''){
				$condiciones[] = "fecha >= :desde";
				$valores[':desde'] = $filtros['desde'];
			}
			if(($filtros['hasta'] ?? '') !== ''){
				$condiciones[] = "fecha <= :hasta";
				$valores[':hasta'] = $filtros['hasta'];
			}
			return $condiciones ? " WHERE ".implode(" AND ", $condiciones) : "";
		}

		public function buscar(array $filtros, int $pagina, int $porPagina): array {
			$valores = [];
			$sql = "SELECT * FROM entrenos".$this->donde($filtros, $valores)." ORDER BY fecha DESC, id DESC LIMIT :limite OFFSET :desplazamiento";
			$consulta = $this->bd->prepare($sql);
			foreach($valores as $marcador => $valor){
				$consulta->bindValue($marcador, $valor);
			}
			$consulta->bindValue(':limite', $porPagina, PDO::PARAM_INT);
			$consulta->bindValue(':desplazamiento', max(0, $pagina - 1) * $porPagina, PDO::PARAM_INT);
			$consulta->execute();
			return $consulta->fetchAll(PDO::FETCH_CLASS, 'Entreno');
		}

		public function contar(array $filtros): int {
			$valores = [];
			$consulta = $this->bd->prepare("SELECT COUNT(*) FROM entrenos".$this->donde($filtros, $valores));
			$consulta->execute($valores);
			return (int)$consulta->fetchColumn();
		}

		public function obtener(int $id): ?Entreno {
			$consulta = $this->bd->prepare("SELECT * FROM entrenos WHERE id = :id");
			$consulta->execute([':id' => $id]);
			$consulta->setFetchMode(PDO::FETCH_CLASS, 'Entreno');
			return $consulta->fetch() ?: null;
		}

		public function guardar(Entreno $entreno): int {
			$datos = $entreno->aArray();
			if($entreno->id){
				$consulta = $this->bd->prepare("UPDATE entrenos SET socio = :socio, fecha = :fecha, tipo = :tipo, minutos = :minutos, intensidad = :intensidad, notas = :notas WHERE id = :id");
				$consulta->execute($datos + ['id' => $entreno->id]);
				return (int)$entreno->id;
			}
			$consulta = $this->bd->prepare("INSERT INTO entrenos (socio, fecha, tipo, minutos, intensidad, notas) VALUES (:socio, :fecha, :tipo, :minutos, :intensidad, :notas)");
			$consulta->execute($datos);
			return (int)$this->bd->lastInsertId();
		}

		public function eliminar(int $id): bool {
			$consulta = $this->bd->prepare("DELETE FROM entrenos WHERE id = :id");
			$consulta->execute([':id' => $id]);
			return $consulta->rowCount() > 0;
		}

		public function estadisticas(array $filtros): array {
			$valores = [];
			$donde = $this->donde($filtros, $valores);

			$consulta = $this->bd->prepare("SELECT COUNT(*) AS sesiones, COALESCE(SUM(minutos), 0) AS minutos FROM entrenos".$donde);
			$consulta->execute($valores);
			$totales = $consulta->fetch(PDO::FETCH_ASSOC);

			$consulta = $this->bd->prepare("SELECT tipo, SUM(minutos) FROM entrenos".$donde." GROUP BY tipo ORDER BY SUM(minutos) DESC");
			$consulta->execute($valores);
			$porTipo = array_map('intval', $consulta->fetchAll(PDO::FETCH_KEY_PAIR));

			$consulta = $this->bd->prepare("SELECT socio, COUNT(*) FROM entrenos".$donde." GROUP BY socio ORDER BY COUNT(*) DESC, socio");
			$consulta->execute($valores);
			$porSocio = array_map('intval', $consulta->fetchAll(PDO::FETCH_KEY_PAIR));

			return ['sesiones' => (int)$totales['sesiones'], 'minutos' => (int)$totales['minutos'], 'por_tipo' => $porTipo, 'por_socio' => $porSocio];
		}

		public function socios(): array {
			return $this->bd->query("SELECT DISTINCT socio FROM entrenos ORDER BY socio")->fetchAll(PDO::FETCH_COLUMN);
		}

		public function importar(array $entrenos): int {
			$this->bd->beginTransaction();
			try{
				foreach($entrenos as $entreno){
					$entreno->id = null;
					$this->guardar($entreno);
				}
				$this->bd->commit();
				return count($entrenos);
			}catch(Throwable $e){
				$this->bd->rollBack();
				throw $e;
			}
		}
	}
?>
