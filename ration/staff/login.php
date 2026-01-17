<?php
session_start();
include("../db.php");

/* ---------- ALREADY LOGGED-IN CHECK ---------- */
if (isset($_SESSION['staff'], $_SESSION['staff_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

/* ---------- LOGIN PROCESS ---------- */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {

    $username = trim($_POST['username']);
    $password = hash('sha256', trim($_POST['password']));

    $stmt = $conn->prepare(
        "SELECT id, username FROM staff WHERE username=? AND password=?"
    );
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {

        $row = $result->fetch_assoc();
        $_SESSION['staff']    = $row['username'];
        $_SESSION['staff_id'] = $row['id'];

        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Staff Login | Ration Queue System</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}
body{
    font-family:'Poppins',sans-serif;
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    background:
        linear-gradient(rgba(0,0,0,0.65), rgba(0,0,0,0.65)),
        url('img/ration 1.jpg');
    background-size:cover;
    background-position:center;
}

/* ---------- LOGIN CARD ---------- */
.login-box{
    width:100%;
    max-width:420px;
    padding:45px 35px;
    background:rgba(255,255,255,0.16);
    backdrop-filter:blur(18px);
    border-radius:22px;
    box-shadow:0 30px 60px rgba(0,0,0,0.45);
    color:#fff;
    animation:fadeIn .6s ease;
}

@keyframes fadeIn{
    from{opacity:0;transform:translateY(20px)}
    to{opacity:1;transform:none}
}

/* ---------- TITLE ---------- */
.login-box h2{
    text-align:center;
    font-weight:600;
    margin-bottom:6px;
}
.subtitle{
    text-align:center;
    font-size:14px;
    opacity:.9;
    margin-bottom:26px;
}

/* ---------- INPUT ---------- */
.login-box input{
    width:100%;
    padding:14px 15px;
    margin-bottom:15px;
    border:none;
    border-radius:12px;
    outline:none;
    font-size:14px;
}
.login-box input:focus{
    box-shadow:0 0 0 2px rgba(0,198,255,.7);
}

/* ---------- SHOW PASSWORD (FIXED) ---------- */
.show-pass{
    display:flex;
    align-items:center;          /* ✅ correct */
    justify-content:flex-start;  /* ✅ left aligned */
    gap:8px;
    font-size:13px;
    margin-bottom:18px;
    opacity:.9;
}

.show-pass input[type="checkbox"]{
    accent-color:#00c6ff;        /* modern checkbox color */
    cursor:pointer;
}

.show-pass label{
    cursor:pointer;
    user-select:none;
}

/* ---------- BUTTON ---------- */
.login-box button{
    width:100%;
    padding:14px;
    border:none;
    border-radius:30px;
    background:linear-gradient(90deg,#00c6ff,#007bff);
    color:#fff;
    font-size:16px;
    font-weight:500;
    cursor:pointer;
    transition:.3s;
}
.login-box button:hover{
    transform:translateY(-2px);
    box-shadow:0 12px 30px rgba(0,0,0,.35);
    background:linear-gradient(90deg,#007bff,#0056b3);
}

/* ---------- ERROR ---------- */
.error{
    background:rgba(220,53,69,.28);
    color:#ffdede;
    padding:12px;
    border-radius:12px;
    text-align:center;
    margin-top:18px;
    font-size:14px;
}

/* ---------- BACK ---------- */
.back{
    text-align:center;
    margin-top:22px;
}
.back a{
    color:#00c6ff;
    text-decoration:none;
    font-weight:500;
}
.back a:hover{
    text-decoration:underline;
}

/* ---------- RESPONSIVE ---------- */
@media(max-width:500px){
    .login-box{
        padding:32px 25px;
    }
}
</style>

</head>

<body>

<div class="login-box">
    <h2>👮 Staff Login</h2>
    <p class="subtitle">Ration Card Queue Management System</p>

    <form method="post">
        <input type="text" name="username" placeholder="Username" required autofocus>
        <input type="password" name="password" id="password" placeholder="Password" required>

        

        <button type="submit" name="login">Login</button>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
    </form>

    <div class="back">
        <a href="../index.php">← Back to Home</a>
    </div>
</div>

</body>
</html>
