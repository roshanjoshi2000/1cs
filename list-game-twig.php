<?php
include("db.php");
include("navbar.php");
require_once "vendor/autoload.php";

// Create inline template
$template = '
<!DOCTYPE html>
<html>
<head>
    <title>All Games</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>List of All Games</h1>
        
        {% if num_rows > 0 %}
            <table class="table">
                {% for game in results %}
                <tr>
                    <td>
                        <a href="game-details.php?id={{ game.game_id }}">
                            {{ game.game_name }}
                        </a>
                    </td>
                    <td>{{ game.rating }}</td>
                    <td>
                        <a href="edit-games.php?id={{ game.game_id }}">Edit</a>
                        <a href="delete.php?id={{ game.game_id }}" onclick="return confirm(\'Delete?\')">Delete</a>
                    </td>
                </tr>
                {% endfor %}
            </table>
        {% else %}
            <p>No games found.</p>
        {% endif %}
    </div>
</body>
</html>';

$loader = new \Twig\Loader\ArrayLoader([
    'list' => $template,
]);
$twig = new \Twig\Environment($loader);

// Fetch data
$sql = "SELECT * FROM videogames ORDER BY released_date DESC";
$result = $mysqli->query($sql);

$games = [];
$num_rows = $result->num_rows;

while ($row = $result->fetch_assoc()) {
    $games[] = $row;
}

echo $twig->render('list', [
    'results' => $games,
    'num_rows' => $num_rows,
]);
?>