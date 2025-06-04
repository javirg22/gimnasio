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
    aforo INT,
    aforo_disponible INT
);

-- TABLA RESERVAS
CREATE TABLE reservas (
    id_reserva INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    id_clase INT,
    fecha_reserva DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (id_usuario, id_clase),
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
('Ana López', 'ana@example.com', '1234');

INSERT INTO productos (nombre, precio, descripcion, imagen) VALUES
('Cinta de correr', 499.99, 'Cinta eléctrica para correr', 'assets/img/cinta.jpg'),
('Mancuernas 10kg', 59.99, 'Par de mancuernas', 'assets/img/10kg.jpg'),
-- Proteínas sabores diferentes
('Proteína sabor Chocolate', 29.99, 'Proteína en polvo sabor chocolate para recuperación muscular', 'assets/img/1.jpg'),
('Proteína sabor Fresa', 29.99, 'Proteína en polvo sabor fresa para recuperación muscular', 'assets/img/2.jpg'),
('Proteína sabor Vainilla', 29.99, 'Proteína en polvo sabor vainilla para recuperación muscular', 'assets/img/3.jpg'),

-- Mancuernas varios pesos
('Mancuernas 5kg', 29.99, 'Par de mancuernas de 5 kilogramos', 'assets/img/4.jpg'),
('Mancuernas 15kg', 89.99, 'Par de mancuernas de 15 kilogramos', 'assets/img/5.jpg'),
('Mancuernas 20kg', 119.99, 'Par de mancuernas de 20 kilogramos', 'assets/img/6.jpg'),

-- Otros suplementos
('Creatina Monohidratada', 19.99, 'Suplemento de creatina para mejorar fuerza y resistencia', 'assets/img/7.jpg'),
('BCAA', 24.99, 'Aminoácidos ramificados para recuperación muscular', 'assets/img/8.jpg'),

-- Equipamiento de soporte
('Straps para levantamiento', 15.99, 'Straps para mejorar agarre en levantamiento de pesas', 'assets/img/9.jpg'),
('Muñequeras deportivas', 12.99, 'Muñequeras para soporte y protección de muñecas', 'assets/img/10.jpg'),
('Rodilleras de compresión', 18.99, 'Rodilleras para soporte y protección durante entrenamiento', 'assets/img/11.jpg'),
('Cinturón lumbar', 25.99, 'Cinturón para soporte lumbar en levantamiento de pesas', 'assets/img/12.jpg');


INSERT INTO clases (nombre, fecha_hora, aforo, aforo_disponible) VALUES
('Spinning', '2025-05-01 10:00:00', 15,15),
('Boxeo', '2025-05-02 18:00:00', 10,10),
('Yoga', '2025-06-07 10:00:00', 15, 15),
('Pilates', '2025-06-08 17:00:00', 20, 20),
('Zumba', '2025-06-09 19:00:00', 25, 25);
