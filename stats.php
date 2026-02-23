<?php
require_once __DIR__ . "/db.php";

try {
  $total = (int)$pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();

  $popularStmt = $pdo->query("
    SELECT genre, COUNT(*) AS c
    FROM books
    GROUP BY genre
    ORDER BY c DESC, genre ASC
    LIMIT 1
  ");
  $popular = $popularStmt->fetch();

  echo json_encode([
    "total" => $total,
    "mostPopularGenre" => $popular ? $popular["genre"] : null,
    "mostPopularGenreCount" => $popular ? (int)$popular["c"] : 0
  ]);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(["error" => "Failed to load stats", "details" => $e->getMessage()]);
}