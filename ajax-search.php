<?php
$db = new PDO("mysql:host=localhost;dbname=gamesdb", "root", "");

$term = "%" . $_GET["term"] . "%";

$query = $db->prepare("SELECT name FROM games WHERE name LIKE ? LIMIT 10");
$query->execute([$term]);

$results = $query->fetchAll(PDO::FETCH_COLUMN);

echo json_encode($results);
