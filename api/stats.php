<?php
require_once __DIR__ . "/db.php";

$totalRes = $conn->query("SELECT COUNT(*) AS total FROM books");
$total = (int)$totalRes->fetch_assoc()["total"];

$popularRes = $conn->query("
  SELECT genre, COUNT(*) AS c
  FROM books
  GROUP BY genre
  ORDER BY c DESC, genre ASC
  LIMIT 1
");
$popular = $popularRes->fetch_assoc();

echo json_encode([
  "total" => $total,
  "mostPopularGenre" => $popular ? $popular["genre"] : null,
  "mostPopularGenreCount" => $popular ? (int)$popular["c"] : 0
]);