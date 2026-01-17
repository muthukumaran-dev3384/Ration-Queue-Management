<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ration Card Queue Management System</title>

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
/* ================= GLOBAL ================= */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}
body{
    font-family:'Poppins',sans-serif;
    min-height:100vh;
    background:
        linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.55)),
        url('img/r1.jpg');
    background-size:cover;
    background-position:center;
    background-attachment:fixed;
    color:#fff;
}
i {
    margin-right:6px;
}

/* ================= HEADER ================= */
header{
    width:100%;
    padding:15px 50px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    background:rgba(255,255,255,0.15);
    backdrop-filter:blur(10px);
    position:fixed;
    top:0;
    z-index:100;
}

/* Logo */
.logo{
    display:flex;
    align-items:center;
    gap:10px;
}
.logo svg{
    width:40px;
    height:40px;
}
.logo span{
    font-size:18px;
    font-weight:600;
    letter-spacing:0.5px;
}

/* Header Buttons */
.nav-buttons a{
    margin-left:15px;
    padding:10px 20px;
    text-decoration:none;
    border-radius:25px;
    font-size:14px;
    font-weight:500;
    color:#fff;
    background:linear-gradient(90deg,#00c6ff,#007bff);
    transition:0.3s;
}
.nav-buttons a:hover{
    transform:translateY(-2px);
    background:linear-gradient(90deg,#007bff,#0056b3);
}

/* ================= MAIN CONTAINER ================= */
.main{
    max-width:1100px;
    margin:140px auto 50px;
    padding:20px;
}

/* Card Layout */
.card{
    background:rgba(255,255,255,0.15);
    backdrop-filter:blur(15px);
    border-radius:20px;
    padding:40px;
    box-shadow:0 20px 40px rgba(0,0,0,0.3);
    text-align:center;
}

/* Title */
.card h1{
    font-size:32px;
    margin-bottom:10px;
    font-weight:600;
}
.card p{
    font-size:15px;
    opacity:0.9;
    margin-bottom:30px;
}

/* Action Buttons */
.actions{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(230px,1fr));
    gap:20px;
}

.actions a{
    display:block;
    padding:18px;
    border-radius:15px;
    text-decoration:none;
    color:#fff;
    background:linear-gradient(135deg,#00c6ff,#007bff);
    box-shadow:0 10px 25px rgba(0,0,0,0.3);
    transition:0.3s;
}
.actions a h3{
    font-size:18px;
    margin-bottom:5px;
}
.actions a span{
    font-size:13px;
    opacity:0.9;
}
.actions a:hover{
    transform:translateY(-5px) scale(1.02);
}

/* ================= INFO SECTION ================= */
.info{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:20px;
    margin-top:40px;
}

.info-box{
    background:rgba(255,255,255,0.12);
    padding:25px;
    border-radius:15px;
    text-align:center;
}
.info-box h4{
    margin-bottom:8px;
    font-weight:600;
}
.info-box p{
    font-size:13px;
    opacity:0.85;
}

/* ================= FOOTER ================= */
footer{
    text-align:center;
    padding:15px;
    font-size:13px;
    opacity:0.8;
}

/* ================= RESPONSIVE ================= */
@media(max-width:600px){
    header{
        flex-direction:column;
        align-items:flex-start;
        gap:10px;
        padding:15px 20px;
    }
    .nav-buttons a{
        margin:5px 5px 0 0;
    }
    .card h1{
        font-size:26px;
    }
}
</style>
</head>

<body>

<!-- ================= HEADER ================= -->
<header>
    <div class="logo">
        <!-- SVG LOGO -->
        <svg viewBox="0 0 64 64" fill="none">
            <rect x="8" y="22" width="48" height="28" rx="4" fill="#00c6ff"/>
            <rect x="14" y="28" width="36" height="16" rx="2" fill="#ffffff"/>
            <path d="M20 22L32 12L44 22" stroke="#ffffff" stroke-width="3" stroke-linecap="round"/>
        </svg>
        <span>Ration Queue System</span>
    </div>

    <div class="nav-buttons">
        <a href="admin/login.php"><i class="fa-solid fa-user-shield"></i> Admin</a>
        <a href="staff/login.php"><i class="fa-solid fa-user-gear"></i> Staff</a>
        <a href="user/login.php"><i class="fa-solid fa-user"></i> User</a>
    </div>
</header>

<!-- ================= MAIN ================= -->
<div class="main">
    <div class="card">
        <h1><i class="fa-solid fa-warehouse"></i> Ration Card Queue Management System</h1>
        <p>Smart digital solution to reduce waiting time and manage ration distribution efficiently.</p>

        <div class="actions">
            <a href="user/register.php">
                <h3><i class="fa-solid fa-id-card"></i> User Registration</h3>
                <span>Create ration account & profile</span>
            </a>

            
        </div>

        <!-- Extra Info -->
        <div class="info">
            <div class="info-box">
                <h4><i class="fa-solid fa-clock"></i> Shop Timings</h4>
                <p>Mon – Sat : 9:00 AM – 6:00 PM</p>
            </div>

            <div class="info-box">
                <h4><i class="fa-solid fa-diagram-project"></i> Smart Distribution</h4>
                <p>Token-based, fast & transparent system</p>
            </div>

            
        </div>
    </div>
</div>

<!-- ================= FOOTER ================= -->
<footer>
    © 2025 Ration Card Queue Management System | Digital India Initiative
</footer>

</body>
</html>
