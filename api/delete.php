<?php
require_once __DIR__ . "/db.php";

$input = json_decode(file_get_contents("php://input"), true);
$id = intval($input["id"] ?? 0);

if ($id <= 0) {
  http_response_code(400);
  echo json_encode(["error" => "Invalid id"]);
  exit;
}

$stmt = $conn->prepare("DELETE FROM books WHERE id=?");
$stmt->bind_param("i", $id);
$ok = $stmt->execute();
$stmt->close();

echo json_encode(["success" => $ok]);