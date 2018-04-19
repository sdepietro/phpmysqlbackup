# Sistema de Backups para MySQL
Sistema de backups para bases de datos MySql que integra Dropbox para subir los backups a un lugar seguro.

## Instrucciones

Crear una carpeta llamada sql_backups en el mismo directorio de index.php

Tirar en consola
```
composer install
```

Configurar las variables:
```
define('DB_HOST', 'serverIp');
define('DB_NAME', 'Database_name');
define('DB_USER', 'database_username');
define('DB_PASSWORD', 'database_password');
define('DROPBOX_CLIENTID', 'Client_id');
define('DROPBOX_CLIENTSECRET', 'client_secret');
define('DROPBOX_ACCESSTOKEN', 'access_token');
```
