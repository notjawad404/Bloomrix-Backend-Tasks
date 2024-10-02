<?php
session_start();
if(!isset($_SESSION['username'])){
    header('location: login.php');
    exit();
}

if(isset($_POST['Logout'])){
    session_unset();
    session_destroy();
    header('location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <style>
        body {
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        h1 {
            color: #333;
        }

        p {
            font-size: 20px;
            color: #555;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        input[type="submit"] {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

    </style>
</head>
<body>

    <h1>Welcome</h1>
    <p><?php echo htmlspecialchars($_SESSION['username']); ?></p>   
    
    <form action="" method="POST">
        <input type="submit" value="Logout" name="Logout">
    </form>

</body>
</html>
