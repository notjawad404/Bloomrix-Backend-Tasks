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
        h1 {
            color: #2980b9;
            text-align: center;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
        .add-comment {
            text-align: right;
            margin-bottom: 20px;
        }
        button {
            background-color: #2980b9;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #1a6699;
        }
        .comments-section {
            margin-top: 40px;
        }
        .comment {
            padding: 10px;
            border-bottom: 1px solid #eaeaea;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($post['title']); ?></h1>
        <div class="post">
            <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
            <p class="post-meta">Posted by <?php echo htmlspecialchars($post['author']); ?> on <?php echo htmlspecialchars($post['created_at']); ?></p>
        </div>
        
        <div class="add-comment">
            <a href="addComment.php?post_id=<?php echo $post_id; ?>">
                <button>Add Comment</button>
            </a>
        </div>

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
