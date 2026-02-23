<?php
// api/db.php

// CORS (handy during dev; you can tighten later)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
  http_response_code(204);
  exit;
}

$databaseUrl = getenv("DATABASE_URL");

if (!$databaseUrl) {
  http_response_code(500);
  echo json_encode(["error" => "DATABASE_URL is not set"]);
  exit;
}

$parts = parse_url($databaseUrl);
if ($parts === false) {
  http_response_code(500);
  echo json_encode(["error" => "Invalid DATABASE_URL"]);
  exit;
}

$host = $parts["host"] ?? "";
$port = $parts["port"] ?? 5432;
$user = $parts["user"] ?? "";
$pass = $parts["pass"] ?? "";
$db   = isset($parts["path"]) ? ltrim($parts["path"], "/") : "";

try {
  // sslmode=require is commonly needed on managed Postgres
  $dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require";
  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(["error" => "DB connection failed", "details" => $e->getMessage()]);
  exit;
}