<?php
session_start();
include("../db.php");

/* -------- SESSION PROTECTION -------- */
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['user'];
$success = "";
$error = "";

/* -------- HANDLE FEEDBACK SUBMISSION -------- */
if (isset($_POST['send'])) {

    $message = trim($_POST['message']);
    $rating = intval($_POST['rating']);

    if ($message == "") {
        $error = "Feedback message cannot be empty!";
    } elseif ($rating < 1 || $rating > 5) {
        $error = "Please select a valid star rating!";
    } else {

        $stmt = $conn->prepare(
            "INSERT INTO feedback (user_name, message, rating) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("ssi", $username, $message, $rating);

        if ($stmt->execute()) {
            $success = "Thank you! Your feedback has been submitted successfully.";
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Feedback</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../assets/style.css">
<style>
/* ---------- GENERAL ---------- */
body { font-family: 'Poppins', sans-serif; background: #f0f2f5; margin: 0; padding: 0; }
.container {
    max-width: 600px;
    margin: 80px auto;
    background: #fff;
    padding: 30px 25px;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    text-align: center;
}
h2 { color: #007bff; margin-bottom: 20px; }
textarea { width: 100%; height: 120px; resize: none; padding: 10px; border-radius: 6px; border: 1px solid #ccc; }
button[type="submit"] {
    margin-top: 15px;
    padding: 10px 20px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}
button[type="submit"]:hover { background: #0056b3; }

/* ---------- MESSAGES ---------- */
.msg-success { color: green; font-weight: bold; margin-bottom: 10px; }
.msg-error { color: red; font-weight: bold; margin-bottom: 10px; }

/* ---------- STAR RATING ---------- */
.star-rating {
    direction: rtl;
    font-size: 25px;
    display: inline-block;
    padding: 10px 0;
}
.star-rating input[type=radio] {
    display: none;
}
.star-rating label {
    color: #ccc;
    cursor: pointer;
    transition: 0.3s;
}
.star-rating label:hover,
.star-rating label:hover ~ label,
.star-rating input[type=radio]:checked ~ label {
    color: #f8ce0b;
}

/* ---------- BUTTON GROUP ---------- */
.btn-group { margin-top: 20px; }
.btn-group a {
    display: inline-block;
    padding: 10px 15px;
    background: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 6px;
}
.btn-group a:hover { background: #0056b3; }

/* ---------- RESPONSIVE ---------- */
@media(max-width:600px){
    .container { width: 90%; padding: 25px; }
    .star-rating { font-size: 20px; }
}
</style>
</head>

<body>

<div class="container">
    <h2>Submit Feedback</h2>
    <p>Logged in as: <strong><?php echo htmlspecialchars($username); ?></strong></p>

    <?php if ($success != "") { ?>
        <p class="msg-success"><?php echo $success; ?></p>
    <?php } ?>
    <?php if ($error != "") { ?>
        <p class="msg-error"><?php echo $error; ?></p>
    <?php } ?>

    <form method="post">
        <textarea name="message" placeholder="Write your feedback here..." required></textarea>
        
        <!-- STAR RATING -->
        <div class="star-rating">
            <input type="radio" name="rating" id="star5" value="5"><label for="star5">&#9733;</label>
            <input type="radio" name="rating" id="star4" value="4"><label for="star4">&#9733;</label>
            <input type="radio" name="rating" id="star3" value="3"><label for="star3">&#9733;</label>
            <input type="radio" name="rating" id="star2" value="2"><label for="star2">&#9733;</label>
            <input type="radio" name="rating" id="star1" value="1"><label for="star1">&#9733;</label>
        </div>

        <button type="submit" name="send">Send Feedback</button>
    </form>

    <div class="btn-group">
        <a href="dashboard.php">⬅ Back to Dashboard</a>
    </div>
</div>

</body>
</html>
