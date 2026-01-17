<?php
session_start();
include("../db.php");

if (!isset($_SESSION['user'])) {
    echo json_encode(["error"=>"Unauthorized"]);
    exit();
}

/* Get ration ID */
$stmt = $conn->prepare("SELECT ration_id FROM users WHERE username=?");
$stmt->bind_param("s", $_SESSION['user']);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows !== 1) {
    echo json_encode(["error"=>"Invalid"]);
    exit();
}

$ration_id = $res->fetch_assoc()['ration_id'];

/* Get user's active token */
$tStmt = $conn->prepare("
    SELECT token_no 
    FROM tokens 
    WHERE ration_id=? 
    AND status IN ('Pending','Waiting List')
    ORDER BY date,time
    LIMIT 1
");
$tStmt->bind_param("s", $ration_id);
$tStmt->execute();
$tRes = $tStmt->get_result();

if ($tRes->num_rows !== 1) {
    echo json_encode([
        "queue_position"=>"Completed",
        "wait_time"=>"0 mins"
    ]);
    exit();
}

$myToken = $tRes->fetch_assoc()['token_no'];

/* Calculate queue position */
$qStmt = $conn->prepare("
    SELECT COUNT(*) AS pos
    FROM tokens
    WHERE date=CURDATE()
    AND status IN ('Pending','Waiting List')
    AND token_no < ?
");
$qStmt->bind_param("i", $myToken);
$qStmt->execute();
$qRes = $qStmt->get_result();
$position = $qRes->fetch_assoc()['pos'] + 1;

/* Waiting time */
$avgTime = 5;
$waitTime = ($position - 1) * $avgTime;

echo json_encode([
    "queue_position"=>$position,
    "wait_time"=>$waitTime." mins"
]);
