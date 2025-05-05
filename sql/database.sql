-- CREACIÓN DE BASE DE DATOS
CREATE DATABASE IF NOT EXISTS gymrat;
USE gymrat;

-- TABLA USUARIOS
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    contrasena VARCHAR(255),
    tipo_usuario ENUM('cliente', 'admin') DEFAULT 'cliente'
);

-- TABLA PRODUCTOS
CREATE TABLE productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    precio DECIMAL(10,2),
    descripcion TEXT,
    imagen VARCHAR(255)
);

-- TABLA CARRITO
CREATE TABLE carrito (
    id_carrito INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    id_producto INT,
    cantidad INT,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
);

-- TABLA MEMBRESÍAS
CREATE TABLE membresias (
    id_membresia INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    fecha_inicio DATE,
    fecha_fin DATE,
    tipo VARCHAR(50),
    estado ENUM('activa', 'inactiva') DEFAULT 'inactiva',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- TABLA CLASES
CREATE TABLE clases (
    id_clase INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    fecha_hora DATETIME,
    aforo INT
);

-- TABLA RESERVAS
CREATE TABLE reservas (
    id_reserva INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    id_clase INT,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
    FOREIGN KEY (id_clase) REFERENCES clases(id_clase)
);

-- TABLA PEDIDOS
CREATE TABLE pedidos (
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    fecha DATETIME,
    total DECIMAL(10,2),
    estado ENUM('pendiente', 'pagado') DEFAULT 'pendiente',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- TABLA DETALLE PEDIDO
CREATE TABLE detalle_pedido (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT,
    id_producto INT,
    cantidad INT,
    precio DECIMAL(10,2),
    FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido),
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
);

-- DATOS DE PRUEBA
INSERT INTO usuarios (nombre, email, contrasena) VALUES
('Juan Pérez', 'juan@example.com', '1234'),
('Ana López', 'ana@example.com', 'abcd');

INSERT INTO productos (nombre, precio, descripcion, imagen) VALUES
('Cinta de correr', 499.99, 'Cinta eléctrica para correr', '1.jpg'),
('Mancuernas 10kg', 59.99, 'Par de mancuernas', '2.jpg');

INSERT INTO clases (nombre, fecha_hora, aforo) VALUES
('Spinning', '2025-05-01 10:00:00', 15),
('Boxeo', '2025-05-02 18:00:00', 10);
