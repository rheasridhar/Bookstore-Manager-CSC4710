<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = uniqid('user_');
}

$book_isbn = $_GET['bookisbn'];
// Connect to database
require_once "./functions/database_functions.php";
$conn = db_connect();

// Fetch book details
$query = "SELECT * FROM books WHERE book_isbn = '$book_isbn'";
$result = mysqli_query($conn, $query);
if (!$result) {
  echo "Can't retrieve data " . mysqli_error($conn);
  exit;
}

$row = mysqli_fetch_assoc($result);
if (!$row) {
  echo "Empty book";
  exit;
}

// Fetch average rating for the book
$query = "SELECT AVG(rating_value) AS average_rating FROM ratings WHERE book_isbn = '$book_isbn'";
$result = mysqli_query($conn, $query);
if (!$result) {
  echo "Error fetching average rating: " . mysqli_error($conn);
  exit;
}
$average_rating = mysqli_fetch_assoc($result)['average_rating'];

$title = $row['book_title'];
require "./template/header.php";

// Handle rating submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST['rating'])) {
    $rating = $_POST['rating'];
    // Insert the rating into the ratings table
    $user_id = $_SESSION['user_id']; // Assuming user is logged in
    $query = "INSERT INTO ratings (book_isbn, user_id, rating_value) VALUES ('$book_isbn', '$user_id', '$rating')";
    $result = mysqli_query($conn, $query);
    if (!$result) {
      echo "Error submitting rating: " . mysqli_error($conn);
    } else {
      echo "Rating submitted successfully!";
      // Refresh the page to display updated average rating
      echo "<meta http-equiv='refresh' content='1'>";
    }
  }
}
?>

<!-- Example row of columns -->
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="books.php" class="text-decoration-none text-muted fw-light">All Books</a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo $row['book_title']; ?></li>
  </ol>
</nav>
<div class="row">
  <div class="col-md-3 text-center book-item">
    <div class="img-holder overflow-hidden">
      <img class="img-top" src="./bootstrap/img/<?php echo $row['book_image']; ?>">
    </div>
    <!-- Display average rating -->
    <div>Average Rating: <?php echo round($average_rating, 1); ?></div>
  <!-- Rating form -->
<form id="ratingForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
  <label for="rating">Give a rating:</label>
  <select name="rating" id="rating">
    <option value="1">1 Star</option>
    <option value="2">2 Stars</option>
    <option value="3">3 Stars</option>
    <option value="4">4 Stars</option>
    <option value="5">5 Stars</option>
  </select>
  <input type="submit" value="Submit Rating" onclick="return false;"> <!-- Add onclick attribute here -->
</form>

<script>
// Prevent default form submission
document.getElementById("ratingForm").addEventListener("submit", function(event) {
  event.preventDefault();
});
</script>


  </div>
  <div class="col-md-9">
    <div class="card rounded-0 shadow">
      <div class="card-body">
        <div class="container-fluid">
          <h4><?= $row['book_title'] ?></h4>
          <hr>
          <p><?php echo $row['book_descr']; ?></p>
          <h4>Details</h4>
          <table class="table">
            <?php foreach ($row as $key => $value) {
              if ($key == "book_descr" || $key == "book_image" || $key == "publisherid" || $key == "book_title") {
                continue;
              }
              switch ($key) {
                case "book_isbn":
                  $key = "ISBN";
                  break;
                case "book_title":
                  $key = "Title";
                  break;
                case "book_author":
                  $key = "Author";
                  break;
                case "book_price":
                  $key = "Price";
                  break;
              }
            ?>
              <tr>
                <td><?php echo $key; ?></td>
                <td><?php echo $value; ?></td>
              </tr>
            <?php
            }
            if (isset($conn)) {
              mysqli_close($conn);
            }
            ?>
          </table>
          <form method="post" action="cart.php">
            <input type="hidden" name="bookisbn" value="<?php echo $book_isbn; ?>">
            <div class="text-center">
              <input type="submit" value="Purchase / Add to cart" name="cart" class="btn btn-success rounded-0">
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
require "./template/footer.php";
?>
