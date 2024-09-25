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

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $author = $_POST['author'];

    $stmt = $conn->prepare("INSERT INTO posts (title, content, author) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $content, $author);
    $stmt->execute();
    $stmt->close();

    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Post</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: white;
            color: black;
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
            background: lightgray;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);

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
            border: 1px solid black;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: auto;
            background-color: #2980b9;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 50%;
        }
        input[type="submit"]:hover {
            background-color: #1a6699;
        }

        button{
            background-color: red;
            color: white;
            cursor: pointer;
            padding: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php">
            <button>Back</button>
        </a>
        <h2>Add a New Blog Post</h2>
        <form action="addPost.php" method="post">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>

            <label for="content">Content:</label>
            <textarea id="content" name="content" rows="5" required></textarea>

            <label for="author">Author:</label>
            <input type="text" id="author" name="author" required>

            <input type="submit" value="Submit">
        </form>
    </div>
</body>
</html>
