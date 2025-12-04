<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  </head>
  <body>
    <div class="container">
      <h1>Add a videogames</h1>
      <form action="add-game.php" method="post">
        <div class="mb-3">
          <label for="game_name" class="form-label">Game name</label>
          <input type="text" class="form-control" id="game_name" name="game_name">
        </div>
        <div class="mb-3">
          <label for="game_description" class="form-label">Description</label>
          <textarea class="form-control" id="game_description" name="game_description" rows="5"></textarea>
        </div>
        <div class="mb-3">
          <label for="released_date" class="form-label">Date released</label>
          <input type="date" class="form-control" id="released_date" name="released_date">
        </div>  
        <div class="mb-3">
          <label for="rating" class="form-label">Rating</label>
          <input type="number" class="form-control" id="rating" name="rating">
        </div>        
        <input type="submit" class="btn btn-primary" value="Add game">
      </form>
    </div>
  </body>
</html>   
