<?php
session_start();
include("../db.php");

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

$admin = $_SESSION['admin'];

/* ---------- DASHBOARD COUNTS ---------- */
$userCount       = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$staffCount      = $conn->query("SELECT COUNT(*) AS total FROM staff")->fetch_assoc()['total'];
$tokenCount      = $conn->query("SELECT COUNT(*) AS total FROM tokens")->fetch_assoc()['total'];
$completedCount  = $conn->query("SELECT COUNT(*) AS total FROM tokens WHERE status='Completed'")->fetch_assoc()['total'];
$feedbackCount   = $conn->query("SELECT COUNT(*) AS total FROM feedback")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
/* ---------- GLOBAL ---------- */
body {
    margin:0;
    font-family: 'Poppins', sans-serif;
    background: #f0f2f5;
}
i {
    margin-right:6px;
}

/* ---------- LAYOUT ---------- */
.wrapper {
    display:flex;
    min-height:100vh;
}

/* ---------- SIDEBAR ---------- */
.sidebar {
    width:250px;
    background:#343a40;
    color:white;
    padding-top:20px;
}
.sidebar h2 {
    text-align:center;
    margin-bottom:20px;
    font-size:22px;
    letter-spacing:1px;
}
.sidebar a {
    display:block;
    padding:14px 20px;
    color:white;
    text-decoration:none;
    font-weight:500;
    transition:0.3s;
    border-left:4px solid transparent;
}
.sidebar a:hover {
    background:#007bff;
    border-left:4px solid #ffc107;
}
.sidebar a.logout {
    background:#dc3545;
    margin:20px;
    border-radius:6px;
    text-align:center;
    transition:0.3s;
}
.sidebar a.logout:hover {
    background:#b02a37;
}

/* ---------- MAIN CONTENT ---------- */
.main {
    flex:1;
    padding:20px;
    background:#f4f6f9;
}

/* ---------- HEADER ---------- */
.header {
    background:#007bff;
    color:white;
    padding:12px 20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    border-radius:6px;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
}

/* ---------- CARDS ---------- */
.cards {
    margin-top:25px;
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));
    gap:20px;
}

.card {
    background:white;
    padding:25px;
    text-align:center;
    border-radius:12px;
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
    transition:0.3s;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow:0 10px 25px rgba(0,0,0,0.15);
}
.card h3 {
    font-size:32px;
    color:#007bff;
    margin-bottom:8px;
}
.card p {
    font-weight:600;
    color:#333;
}

/* ---------- FOOTER ---------- */
.footer {
    margin-top:40px;
    text-align:center;
    font-size:14px;
    color:#555;
}

/* ---------- RESPONSIVE ---------- */
@media(max-width:768px){
    .wrapper { flex-direction:column; }
    .sidebar { width:100%; display:flex; justify-content:space-around; padding:10px 0; }
    .sidebar h2 { display:none; }
    .main { padding:15px; }
    .cards { grid-template-columns:repeat(auto-fit, minmax(150px, 1fr)); gap:15px; }
}
</style>
</head>

<body>
<div class="wrapper">

    <!-- ===== SIDEBAR ===== -->
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
        <a href="view_users.php"><i class="fa-solid fa-users"></i> View Users</a>
        <a href="add_staff.php"><i class="fa-solid fa-user-plus"></i> Add Officer</a>
        <a href="monitor_token.php"><i class="fa-solid fa-chart-line"></i> Monitor Tokens </a>
        <a href="view_feedback.php"><i class="fa-solid fa-comments"></i> View Feedback</a>
        <a href="../logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>

    <!-- ===== MAIN CONTENT ===== -->
    <div class="main">

        <!-- HEADER -->
        <div class="header">
            <div>strong><i class="fa-solid fa-chart-line"></i> Dashboard Overview</strong></div>
            <div>Welcome, <?php echo htmlspecialchars($admin); ?></div>
        </div>

        <!-- DASHBOARD CARDS -->
        <div class="cards">
            <div class="card">
                <h3><i class="fa-solid fa-users"></i> <?php echo $userCount; ?></h3>
                <p>Total Users</p>
            </div>
            <div class="card">
                <h3><i class="fa-solid fa-user-tie"></i> <?php echo $staffCount; ?></h3>
                <p>Total Officers</p>
            </div>
            <div class="card">
                <h3><i class="fa-solid fa-ticket"></i>  <?php echo $tokenCount; ?></h3>
                <p>Total Tokens</p>
            </div>
            <div class="card">
                <h3><i class="fa-solid fa-check-circle"></i> <?php echo $completedCount; ?></h3>
                <p>Completed Tokens</p>
            </div>
            <div class="card">
               <h3><i class="fa-solid fa-comment-dots"></i> <?php echo $feedbackCount; ?></h3>
                <p>User Feedback</p>
            </div>
        </div>

        <div class="footer">
            © <?php echo date("Y"); ?> Ration Card Queue Management System | Admin Panel
        </div>

    </div>
</div>
</body>
</html>
