<?php

include 'db_connection.php';
$error_message = "";

if($_SERVER["REQUEST_METHOD"]== "POST"){
   $username = $_POST['username'];
   $password = $_POST['password'];
   
   $login_user = $conn->prepare("SELECT * FROM userAuth WHERE username = ? AND password = ?");
   $login_user->bind_param("ss", $username, $password);
   $login_user->execute();
   $result = $login_user->get_result();

   if($result->num_rows > 0){
     session_start();
     $_SESSION['bloguser'] = $username;
     header("location: index.php");
     exit();
   }
   else{
      $error_message = "Invalid username or password";
   }

   $login_user->close();
 
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        label {
            display: block;
            font-size: 14px;
            margin-bottom: 8px;
            color: #666;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            width: 100%;
            font-size: 16px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        a {
            text-decoration: none;
            color: #007bff;
            display: inline-block;
            margin-top: 20px;
        }

        a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <div class="login-container">
        <h2>Login</h2>
        <form action="login.php" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <?php if($error_message): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <input type="submit" value="Login">
        </form>
        <a href="register.php">New to this? Register now</a>
    </div>

</body>

</html>
