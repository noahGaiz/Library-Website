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
    <title>Books</title>
    <!--Link the main css page to books page-->
    <link rel="stylesheet" href="../CSS/books.css">
</head>
<body>
    <header>
        <div class="navbar">
            <div>
                <!--naviagtion bar (home page,reserved books page)-->
                <a href="books.php">Home</a>
                <a href="Reserved.php">Reserved Books</a>
            </div>
            <a href="books.php?action=logout">Logout</a><!--if logout link is clicked then go to php action where logout is-->
        </div>
        <!--Prints welcome and users name-->
        <?php echo "<header><h1>Welcome to the Books Page, " . ($_SESSION['Username']) . "!</h1></header>"; ?>
        <h1>Available Books</h1>
        
    </header>

    <div class="search-container">
        <form method="GET" action="books.php">
            <!--The search bar and gets the data based on the input from the searchtitle section in php-->
            <input type="text" name="searchTitle" placeholder="Search by Title or Author" value="<?php echo isset($_GET['searchTitle']) ? ($_GET['searchTitle']) : ''; ?>">
            <select name="searchCategory"><!--used as the key to access the selected value-->
                <option value="">All Categories</option><!--creates drop down menu-->
                <?php
                //start of database connection
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

                //fetch categories for the dropdown menu
                $categorySql = "SELECT categoryID, categoryDesc FROM category";
                $categoryResult = $conn->query($categorySql);#execute sql
                if ($categoryResult->num_rows > 0)#checks if there are any rows 
                {
                    while ($category = $categoryResult->fetch_assoc())#fetch row from the query result as an associative array
                    {
                        #checks if user inputted a category, checks if submitted categoryID is the same as the categoryID in loop query
                        $selected = isset($_GET['searchCategory']) && $_GET['searchCategory'] == $category['categoryID'] ? "selected" : "";
                        #sets users categoryID choice into option, displays category description
                        echo "<option value='" . ($category['categoryID']) . "' $selected>" . ($category['categoryDesc']) . "</option>";
                    }
                }
                ?>
            </select>
            <button type="submit">Search</button>
        </form>
    </div>

    <div class="books">
        <?php
        //build the search query
        #puts wildcard(%) around title so it can find partial searchs,if no search then search anything
        $searchTitle = isset($_GET['searchTitle']) ? "%" . $_GET['searchTitle'] . "%" : "%%";
        #capture category if one is selected,ensures it not empty, if false then category search is null
        $searchCategory = isset($_GET['searchCategory']) && !empty($_GET['searchCategory']) ? $_GET['searchCategory'] : null;

        

        //selects book with similar title or author
        $sql = "SELECT ISBN, bookTitle, author, editor, yearMake, c.categoryDesc AS category, reserved 
        FROM books b
        JOIN category c ON b.category = c.categoryID
        WHERE (bookTitle LIKE '$searchTitle' OR author LIKE '$searchTitle')";

        if ($searchCategory)#if category added to search then append to the sql statement
        {
            $sql .= " AND b.category = '$searchCategory'"; 
        }

        $result = $conn->query($sql);#execute sql

        //display books
        if ($result->num_rows > 0) 
        {
            #print every singular book
            while ($row = $result->fetch_assoc()) 
            {
                #variable to check if reserved or not
                $isReserved = $row['reserved'] === 'Y';
                #print all data about books
                echo "<div class='bookBlocks'>";
                echo "<h2>" . ($row['bookTitle']) . "</h2>";
                echo "<p><strong>Author:</strong> " . ($row['author']) . "</p>";
                echo "<p><strong>Edition:</strong> " . ($row['editor']) . "</p>";
                echo "<p><strong>Year:</strong> " . ($row['yearMake']) . "</p>";
                echo "<p><strong>Genre:</strong> " . ($row['category']) . "</p>";
                #returns boolean value if reserved or not
                echo "<p><strong>Reserved:</strong> " . ($isReserved ? "Yes" : "No") . "</p>";

                //check if the user already has a reservation
                $username = $_SESSION['Username'];
                $checkReservationSql = "SELECT * FROM reserved WHERE Username = '$username'";#checks all reservation from this user
                $checkResult = $conn->query($checkReservationSql);#execute sql
                
                // Only show the reserve button if the user doesn't already have a reservation
                if ($checkResult->num_rows == 0 && !$isReserved)#checks if the user has 0 books booked
                {
                    #reserve button only if user has 0 books reserved
                    echo "<form method='POST' action='books.php'>";
                    echo "<input type='hidden' name='bookID' value='" . ($row['ISBN']) . "'>";
                    echo "<button type='submit' name='reserveBook'>Reserve</button>";
                    echo "</form>";
                } 
                else 
                {#print if user has reserved a book
                    echo "<p>You can only reserve one book at a time, or this book is already reserved.</p>";
                }
                echo "</div>";
            }
        } else {
            echo "<div class='messages'>No books found</div>";
        }

        // Handle book reservation
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reserveBook'])) 
        {
            $bookID = $_POST['bookID']; // ISBN or book identifier
            $username = $_SESSION['Username']; // Logged-in username

            // Check if the user has already reserved a book
            $checkReservationSql = "SELECT * FROM reserved WHERE Username = '$username'";
            $checkResult = $conn->query($checkReservationSql); // Execute the SQL query

            if ($checkResult->num_rows <= 0) {
                //proceed with reservation
                $reserveSql = "INSERT INTO reserved (Username, ISBN, reservedDate) VALUES ('$username', '$bookID', CURDATE())";
                $result = $conn->query($reserveSql);//execute sql

                //mark the book as reserved in the books table
                $updateBookSql = "UPDATE books SET reserved = 'Y' WHERE ISBN = '$bookID'";
                $result = $conn->query($updateBookSql);//execute sql

            }
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
