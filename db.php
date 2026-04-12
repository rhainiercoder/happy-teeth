<?php
$config = require __DIR__ . "/config.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli(
        $config["db_host"],
        $config["db_user"],
        $config["db_pass"],
        $config["db_name"],
        (int)$config["db_port"]
    );
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    die("Database connection failed."); // don’t expose details publicly
}