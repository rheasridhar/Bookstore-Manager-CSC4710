<?php
  session_start();
  $count = 0;
  // connect to database
  require_once "./functions/database_functions.php";
  $conn = db_connect();

  // Check if search query is provided
  if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    // Search by title or author
    $query = "SELECT book_isbn, book_image, book_title FROM books WHERE book_title LIKE '%$search%' OR book_author LIKE '%$search%'";
  } elseif (isset($_GET['genre'])) {
    $genre = mysqli_real_escape_string($conn, $_GET['genre']);
    // Filter by genre
    if ($genre == 'All') {
      $query = "SELECT book_isbn, book_image, book_title FROM books";
    } else {
      $query = "SELECT book_isbn, book_image, book_title FROM books WHERE genre = '$genre'";
    }
  } else {
    // If no search query or genre filter, fetch all books
    $query = "SELECT book_isbn, book_image, book_title FROM books";
  }

  $result = mysqli_query($conn, $query);
  if(!$result){
    echo "Can't retrieve data " . mysqli_error($conn);
    exit;
  }

  $title = "List of Books";
  require_once "./template/header.php";
?>

<!-- Search form -->
<form class="row mb-3 justify-content-center" action="" method="GET">
  <div class="col-auto">
    <input type="text" class="form-control rounded-pill" name="search" placeholder="Search Books">
  </div>
  <div class="col-auto">
    <button type="submit" class="btn btn-success rounded-pill">Search</button>
  </div>
</form>

<!-- Genre filter -->
<form class="row mb-3 justify-content-center" action="" method="GET">
  <div class="col-auto">
    <select class="form-select rounded-pill" name="genre">
      <option value="" selected>Filter by Genre</option>
      <option value="All">All Genres</option>
      <option value="Adventure">Adventure</option>
      <option value="Drama">Drama</option>
      <option value="Dystopian">Dystopian</option>
      <option value="Fantasy">Fantasy</option>
    </select>
  </div>
  <div class="col-auto">
    <button type="submit" class="btn btn-success rounded-pill">Apply</button>
  </div>
</form>

<p class="lead text-center text-muted">List of All Books</p>

<?php 
  // Loop through the search results or all books
  for($i = 0; $i < mysqli_num_rows($result); $i++){ 
?>

<!-- Book Cards -->
<div class="row">
  <?php while($book = mysqli_fetch_assoc($result)){ ?>
    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 py-2 mb-2">
      <a href="book.php?bookisbn=<?php echo $book['book_isbn']; ?>" class="card rounded-0 shadow book-item text-reset text-decoration-none">
        <div class="img-holder overflow-hidden">
          <img class="img-top" src="./bootstrap/img/<?php echo $book['book_image']; ?>">
        </div>
        <div class="card-body">
          <div class="card-title fw-bolder h5 text-center"><?= $book['book_title'] ?></div>
        </div>
      </a>
    </div>
  <?php
      $count++;
      if($count >= 4){
          $count = 0;
          break;
        }
      } ?> 
</div>

<?php
  }
  if(isset($conn)) { mysqli_close($conn); }
  require_once "./template/footer.php";
?>
