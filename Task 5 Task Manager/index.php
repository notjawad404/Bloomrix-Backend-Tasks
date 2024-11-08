<?php
include 'db_connection.php';

session_start();

// Ensure the user is logged in
if (!isset($_SESSION['taskmanager'])) {
    header('Location: login.php');
    exit();
}

if(isset($_POST['Logout'])){
    session_unset();
    session_destroy();
    header('location: login.php');
    exit();
}

$user_id = $_SESSION['taskmanager'];
$editMode = false;
$editTaskId = "";
$editTaskTitle = "";
$editTaskDescription = "";
$editTaskStatus = "";
$editTaskDueDate = "";

// Add new task or edit a task
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['submitTask'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $due_date = $_POST['due_date'];

    if (isset($_POST['task_id']) && $_POST['task_id'] !== '') {
        // Editing an existing task
        $task_id = $_POST['task_id'];
        $stmt = $conn->prepare("UPDATE tasks SET title = ?, description = ?, status = ?, due_date = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ssssii", $title, $description, $status, $due_date, $task_id, $user_id);
        $stmt->execute();
        $stmt->close();
    } else {
        // Adding a new task
        $stmt = $conn->prepare("INSERT INTO tasks (title, description, status, due_date, user_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $title, $description, $status, $due_date, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Delete Task
if (isset($_POST['deleteTask'])) {
    $task_id = $_POST['task_id'];

    $deleteQuery = "DELETE FROM tasks WHERE id = ? AND user_id = ?";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->bind_param('ii', $task_id, $user_id);

    if ($deleteStmt->execute()) {
        echo "<p>Task deleted successfully!</p>";
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "<p>Error deleting task: " . $conn->error . "</p>";
    }
}

// Edit task
if (isset($_POST['editTask'])) {
    $editMode = true;
    $editTaskId = $_POST['task_id'];

    $query = "SELECT id, title, description, status, due_date FROM tasks WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $editTaskId, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $task = $result->fetch_assoc();
        $editTaskTitle = $task['title'];
        $editTaskDescription = $task['description'];
        $editTaskStatus = $task['status'];
        $editTaskDueDate = $task['due_date'];
    }
}

// Fetch all tasks for the logged-in user
$query = "SELECT t.id, t.title, t.description, t.status, t.due_date, t.created_at
          FROM tasks t
          WHERE t.user_id = ?
          ORDER BY t.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: lightblue;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .logout-btn {
            background-color: red;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            margin-left: auto;
        }

        .logout-btn:hover {
            background-color: darkred;
        }

        form {
            margin-bottom: 30px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="date"],
        textarea,
        select {
            width: 50%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid black;
            border-radius: 5px;
        }

        select{
            width: 25%;
        }

        input[type="submit"] {
            background-color: green;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
        }

        input[type="submit"]:hover {
            background-color: darkgreen;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid black;
        }

        .task-completed{
            text-decoration: line-through;
            color: gray;
        }

        .task-actions form {
            display: inline-block;
        }

        .task-actions button {
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 10px;
            width: 60px;
        }

        .edit-btn {
            background-color: blue;
        }

        .edit-btn:hover {
            background-color: darkblue;
        }

        .disable-edit{
            opacity: 0.5;
        }

        .disable-edit:hover{
            cursor: not-allowed;
        }

        .delete-btn {
            background-color: red;
        }

        .delete-btn:hover {
            background-color: darkred;
        }
    </style>
</head>

<body>
    <div class="container">
        <form action="" method="POST" class="logout">
            <button type="submit" value="Logout" name="Logout" class="logout-btn">Logout</button>
        </form>
        <h1>Task Management</h1>

        <!-- Add/Edit Task Form -->
        <h2><?php echo $editMode ? "Edit Task" : "Add a New Task"; ?></h2>
        <form action="" method="post" onsubmit="return confirmEdit()">
            <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($editTaskId); ?>" re>
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($editTaskTitle); ?>" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="5" required><?php echo htmlspecialchars($editTaskDescription); ?></textarea>

            <label for="status">Status:</label>
            <select name="status" id="status" required>
                <option value="pending" <?php echo $editTaskStatus == 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="in_progress" <?php echo $editTaskStatus == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                <option value="completed" <?php echo $editTaskStatus == 'completed' ? 'selected' : ''; ?>>Completed</option>
            </select>

            <label for="due_date">Due Date:</label>
            <input type="date" id="due_date" name="due_date" value="<?php echo htmlspecialchars($editTaskDueDate); ?>" required><br>

            <input type="submit" value="<?php echo $editMode ? 'Save' : 'Submit'; ?>" name="submitTask">
        </form>

        <hr>

        <!-- Display user's Tasks -->
        <?php if ($result->num_rows > 0):  ?>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Due Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="<?php echo $row['status'] == 'completed' ? 'task-completed' : ''; ?>">
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($row['description'])); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($row['status'])); ?></td>
                            <td><?php echo htmlspecialchars($row['due_date']); ?></td>
                            <td class="task-actions">
                                <form method="POST" onsubmit="return confirmEdit()">
                                    <input type="hidden" name="task_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="editTask" class="edit-btn <?php echo $row['status'] == 'completed' ? 'disable-edit' : ''; ?>" <?php echo $row['status'] == 'completed' ? 'disabled' : ''; ?>>Edit</button>
                                </form>
                                <form method="POST" onsubmit="return confirmDelete()">
                                    <input type="hidden" name="task_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="deleteTask" class="delete-btn">Delete</button>
                                </form>

                                <script>
                                    function confirmEdit() {
                                        const taskId = document.querySelector('input[name="task_id"]').value;
                                        if (taskId) {
                                            return confirm("Are you sure you want to save the changes to this task?");
                                        }
                                    }

                                    function confirmDelete() {
                                        return confirm('Are you sure you want to delete this task?');
                                    }
                                </script>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No tasks found.</p>
        <?php endif; ?>
    </div>
</body>

</html>