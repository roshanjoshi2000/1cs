<?php

include("db.php");
include("navbar.php");
require_once"../vendor/autoload.php";

$loader = new \Twig\Loader\FilesystemLoader('.');
$twig = new \Twig\Environment($loader);


// Fetch latest games
$sql = "SELECT * FROM games ORDER BY released_date DESC";
$result = $mysqli->query($sql);
$games =[];
$num_rows = $result->num_rows;

/* while ($row =$result->fetch_assoc()){
	$games[] =$row;
} */

echo $twig->render('index.html',[
 'results' => $games,
 'num_rows'=> $num_rows,
 ]);
	
?>
