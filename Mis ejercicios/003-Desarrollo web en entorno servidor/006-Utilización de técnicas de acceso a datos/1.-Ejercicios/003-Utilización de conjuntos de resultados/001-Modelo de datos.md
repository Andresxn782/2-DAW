# Modelo de datos

## Tabla plana (relacional)

Cada socio es una fila y cada dato una columna. Si un socio tiene varios teléfonos, o necesito varias columnas o una tabla aparte.

Socios
- nombre
- apellidos
- email
- telefono

## Documento anidado (JSON)

Un dato puede contener otros datos. El socio lleva dentro sus contactos y su historial de reservas:

Socios
- nombre
- apellidos
- contacto
	- email
	- telefonos
		- movil
		- casa
- reservas
	- clase, fecha
	- clase, fecha

En una base de datos relacional esto serían tres tablas (`socios`, `telefonos`, `reservas`) unidas por claves foráneas. En JSON va todo junto en un mismo documento.
