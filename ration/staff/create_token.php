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

/* ---------- CREATE TOKEN ---------- */
if (isset($_POST['create'])) {

    $rid = trim($_POST['rid']);

    /* VALIDATE USER */
    $userStmt = $conn->prepare("SELECT name FROM users WHERE ration_id=?");
    $userStmt->bind_param("s", $rid);
    $userStmt->execute();
    $userRes = $userStmt->get_result();

    if ($userRes->num_rows === 0) {
        $error = "Ration ID not found!";
    } else {

        $name = $userRes->fetch_assoc()['name'];

        /* UNIQUE TOKEN */
        do {
            $token = rand(1000, 9999);
            $check = $conn->prepare("SELECT id FROM tokens WHERE token_no=?");
            $check->bind_param("i", $token);
            $check->execute();
            $res = $check->get_result();
        } while ($res->num_rows > 0);

        $date = date("Y-m-d");
        $time = date("H:i:s");

        $stmt = $conn->prepare(
            "INSERT INTO tokens (token_no, ration_id, name, date, time, status, staff_id)
             VALUES (?,?,?,?,?,'Pending',?)"
        );
        $stmt->bind_param("issssi", $token, $rid, $name, $date, $time, $staffId);

        if ($stmt->execute()) {
            $msg = "Token Created Successfully. Token No: " . $token;
        } else {
            $error = "Failed to create token!";
        }
    }
}

/* ---------- FETCH STAFF RECENT TOKENS ---------- */
$recentTokensStmt = $conn->prepare(
    "SELECT token_no, ration_id, name, date, time, status
     FROM tokens
     WHERE staff_id=?
     ORDER BY id DESC LIMIT 5"
);
$recentTokensStmt->bind_param("i", $staffId);
$recentTokensStmt->execute();
$recentTokens = $recentTokensStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Create Token | Staff</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{
    font-family:Poppins,sans-serif;
    background:radial-gradient(circle at top,#0f2027,#203a43,#2c5364);
    min-height:100vh;
    color:#fff;
}

/* CONTAINER */
.container{
    max-width:900px;
    margin:40px auto;
    background:rgba(255,255,255,0.12);
    backdrop-filter:blur(18px);
    border-radius:25px;
    padding:35px;
    box-shadow:0 25px 45px rgba(0,0,0,.35);
}

h3,h4{text-align:center;margin-bottom:20px}

/* FORM */
input{
    width:100%;
    padding:15px;
    border:none;
    border-radius:14px;
    margin-bottom:15px;
}
button{
    width:100%;
    padding:15px;
    border:none;
    border-radius:30px;
    background:linear-gradient(90deg,#00c6ff,#007bff);
    color:#fff;
    font-size:16px;
    cursor:pointer;
}

/* ALERTS */
.success{background:rgba(40,167,69,.25);padding:12px;border-radius:12px;text-align:center}
.error{background:rgba(220,53,69,.25);padding:12px;border-radius:12px;text-align:center}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
    background:#fff;
    color:#333;
    border-radius:15px;
    overflow:hidden;
    margin-top:25px;
}
th{background:#007bff;color:#fff;padding:12px}
td{padding:12px;text-align:center}
tr:nth-child(even){background:#f1f5f9}

.status-pending{color:#ff9800;font-weight:600}
.status-waiting{color:#dc3545;font-weight:600}
.status-completed{color:#28a745;font-weight:600}

/* BACK */
.actions{text-align:center;margin-top:25px}
.actions a{
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

<h3>📝 Create Ration Token</h3>

<form method="post">
    <input type="text" name="rid" placeholder="Enter Ration ID" required>
    <button name="create">Generate Token</button>
</form>

<?php if ($msg): ?><div class="success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="actions">
    <a href="dashboard.php">← Back to Dashboard</a>
</div>

<h4 style="margin-top:35px;">Recent Tokens</h4>

<?php if ($recentTokens->num_rows > 0): ?>
<table>
<tr>
    <th>Token</th>
    <th>Ration ID</th>
    <th>Name</th>
    <th>Date</th>
    <th>Time</th>
    <th>Status</th>
</tr>

<?php while ($r = $recentTokens->fetch_assoc()):
    $cls = "status-" . strtolower($r['status']);
?>
<tr>
    <td><?= $r['token_no'] ?></td>
    <td><?= htmlspecialchars($r['ration_id']) ?></td>
    <td><?= htmlspecialchars($r['name']) ?></td>
    <td><?= $r['date'] ?></td>
    <td><?= $r['time'] ?></td>
    <td class="<?= $cls ?>"><?= $r['status'] ?></td>
</tr>
<?php endwhile; ?>
</table>
<?php else: ?>
<p style="text-align:center;margin-top:15px;">No tokens created yet.</p>
<?php endif; ?>

</div>
</body>
</html>
