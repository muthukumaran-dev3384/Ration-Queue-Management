<?php
session_start();
include("../db.php");

/* ---------- SESSION PROTECTION ---------- */
if (!isset($_SESSION['staff'], $_SESSION['staff_id'])) {
    header("Location: login.php");
    exit();
}

$staff     = $_SESSION['staff'];
$staff_id  = $_SESSION['staff_id'];

/* ---------- FETCH DASHBOARD COUNTS (STAFF-WISE) ---------- */

$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total FROM tokens WHERE staff_id=?"
);
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$totalTokens = $stmt->get_result()->fetch_assoc()['total'];

$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total FROM tokens WHERE status='Pending' AND staff_id=?"
);
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$pendingTokens = $stmt->get_result()->fetch_assoc()['total'];

$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total FROM tokens WHERE status='Waiting List' AND staff_id=?"
);
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$waitingTokens = $stmt->get_result()->fetch_assoc()['total'];

$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total FROM tokens WHERE status='Completed' AND staff_id=?"
);
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$completedTokens = $stmt->get_result()->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Staff Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="../assets/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
.wrapper { display:flex; min-height:100vh; }
.sidebar {
    width:220px;
    background:#343a40;
    color:white;
    padding-top:20px;
}
.sidebar a{
    display:block;
    padding:12px 20px;
    color:white;
    text-decoration:none;
}
.sidebar a:hover{background:#007bff;}
.sidebar .logout{
    background:#dc3545;
    margin:15px 20px;
    border-radius:5px;
    text-align:center;
}

.main{
    flex:1;
    padding:20px;
    background:#f4f6f9;
}

.cards{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
    gap:15px;
    margin-top:20px;
}

.card{
    background:#fff;
    padding:20px;
    border-radius:10px;
    box-shadow:0 4px 10px rgba(0,0,0,.1);
    text-align:center;
}

.card h3{
    margin:0;
    font-size:28px;
    color:#007bff;
}

.card p{
    margin-top:6px;
    font-weight:600;
}
</style>
</head>

<body>

<div class="wrapper">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <a href="dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
        <a href="create_token.php"><i class="fa-solid fa-file-circle-plus"></i> Create Token</a>
        <a href="update_token.php"><i class="fa-solid fa-arrows-rotate"></i> Update Token</a>
        <a href="../logout.php" class="logout">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>
    </div>

    <!-- MAIN -->
    <div class="main">
        <h2>
            Welcome <i class="fa-solid fa-user"></i>
            <?= htmlspecialchars($staff) ?>
        </h2>

        <div class="cards">
            <div class="card">
                <h3><?= $totalTokens ?></h3>
                <p>Total Tokens</p>
            </div>
            <div class="card">
                <h3><?= $pendingTokens ?></h3>
                <p>Pending</p>
            </div>
            <div class="card">
                <h3><?= $waitingTokens ?></h3>
                <p>Waiting</p>
            </div>
            <div class="card">
                <h3><?= $completedTokens ?></h3>
                <p>Completed</p>
            </div>
        </div>
    </div>

</div>

</body>
</html>
