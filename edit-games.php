<?php
include("db.php");

// Get ID from URL
$id = $_GET['id'];

// Load existing game data
$sql = "SELECT * FROM videogames WHERE game_id = {$id}";
$result = mysqli_query($mysqli, $sql);
$row = mysqli_fetch_assoc($result);
?>

<h1>Edit Game</h1>

<form action="update-game.php" method="post">
  
  <!-- Hidden field to send ID -->
  <input type="hidden" name="id" value="<?=$row['game_id']?>">

  <label>Game Name:</label>
  <input type="text" name="game_name" value="<?=$row['game_name']?>"><br>

  <label>Description:</label>
  <textarea name="game_description"><?=$row['game_description']?></textarea><br>

  <label>Release Date:</label>
  <input type="date" name="released_date" value="<?=$row['released_date']?>"><br>

  <label>Rating:</label>
  <input type="number" name="rating" value="<?=$row['rating']?>"><br>

  <button type="submit">Update Game</button>

</form>

<a href="list-game.php"><< Back to list</a>
