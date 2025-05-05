# TechShop - Tienda Online

## Descripción
TechShop es una tienda online diseñada para la venta de productos tecnológicos. Este proyecto incluye un backend en PHP con una API REST y un frontend en JavaScript.

## Estructura del Proyecto
```
techshop/
├── frontend/         # Interfaz de usuario
│   ├── public/      # Páginas HTML
│   ├── assets/      # CSS, JS, imágenes
│
├── backend/          # API y lógica de negocio
│   ├── api/         # Endpoints REST
│   ├── config/      # Configuraciones
│   ├── models/      # Modelos de base de datos
│   ├── utils/       # Utilidades
│   ├── vendor/      # Dependencias PHP
│
├── sql/              # Esquema de base de datos
├── logs/             # Registros de errores
├── composer.json     # Dependencias PHP
└── README.md         # Documentación
```

## Instalación
1. Clonar el repositorio:
   ```sh
   git clone https://github.com/tuusuario/techshop.git
   ```
2. Instalar dependencias del backend:
   ```sh
   cd backend
   composer install
   ```
3. Configurar variables de entorno:
   - Copiar `.env.example` a `.env`
   - Configurar base de datos y credenciales
4. Importar la base de datos desde `sql/database.sql`.
5. Iniciar el servidor:
   ```sh
   php -S localhost:8000 -t backend
   ```

## Uso
- Acceder al frontend en `http://localhost/public/index.html`
- Usar la API en `http://localhost:8000/api/v1/`

## Tecnologías Utilizadas
- **Backend:** PHP, MySQL, Composer
- **Frontend:** JavaScript, HTML, CSS
- **Autenticación:** JWT
- **Pagos:** PayPal API

## Contribuciones
Las contribuciones son bienvenidas. Para colaborar:
1. Haz un fork del repositorio.
2. Crea una rama con tus cambios.
3. Envía un pull request.

## Licencia
Este proyecto está bajo la licencia MIT.

