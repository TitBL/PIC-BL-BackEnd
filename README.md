# Aplicativo web para la centralización de documentos electrónicos tributarios
## Colaboradores 
- **[Basurto Cruz Edgar Daniel](https://github.com/edgarbasurto)**
- **[Larrea Buste Edwin Rafael](https://github.com/Rafael1108)**
 
## Funcionalidad 
### Version 1.0
- Módulo de Seguridad:
    - Gestión de usuarios.
    - LogIn.
- Módulo de Parámetros:
    - Gestión de empresas.
    - Configuración de correos para notificaciones.
- Módulo de Negocio: 
    - Gestión documentos eléctricos emitidos. 
    - Gestión documentos eléctricos recibidos.
- Módulo de Reportes: 
    - Reportes de documentos eléctricos emitidos. 
    - Descarga de documentos eléctricos emitidos. 
    - Reportes de documentos eléctricos recibidos. 
    - Descarga de documentos eléctricos recibidos.
      
## Requisitos del Sistema
### Tecnologías usadas
* [Lavarel](https://laravel.com/docs/9.x): Versión 10
* [PHP](https://www.php.net/): Versión ^8.0.2
* [SQL Server](https://www.microsoft.com/es-es/sql-server)
  
### Servidor Web
La API está diseñaada para ser ejecutada en servidores web que soporten PHP. Se recomienda
el uso de Apache, IIS o Nginx.

### Base de Datos
La aplicación utiliza SQL Server como base de datos, asegúrese de tener las credenciales de
acceso a un servidor de SQL Server, se recomienda usar la autenticación de SQL Server que no
se basan en cuentas de usuario de Windows.

## Pre Requisitos 
- Tener previamente instalado y configurado las técnologías usadas.
- [Lavarel](https://laravel.com/docs/9.x) para gestionar sus directivas recomienda el uso de [Composer](https://getcomposer.org/) se requiere que el equipo tenga instalado este componente.

## Instalación
1. Clonar Repositorio
```
$  git clone https://github.com/TitBL/PIC-BL-BackEnd.git
```
2. Abrir una consola de comandos y ubicarse dentro del directorio clonado.
3. Ejecute el siguiente comando para instalar las dependencias del proyecto.
```
$ composer install
```
4. Ejecutar para actualizar las dependencias.
```
$ composer update 
``` 
Este paso descargará y creará la carpeta <strong>Ventor</strong>.

## Configuración del Entorno
Copie el archivo .env.example a .env y configure las variables de entorno:
### Variables de información para el API
```
APP_NAME ="nombre_api"
APP_ENV = local
APP_DEBUG =false
APP_URL ="url_api’
APP_ENTERPRISE_NAME="nombre_empresa"
APP_ENTERPRISE_WEB="pagina_web"
```
### Variables para la gestión de archivos y servicios SRI
```
STORAGE_LOCAL ="D:/PRODUCCION_MAIL/"
STORAGE_PUBLIC ="D:/PRODUCCION_MAIL/"
```
### Variables para servicios SRI
```
$ SRI_URL ="https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobante
```

### Variables para la conexión con base de datos
```
DB_CONNECTION = sqlsrv
DB_HOST ="nombre_instancia_SQL_Server"
DB_PORT =1433
DB_DATABASE"nombre_base_datos_SQL_Server"
DB_USERNAME ="nombre_usuario_SQL_Server"
DB_PASSWORD =’contrasenia_acceso_SQL_Server’
```

### Generar Clave de Aplicación
Ejecute el siguiente comando para generar la clave de aplicación:
```
$ php artisan key : generate
```

### Migraciones y Semillas
Previo a realizar la ejecuci´on para las migraciones debe asegurarse que la base de datos exista
en SQL Server.
Ejecute el siguiente comando para generar las migraciones:
```
$ php artisan migrate
```
