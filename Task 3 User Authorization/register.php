<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user";

$error_message = '';

$conn = new mysqli($servername, $username, $password, $dbname);

if($conn->connect_error){
    die("Connection failed: ". $conn->connect_error);
}

if($_SERVER['REQUEST_METHOD']== "POST"){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $check_user = $conn->prepare("SELECT * FROM userAuth WHERE username = ?");
    $check_user->bind_param("s", $username);
    $check_user->execute();

    $userResult = $check_user->get_result();

    if($userResult -> num_rows > 0){
        $error_message = "Username already exists";
    }else{
        $add_user = $conn->prepare("INSERT INTO userAuth (username, password) VALUES (?, ?)");
        $add_user->bind_param("ss", $username, $password);

        if($add_user->execute()){
            session_start();
            $_SESSION['username'] = $username;
            header('location: index.php');
            exit();
        }else{
            $error_message = "Failed to add user";
        }

        $add_user->close();
    }
    
    $check_user->close();
}

$conn->close();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <div>
        <h1>Register</h1>

        <form action="register.php" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <br>
            <p><?php echo $error_message; ?></p>
            <input type="submit" value="Register">
        </form>
   <a href="login.php">Already have an account? Login now</a> 

    </div>
</body>
</html>