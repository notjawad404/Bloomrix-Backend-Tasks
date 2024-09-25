<?php
session_start();

$contacts = isset($_SESSION['contacts']) ? $_SESSION['contacts'] : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_contact'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $index = isset($_POST['index']) ? $_POST['index'] : null;

    if ($name && $email && $phone) {
        if ($index !== null) {
            $contacts[$index] = [
                'name' => htmlspecialchars($name),
                'email' => htmlspecialchars($email),
                'phone' => htmlspecialchars($phone)
            ];
        } else {
            $contacts[] = [
                'name' => htmlspecialchars($name),
                'email' => htmlspecialchars($email),
                'phone' => htmlspecialchars($phone)
            ];
        }
        $_SESSION['contacts'] = $contacts;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_contact'])) {
    $index = $_POST['index'];
    if (isset($contacts[$index])) {
        unset($contacts[$index]);
        $_SESSION['contacts'] = array_values($contacts);
    }
}

$editContact = ['name' => '', 'email' => '', 'phone' => ''];
$editIndex = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_contact'])) {
    $editIndex = $_POST['index'];
    if (isset($contacts[$editIndex])) {
        $editContact = $contacts[$editIndex];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Manager</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function confirmDelete() {
            return confirm('Are you sure you want to delete this contact?');
        }

        function confirmUpdate() {
            return confirm('Are you sure you want to update this contact?');
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Contact Manager</h1>

        <form method="post" onsubmit="return <?php echo $editIndex !== null ? 'confirmUpdate()' : 'true'; ?>;">
            <input type="hidden" name="index" value="<?php echo $editIndex !== null ? $editIndex : ''; ?>">
            <input type="text" name="name" value="<?php echo $editContact['name']; ?>" placeholder="Name" required>
            <input type="email" name="email" value="<?php echo $editContact['email']; ?>" placeholder="Email" required>
            <input type="number" name="phone" value="<?php echo $editContact['phone']; ?>" placeholder="Phone Number" required>
            <button type="submit" name="save_contact"><?php echo $editIndex !== null ? 'Update' : 'Add'; ?> Contact</button>
        </form>

        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($contacts as $index => $contact): ?>
                <tr>
                    <td><?php echo $contact['name']; ?></td>
                    <td><?php echo $contact['email']; ?></td>
                    <td><?php echo $contact['phone']; ?></td>
                    <td class="actions">
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="index" value="<?php echo $index; ?>">
                            <button type="submit" name="edit_contact" class="edit-btn">Edit</button>
                        </form>

                        <form method="post" style="display:inline;" onsubmit="return confirmDelete();">
                            <input type="hidden" name="index" value="<?php echo $index; ?>">
                            <button type="submit" name="remove_contact" class="remove-btn">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
