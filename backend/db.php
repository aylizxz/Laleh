<?php
require_once __DIR__ . '/config.php';

try {
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    // Enable MySQL TLS if configured (best effort; silently ignored if driver lacks constants).
    if (defined('DB_SSL_CA') && DB_SSL_CA !== '' && defined('PDO::MYSQL_ATTR_SSL_CA')) {
        $options[PDO::MYSQL_ATTR_SSL_CA] = DB_SSL_CA;
        if (defined('DB_SSL_CERT') && DB_SSL_CERT !== '' && defined('PDO::MYSQL_ATTR_SSL_CERT')) {
            $options[PDO::MYSQL_ATTR_SSL_CERT] = DB_SSL_CERT;
        }
        if (defined('DB_SSL_KEY') && DB_SSL_KEY !== '' && defined('PDO::MYSQL_ATTR_SSL_KEY')) {
            $options[PDO::MYSQL_ATTR_SSL_KEY] = DB_SSL_KEY;
        }
        if (defined('DB_SSL_VERIFY') && defined('PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT')) {
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = (string)DB_SSL_VERIFY !== '0';
        }
    }

    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        $options
    );
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'msg' => 'Database connection failed']);
    exit;
}

function clean(string $s): string
{
    return htmlspecialchars(trim($s), ENT_QUOTES, 'UTF-8');
}

function json_out(array $d): never
{
    header('Content-Type: application/json');
    echo json_encode($d);
    exit;
}