<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit;
}

// Redirect to login if not logged in
if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit;
}

include("db.php");
include("navbar.php");

// Fetch latest games
$sql = "SELECT * FROM videogames ORDER BY released_date DESC";
$result = $mysqli->query($sql);
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Game Portal - Home</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">

  <div class="text-center mb-5">
    <h1>Welcome to Game Portal</h1>
    <p class="lead">Discover, add, and manage your favorite games!</p>
  </div>

  <!-- AJAX Search -->
<div class="mb-5">
  <h3>Search Games</h3>

  <input type="text" id="searchBox" class="form-control" placeholder="Type to search games..." autocomplete="off">

  <ul id="results" class="list-group mt-2"></ul>
</div>


  <!-- Latest Games -->
  <h3>Latest Added Games</h3>
  <div class="row mt-3">

    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($game = $result->fetch_assoc()): ?>
          <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($game['game_name']) ?></h5>
                <p class="card-text"><?= substr(htmlspecialchars($game['game_description']), 0, 100) ?>...</p>
              </div>
              <div class="card-footer d-flex justify-content-between align-items-center">
                <small>Rating: <?= htmlspecialchars($game['rating']) ?></small>
                <a href="game-details.php?id=<?= $game['game_id'] ?>" class="btn btn-sm btn-primary">Details</a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="text-muted">No games added yet. <a href="add-game-form.php">Add your first game!</a></p>
    <?php endif; ?>

  </div>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- AJAX Search Script -->
<script>
document.getElementById("searchBox").addEventListener("keyup", function() {
    let keywords = this.value.trim();
    let resultsBox = document.getElementById("results");
    resultsBox.innerHTML = '';

    if (keywords.length < 2) {
        return;
    }

    fetch('ajax-search.php?search=' + encodeURIComponent(keywords))
    .then(response => response.json())
    .then(data => {
        resultsBox.innerHTML = '';

        if (data.length === 0) {
            resultsBox.innerHTML = '<li class="list-group-item text-muted">No results found</li>';
        } else {
            data.forEach(game => {
                let li = document.createElement('li');
                li.className = 'list-group-item';
                li.innerHTML = `
                    <strong>${game.game_name}</strong><br>
                    <small>${game.game_description.substring(0, 60)}...</small>
                `;

                li.style.cursor = "pointer";
                li.onclick = () => window.location = "game-details.php?id=" + game.game_id;

                resultsBox.appendChild(li);
            });
        }
    })
    .catch(err => console.error('Error fetching search results:', err));
});
</script>


</body>
<a href="logout.php" class="btn btn-danger">Logout</a>
</html>
