<?php
session_start();
require_once "./functions/database_functions.php";

// Connect to database
$conn = db_connect();

// Retrieve user's order history and ratings
$user_id = $_SESSION['user_id']; // Assuming user is logged in
$query = "SELECT o.*, b.genre, AVG(r.rating_value) AS average_rating
          FROM orders o
          JOIN books b ON o.book_isbn = b.book_isbn
          LEFT JOIN ratings r ON o.book_isbn = r.book_isbn
          WHERE o.user_id = '$user_id'
          GROUP BY o.book_isbn";
$result = mysqli_query($conn, $query);

// Initialize variables to store user's preferred genres and ratings
$genre_counts = array();
$genre_ratings = array();

// Loop through user's order history to calculate genre counts and ratings
while ($row = mysqli_fetch_assoc($result)) {
    $genre = $row['genre'];
    
    // Update genre counts
    if (isset($genre_counts[$genre])) {
        $genre_counts[$genre]++;
    } else {
        $genre_counts[$genre] = 1;
    }
    
    // Update genre ratings
    $average_rating = $row['average_rating'];
    if (isset($genre_ratings[$genre])) {
        $genre_ratings[$genre] += $average_rating;
    } else {
        $genre_ratings[$genre] = $average_rating;
    }
}

// Calculate average ratings for each genre
foreach ($genre_ratings as $genre => $total_rating) {
    $genre_ratings[$genre] = $total_rating / $genre_counts[$genre];
}

// Recommend books based on user's preferred genres and ratings
arsort($genre_ratings); // Sort genres by average rating in descending order
$recommended_genre = key($genre_ratings); // Get the highest rated genre

// Fetch recommended books from the highest rated genre
$recommendation_query = "SELECT * FROM books WHERE genre = '$recommended_genre' ORDER BY RAND() LIMIT 5";
$recommendation_result = mysqli_query($conn, $recommendation_query);

// Display recommended books to the user
while ($recommended_book = mysqli_fetch_assoc($recommendation_result)) {
    // Display recommended book details
    echo "<p>Recommended Book: " . $recommended_book['book_title'] . "</p>";
    // You can display more details if needed
}

// Close database connection
if (isset($conn)) {
    mysqli_close($conn);
}
?>
