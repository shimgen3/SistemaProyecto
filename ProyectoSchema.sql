DROP DATABASE IF EXISTS barberia;
CREATE DATABASE barberia CHARACTER SET utf8mb4;
USE barberia;
CREATE TABLE barberos (
    idbarber INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    rut VARCHAR(15) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE clientes (
    idcliente INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    rut VARCHAR(15) UNIQUE NOT NULL,
    telefono VARCHAR(20) NOT NULL
);

CREATE TABLE servicios (
    idservicio INT PRIMARY KEY AUTO_INCREMENT,
    servicename VARCHAR(255) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    duracion INT NOT NULL
);

CREATE TABLE reservas (
    idreserva INT PRIMARY KEY AUTO_INCREMENT,
    hora DATETIME NOT NULL,
    idbarber INT NOT NULL,
    idcliente INT,
    idservicio INT,
    realizada BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (idbarber) REFERENCES barberos(idbarber),
    FOREIGN KEY (idcliente) REFERENCES clientes(idcliente),
    FOREIGN KEY (idservicio) REFERENCES servicios(idservicio)
);

INSERT INTO barberos (username, email, rut, password)
VALUES 
('Juan Pérez', 'juanperez@example.com', '12345678-9', 'password123'),
('Carlos López', 'carloslopez@example.com', '98765432-1', 'password456'),
('Ana Soto', 'anasoto@example.com', '13579246-8', 'password789');

INSERT INTO clientes (username, email, rut, telefono)
VALUES 
('Pedro Gómez', 'pedrogomez@example.com', '23456789-5', '123456789'),
('María Rodríguez', 'mariarodriguez@example.com', '87654321-0', '987654321'),
('Lucas Martínez', 'lucasmartinez@example.com', '21436587-9', '456123789');

INSERT INTO servicios (servicename, precio, duracion)
VALUES 
('Corte de cabello', 10000, 30),
('Afeitado', 8000, 20),
('Corte y afeitado', 15000, 45);

INSERT INTO reservas (hora, idbarber, idcliente, idservicio, realizada)
VALUES 
('2023-11-12 10:00:00', 1, 1, 1, FALSE),
('2023-11-12 11:00:00', 1, 2, 2, FALSE),
('2023-11-12 12:00:00', 2, 3, 3, FALSE),
('2023-11-12 14:00:00', 2, 1, 1, TRUE),
('2023-11-12 15:00:00', 3, 2, 2, TRUE);