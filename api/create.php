<?php
require_once __DIR__ . "/db.php";

$input = json_decode(file_get_contents("php://input"), true);
$name = trim($input["name"] ?? "");
$pub_date = trim($input["pub_date"] ?? ""); // YYYY-MM-DD or ""
$genre = trim($input["genre"] ?? "");
$author = trim($input["author"] ?? "");

$pub_date_or_null = ($pub_date === "") ? null : $pub_date;

$image_url = trim($input["image_url"] ?? "");

if ($name === "" || $genre === "" || $author === "" || $image_url === "") {
  http_response_code(400);
  echo json_encode(["error" => "Missing required fields (name, genre, author, image_url)"]);
  exit;
}

$stmt = $conn->prepare("INSERT INTO books (name, pub_date, genre, author, image_url) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $name, $pub_date_or_null, $genre, $author, $image_url);
$ok = $stmt->execute();
$id = $conn->insert_id;
$stmt->close();

echo json_encode(["success" => $ok, "id" => (int)$id]);