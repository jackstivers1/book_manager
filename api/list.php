<?php
require_once __DIR__ . "/db.php";

$page = isset($_GET["page"]) ? max(1, intval($_GET["page"])) : 1;
$pageSize = isset($_GET["pageSize"]) ? max(1, min(200, intval($_GET["pageSize"]))) : 10;
$genre = isset($_GET["genre"]) ? trim($_GET["genre"]) : "all";

$sortBy = isset($_GET["sortBy"]) ? trim($_GET["sortBy"]) : "id";
$sortDirRaw = isset($_GET["sortDir"]) ? strtolower(trim($_GET["sortDir"])) : "desc";
$sortDir = ($sortDirRaw === "asc") ? "ASC" : "DESC";

$allowedSort = [
  "id" => "id",
  "name" => "name",
  "pub_date" => "pub_date",
  "genre" => "genre",
  "author" => "author"
];
$sortCol = $allowedSort[$sortBy] ?? "id";

$offset = ($page - 1) * $pageSize;

$where = "";
$params = [];

if ($genre !== "" && strtolower($genre) !== "all") {
  $where = " WHERE genre = ? ";
  $params[] = $genre;
}

// total count
$sqlCount = "SELECT COUNT(*) AS total FROM books" . $where;
$stmt = $pdo->prepare($sqlCount);
$stmt->execute($params);
$total = (int)$stmt->fetchColumn();

$totalPages = (int)ceil($total / $pageSize);

// list page
$sql = "SELECT id, name, pub_date, genre, author, image_url
        FROM books
        $where
        ORDER BY $sortCol $sortDir, id DESC
        LIMIT ? OFFSET ?";

$params2 = array_merge($params, [$pageSize, $offset]);
$stmt = $pdo->prepare($sql);
$stmt->execute($params2);
$rows = $stmt->fetchAll();

echo json_encode([
  "items" => $rows,
  "meta" => [
    "page" => $page,
    "pageSize" => $pageSize,
    "total" => $total,
    "totalPages" => $totalPages,
    "genre" => $genre,
    "sortBy" => $sortBy,
    "sortDir" => strtolower($sortDir)
  ]
]);