<?php
session_start();
include("../db.php");

/* ---------- SESSION CHECK ---------- */
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['user'];

/* ---------- USER DETAILS ---------- */
$stmt = $conn->prepare("SELECT ration_id, name FROM users WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows !== 1) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$user = $res->fetch_assoc();
$ration_id = $user['ration_id'];
$name = $user['name'];

/* ---------- TOKEN HISTORY ---------- */
$tok = $conn->prepare("
    SELECT token_no, date, time, status 
    FROM tokens 
    WHERE ration_id=? 
    ORDER BY date DESC, time DESC
");
$tok->bind_param("s", $ration_id);
$tok->execute();
$tokens = $tok->get_result();
$totalTokens = $tokens->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Dashboard | Ration Queue</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
/* ========== GLOBAL ========== */
*{margin:0;padding:0;box-sizing:border-box;}
body{
    font-family:'Poppins',sans-serif;
    background:linear-gradient(135deg,#e3f2fd,#f8f9fa);
}

/* ========== HEADER ========== */
.header{
    background:linear-gradient(90deg,#0047ab,#00c6ff);
    padding:15px 30px;
    color:#fff;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 5px 15px rgba(0,0,0,0.2);
}
.header h1{font-size:22px;font-weight:600;}
.header a{
    color:#fff;
    text-decoration:none;
    margin-left:15px;
    padding:8px 14px;
    border-radius:20px;
    transition:.3s;
}
.header a:hover{background:rgba(255,255,255,.25);}

i{
    margin-right:6px;
}

/* ========== CONTAINER ========== */
.container{
    max-width:1200px;
    margin:40px auto;
    padding:0 20px;
}

/* ========== WELCOME ========== */
.welcome{
    text-align:center;
    margin-bottom:35px;
}
.welcome h2{color:#0047ab;}
.welcome span{color:#555;font-size:15px;}

/* ========== DASHBOARD CARDS ========== */
.cards{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(230px,1fr));
    gap:20px;
    margin-bottom:35px;
}
.card{
    background:rgba(255,255,255,0.8);
    backdrop-filter:blur(12px);
    border-radius:18px;
    padding:30px 20px;
    box-shadow:0 15px 35px rgba(0,0,0,0.12);
    text-align:center;
}
.card h3{
    font-size:30px;
    color:#007bff;
    margin-bottom:8px;
}
.card p{font-weight:500;color:#555;}

/* ========== ACTION BUTTONS ========== */
.actions{
    text-align:center;
    margin-bottom:30px;
}
.actions button{
    padding:12px 25px;
    margin:5px;
    border:none;
    border-radius:25px;
    background:#007bff;
    color:#fff;
    font-size:14px;
    cursor:pointer;
}
.actions button:hover{background:#0056b3;}

/* ========== TABLE ========== */
.table-wrap{
    overflow-x:auto;
}
table{
    width:100%;
    border-collapse:collapse;
    background:#fff;
    border-radius:15px;
    overflow:hidden;
    box-shadow:0 10px 25px rgba(0,0,0,0.12);
}
th{
    background:#007bff;
    color:#fff;
    padding:12px;
}
td{
    padding:12px;
    text-align:center;
    border-bottom:1px solid #eee;
}
tr:hover{background:#f2f8ff;}

.badge{
    padding:6px 14px;
    border-radius:20px;
    font-size:13px;
    font-weight:600;
}
.pending{background:#fff3cd;color:#856404;}
.waiting{background:#f8d7da;color:#721c24;}
.completed{background:#d4edda;color:#155724;}

.hidden{display:none;}

/* ========== FOOTER ========== */
.footer{
    text-align:center;
    padding:20px;
    font-size:14px;
    color:#666;
    margin-top:40px;
}
</style>
</head>

<body>

<!-- HEADER -->
<div class="header">
    <h1>Ration Queue Dashboard</h1>
    <div>
        <a href="#" onclick="toggleTable()"><i class="fa-solid fa-clock-rotate-left"></i> Token History</a>
        <a href="feedback.php"><i class="fa-solid fa-comment-dots"></i> Feedback</a>
        <a href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>
</div>

<div class="container">

<!-- WELCOME -->
<div class="welcome">
    <h2>Welcome, <?= htmlspecialchars($name) ?></h2>
    <span>Ration ID: <?= htmlspecialchars($ration_id) ?></span>
</div>

<!-- LIVE CARDS -->
<div class="cards">
    <div class="card">
        <h3><i class="fa-solid fa-ticket"></i> <?= $totalTokens ?></h3>
        <p>Total Tokens</p>
    </div>

    <div class="card">
        <h3 id="queuePos"><i class="fa-solid fa-users"></i> --</h3>
        <p>Live Queue Position</p>
    </div>

    <div class="card">
        <h3 id="waitTime"><i class="fa-solid fa-hourglass-half"></i> --</h3>
        <p>Estimated Waiting Time</p>
    </div>
</div>

<!-- ACTIONS -->
<div class="actions">
    <button onclick="toggleTable()"> <i class="fa-solid fa-table"></i> View Token History</button>
    <button onclick="location.reload()"><i class="fa-solid fa-rotate"></i> Refresh</button>
</div>

<!-- TOKEN TABLE -->
<div id="tokenTable" class="table-wrap hidden">
<table>
<tr>
    <th>Token No</th>
    <th>Date</th>
    <th>Time</th>
    <th>Status</th>
</tr>

<?php if($totalTokens>0): ?>
<?php while($row=$tokens->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($row['token_no']) ?></td>
<td><?= htmlspecialchars($row['date']) ?></td>
<td><?= htmlspecialchars($row['time']) ?></td>
<td>
<span class="badge 
<?= $row['status']=='Pending'?'pending':($row['status']=='Waiting List'?'waiting':'completed') ?>">
<?= htmlspecialchars($row['status']) ?>
</span>
</td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr><td colspan="4">No token records found.</td></tr>
<?php endif; ?>
</table>
</div>

</div>

<div class="footer">
© <?= date("Y") ?> Ration Card Queue Management System |
</div>

<script>
function toggleTable(){
    document.getElementById("tokenTable").classList.toggle("hidden");
}

/* LIVE QUEUE AUTO UPDATE */
function updateQueue(){
    fetch("live_queue.php")
    .then(res=>res.json())
    .then(data=>{
        document.getElementById("queuePos").innerText = data.queue_position;
        document.getElementById("waitTime").innerText = data.wait_time;
    });
}

updateQueue();
setInterval(updateQueue, 5000);
</script>

</body>
</html>
