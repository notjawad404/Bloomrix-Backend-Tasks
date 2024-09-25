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
            $_SESSION['bloguser'] = $username;
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
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f7f7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .register-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
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
            background-color: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            width: 100%;
            font-size: 16px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #218838;
        }

        a {
            display: block;
            text-align: center;
            color: #007bff;
            text-decoration: none;
            margin-top: 20px;
        }

        a:hover {
            text-decoration: underline;
        }

        p {
            color: red;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h1>Register</h1>

        <form action="register.php" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <p><?php echo $error_message; ?></p>

            <input type="submit" value="Register">
        </form>
        <a href="login.php">Already have an account? Login now</a>
    </div>
</body>
</html>
