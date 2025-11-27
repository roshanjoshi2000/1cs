<!doctype html>
<html lang="en">

<head>
  <title></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<h1>List of ALL my games!!!</h1>

<?php
  // Connect to database
  include 'db.php';
  // Run SQL query
  $sql = "SELECT * FROM videogames ORDER BY released_date";
  $results = mysqli_query($mysqli, $sql);
?>

<form action="search-game.php" method="post" class="mb-3">
  <input type="text" name="keywords" placeholder="Search">
  <input type="submit" value="Go!" class="btn btn-primary btn-sm">
</form>

<table class="table table-striped">
  <?php while($a_row = mysqli_fetch_assoc($results)): ?>
    <tr>
      <td>
        <a href="game-details.php?id=<?=$a_row['game_id']?>">
          <?=$a_row['game_name']?>
        </a>
      </td>

      <td>
        <a class="btn btn-primary" href="edit-games.php?id=<?=$a_row['game_id']?>"role="button">Edit</a>
      </td>

      <td>
        <a class="btn btn-primary" href="delete-game.php?id=<?=$a_row['game_id']?>"role="button"onclick="return confirm('Are you sure?')">Delete</a>
      </td>

      <td><?=$a_row['rating']?></td>
    </tr>
  <?php endwhile; ?>
</table>

<a href="add-game-form.php" class="btn btn-primary">Add a game</a>

</body>
</html>
