<?php

require_once __DIR__ . '/environment.php';

$databaseHostSetting = getenv('EDUSCOPE_DB_HOST');
$databaseUserSetting = getenv('EDUSCOPE_DB_USER');
$databasePasswordSetting = getenv('EDUSCOPE_DB_PASSWORD');
$databaseNameSetting = getenv('EDUSCOPE_DB_NAME');
$databaseHost = $databaseHostSetting ?: 'localhost';
$databaseUser = $databaseUserSetting ?: (is_production_environment() ? '' : 'root');
$databasePassword = $databasePasswordSetting ?: '';
$databaseName = $databaseNameSetting ?: 'pemweb-db';

if (
    is_production_environment() &&
    (
        !$databaseHostSetting || !$databaseUserSetting || !$databasePasswordSetting || !$databaseNameSetting ||
        $databaseUser === 'root' || trim($databasePassword) === ''
    )
) {
    error_log('Production database credentials are missing or unsafe.');
    http_response_code(500);
    exit('Konfigurasi database belum tersedia. Hubungi pengelola situs.');
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $db_connect = new mysqli(
        $databaseHost,
        $databaseUser,
        $databasePassword,
        $databaseName
    );
    $db_connect->set_charset('utf8mb4');
} catch (mysqli_sql_exception $exception) {
    error_log('Database connection failed: ' . $exception->getMessage());
    http_response_code(500);
    exit('Koneksi ke database sedang bermasalah. Coba lagi nanti.');
}
