<?php

$databaseHost = getenv('EDUSCOPE_DB_HOST') ?: 'localhost';
$databaseUser = getenv('EDUSCOPE_DB_USER') ?: 'root';
$databasePassword = getenv('EDUSCOPE_DB_PASSWORD') ?: '';
$databaseName = getenv('EDUSCOPE_DB_NAME') ?: 'pemweb-db';

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
