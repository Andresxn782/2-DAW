Base de datos `socios.db` que uso en todos los ejercicios de este apartado:

```sql
CREATE TABLE "socios" (
	"id"	INTEGER,
	"nombre"	TEXT,
	"apellidos"	TEXT,
	"email"	TEXT,
	"plan"	TEXT,
	PRIMARY KEY("id" AUTOINCREMENT)
);

INSERT INTO socios (nombre, apellidos, email, plan) VALUES
('Lucía', 'Martín Soler', 'lucia.martin@velnora.es', 'Musculación'),
('Carlos', 'Pérez Vidal', 'carlos.perez@velnora.es', 'Spinning'),
('Nerea', 'Gil Campos', 'nerea.gil@velnora.es', 'Crossfit'),
('Iker', 'Ramos Ortega', 'iker.ramos@velnora.es', 'Natación'),
('Sofía', 'Navarro Cano', 'sofia.navarro@velnora.es', 'Yoga'),
('Hugo', 'Serrano Prieto', 'hugo.serrano@velnora.es', 'Musculación'),
('Valeria', 'Molina Rey', 'valeria.molina@velnora.es', 'Pilates'),
('Mateo', 'Calvo Herrera', 'mateo.calvo@velnora.es', 'Crossfit'),
('Daniela', 'Lozano Pastor', 'daniela.lozano@velnora.es', 'Spinning'),
('Bruno', 'Esteban Marín', 'bruno.esteban@velnora.es', 'Natación'),
('Alba', 'Crespo Nieto', 'alba.crespo@velnora.es', 'Yoga'),
('Leo', 'Fuentes Arias', 'leo.fuentes@velnora.es', 'Musculación'),
('Martina', 'Peña Blasco', 'martina.pena@velnora.es', 'Pilates'),
('Pablo', 'Reyes Garrido', 'pablo.reyes@velnora.es', 'Crossfit'),
('Irene', 'Vega Domínguez', 'irene.vega@velnora.es', 'Spinning');
```

Para el ejercicio `005` añado también la tabla `material` (ver `005-separacion por capas solo php/creomaterial.sql`).
