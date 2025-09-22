<?php
// --- CONFIG ---
$owner_email = "codingonly55@gmail.com";
$admin_user = "admin";      // Admin username
$admin_pass = "admin123";   // Admin password

// Store CSV outside web root if possible. For simplicity, we'll store in a folder called 'data'
$data_folder = __DIR__ . '/data';
if (!is_dir($data_folder)) mkdir($data_folder, 0755, true);
$file = $data_folder . '/submissions.csv';

// --- SESSION FOR ADMIN ---
session_start();

// --- ADMIN LOGIN ---
if (isset($_POST['admin_login'])) {
    if ($_POST['username'] === $admin_user && $_POST['password'] === $admin_pass) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $admin_error = "Invalid username or password";
    }
}

// --- ADMIN LOGOUT ---
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// --- ENROLL FORM ---
$success = '';
$error = '';
if (isset($_POST['enroll'])) {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $course = htmlspecialchars($_POST['course']);
    $message = htmlspecialchars($_POST['message']);

    $data = [$name, $email, $phone, $course, $message, date("Y-m-d H:i:s")];

    $fp = fopen($file, 'a');
    if ($fp) {
        fputcsv($fp, $data);
        fclose($fp);

        // Email to owner
        $subject_owner = "New Enrollment: $name";
        $body_owner = "<h3>New Enrollment Details</h3>
        <p><b>Name:</b> $name</p>
        <p><b>Email:</b> $email</p>
        <p><b>Phone:</b> $phone</p>
        <p><b>Course:</b> $course</p>
        <p><b>Message:</b> $message</p>";
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8\r\n";
        $headers .= "From: IT Coaching <$owner_email>\r\n";
        if(mail($owner_email, $subject_owner, $body_owner, $headers)){
            // Thank you email to student
            $subject_student = "Thank you for enrolling at IT Coaching Institute";
            $body_student = "<h3>Hello $name,</h3><p>Thank you for enrolling in our <b>$course</b> course!</p><p>We will contact you shortly.</p><br><p>Regards,<br>IT Coaching Institute</p>";
            mail($email, $subject_student, $body_student, $headers);
            $success = "Thank you! Your enrollment has been received.";
        } else {
            $error = "Submission saved but email could not be sent.";
        }
    } else {
        $error = "Unable to save submission. Check folder permissions.";
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>IT Coaching Institute</title>
<style>
body{font-family:Arial,sans-serif;margin:0;background:#f7fafc;color:#0f172a}
header{background:#fff;box-shadow:0 2px 10px rgba(0,0,0,0.08);position:sticky;top:0;z-index:30}
.container{max-width:1100px;margin:0 auto;padding:24px}
.nav{display:flex;align-items:center;justify-content:space-between}
.logo{width:48px;height:48px;background:#0b76ef;color:#fff;font-weight:700;border-radius:8px;display:flex;align-items:center;justify-content:center}
nav ul{display:flex;gap:18px;list-style:none;padding:0;margin:0}
a{color:inherit;text-decoration:none}
.btn{background:#0b76ef;color:#fff;padding:10px 16px;border-radius:10px;display:inline-block;margin-left:4px}
.hero{padding:64px 0;display:grid;grid-template-columns:1fr 420px;gap:32px;align-items:center}
.card{background:#fff;padding:18px;border-radius:14px;box-shadow:0 6px 18px rgba(0,0,0,0.06);margin-bottom:12px}
form{display:grid;gap:10px}
input,textarea,select{padding:10px;border-radius:8px;border:1px solid #e6eef8}
.alert{padding:10px;margin-bottom:12px;border-radius:8px}
.success{background:#d1fae5;color:#065f46}
.error{background:#fee2e2;color:#b91c1c}
table{width:100%;border-collapse:collapse;background:#fff;border-radius:12px;overflow:hidden;margin-top:12px}
th,td{padding:12px;border-bottom:1px solid #edf2f7;text-align:left}
th{background:#fbfdff}
</style>
</head>
<body>
<header class="container nav">
<div class="logo">IT</div>
<nav>
<ul>
<li><a href="#courses">Courses</a></li>
<li><a href="#contact">Enroll</a></li>
</ul>
</nav>
<div>
<a class="btn" href="tel:+911234567890">Call</a>
<a class="btn" href="mailto:codingonly55@gmail.com">Email</a>
<a class="btn" href="https://wa.me/911234567890" target="_blank">WhatsApp</a>
<?php if(!isset($_SESSION['admin_logged_in'])): ?>
<a class="btn" href="#admin">Admin Login</a>
<?php else: ?>
<a class="btn" href="?logout=1">Logout</a>
<?php endif; ?>
</div>
</header>

<main class="container">
<section class="hero">
<h1>Learn IT Skills with Experts</h1>
<p>Small batches • Expert faculty • Project-based learning • Weekend workshops available</p>
</section>

<section id="courses">
<h2>Our Courses</h2>
<div class="card"><b>Web Development</b></div>
<div class="card"><b>Data Science</b></div>
<div class="card"><b>Python Bootcamp</b></div>
</section>

<section id="contact">
<h2>Enroll Now</h2>
<?php if($success) echo "<div class='alert success'>$success</div>"; ?>
<?php if($error) echo "<div class='alert error'>$error</div>"; ?>
<form method="post">
<label>Name<input name="name" required></label>
<label>Email<input type="email" name="email" required></label>
<label>Phone<input name="phone" required></label>
<label>Course<select name="course"><option>Web Development</option><option>Data Science</option><option>Python Bootcamp</option></select></label>
<label>Message<textarea name="message"></textarea></label>
<button type="submit" name="enroll" class="btn">Submit Enquiry</button>
</form>
</section>

<section id="admin">
<?php if(!isset($_SESSION['admin_logged_in'])): ?>
<h2>Admin Login</h2>
<?php if(isset($admin_error)) echo "<div class='alert error'>$admin_error</div>"; ?>
<form method="post">
<label>Username<input name="username" required></label>
<label>Password<input type="password" name="password" required></label>
<button type="submit" name="admin_login" class="btn">Login</button>
</form>
<?php else: ?>
<h2>All Submissions</h2>
<table>
<tr><th>Name</th><th>Email</th><th>Phone</th><th>Course</th><th>Message</th><th>Date</th></tr>
<?php
if(file_exists($file)){
    $rows = array_map('str_getcsv', file($file));
    foreach($rows as $row){
        echo "<tr><td>".implode("</td><td>", $row)."</td></tr>";
    }
}
?>
</table>
<?php endif; ?>
</section>
</main>
</body>
</html>
