<?php
require_once __DIR__ . "/db.php";

$res = $conn->query("SELECT DISTINCT genre FROM books ORDER BY genre ASC");
$genres = [];
while ($row = $res->fetch_assoc()) {
  $genres[] = $row["genre"];
}
echo json_encode(["genres" => $genres]);