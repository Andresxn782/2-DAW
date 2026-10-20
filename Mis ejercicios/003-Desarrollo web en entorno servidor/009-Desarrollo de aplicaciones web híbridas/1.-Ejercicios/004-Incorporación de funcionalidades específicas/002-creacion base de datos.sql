PRAGMA foreign_keys = ON;

CREATE TABLE socios (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	nombre TEXT NOT NULL,
	apellidos TEXT NOT NULL,
	usuario TEXT NOT NULL UNIQUE,
	email TEXT NOT NULL UNIQUE,
	contrasena TEXT NOT NULL,
	plan TEXT NOT NULL
);

CREATE TABLE publicaciones (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	id_socio INTEGER NOT NULL REFERENCES socios(id),
	fecha TEXT NOT NULL DEFAULT (datetime('now', 'localtime')),
	texto TEXT NOT NULL,
	tipo TEXT NOT NULL CHECK (tipo IN ('entreno', 'logro', 'pregunta'))
);

CREATE TABLE reacciones (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	id_publicacion INTEGER NOT NULL REFERENCES publicaciones(id) ON DELETE CASCADE,
	id_socio INTEGER NOT NULL REFERENCES socios(id),
	tipo TEXT NOT NULL CHECK (tipo IN ('fuerza', 'fuego', 'aplauso')),
	fecha TEXT NOT NULL DEFAULT (datetime('now', 'localtime')),
	UNIQUE (id_publicacion, id_socio, tipo)
);

CREATE TABLE seguimientos (
	id_seguidor INTEGER NOT NULL REFERENCES socios(id),
	id_seguido INTEGER NOT NULL REFERENCES socios(id),
	fecha TEXT NOT NULL DEFAULT (datetime('now', 'localtime')),
	PRIMARY KEY (id_seguidor, id_seguido),
	CHECK (id_seguidor != id_seguido)
);

-- Índices para que el muro y las sugerencias vayan rápido
CREATE INDEX idx_publicaciones_socio ON publicaciones(id_socio, fecha);
CREATE INDEX idx_reacciones_publicacion ON reacciones(id_publicacion);
