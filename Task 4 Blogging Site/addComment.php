<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blogs";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();
if(!isset($_SESSION['bloguser'])){
    header('location: login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $post_id = $_POST['post_id'];
    $comment_text = $_POST['comment_text'];
    // $commenter = $_POST['commenter'];
    $commenter = $_SESSION['bloguser'];

    // Insert comment using prepared statement
    $stmt = $conn->prepare("INSERT INTO comments (post_id, comment_text, commenter) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $post_id, $comment_text, $commenter);
    $stmt->execute();
    $stmt->close();

    // Redirect back to the index page after comment is added
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Comment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        h2 {
            color: #2980b9;
            text-align: center;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"],
        button {
            background-color: #2980b9;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
        }
        input[type="submit"]:hover,
        button:hover {
            background-color: #1a6699;
        }
        .back-button {
            margin-bottom: 20px;
            width: 20%;
            text-align: center;
            background-color: firebrick;
        }
    </style>
</head>
<body>

    <div class="container">
        
            <a href="index.php">
                <button class="back-button">Back</button>
            </a>
        
        
        <h2>Add a Comment</h2>
        <form action="addComment.php" method="post">
            <input type="hidden" name="post_id" value="<?php echo $_GET['post_id']; ?>">

            <label for="commenter">Your Name:</label>
            <input type="text" id="commenter" name="commenter" required>

            <input type="submit" value="Post Comment">
        </form>
    </div>

</body>
</html>
