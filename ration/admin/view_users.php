<?php
session_start();
include("../db.php");

/* ---------- SESSION PROTECTION ---------- */
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

/* ---------- DELETE USER ---------- */
if (isset($_POST['delete_user'])) {
    $rid = $_POST['ration_id'];

    $del = $conn->prepare("DELETE FROM users WHERE ration_id = ?");
    $del->bind_param("s", $rid);
    $del->execute();

    header("Location: view_users.php");
    exit();
}

/* ---------- SEARCH FEATURE ---------- */
$search = "";
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

/* ---------- FETCH USERS ---------- */
if ($search != "") {
    $stmt = $conn->prepare(
        "SELECT ration_id, name, username 
         FROM users 
         WHERE ration_id LIKE ? OR name LIKE ? OR username LIKE ?
         ORDER BY name"
    );
    $like = "%$search%";
    $stmt->bind_param("sss", $like, $like, $like);
    $stmt->execute();
    $res = $stmt->get_result();
} else {
    $res = $conn->query(
        "SELECT ration_id, name, username FROM users ORDER BY name"
    );
}

$userCount = $res->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Registered Users</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #f0f2f5;
    margin: 0;
    padding: 0;
}
.container {
    max-width: 1000px;
    margin: 50px auto;
    background: #fff;
    padding: 30px 25px;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}
.header {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}
.header h3 { color:#007bff; font-size:24px; }
.header strong { color:#333; }

/* SEARCH */
.search-box {
    display:flex;
    gap:10px;
    margin-bottom:20px;
}
.search-box input {
    flex:1;
    padding:10px 12px;
    border-radius:6px;
    border:1px solid #ccc;
}
.search-box button {
    padding:10px 18px;
    background:#007bff;
    color:white;
    border:none;
    border-radius:6px;
    cursor:pointer;
}

/* TABLE */
table {
    width:100%;
    border-collapse: collapse;
}
th, td {
    padding:12px;
    text-align:center;
    border-bottom:1px solid #ddd;
}
th {
    background:#007bff;
    color:white;
}
tr:hover { background:#f1f9ff; }

/* DELETE BUTTON */
.delete-btn {
    background:#dc3545;
    color:white;
    border:none;
    padding:6px 12px;
    border-radius:6px;
    cursor:pointer;
    font-size:13px;
}
.delete-btn:hover {
    background:#b02a37;
}

/* ACTIONS */
.actions {
    margin-top: 25px;
    text-align: center;
}
.actions a {
    display:inline-block;
    padding:12px 20px;
    background:#007bff;
    color:white;
    text-decoration:none;
    border-radius:8px;
}
</style>
</head>

<body>

<div class="container">

    <div class="header">
        <h3>Registered Users</h3>
        <strong>Total Users: <?php echo $userCount; ?></strong>
    </div>

    <!-- SEARCH -->
    <form class="search-box" method="get">
        <input type="text" name="search" placeholder="Search by Ration ID, Name or Username"
               value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>

    <!-- USERS TABLE -->
    <table>
        <tr>
            <th>Ration ID</th>
            <th>Name</th>
            <th>Username</th>
            <th>Action</th>
        </tr>

        <?php if ($userCount > 0) {
            while ($row = $res->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['ration_id']) ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td>
                    <form method="post" onsubmit="return confirm('Are you sure you want to delete this user?');">
                        <input type="hidden" name="ration_id" value="<?= $row['ration_id'] ?>">
                        <button type="submit" name="delete_user" class="delete-btn">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
        <?php }} else { ?>
            <tr>
                <td colspan="4">No users found</td>
            </tr>
        <?php } ?>
    </table>

    <div class="actions">
        <a href="dashboard.php">Back to Dashboard</a>
    </div>

</div>

</body>
</html>
