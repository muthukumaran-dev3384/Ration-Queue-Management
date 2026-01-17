<?php
session_start();
include("../db.php");

/* ---------- ALREADY LOGGED-IN CHECK ---------- */
if (isset($_SESSION['admin'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

/* ---------- LOGIN PROCESS ---------- */
if (isset($_POST['login'])) {

    $username = trim($_POST['username']);
    $passwordRaw = trim($_POST['password']);
    $password = hash('sha256', $passwordRaw);

    if ($username === "" || $passwordRaw === "") {
        $error = "All fields are required!";
    } else {

        $stmt = $conn->prepare(
            "SELECT id, username FROM admin WHERE username=? AND password=?"
        );
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            $_SESSION['admin'] = $admin['username'];
            $_SESSION['admin_id'] = $admin['id'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid username or password!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login | Ration Queue System</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
/* ---------- GLOBAL ---------- */
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
        linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.55)),
        url('img/ration 1.jpg');
    background-size:cover;
    background-position:center;
}

/* ---------- LOGIN CARD ---------- */
.login-box{
    width:100%;
    max-width:420px;
    padding:40px 35px;
    background:rgba(255,255,255,0.15);
    backdrop-filter:blur(15px);
    border-radius:18px;
    box-shadow:0 20px 40px rgba(0,0,0,0.35);
    color:#fff;
    text-align:center;
}

/* ---------- TITLE ---------- */
.login-box h2{
    font-weight:600;
    margin-bottom:5px;
}
.login-box .subtitle{
    font-size:14px;
    opacity:.9;
    margin-bottom:22px;
}

/* ---------- INPUT ---------- */
.login-box input{
    width:100%;
    padding:14px;
    margin-bottom:14px;
    border:none;
    border-radius:10px;
    font-size:14px;
}
.login-box input:focus{
    outline:2px solid #00c6ff;
}

/* ---------- BUTTON ---------- */
.login-box button{
    width:100%;
    padding:14px;
    border:none;
    border-radius:25px;
    background:linear-gradient(90deg,#00c6ff,#007bff);
    color:#fff;
    font-size:15px;
    font-weight:500;
    cursor:pointer;
    transition:.3s;
}
.login-box button:hover{
    transform:translateY(-2px);
    background:linear-gradient(90deg,#007bff,#0056b3);
}

/* ---------- ERROR ---------- */
.error{
    background:rgba(220,53,69,.25);
    color:#ffdede;
    padding:10px;
    border-radius:8px;
    margin-top:15px;
    font-size:14px;
}

/* ---------- FOOTER ---------- */
.footer{
    position:fixed;
    bottom:15px;
    width:100%;
    text-align:center;
    font-size:13px;
    color:#ddd;
}
</style>
</head>

<body>

<div class="login-box">
    <h2>🛡 Admin Login</h2>
    <p class="subtitle">Ration Card Queue Management System</p>

    <form method="post">
        <input type="text" name="username" placeholder="Admin Username" required autofocus>
        <input type="password" name="password" placeholder="Password" required>

        <button type="submit" name="login">Login</button>

        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
    </form>
</div>

<div class="footer">
    © <?= date("Y") ?> Ration Card Queue Management System
</div>

</body>
</html>
