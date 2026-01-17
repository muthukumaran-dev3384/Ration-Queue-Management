<?php
session_start();
include("../db.php");

/* ---------- SESSION PROTECTION ---------- */
if (!isset($_SESSION['staff'], $_SESSION['staff_id'])) {
    header("Location: login.php");
    exit();
}

$staffId = $_SESSION['staff_id'];
$msg = "";
$error = "";

/* ---------- CALL NEXT TOKEN ---------- */
if (isset($_POST['call_next'])) {

    $stmt = $conn->prepare(
        "UPDATE tokens SET status='Completed'
         WHERE status='Serving' AND staff_id=?"
    );
    $stmt->bind_param("i", $staffId);
    $stmt->execute();

    $stmt = $conn->prepare(
        "SELECT token_no FROM tokens
         WHERE status='Waiting' AND staff_id=?
         ORDER BY date, time LIMIT 1"
    );
    $stmt->bind_param("i", $staffId);
    $stmt->execute();
    $next = $stmt->get_result();

    if ($next->num_rows > 0) {
        $tokenNo = $next->fetch_assoc()['token_no'];

        $stmt = $conn->prepare(
            "UPDATE tokens SET status='Serving'
             WHERE token_no=? AND staff_id=?"
        );
        $stmt->bind_param("ii", $tokenNo, $staffId);
        $stmt->execute();

        $msg = "Now Serving Token #".$tokenNo;
    } else {
        $error = "No tokens in waiting queue!";
    }
}

/* ---------- MANUAL STATUS UPDATE ---------- */
if (isset($_POST['update'])) {

    $token  = (int)$_POST['token'];
    $status = $_POST['status'];

    if ($status === "Serving") {
        $stmt = $conn->prepare(
            "UPDATE tokens SET status='Waiting'
             WHERE status='Serving' AND staff_id=?"
        );
        $stmt->bind_param("i", $staffId);
        $stmt->execute();
    }

    $stmt = $conn->prepare(
        "UPDATE tokens SET status=?
         WHERE token_no=? AND staff_id=?"
    );
    $stmt->bind_param("sii", $status, $token, $staffId);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $msg = "Token #$token updated to $status";
    } else {
        $error = "Token not found or not assigned to you!";
    }
}

/* ---------- LIVE QUEUE DATA ---------- */
$stmt = $conn->prepare(
    "SELECT token_no FROM tokens
     WHERE status='Serving' AND staff_id=? LIMIT 1"
);
$stmt->bind_param("i", $staffId);
$stmt->execute();
$serving = $stmt->get_result();

$stmt = $conn->prepare(
    "SELECT COUNT(*) total FROM tokens
     WHERE status='Waiting' AND staff_id=?"
);
$stmt->bind_param("i", $staffId);
$stmt->execute();
$waitingCount = $stmt->get_result()->fetch_assoc()['total'];

$stmt = $conn->prepare(
    "SELECT token_no, ration_id, name, status
     FROM tokens
     WHERE staff_id=?
     ORDER BY id DESC LIMIT 6"
);
$stmt->bind_param("i", $staffId);
$stmt->execute();
$recentTokens = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Live Queue Control</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box}
body{
    font-family:Poppins,sans-serif;
    background:linear-gradient(135deg,#0f2027,#203a43,#2c5364);
    min-height:100vh;
    color:#fff;
}
.container{
    max-width:1100px;
    margin:40px auto;
    background:rgba(255,255,255,.12);
    backdrop-filter:blur(18px);
    padding:35px;
    border-radius:25px;
    box-shadow:0 30px 60px rgba(0,0,0,.4);
}
h2,h3{text-align:center;margin-bottom:20px;font-weight:600}

/* QUEUE CARDS */
.queue{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:25px;
    margin:30px 0;
}
.card{
    background:linear-gradient(145deg,rgba(255,255,255,.3),rgba(255,255,255,.05));
    padding:30px;
    border-radius:20px;
    text-align:center;
}
.card h3{font-size:46px}
.card p{opacity:.85}

/* BUTTONS */
button{
    width:100%;
    padding:15px;
    border:none;
    border-radius:30px;
    background:linear-gradient(90deg,#00c6ff,#007bff);
    color:#fff;
    font-size:16px;
    cursor:pointer;
    transition:.3s;
}
button:hover{transform:translateY(-2px)}

/* FORMS */
form{margin-top:25px}
input,select{
    width:100%;
    padding:14px;
    border-radius:14px;
    border:none;
    margin-bottom:14px;
    font-size:14px;
}

/* ALERTS */
.success{
    background:rgba(40,167,69,.3);
    padding:14px;
    border-radius:12px;
    text-align:center;
    margin:15px 0;
}
.error{
    background:rgba(220,53,69,.3);
    padding:14px;
    border-radius:12px;
    text-align:center;
    margin:15px 0;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
    background:#fff;
    color:#333;
    border-radius:15px;
    overflow:hidden;
}
th,td{
    padding:14px;
    text-align:center;
}
th{
    background:#007bff;
    color:#fff;
}
tr:nth-child(even){background:#f2f6fa}

/* STATUS COLORS */
.waiting{color:#ff9800;font-weight:600}
.serving{color:#28a745;font-weight:600}
.completed{color:#6c757d;font-weight:600}

/* BACK */
.actions{text-align:center;margin-top:25px}
.actions a{
    padding:12px 25px;
    background:#007bff;
    color:#fff;
    border-radius:10px;
    text-decoration:none;
}
.actions a:hover{background:#0056b3}
</style>
</head>

<body>

<div class="container">
<h2>📢 Live Queue Control</h2>

<div class="queue">
    <div class="card">
        <h3><?= $serving->num_rows ? $serving->fetch_assoc()['token_no'] : "—" ?></h3>
        <p>Now Serving</p>
    </div>
    <div class="card">
        <h3><?= $waitingCount ?></h3>
        <p>Waiting Tokens</p>
    </div>
</div>

<form method="post">
    <button name="call_next">▶ Call Next Token</button>
</form>

<?php if($msg): ?><div class="success"><?= $msg ?></div><?php endif; ?>
<?php if($error): ?><div class="error"><?= $error ?></div><?php endif; ?>

<form method="post">
    <input type="number" name="token" placeholder="Token Number" required>
    <select name="status">
        <option value="Waiting">Waiting</option>
        <option value="Serving">Serving</option>
        <option value="Completed">Completed</option>
    </select>
    <button name="update">Update Status</button>
</form>

<h3>Recent Tokens</h3>
<table>
<tr>
<th>Token</th><th>Ration ID</th><th>Name</th><th>Status</th>
</tr>
<?php while($r=$recentTokens->fetch_assoc()): ?>
<tr>
<td><?= $r['token_no'] ?></td>
<td><?= htmlspecialchars($r['ration_id']) ?></td>
<td><?= htmlspecialchars($r['name']) ?></td>
<td class="<?= strtolower($r['status']) ?>"><?= $r['status'] ?></td>
</tr>
<?php endwhile; ?>
</table>

<div class="actions">
    <a href="dashboard.php">Back to Dashboard</a>
</div>
</div>

</body>
</html>
