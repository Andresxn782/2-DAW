# Actualización y eliminación de información proveniente de una base de datos

- `001-editar socio.php`: formulario que carga un socio, lo modifica con `UPDATE` y muestra cuántas filas han cambiado.
- `002-eliminar con confirmacion.php`: borrado por POST con una pantalla de confirmación. Si el socio tiene pagos, en lugar de borrarlo lo doy de baja (borrado lógico) para no perder el historial.
- `003-transacciones.php`: cobrar una cuota descuenta saldo e inserta un pago. Las dos cosas van en una transacción: o se hacen las dos o ninguna.

La base de datos es `gimnasio.db`, con las tablas `socios` y `pagos`.
