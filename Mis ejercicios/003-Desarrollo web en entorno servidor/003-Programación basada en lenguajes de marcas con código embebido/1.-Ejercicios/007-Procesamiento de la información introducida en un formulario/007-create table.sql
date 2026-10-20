CREATE TABLE "socios" (
	"Identificador"	INTEGER,
	"nombre"	TEXT,
	"apellidos"	TEXT,
	"telefono"	TEXT,
	"email"	TEXT,
	"plan"	TEXT,
	PRIMARY KEY("Identificador" AUTOINCREMENT)
);

CREATE TABLE "clases" (
	"Identificador"	INTEGER,
	"nombre"	TEXT,
	"monitor"	TEXT,
	"dia"	TEXT,
	"hora"	TEXT,
	PRIMARY KEY("Identificador" AUTOINCREMENT)
);

CREATE TABLE "entrenadores" (
	"Identificador"	INTEGER,
	"nombre"	TEXT,
	"especialidad"	TEXT,
	"email"	TEXT,
	PRIMARY KEY("Identificador" AUTOINCREMENT)
);

INSERT INTO socios (nombre, apellidos, telefono, email, plan) VALUES
	('Lucía', 'Martín Sanz', '611223344', 'lucia@velnora.com', 'Mensual'),
	('Marcos', 'Gil Ortega', '622334455', 'marcos@velnora.com', 'Anual'),
	('Andres', 'Ruiz Torres', '633445566', 'andres@velnora.com', 'Trimestral');

INSERT INTO clases (nombre, monitor, dia, hora) VALUES
	('Spinning', 'Sara', 'lunes', '18:00'),
	('Yoga', 'Pablo', 'miércoles', '10:00'),
	('Crossfit', 'Sara', 'viernes', '19:30');

INSERT INTO entrenadores (nombre, especialidad, email) VALUES
	('Sara López', 'Ciclo indoor y crossfit', 'sara@velnora.com'),
	('Pablo Ruiz', 'Yoga y movilidad', 'pablo@velnora.com');
