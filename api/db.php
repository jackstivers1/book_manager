<?php
// api/db.php
header("Content-Type: application/json; charset=utf-8");

$DB_HOST = getenv("DB_HOST") ?: "127.0.0.1";
$DB_USER = getenv("DB_USER") ?: "root";
$DB_PASS = getenv("DB_PASS") ?: "";
$DB_NAME = getenv("DB_NAME") ?: "book_manager";
$DB_PORT = getenv("DB_PORT") ?: "3306";

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, (int)$DB_PORT);
if ($conn->connect_error) {
  http_response_code(500);
  echo json_encode(["error" => "DB connection failed", "details" => $conn->connect_error]);
  exit;
}

$conn->set_charset("utf8mb4");