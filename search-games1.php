<?php
include("db.php");

// If AJAX request, return ONLY the table rows
if(isset($_POST['ajax']) && $_POST['ajax'] == 1){

    $keywords = $_POST['keywords'] ?? '';

    $sql = "SELECT * FROM videogames
            WHERE game_name LIKE '%{$keywords}%'
            ORDER BY released_date";

    $results = mysqli_query($mysqli, $sql);

    echo "<table>";

    while($row = mysqli_fetch_assoc($results)) {
        echo "<tr>";
        echo "<td><a href='game-details.php?id={$row['game_id']}'>".$row['game_name']."</a></td>";
        echo "<td>".$row['rating']."</td>";
        echo "</tr>";
    }

    echo "</table>";
    exit; // Stop the page here for AJAX
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>AJAX Game Search</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

<h1>Search Results</h1>

<input type="text" id="keywords" placeholder="Search games...">
<div id="results">Loading...</div>

<script>
$(document).ready(function(){

    // Load all games initially
    loadGames("");

    // Function to load results using AJAX
    function loadGames(query){
        $.post("search-games1.php", { keywords: query, ajax: 1 }, function(data){
            $("#results").html(data);
        });
    }

    // Search as the user types
    $("#keywords").keyup(function(){
        let query = $(this).val();
        loadGames(query);
    });

});
</script>

</body>
</html>