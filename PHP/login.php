<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!--Link the main css page to login page-->
    <link rel="stylesheet" href="../CSS/main.css">
</head>
<body>
    <header>
        <h1>Login</h1>
    </header>
    <div class = "login">
        <h2>Login Form</h2>
        <!--link to return to main page-->
        <a href="../index.php" class="button">Return to home page</a>
        <!--form goes to this page and sends it to the php part of the file using POST-->
        <form action="login.php" method="POST">
            <label for="username">Username:</label><!--username box-->
            <input type="text" id="username" name="username" required>
            <br><br>
        
            <label for="password">Password:</label><!--password box-->
            <input type="password" id="password" name="password" required>
            <br><br>
        
            <button type="submit">Login</button><!--submit button-->
        </form>
        
        
    </div>
    <?php
        session_start();#start session so make sure user doesnt go to books page without login/register
        if ($_SERVER["REQUEST_METHOD"] == "POST") //checks if person sends data through POST method
        {
            $servername = "localhost";//start of connection of the databse
            $username = "root";
            $password = "";
            $dbname = "librarydb";
            //create connection
            $conn = new mysqli($servername,$username, $password, $dbname);

            //check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }//prints if database doesnt connect

            //variables when user inputs data
            $username = $_POST['username'];
            $password = $_POST['password'];

            #selects username and password from the databse
            $sql = "SELECT * FROM users WHERE userName = '$username' AND passwords = '$password'";
            $result = $conn->query($sql);#executes sql
            
            if ($result->num_rows > 0)#iterates through the rows of data in the databse
            {
                //if match found
                echo "<div class='login-success'>Login successful!</div>";
                $_SESSION['Username'] = $username;#global variable,inputs the username into the list
                header("Location: books.php");#redirects to books page
            } else {
                //if no match found 
                echo "<div class='login-failure'>Invalid username or password.</div>";
            }
            $conn->close();//close connection
        }
    ?>
    <footer>
        <p>2024 Web Development Ca</p>
    </footer>
</body>
</html>