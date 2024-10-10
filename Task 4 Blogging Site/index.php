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

// Get all blogs
$query = "SELECT p.id, p.title, p.content, p.author, p.created_at,
            (SELECT COUNT(*) FROM comments c where c.post_id = p.id) AS comment_count
        FROM posts p
        ORDER BY p.created_at DESC
        ";

$result = $conn->query($query);


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

        .top-section {
            display: flex;
            justify-content: space-between;
        }

        .logout {
            background-color: red;
        }

        .logout:hover {
            background-color: darkred;
        }

        .login{
            background-color: green;
        }
        .login:hover{
            background-color: darkgreen;
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
        .user-posts {
            display: block;
            text-align: right;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
    <section class="top-section">
        <section>
            <h1><?php echo htmlspecialchars(isset($_SESSION['bloguser'])? $_SESSION['bloguser']: 'Not Login yet'); ?></h1>
        </section>
        <form action="" method="POST">
            <button type="submit" value="Logout" name="Logout" class="<?php echo htmlspecialchars(isset($_SESSION['bloguser'])? "logout": "login"); ?>"><?php echo htmlspecialchars(isset($_SESSION['bloguser'])? "Logout": "Login Now"); ?></button>
        </form>
        </section>
        <h1>BlogVerse</h1>
        <div class="user-posts">
            <a href="userPosts.php"><button>Your Posts</button></a>
        </div>
        
        <?php if ($result->num_rows > 0):  ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="post">
                    <section>
                        <h2><?php echo htmlspecialchars($row['title']); ?></h2>
                        <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                        <p>By: <span><?php echo htmlspecialchars($row['author']); ?></span></p>
                        <p><?php echo htmlspecialchars($row['created_at']); ?></p>
                    </section>
                    <section class="button-container">
                        <a href="comments.php?post_id=<?php echo $row['id']?>">
                            <button><span>(<?php echo $row['comment_count']?>)</span> View Comments</button>
                        </a>
                    </section>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No posts available.</p>
        <?php endif; ?>
    </div>
</body>

</html>
