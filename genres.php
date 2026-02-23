<?php
require_once __DIR__ . "/db.php";

try {
  $stmt = $pdo->query("SELECT DISTINCT genre FROM books ORDER BY genre ASC");
  $genres = $stmt->fetchAll(PDO::FETCH_COLUMN);

  echo json_encode(["genres" => $genres]);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(["error" => "Failed to load genres", "details" => $e->getMessage()]);
}