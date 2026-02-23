<?php
require_once __DIR__ . "/db.php";

$input = json_decode(file_get_contents("php://input"), true);

$id = intval($input["id"] ?? 0);
$name = trim($input["name"] ?? "");
$pub_date = trim($input["pub_date"] ?? "");
$genre = trim($input["genre"] ?? "");
$author = trim($input["author"] ?? "");
$image_url = trim($input["image_url"] ?? "");

if ($id <= 0 || $name === "" || $genre === "" || $author === "" || $image_url === "") {
  http_response_code(400);
  echo json_encode(["error" => "Missing/invalid fields (id, name, genre, author, image_url)"]);
  exit;
}

$pub_date_or_null = ($pub_date === "") ? null : $pub_date;

try {
  $stmt = $pdo->prepare("
    UPDATE books
    SET name = ?, pub_date = ?, genre = ?, author = ?, image_url = ?
    WHERE id = ?
  ");
  $stmt->execute([$name, $pub_date_or_null, $genre, $author, $image_url, $id]);

  echo json_encode(["success" => true]);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(["error" => "Update failed", "details" => $e->getMessage()]);
}