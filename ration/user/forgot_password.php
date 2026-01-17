<?php
include("../db.php");

$msg = "";
$error = "";

if (isset($_POST['reset'])) {

    $rid = trim($_POST['ration_id']);
    $newpass = trim($_POST['new_password']);

    if ($rid == "" || $newpass == "") {
        $error = "All fields are required!";
    } elseif (!preg_match('/^[0-9]{10}$/', $rid)) {
        $error = "Ration ID must be 10 digits!";
    } else {

        $hash = hash('sha256', $newpass);

        $stmt = $conn->prepare(
            "UPDATE users SET password=? WHERE ration_id=?"
        );
        $stmt->bind_param("ss", $hash, $rid);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $msg = "Password updated successfully!";
        } else {
            $error = "Invalid Ration ID!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Forgot Password</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<style>
body{
    font-family:Poppins;
    background: linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.55));
    height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
}
.box{
    background:rgba(255,255,255,0.15);
    padding:35px;
    width:380px;
    border-radius:15px;
    color:#fff;
}
input,button{
    width:100%;
    padding:12px;
    margin-top:12px;
    border-radius:8px;
    border:none;
}
button{
    background:#00c6ff;
    color:#fff;
    cursor:pointer;
}
.error{background:rgba(220,53,69,.3);padding:8px;border-radius:6px;}
.success{background:rgba(40,167,69,.3);padding:8px;border-radius:6px;}
a{color:#00c6ff;text-decoration:none;}
</style>
</head>

<body>

<div class="box">
<h3 style="text-align:center;">Forgot Password</h3>

<?php if($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
<?php if($msg): ?><div class="success"><?= $msg ?></div><?php endif; ?>

<form method="post">
    <input type="text" name="ration_id" placeholder="Enter Ration ID (10 digits)" required>
    <input type="password" name="new_password" placeholder="New Password" required>
    <button name="reset">Reset Password</button>
</form>

<p style="text-align:center;margin-top:15px;">
    <a href="login.php">← Back to Login</a>
</p>
</div>

</body>
</html>
