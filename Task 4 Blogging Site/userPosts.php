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

// Ensure the user is logged in
if (!isset($_SESSION['bloguser'])) {
    header('Location: login.php');
    exit();
}

$bloguser = $_SESSION['bloguser']; // The logged-in user's username
$editMode = false;
$editPostId = "";
$editPostTitle = "";
$editPostContent = "";

// Handle new post submission or update post
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['submitPost'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $author = $_SESSION['bloguser'];

    if (isset($_POST['post_id']) && $_POST['post_id'] !== '') {
        // Editing an existing post
        $post_id = $_POST['post_id'];
        $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ? AND author = ?");
        $stmt->bind_param("ssis", $title, $content, $post_id, $author);
        $stmt->execute();
        $stmt->close();
    } else {
        // Adding a new post
        $stmt = $conn->prepare("INSERT INTO posts (title, content, author) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $content, $author);
        $stmt->execute();
        $stmt->close();
    }

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Handle post deletion
if (isset($_POST['deletePost'])) {
    $post_id = $_POST['post_id'];

    // Ensure only the author of the post can delete it
    $deleteQuery = "DELETE FROM posts WHERE id = ? AND author = ?";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->bind_param('is', $post_id, $bloguser);

    if ($deleteStmt->execute()) {
        echo "<p>Post deleted successfully!</p>";
        header('Location: ' . $_SERVER['PHP_SELF']); // Refresh the page
        exit();
    } else {
        echo "<p>Error deleting post: " . $conn->error . "</p>";
    }
}

// Handle editing post
if (isset($_POST['editPost'])) {
    $editMode = true;
    $editPostId = $_POST['post_id'];

    $query = "SELECT id, title, content FROM posts WHERE id = ? AND author = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('is', $editPostId, $bloguser);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $post = $result->fetch_assoc();
        $editPostTitle = $post['title'];
        $editPostContent = $post['content'];
    }
}

// Fetch posts only from the logged-in user
$query = "SELECT p.id, p.title, p.content, p.author, p.created_at,
            (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) AS comment_count
          FROM posts p
          WHERE p.author = ?
          ORDER BY p.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param('s', $bloguser);
$stmt->execute();
$result = $stmt->get_result();


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
    <title>BlogVerse</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: lightcyan;
            color: black;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #2c3e50;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .top-section {
            display: flex;
            justify-content: space-between;
        }
        .post {
            margin-bottom: 20px;
            background-color: lightgrey;
            padding: 15px;
            border-bottom: 1px solid #eaeaea;
        }
        .post h2 {
            color: #2980b9;
        }
        .post p {
            line-height: 1.6;
        }
        .post section {
            margin-bottom: 10px;
        }
        .button-container {
            display: flex;
            flex-direction: row;
            gap: 10px;
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
        a {
            text-decoration: none;
        }
        .add-post {
            display: block;
            margin-bottom: 20px;
        }
        .logout {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }
        .logout-btn {
            background-color: red;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .logout-btn:hover {
            background-color: darkred;
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
            display: block;
            margin: auto;
            background-color: #2980b9;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #1a6699;
        }
    </style>
</head>

<body>
    <div class="container">
    <form action="" method="POST" class="logout">
            <button type="submit" value="Logout" name="Logout" class="logout-btn">Logout</button>
        </form>
        <h1>BlogVerse</h1>

        <!-- Add/Edit Post Form -->
        <h2><?php echo $editMode ? "Edit Post" : "Add a New Blog Post"; ?></h2>
        <form action="" method="post" onsubmit="<?php echo $editMode ? "return confirm('Are you sure you want to save this?');" : "true"; ?>">
            <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($editPostId); ?>">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($editPostTitle); ?>" required>

            <label for="content">Content:</label>
            <textarea id="content" name="content" rows="5" required><?php echo htmlspecialchars($editPostContent); ?></textarea>

            <input type="submit" value="<?php echo $editMode ? "Save" : "Submit"; ?>" name="submitPost">
        </form>

        <hr>

        <!-- Display User's Posts -->
        <?php if ($result->num_rows > 0):  ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="post">
                    <section class="top-section">
                    <h2><?php echo htmlspecialchars($row['title']); ?></h2>

                    <section>
                    <form action="" method="POST" style="display:inline;">
                        <input type="hidden" name="post_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="editPost">Edit</button>
                    </form>

                    <form action="" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this post?');">
                        <input type="hidden" name="post_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="deletePost">Delete</button>
                    </form>
                    </section>
                    </section>
                    <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                    <p>By: <span><?php echo htmlspecialchars($row['author']); ?></span></p>
                    <p><?php echo htmlspecialchars($row['created_at']); ?></p>

                    <section class="button-container">
                        <a href="comments.php?post_id=<?php echo $row['id']?>"><button><span>(<?php echo $row['comment_count']?>)</span> View Comments</button></a>
                    </section>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No posts available.</p>
        <?php endif; ?>
    </div>
</body>

</html>
