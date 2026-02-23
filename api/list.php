<?php
require_once __DIR__ . "/db.php";

$page = isset($_GET["page"]) ? max(1, intval($_GET["page"])) : 1;
$pageSize = isset($_GET["pageSize"]) ? max(1, min(200, intval($_GET["pageSize"]))) : 10;
$genre = isset($_GET["genre"]) ? trim($_GET["genre"]) : "all";

$sortBy = isset($_GET["sortBy"]) ? trim($_GET["sortBy"]) : "id";
$sortDir = isset($_GET["sortDir"]) ? strtolower(trim($_GET["sortDir"])) : "desc";
$sortDir = ($sortDir === "asc") ? "ASC" : "DESC"; // default DESC

// Whitelist columns to prevent SQL injection
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
$types = "";

if ($genre !== "" && strtolower($genre) !== "all") {
  $where = " WHERE genre = ? ";
  $params[] = $genre;
  $types .= "s";
}

// total count
$sqlCount = "SELECT COUNT(*) AS total FROM books" . $where;
$stmt = $conn->prepare($sqlCount);
if ($where !== "") $stmt->bind_param($types, ...$params);
$stmt->execute();
$total = (int)$stmt->get_result()->fetch_assoc()["total"];
$stmt->close();

$totalPages = (int)ceil($total / $pageSize);

// list page
$sql = "SELECT id, name, pub_date, genre, author, image_url
        FROM books " . $where . "
        ORDER BY $sortCol $sortDir, id DESC
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);

if ($where !== "") {
  $types2 = $types . "ii";
  $params2 = array_merge($params, [$pageSize, $offset]);
  $stmt->bind_param($types2, ...$params2);
} else {
  $stmt->bind_param("ii", $pageSize, $offset);
}

$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

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