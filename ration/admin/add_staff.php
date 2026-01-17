<?php
session_start();
include("../db.php");

/* ---------- SESSION PROTECTION ---------- */
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$msg = "";
$error = "";
$showForm = false;
$editStaff = null;

/* ---------- SHOW ADD FORM ---------- */
if (isset($_POST['show_form'])) {
    $showForm = true;
}

/* ---------- ADD STAFF ---------- */
if (isset($_POST['add'])) {
    $showForm = true;

    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $passwordRaw = $_POST['password'];

    if ($name === "" || $username === "" || $passwordRaw === "") {
        $error = "All fields are required!";
    } elseif (strlen($passwordRaw) < 4) {
        $error = "Password must be at least 4 characters!";
    } else {

        $check = $conn->prepare("SELECT id FROM staff WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();
        $checkRes = $check->get_result();

        if ($checkRes->num_rows > 0) {
            $error = "Username already exists!";
        } else {
            $password = hash('sha256', $passwordRaw);
            $stmt = $conn->prepare(
                "INSERT INTO staff (name, username, password) VALUES (?, ?, ?)"
            );
            $stmt->bind_param("sss", $name, $username, $password);
            $stmt->execute();
            $msg = "Staff added successfully!";
        }
    }
}

/* ---------- DELETE STAFF ---------- */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM staff WHERE id=$id");
    header("Location: add_staff.php");
    exit();
}

/* ---------- EDIT STAFF FETCH ---------- */
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM staff WHERE id=$id");
    $editStaff = $res->fetch_assoc();
}

/* ---------- UPDATE STAFF ---------- */
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);

    $stmt = $conn->prepare("UPDATE staff SET name=?, username=? WHERE id=?");
    $stmt->bind_param("ssi", $name, $username, $id);
    $stmt->execute();

    $msg = "Staff updated successfully!";
}

/* ---------- FETCH ALL STAFF ---------- */
$allStaff = $conn->query("SELECT id, name, username FROM staff ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Staff Management</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #f0f2f5;
    margin: 0;
    padding: 0;
}
.container{
    max-width:1000px;
    background:#fff;
    padding:30px;
    border-radius:15px;
    margin:auto;
}
h3{color:#007bff}
table{width:100%;border-collapse:collapse;margin-top:20px}
th,td{padding:12px;border-bottom:1px solid #ddd;text-align:center}
th{background:#007bff;color:#fff}
a.btn{
    padding:6px 12px;
    color:#fff;
    border-radius:5px;
    text-decoration:none;
}
.edit{background:#28a745}
.delete{background:#dc3545}
button{
    width:100%;
    padding:12px;
    background:#007bff;
    color:#fff;
    border:none;
    border-radius:8px;
    cursor:pointer;
}
input{width:100%;padding:10px;margin:8px 0}
.success{color:green;text-align:center}
.error{color:red;text-align:center}
.actions {
    margin-top: 25px;
    text-align: center;
}
.actions a {
    display: inline-block;
    padding: 12px 20px;
    background: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 500;
    transition: 0.3s;
}
.actions a:hover {
    background: #0056b3;
}

</style>
</head>

<body>
<div class="container">

<h3>All Staff</h3>
<table>
<tr>
<th>ID</th><th>Name</th><th>Username</th><th>Action</th>
</tr>
<?php while($row=$allStaff->fetch_assoc()){ ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= htmlspecialchars($row['name']) ?></td>
<td><?= htmlspecialchars($row['username']) ?></td>
<td>
<a class="btn edit" href="?edit=<?= $row['id'] ?>">Edit</a>
<a class="btn delete" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this staff?')">Delete</a>
</td>
</tr>
<?php } ?>
</table>

<?php if ($editStaff) { ?>
<h3>Edit Staff</h3>
<form method="post">
<input type="hidden" name="id" value="<?= $editStaff['id'] ?>">
<input type="text" name="name" value="<?= htmlspecialchars($editStaff['name']) ?>" required>
<input type="text" name="username" value="<?= htmlspecialchars($editStaff['username']) ?>" required>
<button name="update">Update Staff</button>
</form>
<?php } ?>

<?php if (!$showForm && !$editStaff) { ?>
<form method="post">
<button name="show_form">➕ Add Staff</button>
</form>
<?php } ?>

<?php if ($showForm) { ?>
<h3>Add Staff</h3>
<form method="post">
<input type="text" name="name" placeholder="Name" required>
<input type="text" name="username" placeholder="Username" required>
<input type="password" name="password" placeholder="Password" required>
<button name="add">Add Staff</button>
</form>
<?php } ?>

<?php if ($msg) echo "<p class='success'>$msg</p>"; ?>
<?php if ($error) echo "<p class='error'>$error</p>"; ?>
<div class="actions">
        <a href="dashboard.php">Back to Dashboard</a>
    </div>

</div>
</body>
</html>
