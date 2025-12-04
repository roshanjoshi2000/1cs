<?php

require_once __DIR__ . '/vendor/autoload.php';
include __DIR__ . "/db.php";
// Remove navbar echoing before Twig, include it *inside* the template instead
 include("navbar.php");

// Initialize Twig
$loader = new \Twig\Loader\FilesystemLoader(__DIR__);
$twig = new \Twig\Environment($loader, [
    'cache' => false,  // disable cache during development
]);

// Fetch latest games
$sql = "SELECT * FROM videogames ORDER BY released_date DESC";
$result = $mysqli->query($sql);

if (!$result) {
    die("SQL Error: " . $mysqli->error);
}

$games = $result->fetch_all(MYSQLI_ASSOC);
$num_rows = $result->num_rows;

// Render template
echo $twig->render('index.html', [
    'results' => $games,
    'num_rows' => $num_rows,
]);