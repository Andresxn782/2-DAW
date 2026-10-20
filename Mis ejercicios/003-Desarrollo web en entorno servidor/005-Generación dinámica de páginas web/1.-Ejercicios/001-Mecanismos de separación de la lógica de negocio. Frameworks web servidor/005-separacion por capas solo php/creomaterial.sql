CREATE TABLE material (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	nombre TEXT NOT NULL,
	zona TEXT,
	unidades INTEGER DEFAULT 0,
	precio REAL,
	estado TEXT,
	fecha_compra TEXT DEFAULT CURRENT_DATE
);

INSERT INTO material (nombre, zona, unidades, precio, estado) VALUES
('Mancuernas 10 kg', 'Peso libre', 12, 39.90, 'Bueno'),
('Barra olímpica', 'Peso libre', 6, 189.00, 'Bueno'),
('Discos 20 kg', 'Peso libre', 16, 54.50, 'Bueno'),
('Bicicleta de spinning', 'Sala ciclo', 20, 699.00, 'Revisar'),
('Cinta de correr', 'Cardio', 8, 2150.00, 'Bueno'),
('Remo ergómetro', 'Cardio', 4, 990.00, 'Bueno'),
('Esterilla de yoga', 'Sala polivalente', 30, 14.95, 'Bueno'),
('Kettlebell 16 kg', 'Funcional', 10, 45.00, 'Bueno'),
('Comba de velocidad', 'Funcional', 25, 9.90, 'Desgastado'),
('Balón medicinal 5 kg', 'Funcional', 8, 29.90, 'Bueno'),
('Banco ajustable', 'Peso libre', 6, 249.00, 'Revisar'),
('Rueda abdominal', 'Funcional', 12, 12.50, 'Bueno');
