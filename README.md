# 


---

## 🚀 Características Principales

- 📁 **Gestión de archivos** con almacenamiento por usuario.  
- 💾 **Subida y descarga segura** de documentos con diferentes extensiones (PDF, ZIP, TXT, imágenes, etc.).  
- 🔐 **Control de acceso por roles (Administrador / Usuario)**.  
- 📊 **Barra de progreso dinámica** del espacio de almacenamiento usado por cada usuario.  
- 🧹 **Actualización automática** de estadísticas al eliminar archivos.  
- 👥 **Panel de administración completo con Filament** para gestionar usuarios, grupos y archivos.  
- 💬 **Notificaciones visuales** en cada acción (subida, eliminación, error, etc.).  
- 🖼️ **Vista “Mis Archivos”** donde cada usuario puede administrar sus propios documentos.  

---

## 🧱 Estructura de Roles

| Rol | Permisos |
|-----|-----------|
| 🛠️ **Administrador** | Acceso total a todos los módulos, usuarios y archivos. |
| 👤 **Usuario** | Solo puede acceder a “Mis Archivos” y gestionar sus propios documentos. |

---

## 🗃️ Tecnologías Utilizadas

- **Backend:** Laravel 11  
- **Panel:** Filament 3  
- **Base de datos:** MySQL  
- **Frontend:** Blade + Filament Components  
- **Storage:** Laravel Filesystem (`public` disk)  
- **Autenticación y Roles:** Laravel Auth + Spatie Permissions  

---

## ⚙️ Instalación del Proyecto

```bash
# 1. Clonar el repositorio
git clone https://github.com//ssetp.git](https://github.com/Andres2389/control_almacenamiento.git
cd desktop
cd control_almacenamineto

# 2. Instalar dependencias
composer install
npm install && npm run build

# 3. Copiar el archivo de entorno
cp .env.example .env

# 4. Configurar la base de datos en .env
# DB_DATABASE=tubase
# DB_USERNAME=tuusuario
# DB_PASSWORD=tucontraseña

# 5. Ejecutar migraciones y seeders
php artisan migrate --seed



# 6. Generar la key
php artisan key:generate


# 7. Iniciar servidor local
php artisan serve

# 7. usuarios de prueba
Admin:admin@example.com
contraseña: password

Usuario:usuario@example.com
contraseña: password
