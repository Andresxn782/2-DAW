<?php
	// Cobrar una sesión de entrenamiento personal: resta saldo al socio y apunta el pago.
	// Si algo falla a mitad, rollBack() deshace todo lo hecho dentro de la transacción.
	$db = new PDO('sqlite:gimnasio.db');
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	function cobrar(PDO $db, int $socio, float $importe, string $concepto): string {
		try{
			$db->beginTransaction();

			$consulta = $db->prepare("UPDATE socios SET saldo = saldo - :importe WHERE id = :id AND activo = 1");
			$consulta->execute([':importe' => $importe, ':id' => $socio]);
			if($consulta->rowCount() == 0){
				throw new Exception("el socio no existe o está de baja");
			}

			$consulta = $db->prepare("SELECT saldo FROM socios WHERE id = :id");
			$consulta->execute([':id' => $socio]);
			if($consulta->fetchColumn() < 0){
				throw new Exception("no tiene saldo suficiente");
			}

			$consulta = $db->prepare("INSERT INTO pagos (socio_id, fecha, importe, concepto) VALUES (:id, :fecha, :importe, :concepto)");
			$consulta->execute([':id' => $socio, ':fecha' => date('Y-m-d'), ':importe' => $importe, ':concepto' => $concepto]);

			$db->commit();
			return "Cobrado: ".$concepto." (".number_format($importe, 2, ',', '.')." €)";
		}catch(Exception $e){
			$db->rollBack();
			return "No se ha cobrado porque ".$e->getMessage().". No ha cambiado nada.";
		}
	}

	function saldos(PDO $db): string {
		$filas = $db->query("SELECT nombre, saldo FROM socios ORDER BY id")->fetchAll(PDO::FETCH_KEY_PAIR);
		$texto = [];
		foreach($filas as $nombre => $saldo){
			$texto[] = $nombre.": ".number_format($saldo, 2, ',', '.')." €";
		}
		return implode(" · ", $texto);
	}

	$pagosAntes = $db->query("SELECT COUNT(*) FROM pagos")->fetchColumn();
	echo "<p><strong>Antes</strong>: ".saldos($db)."</p>";
	echo "<p>".cobrar($db, 4, 20, "Entrenamiento personal")."</p>";
	echo "<p>".cobrar($db, 2, 20, "Entrenamiento personal")."</p>";		// Solo tiene 10 €
	echo "<p>".cobrar($db, 99, 20, "Entrenamiento personal")."</p>";		// No existe
	echo "<p><strong>Después</strong>: ".saldos($db)."</p>";
	echo "<p>Pagos nuevos: ".($db->query("SELECT COUNT(*) FROM pagos")->fetchColumn() - $pagosAntes)."</p>";

	// Devuelvo el saldo para que la demostración se pueda repetir
	$db->exec("UPDATE socios SET saldo = saldo + 20 WHERE id = 4");
	$db->exec("DELETE FROM pagos WHERE concepto = 'Entrenamiento personal'");
?>
