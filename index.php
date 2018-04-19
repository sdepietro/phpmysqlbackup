<?php
######################################################################
## Sistema de Backups para MySQL - 04/2018
######################################################################
## Documentación
## https://github.com/sdepietro/phpmysqlbackup
## -------------------------------------------------------------------
## Creado por Sergio De Pietro para www.woopi.com.ar
## Es un simple sistema de backups.
## Permite la subida de los backups a Dropbox, usando el SDK.
######################################################################

require_once 'vendor/autoload.php';

use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\DropboxFile;
use Kunnu\Dropbox\Exceptions\DropboxClientException;


// Configurar las constantes a continuacion
define('DB_HOST', 'serverIp');
define('DB_NAME', 'Database_name');
define('DB_USER', 'database_username');
define('DB_PASSWORD', 'database_password');
define('DROPBOX_CLIENTID', 'Client_id');
define('DROPBOX_CLIENTSECRET', 'client_secret');
define('DROPBOX_ACCESSTOKEN', 'access_token');
//------No es necesario tocar nada a partir de esta línea-------


$base_path = 'sql_backups';

// Dejamos solamente los últimos 60 backups. Porque asi no llenamos el disco.
$files = array();
$my_directory = opendir($base_path) or die('Error');
while ($entry = @readdir($my_directory)) {
    if ($entry != "." && $entry != "..") {
        $files[] = $entry;
    }
}
closedir($my_directory);
$files = array_reverse($files);
$i = 1;
foreach ($files as $file) {
    if ($i > 60) {
        unlink($base_path . '/' . $file);
    }
    $i++;
}

//Hacemos un backup nuevo de la DB.
define('BACKUP_SAVE_TO', $base_path); // without trailing slash
$date = date('YmdHi');
$backupFile = BACKUP_SAVE_TO . '/' . DB_NAME . '_' . $date . '.gz';

//Si ese archivo ya existía, lo borramos. Esto es muy raro, pero si se ejecutan 2 backups en el mismo minuto...
if (file_exists($backupFile)) {
    unlink($backupFile);
}

//Comando para exportar la DB. Además la comprimimos para que pese siempre lo menos posible.
$command = 'mysqldump --opt -h ' . DB_HOST . ' -u ' . DB_USER . ' -p\'' . DB_PASSWORD . '\' ' . DB_NAME . ' | gzip > ' . $backupFile;

$result = shell_exec($command);

//Para crear una app en tu dropbox: https://www.dropbox.com/developers/apps/
$app = new DropboxApp(DROPBOX_CLIENTID, DROPBOX_CLIENTSECRET, DROPBOX_ACCESSTOKEN);

//Configuramos el servicio de Dropbox
$dropbox = new Dropbox($app);

try {
    // Create Dropbox File from Path
    $dropboxFile = new DropboxFile($filePath);

    // Upload the file to Dropbox
    $uploadedFile = $dropbox->upload($dropboxFile, $backupFile, ['autorename' => true]);

    // File Uploaded
    echo $uploadedFile->getPathDisplay();
} catch (DropboxClientException $e) {
    echo $e->getMessage();
}



