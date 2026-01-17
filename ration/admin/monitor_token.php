<?php
session_start();
include("../db.php");

/* ---------- SESSION PROTECTION ---------- */
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

/* ---------- FILTER ---------- */
$statusFilter = $_GET['status'] ?? "All";

$sql = "SELECT token_no, ration_id, name, date, time, status FROM tokens";

if ($statusFilter !== "All") {
    $sql .= " WHERE status = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $statusFilter);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql .= " ORDER BY date DESC, time DESC";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Monitor Tokens | Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
body{
    font-family:Poppins,sans-serif;
    background: #f0f2f5;
    min-height:100vh;
    color:#fff;
    margin:0;
}
.container{
    max-width:1200px;
    margin:40px auto;
    background:#fff;
    backdrop-filter:blur(16px);
    padding:35px;
    border-radius:22px;
    box-shadow:0 20px 45px rgba(0,0,0,.35);
}
h2{
    text-align:center;
    margin-bottom:25px;
    font-weight:600;
}
h2{color:#007bff}

/* ---------- FILTER ---------- */
.filter-box{
    display:flex;
    justify-content:center;
    align-items:center;
    gap:15px;
    margin-bottom:25px;
}
select{
    padding:12px 18px;
    border-radius:25px;
    border:green;
    font-size:14px;
}
button{
    padding:12px 22px;
    border-radius:25px;
    border:none;
    cursor:pointer;
    font-size:14px;
    background:linear-gradient(90deg,#00c6ff,#007bff);
    color:#fff;
    transition:.3s;
}
button:hover{
    transform:translateY(-2px);
    opacity:.95;
}

/* ---------- TABLE ---------- */
.table-wrapper{
    overflow-x:auto;
}
table{
    width:100%;
    border-collapse:collapse;
    background:#fff;
    color:#333;
    border-radius:14px;
    overflow:hidden;
}
th,td{
    padding:14px;
    text-align:center;
}
th{
    background:#007bff;
    color:#fff;
    font-weight:500;
}
tr:nth-child(even){background:#f1f5f9}
tr:hover{background:#e9f4ff}

/* ---------- STATUS COLORS ---------- */
.waiting{color:#ff9800;font-weight:600}
.serving{color:#28a745;font-weight:600}
.completed{color:#6c757d;font-weight:600}

/* ---------- BACK BUTTON ---------- */
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

<h2 ><i class="fa-solid fa-chart-line"></i> Token Monitoring Panel</h2>

<!-- FILTER -->
<form method="get" class="filter-box">
    <select name="status">
        <option value="All" <?= $statusFilter=="All"?"selected":"" ?>>All Tokens</option>
        <option value="Waiting" <?= $statusFilter=="Waiting"?"selected":"" ?>>Waiting</option>
        <option value="Serving" <?= $statusFilter=="Serving"?"selected":"" ?>>Serving</option>
        <option value="Completed" <?= $statusFilter=="Completed"?"selected":"" ?>>Completed</option>
    </select>
    <button type="submit">
        <i class="fa-solid fa-filter"></i> Apply Filter
    </button>
</form>

<!-- TABLE -->
<div class="table-wrapper">
<?php if ($result && $result->num_rows > 0): ?>
<table>
<tr>
    <th><i class="fa-solid fa-ticket"></i> Token</th>
    <th><i class="fa-solid fa-id-card"></i> Ration ID</th>
    <th><i class="fa-solid fa-user"></i> Name</th>
    <th><i class="fa-solid fa-calendar"></i> Date</th>
    <th><i class="fa-solid fa-clock"></i> Time</th>
    <th><i class="fa-solid fa-circle-info"></i> Status</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['token_no'] ?></td>
    <td><?= htmlspecialchars($row['ration_id']) ?></td>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td><?= $row['date'] ?></td>
    <td><?= $row['time'] ?></td>
    <td class="<?= strtolower($row['status']) ?>">
        <?= $row['status'] ?>
    </td>
</tr>
<?php endwhile; ?>
</table>
<?php else: ?>
<p style="text-align:center;margin-top:20px;">No tokens found.</p>
<?php endif; ?>
</div>

<!-- BACK -->
<div class="back-wrap">
    <div class="actions">
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</div>

</div>

</body>
</html>
