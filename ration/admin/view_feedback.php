<?php
session_start();
include("../db.php");

/* ---------- SESSION PROTECTION ---------- */
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

/* ---------- FETCH FEEDBACK ---------- */
$search = '';
if(isset($_GET['search'])){
    $search = trim($_GET['search']);
    $stmt = $conn->prepare("SELECT * FROM feedback WHERE user_name LIKE ? ORDER BY id DESC");
    $like = "%".$search."%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $res = $stmt->get_result();
} else {
    $res = $conn->query("SELECT * FROM feedback ORDER BY id DESC");
}

$admin = $_SESSION['admin'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Feedback | Admin Panel</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
/* ---------- GLOBAL ---------- */
body {
    font-family: 'Poppins', sans-serif;
    background: #f0f2f5;
    margin: 0;
    padding: 0;
}
/* ---------- HEADER ---------- */
.header {
    background: #0056b3;
    color: white;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
.header a.logout {
    background: #dc3545;
    padding: 8px 15px;
    border-radius: 5px;
    color: white;
    text-decoration: none;
    transition: 0.3s;
}
.header a.logout:hover {
    background: #b02a37;
}
.header h2 {
    margin: 0;
    font-size: 20px;
}

/* ---------- CONTAINER ---------- */
.container {
    max-width: 950px;
    margin: 80px auto 30px;
    background: #fff;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

/* ---------- TITLE ---------- */
.container h3 {
    text-align: center;
    margin-bottom: 25px;
    color: #007bff;
}

/* ---------- SEARCH BAR ---------- */
.search-bar {
    margin-bottom: 20px;
    text-align: center;
}
.search-bar input[type="text"] {
    padding: 8px 12px;
    width: 50%;
    border-radius: 5px;
    border: 1px solid #ccc;
    transition: 0.3s;
}
.search-bar input[type="text"]:focus {
    outline: none;
    border-color: #007bff;
}
.search-bar button {
    padding: 8px 15px;
    background: #007bff;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: 0.3s;
}
.search-bar button:hover {
    background: #0056b3;
}

/* ---------- TABLE ---------- */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}
th, td {
    padding: 12px 10px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}
th {
    background: #007bff;
    color: white;
}
tr:hover {
    background: #f1faff;
}

/* ---------- STAR RATING ---------- */
.star-rating {
    color: gold;
    font-size: 18px;
}

/* ---------- BUTTONS ---------- */
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

/* ---------- RESPONSIVE ---------- */
@media(max-width: 768px){
    .container {
        width: 90%;
        padding: 25px;
    }
    .search-bar input[type="text"] {
        width: 70%;
    }
}
</style>
</head>
<body>

<!-- HEADER -->
<div class="header">
    <h2>Admin Panel | Welcome, <?php echo htmlspecialchars($admin); ?></h2>

</div>

<div class="container">
    <h3>User Feedback</h3>

    <!-- SEARCH BAR -->
    <div class="search-bar">
        <form method="get">
            <input type="text" name="search" placeholder="Search by username" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- FEEDBACK TABLE -->
    <?php if($res->num_rows > 0) { ?>
    <table>
        <tr>
            <th>User Name</th>
            <th>Message</th>
            <th>Rating</th>
        </tr>
        <?php while($row = $res->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['user_name']); ?></td>
            <td><?php echo htmlspecialchars($row['message']); ?></td>
            <td class="star-rating">
                <?php 
                $stars = intval($row['rating']);
                for($i=0; $i<$stars; $i++){
                    echo "★";
                }
                for($i=$stars; $i<5; $i++){
                    echo "☆";
                }
                ?>
            </td>
        </tr>
        <?php } ?>
    </table>
    <?php } else { ?>
        <p style="text-align:center; color:#555;">No feedback found.</p>
    <?php } ?>

    <div class="actions">
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</div>

</body>
</html>
