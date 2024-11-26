<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reigster</title>
    <!--Link the main css page to register page-->
    <link rel="stylesheet" href="../CSS/main.css">
</head>
<body>
    <header>
        <h1>Register</h1>
    </header>
    <div class = "registering">
        <h2>Register Form</h2>
        <!--option to return to front page-->
        <a href="../index.php" class="button">Return to home page</a>
        <!--form goes to this page and sends it to the php part of the file using POST-->
        <form action="register.php" method="POST">
            <label for="username">Username:</label><!--username box-->
            <input type="text" id="username" name="username" required>
            <br><br>
        
            <label for="password">Password:</label><!--password box-->
            <input type="password" id="password" name="password" required minlength="6">
            <br><br>

            <label for="password2">Confirm Password:</label><!--confirm password box-->
            <input type="password" id="password2" name="password2" required minlength="6">
            <br><br>
        
            <label for="firstName">First Name:</label><!--first name box-->
            <input type="text" id="firstname" name="firstname" required>
            <br><br>

            <label for="lastName">Last Name:</label><!--last name box-->
            <input type="text" id="lastname" name="lastname" required>
            <br><br>

            <label for="address">Address:</label><!--address box-->
            <input type="text" id="address" name="address" required>
            <br><br>

            <label for="town">Town:</label><!--town box-->
            <input type="text" id="town" name="town" required>
            <br><br>

            <label for="city">City:</label><!--city box-->
            <input type="text" id="city" name="city" required>
            <br><br>

            <label for="telephone">Telephone:</label><!--telephone no. box-->
            <input type="tel" id="telephone" name="telephone" required>
            <br><br>
        
            <button type="submit">Register</button><!--submut/register box-->
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
            $password2 = $_POST['password2'];
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $address = $_POST['address'];
            $town = $_POST['town'];
            $city = $_POST['city'];
            $telephone = $_POST['telephone'];


            //(Password should be six characters and have a password confirmation function) Used function as was asked in the assignment
            function validatePasswords($password, $password2) {
                return $password === $password2;
            }
            
            #calls function to check if they match
            if (!validatePasswords($password, $password2)) {
                echo "<div class='login-failure'>Passwords do not match.</div>"; 
            }

            #makes sure phone number is 10 chars long and only numbers
            elseif (strlen($telephone) != 10 || !is_numeric($telephone)) {
                echo "<div class='login-failure'>Telephone number must be exactly 10 digits and contain only numbers.</div>";
            }
            else{#if all previous statements false then
                
                //sql be used to check username
                $checkuName = "SELECT * FROM users WHERE userName = '$username'";
                $result = $conn->query($checkuName);//execute sql

                if ($result->num_rows > 0) #if a result comes back with an existing username then error registering
                {
                    echo "<div class='login-failure'>Username is already taken, please choose another.</div>";
                }
            
                else{#if all previous statements false then
                    #sql to insert data into the users table
                    $sql = "INSERT INTO users (userName,passwords,firstName,surName,addresses,town,city,telephone) 
                    VALUES ('$username', '$password', '$firstname','$lastname','$address','$town','$city','$telephone')";

                    if ($conn->query($sql) === TRUE) //checks if sql statement executed
                    {
                        echo "<div class='login-success'>New record created successfully.</div>";
                        $_SESSION['Username'] = $username;#global variable,inputs the username into the list
                        header("Location: books.php");#redirects to books page
                    } else #else something went wrong
                    {
                        echo "<div class='login-failure'>Error: " . $sql . "<br></div<" . $conn->error;
                    }
                }
            }
            $conn->close();#close connection
        }
        
    ?>
    <footer>
        <p>2024 Web Development Ca</p>
    </footer>
</body>
</html>


