<?php
session_start();
$count = 0;
// connect to database
require_once "./functions/database_functions.php";
$conn = db_connect();


// Fetch latest 4 books with the genre "Drama"
$query = "SELECT * FROM books WHERE genre = 'Drama' LIMIT 4";
$result = mysqli_query($conn, $query);


if(!$result){
 echo "Can't retrieve data " . mysqli_error($conn);
 exit;
}


$title = "Home";
require_once "./template/header.php";
?>


<!-- Example row of columns -->
<div class="lead text-center text-dark fw-bolder h4">Recommended Books</div>
<center>
 <hr class="bg-success" style="width:5em;height:3px;opacity:1">
</center>
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
 <?php } ?>
</div>


<?php
if(isset($conn)) { mysqli_close($conn); }
require_once "./template/footer.php";
?>


