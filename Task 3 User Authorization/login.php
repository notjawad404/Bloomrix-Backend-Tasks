<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user";

$error_message = '';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if($_SERVER["REQUEST_METHOD"]== "POST"){
   $username = $_POST['username'];
   $password = $_POST['password'];
   
   $login_user = $conn->prepare("SELECT * FROM userAuth WHERE username = ? AND password = ?");
   $login_user->bind_param("ss", $username, $password);
   $login_user->execute();
   $result = $login_user->get_result();

   if($result->num_rows > 0){
     session_start();
     $_SESSION['username'] = $username;
     header("location: index.php");
     exit();
   }
   else{
      $error_message = "Invalid username or password";
   }
}


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>

<body>

    <div>
        <h2>Login</h2>
        <form action="login.php" method="post">
            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username" required><br>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br>
            <span><?php echo $error_message;?></span><br>
            <input type="submit" value="Login">
        </form>
        <a href="register.php">New to this? Register now</a>

    </div>

</body>

</html>