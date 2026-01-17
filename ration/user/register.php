<?php
session_start();
include("../db.php");

$error = "";
$success = "";

if (isset($_POST['register'])) {

    $name     = trim($_POST['name']);
    $rid      = trim($_POST['rid']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    /* ---------- BASIC VALIDATION ---------- */
    if ($name == "" || $rid == "" || $username == "" || $password == "") {
        $error = "All fields are required!";
    }
    /* ---------- RATION ID VALIDATION ---------- */
    elseif (!ctype_digit($rid) || strlen($rid) != 10) {
        $error = "Ration ID must be exactly 10 digits!";
    }
    else {

        /* ---------- CHECK EXISTING USER ---------- */
        $check = $conn->prepare(
            "SELECT id FROM users WHERE username=? OR ration_id=?"
        );
        $check->bind_param("ss", $username, $rid);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Username or Ration ID already exists!";
        } else {

            /* ---------- PASSWORD HASH ---------- */
            $hashedPassword = hash('sha256', $password);

            /* ---------- INSERT USER ---------- */
            $stmt = $conn->prepare(
                "INSERT INTO users (ration_id, name, username, password)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param("ssss", $rid, $name, $username, $hashedPassword);

            if ($stmt->execute()) {
                $success = "Registration successful! You can now login.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Registration | Ration Queue System</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
/* ---------- GLOBAL ---------- */
*{margin:0;padding:0;box-sizing:border-box;}
body{
    font-family:'Poppins',sans-serif;
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    background:
        linear-gradient(rgba(0,0,0,.6),rgba(0,0,0,.6)),
        url("img/r1.jpg");
    background-size:cover;
    background-position:center;
}

/* ---------- CARD ---------- */
.register-box{
    background:rgba(255,255,255,0.15);
    backdrop-filter:blur(15px);
    width:100%;
    max-width:420px;
    padding:40px 35px;
    border-radius:18px;
    box-shadow:0 20px 40px rgba(0,0,0,0.35);
    color:#fff;
}

/* ---------- TITLE ---------- */
.register-box h2{
    text-align:center;
    margin-bottom:8px;
    font-weight:600;
}
.register-box p.subtitle{
    text-align:center;
    font-size:14px;
    opacity:.9;
    margin-bottom:22px;
}

/* ---------- INPUT ---------- */
.register-box input{
    width:100%;
    padding:14px;
    margin-bottom:14px;
    border:none;
    border-radius:10px;
    outline:none;
    font-size:14px;
}
.register-box input:focus{
    outline:2px solid #00c6ff;
}

/* ---------- BUTTON ---------- */
.register-box button{
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
.register-box button:hover{
    transform:translateY(-2px);
    background:linear-gradient(90deg,#007bff,#0056b3);
}

/* ---------- ALERTS ---------- */
.error{
    background:rgba(220,53,69,.25);
    color:#ffdddd;
    padding:10px;
    border-radius:8px;
    text-align:center;
    margin-bottom:14px;
    font-size:14px;
}
.success{
    background:rgba(40,167,69,.25);
    color:#d4ffd4;
    padding:10px;
    border-radius:8px;
    text-align:center;
    margin-bottom:14px;
    font-size:14px;
}

/* ---------- LINK ---------- */
.register-box a{
    color:#00c6ff;
    text-decoration:none;
    font-weight:500;
}
.register-box a:hover{
    text-decoration:underline;
}

/* ---------- RESPONSIVE ---------- */
@media(max-width:500px){
    .register-box{padding:30px 25px;}
}
</style>
</head>

<body>

<div class="register-box">
    <h2>User Registration</h2>
    <p class="subtitle">Ration Card Queue Management System</p>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="text" name="rid" placeholder="10-digit Ration ID" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>

        <button type="submit" name="register">Register</button>
    </form>

    <p style="text-align:center; margin-top:18px;">
        Already registered?
        <a href="login.php">Login here</a>
    </p>
</div>

</body>
</html>
