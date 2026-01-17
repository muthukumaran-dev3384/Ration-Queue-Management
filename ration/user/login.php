<?php
session_start();
include("../db.php");

$error = "";

if (isset($_POST['login'])) {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username == "" || $password == "") {
        $error = "All fields are required!";
    } else {

        $hashedPassword = hash('sha256', $password);

        $stmt = $conn->prepare(
            "SELECT id, username FROM users WHERE username=? AND password=?"
        );
        $stmt->bind_param("ss", $username, $hashedPassword);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $_SESSION['user'] = $row['username'];
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
<title>User Login | Ration Queue System</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;}
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

.login-box{
    background:rgba(255,255,255,0.15);
    backdrop-filter:blur(15px);
    max-width:400px;
    width:100%;
    padding:60px 35px;
    border-radius:18px;
    box-shadow:0 20px 40px rgba(0,0,0,0.35);
    color:#fff;
}

.login-box h2{text-align:center;margin-bottom:10px;}
.login-box p{text-align:center;margin-bottom:25px;}

.login-box input{
    width:100%;
    padding:14px;
    margin-bottom:15px;
    border:none;
    border-radius:10px;
}

.login-box button{
    width:100%;
    padding:14px;
    border:none;
    border-radius:25px;
    background:linear-gradient(90deg,#00c6ff,#007bff);
    color:#fff;
    cursor:pointer;
}

.login-box button:hover{
    background:linear-gradient(90deg,#007bff,#0056b3);
}

.error{
    background:rgba(220,53,69,.2);
    padding:10px;
    border-radius:8px;
    margin-bottom:15px;
    text-align:center;
}

.links{
    text-align:center;
    margin-top:15px;
}

.links a{
    color:orange;
    text-decoration:none;
    font-size:14px;
}

.back-btn{
    margin-top:18px;
    background:#6c757d;
}
.back-btn:hover{
    background:#495057;
}
</style>
</head>

<body>

<div class="login-box">
    <h2>User Login</h2>
    <p>Ration Card Queue Management System</p>

    <?php if($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>

    <div class="links">
        <p>New user? <a href="register.php">Register here</a></p>
        <p><a href="forgot_password.php">Forgot Password?</a></p>
    </div>

    <form action="../index.php" method="get">
        <button type="submit" class="back-btn">← Back to Home</button>
    </form>
</div>

</body>
</html>
