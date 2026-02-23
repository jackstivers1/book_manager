<?php
require_once __DIR__ . "/db.php";

$input = json_decode(file_get_contents("php://input"), true);
$id = intval($input["id"] ?? 0);

if ($id <= 0) {
  http_response_code(400);
  echo json_encode(["error" => "Invalid id"]);
  exit;
}

try {
  $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
  $stmt->execute([$id]);

  echo json_encode(["success" => true]);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(["error" => "Delete failed", "details" => $e->getMessage()]);
}