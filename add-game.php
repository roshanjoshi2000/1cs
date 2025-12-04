<?php
 
  // Read values from the form
  $game_name = $_POST['game_name'];
  $game_description = $_POST['game_description'];
  $game_release_date = $_POST['released_date'];
  $game_rating = $_POST['rating'];
 
  // Connect to database
  include("db.php");
 
  // Build SQL statement
  $sql = "INSERT INTO videogames(game_name, game_description, released_date, rating)
          VALUE('{$game_name}', '{$game_description}', '{$game_release_date}', '{$game_rating}')";
 
  // Run SQL statement and report errors
  if(!$mysqli -> query($sql)) {
      echo("<h4>SQL error description: " . $mysqli -> error . "</h4>");
  }
 
  // Redirect to list
  header("location: list-game.php");
?>