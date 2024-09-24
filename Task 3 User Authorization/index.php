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
</head>
<body>

    <p><?php echo htmlspecialchars($_SESSION['username']); ?></p>   
    
    <form action="" method="POST">
        <input type="submit" value="Logout" name="Logout">
    </form>

</body>
</html>