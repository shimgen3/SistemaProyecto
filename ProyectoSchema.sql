DROP DATABASE IF EXISTS barberia;
CREATE DATABASE barberia CHARACTER SET utf8mb4;

CREATE TABLE barberos (
    idbarber INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    rut VARCHAR(15) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE clientes (
    idcliente INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    rut VARCHAR(15) UNIQUE NOT NULL,
    telefono VARCHAR(20) NOT NULL
);

CREATE TABLE servicios (
    idservicio INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
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
