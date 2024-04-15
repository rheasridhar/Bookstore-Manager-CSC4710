<?php
// Debug statements
echo "Book ISBN: " . $_GET['bookisbn'] . "<br>";
echo "User ID: " . $_SESSION['user_id'] . "<br>";

session_start();
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = uniqid('user_');
}

// Connect to database
require_once "./functions/database_functions.php";
$conn = db_connect();

// Check if a rating is submitted
if (isset($_POST['rating']) && isset($_POST['bookisbn'])) {
    $rating = mysqli_real_escape_string($conn, $_POST['rating']);
    $bookisbn = mysqli_real_escape_string($conn, $_POST['bookisbn']);
    $user_id = $_SESSION['user_id']; // User identifier
    // Insert the rating into the ratings table
    $query = "INSERT INTO ratings (book_isbn, user_id, rating_value) VALUES ('$bookisbn', '$user_id', '$rating')";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        echo "Error submitting rating: " . mysqli_error($conn);
        exit;
    }
}

// Fetch book information
$bookisbn = mysqli_real_escape_string($conn, $_GET['bookisbn']);
$query = "SELECT * FROM books WHERE book_isbn = '$bookisbn'";
$result = mysqli_query($conn, $query);
if (!$result) {
    echo "Error fetching book information: " . mysqli_error($conn);
    exit;
}
$book = mysqli_fetch_assoc($result);

// Fetch average rating for the book
$query = "SELECT AVG(rating_value) AS average_rating FROM ratings WHERE book_isbn = '$bookisbn'";
$result = mysqli_query($conn, $query);
if (!$result) {
    echo "Error fetching average rating: " . mysqli_error($conn);
    exit;
}
$average_rating = mysqli_fetch_assoc($result)['average_rating'];

// Display book information and rating form
// Your HTML code goes here...
?>


<!-- Display book information -->

<!-- Display average rating -->
<div> Average Rating: <?php echo round($average_rating, 1); ?> </div>

<!-- Rating form -->
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
  <label for="rating">Give a rating:</label>
  <select name="rating" id="rating">
    <option value="1">1 Star</option>
    <option value="2">2 Stars</option>
    <option value="3">3 Stars</option>
    <option value="4">4 Stars</option>
    <option value="5">5 Stars</option>
  </select>
  <input type="submit" value="Submit Rating" disabled> <!-- Add disabled attribute here -->
</form>


<?php
// Close database connection
if (isset($conn)) {
    mysqli_close($conn);
}
?>
