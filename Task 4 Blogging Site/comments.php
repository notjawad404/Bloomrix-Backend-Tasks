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

$post_id = isset($_GET['post_id']) ? (int)$_GET['post_id'] : 0;

$post_query = "SELECT id, title, content, author, created_at FROM posts WHERE id = $post_id";
$post_result = $conn->query($post_query);
if ($post_result->num_rows > 0) {
    $post = $post_result->fetch_assoc();
} else {
    echo "Post not found!";
    exit;
}

$comments_query = "SELECT id, comment_text, commenter, created_at FROM comments WHERE post_id = $post_id ORDER BY created_at DESC";
$comments_result = $conn->query($comments_query);

// Handle comment submission
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['bloguser'])) {
    $comment_text = $_POST['comment_text'];
    $commenter = $_SESSION['bloguser'];

    // Insert comment using prepared statement
    $stmt = $conn->prepare("INSERT INTO comments (post_id, comment_text, commenter) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $post_id, $comment_text, $commenter);
    $stmt->execute();
    $stmt->close();

    // Refresh the comments after adding a new one
    header("Location: " . $_SERVER['PHP_SELF'] . "?post_id=" . $post_id);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BlogVerse - <?php echo htmlspecialchars($post['title']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            color: #2980b9;
            text-align: center;
        }
        .post {
            margin-bottom: 20px;
        }
        .post p {
            line-height: 1.6;
        }
        .post-meta {
            font-size: 0.9em;
            color: #777;
        }
        /* Add Comment Form */
        form {
            margin-top: 30px;
            padding: 20px;
            background-color: #ecf0f1;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
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
            width: 20%;
        }
        input[type="submit"]:hover,
        button:hover {
            background-color: #1a6699;
        }
        /* Comments Section */
        .comments-section {
            margin-top: 40px;
        }
        .comment {
            padding: 10px;
            border-bottom: 1px solid #eaeaea;
            margin-bottom: 10px;
            background-color: #f9f9f9;
            border-radius: 4px;
        }
        .comment p {
            margin: 0;
        }
        .post-meta {
            font-size: 0.85em;
            color: #777;
            margin-top: 5px;
        }
        /* Back Button */
        .back-button {
            background-color: firebrick;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            width: 20%;
            margin-bottom: 20px;
            transition: background-color 0.3s ease;
        }
        .back-button:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php">
            <button class="back-button">Back</button>
        </a>
        <h1><?php echo htmlspecialchars($post['title']); ?></h1>
        <div class="post">
            <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
            <p class="post-meta">Posted by <?php echo htmlspecialchars($post['author']); ?> on <?php echo htmlspecialchars($post['created_at']); ?></p>
        </div>

        <h2>Add a Comment</h2>
        <form action="" method="post">
            <label for="comment_text">Your Comment:</label>
            <input type="text" id="comment_text" name="comment_text" required>
            <input type="submit" value="Post Comment">
        </form>

        <h2>Comments</h2>
        <div class="comments-section">
            <?php if($comments_result->num_rows > 0): ?>
                <?php while($comment = $comments_result->fetch_assoc()): ?>
                    <div class="comment">
                        <p><?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?></p>
                        <p class="post-meta"><?php echo htmlspecialchars($comment['commenter']); ?> on <?php echo htmlspecialchars($comment['created_at']); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No comments yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
