<?php
    session_start();#start session 
    if (isset($_GET['action']) && $_GET['action'] === 'logout') #checks if user is logging out
    {
        session_unset(); //unset all session variables
        session_destroy(); //destroy session
        header("Location: login.php"); //redirect to the login page
        exit();
    }
    
    if (!isset($_SESSION['Username'])) #checks if the username is in the global variable array session if so continue
    {
        header("Location: login.php");#redirect to login page if not logged in or registered
        exit();
    }

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserved Books</title>
    <!--Link the main css page to reserved page-->
    <link rel="stylesheet" href="../CSS/reserved.css">
</head>
<body>
    <header>
        <div class="navbar">
            <div>
                <!--naviagtion bar(home page,reserved books)-->
                <a href="books.php">Home</a>
                <a href="Reserved.php">Reserved Books</a>
            </div>
            <!--if logout link is clicked then go to php action where logout is-->
            <a href="books.php?action=logout">Logout</a>
        </div>
        <h1>Your Reserved Books</h1>
    </header>


    <div class="reserved-books">
        <?php
        //start of connection of the databse
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "librarydb";
        //create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
    
        //check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        //handle unreserve action
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unreserveBook'])) 
        {
            //establish variables
            $bookID = $_POST['bookID'];
            $loggedInUser = $_SESSION['Username'];

            //update the books table to mark the book as not reserved
            $updatebooks = "UPDATE books SET reserved = 'N' WHERE ISBN = '$bookID'";
            $conn->query($updatebooks); //execute SQL

            //remove the record from the `reserved` table
            $deletereserve = "DELETE FROM reserved WHERE ISBN = '$bookID' AND Username = '$loggedInUser'";
            $conn->query($deletereserve); //execute SQL

            $message = "Book unreserved successfully.";
        }
    
        // Fetch books data
        $loggedInUser = $_SESSION['Username']; // The logged-in username
        $sqlFetchBooks = "SELECT b.ISBN, b.bookTitle, b.author, b.editor, b.yearMake, c.categoryDesc AS category, b.reserved
            FROM books b
            JOIN category c ON b.category = c.categoryID
            JOIN reserved r ON b.ISBN = r.ISBN
            WHERE r.Username = '$loggedInUser' AND b.reserved = 'Y'";
        $result = $conn->query($sqlFetchBooks);
        if ($result->num_rows > 0)#checks if there is a book reserved by user
        {
            //display reserved book(works if multiple books are reserved by user but i made it that user can only reserve one book)
            while ($row = $result->fetch_assoc()) 
            {
                echo "<div class='bookBlocks'>";
                echo "<h2>" . ($row['bookTitle']) . "</h2>";#title
                echo "<p><strong>Author:</strong> " . ($row['author']) . "</p>";#author
                echo "<p><strong>Edition:</strong> " . ($row['editor']) . "</p>";#edition
                echo "<p><strong>Year:</strong> " . ($row['yearMake']) . "</p>";#year make
                echo "<p><strong>Genre:</strong> " . ($row['category']) . "</p>";#category description
                #button to unreserve a book
                echo "<form method='POST' action='Reserved.php'>";
                echo "<input type='hidden' name='bookID' value='" . ($row['ISBN']) . "'>";#hidden value of ISBN
                echo "<button type='submit' name='unreserveBook'>Unreserve</button>";#creates the button to unreserve
                echo "</form>";
                echo "</div>";
            }
        } else {
            echo "<div class = 'messages'> <h2>No reserved books.</h2></div>";
        }
        ?>
    </div>
    <footer>
    <p>2024 Web Development Ca</p>
    </footer>
    <?php
    $conn->close();#close connection
    ?>
</body>
</html>