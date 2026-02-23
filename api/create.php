<?php
require_once __DIR__ . "/db.php";

$input = json_decode(file_get_contents("php://input"), true);

$name = trim($input["name"] ?? "");
$pub_date = trim($input["pub_date"] ?? ""); // YYYY-MM-DD or ""
$genre = trim($input["genre"] ?? "");
$author = trim($input["author"] ?? "");
$image_url = trim($input["image_url"] ?? "");

if ($name === "" || $genre === "" || $author === "" || $image_url === "") {
  http_response_code(400);
  echo json_encode(["error" => "Missing required fields (name, genre, author, image_url)"]);
  exit;
}

$pub_date_or_null = ($pub_date === "") ? null : $pub_date;

try {
  $stmt = $pdo->prepare("
    INSERT INTO books (name, pub_date, genre, author, image_url)
    VALUES (?, ?, ?, ?, ?)
    RETURNING id
  ");
  $stmt->execute([$name, $pub_date_or_null, $genre, $author, $image_url]);
  $id = (int)$stmt->fetchColumn();

  echo json_encode(["success" => true, "id" => $id]);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(["error" => "Insert failed", "details" => $e->getMessage()]);
}