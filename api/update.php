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

$stmt = $conn->prepare("UPDATE books SET name=?, pub_date=?, genre=?, author=?, image_url=? WHERE id=?");
$stmt->bind_param("sssssi", $name, $pub_date_or_null, $genre, $author, $image_url, $id);
$ok = $stmt->execute();
$stmt->close();

echo json_encode(["success" => $ok]);