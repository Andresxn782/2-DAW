<?php
	require_once __DIR__."/Entreno.php";
	require_once __DIR__."/RepositorioEntrenos.php";

	/**
	 * Repositorio sobre un archivo JSON. Sin base de datos: los filtros,
	 * el orden, la paginación y los totales los hago yo en PHP.
	 */
	class RepositorioJSON implements RepositorioEntrenos {

		private string $archivo;

		public function __construct(string $archivo){
			$this->archivo = $archivo;
			if(!file_exists($archivo)){
				$this->escribir(['siguiente_id' => 1, 'entrenos' => []]);
			}
		}

		private function leer(): array {
			$datos = json_decode((string)file_get_contents($this->archivo), true);
			return is_array($datos) ? $datos : ['siguiente_id' => 1, 'entrenos' => []];
		}

		private function escribir(array $datos): void {
			file_put_contents($this->archivo, json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
		}

		/** Filtra y ordena como lo haría el WHERE y el ORDER BY. @return array[] */
		private function filtrados(array $filtros): array {
			$filas = array_filter($this->leer()['entrenos'], function($e) use ($filtros){
				return (($filtros['socio'] ?? '') === '' || $e['socio'] === $filtros['socio'])
					&& (($filtros['tipo'] ?? '') === '' || $e['tipo'] === $filtros['tipo'])
					&& (($filtros['desde'] ?? '') === '' || $e['fecha'] >= $filtros['desde'])
					&& (($filtros['hasta'] ?? '') === '' || $e['fecha'] <= $filtros['hasta']);
			});
			usort($filas, fn($a, $b) => [$b['fecha'], $b['id']] <=> [$a['fecha'], $a['id']]);
			return $filas;
		}

		public function buscar(array $filtros, int $pagina, int $porPagina): array {
			$pagina = array_slice($this->filtrados($filtros), max(0, $pagina - 1) * $porPagina, $porPagina);
			return array_map(fn($fila) => Entreno::desdeArray($fila), $pagina);
		}

		public function contar(array $filtros): int {
			return count($this->filtrados($filtros));
		}

		public function obtener(int $id): ?Entreno {
			foreach($this->leer()['entrenos'] as $fila){
				if($fila['id'] === $id){
					return Entreno::desdeArray($fila);
				}
			}
			return null;
		}

		public function guardar(Entreno $entreno): int {
			$datos = $this->leer();
			if($entreno->id){
				foreach($datos['entrenos'] as &$fila){
					if($fila['id'] === (int)$entreno->id){
						$fila = ['id' => (int)$entreno->id] + $entreno->aArray();
					}
				}
				unset($fila);
				$id = (int)$entreno->id;
			}else{
				$id = $datos['siguiente_id']++;
				$datos['entrenos'][] = ['id' => $id] + $entreno->aArray();
			}
			$this->escribir($datos);
			return $id;
		}

		public function eliminar(int $id): bool {
			$datos = $this->leer();
			$antes = count($datos['entrenos']);
			$datos['entrenos'] = array_values(array_filter($datos['entrenos'], fn($e) => $e['id'] !== $id));
			$this->escribir($datos);
			return count($datos['entrenos']) < $antes;
		}

		public function estadisticas(array $filtros): array {
			$porTipo = [];
			$porSocio = [];
			$minutos = 0;
			$filas = $this->filtrados($filtros);
			foreach($filas as $e){
				$porTipo[$e['tipo']] = ($porTipo[$e['tipo']] ?? 0) + $e['minutos'];
				$porSocio[$e['socio']] = ($porSocio[$e['socio']] ?? 0) + 1;
				$minutos += $e['minutos'];
			}
			arsort($porTipo);
			uksort($porSocio, fn($a, $b) => [$porSocio[$b], $a] <=> [$porSocio[$a], $b]);
			return ['sesiones' => count($filas), 'minutos' => $minutos, 'por_tipo' => $porTipo, 'por_socio' => $porSocio];
		}

		public function socios(): array {
			$socios = array_unique(array_column($this->leer()['entrenos'], 'socio'));
			sort($socios);
			return $socios;
		}

		/** Todo o nada: preparo la lista completa en memoria y la escribo una sola vez. */
		public function importar(array $entrenos): int {
			$datos = $this->leer();
			foreach($entrenos as $entreno){
				$datos['entrenos'][] = ['id' => $datos['siguiente_id']++] + $entreno->aArray();
			}
			$this->escribir($datos);
			return count($entrenos);
		}
	}
?>
