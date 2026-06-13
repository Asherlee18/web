CREATE DATABASE IF NOT EXISTS jugueria_aitana;
USE jugueria_aitana;

CREATE TABLE usuarios(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    clave VARCHAR(255) NOT NULL,
    rol ENUM('administradora','mesera') NOT NULL DEFAULT 'mesera'
);

CREATE TABLE productos(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    precio DECIMAL(10,2) NOT NULL
);

CREATE TABLE ventas(
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2) NOT NULL
);

CREATE TABLE detalle_ventas(
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_venta INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY(id_venta) REFERENCES ventas(id) ON DELETE CASCADE,
    FOREIGN KEY(id_producto) REFERENCES productos(id) ON DELETE CASCADE
);

CREATE TABLE gastos(
    id INT AUTO_INCREMENT PRIMARY KEY,
    descripcion VARCHAR(200) NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    fecha DATE NOT NULL
);

INSERT INTO usuarios(nombre, usuario, clave, rol)
VALUES ('Aitana Admin', 'aitana', '123456', 'administradora');

