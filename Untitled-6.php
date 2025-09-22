<?php
session_start();

// ---- Admin credentials ----
$admin_user = "admin";
$admin_pass = "admin123"; // change this to your secure password

// ---- Handle admin login ----
if(isset($_POST['admin_user']) && isset($_POST['admin_pass'])){
    if($_POST['admin_user']==$admin_user && $_POST['admin_pass']==$admin_pass){
        $_SESSION['admin'] = true;
    } else {
        $login_error = "Invalid username or password";
    }
}

// ---- Handle form submission ----
if(isset($_POST['name']) && isset($_POST['email']) && isset($_POST['phone'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $course = $_POST['course'];
    $message = $_POST['message'];

    // Save to CSV
    $file = fopen("submissions.csv","a");
    fputcsv($file, [$name, $email, $phone, $course, $message, date("Y-m-d H:i:s")]);
    fclose($file);

    $success = "Form submitted successfully!";
}

// ---- Handle logout ----
if(isset($_GET['logout'])){
    session_destroy();
    header("Location:index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>IT Coaching Institute</title>
<style>
body{font-family:Arial;margin:0;padding:0;background:#f4f4f4;color:#111;}
header, footer{background:#0b76ef;color:#fff;padding:15px;text-align:center;}
.container{max-width:900px;margin:20px auto;padding:20px;background:#fff;border-radius:8px;}
input,textarea,select{width:100%;padding:10px;margin:5px 0;border-radius:6px;border:1px solid #ccc;}
.btn{background:#0b76ef;color:#fff;padding:10px 15px;border:none;border-radius:6px;cursor:pointer;text-decoration:none;}
nav a{color:#fff;margin:0 10px;text-decoration:none;}
.success{color:green;}
.error{color:red;}
table{width:100%;border-collapse:collapse;margin-top:10px;}
th,td{border:1px solid #ccc;padding:8px;text-align:left;}
th{background:#0b76ef;color:#fff;}
</style>
</head>
<body>

<header>
<h1>IT Coaching Institute</h1>
<nav>
<a href="mailto:codingonly55@gmail.com">Mail</a> |
<a href="https://wa.me/919999999999" target="_blank">WhatsApp</a> |
<a href="tel:+919999999999">Call</a> |
<a href="#enroll">Enroll</a> |
<?php if(isset($_SESSION['admin'])): ?>
<a href="?logout=1">Logout Admin</a>
<?php else: ?>
<a href="#admin">Admin</a>
<?php endif; ?>
</nav>
</header>

<div class="container">

<?php if(isset($success)) echo "<p class='success'>$success</p>"; ?>
<?php if(isset($login_error)) echo "<p class='error'>$login_error</p>"; ?>

<?php if(isset($_SESSION['admin'])): ?>
    <h2>Submissions</h2>
    <?php
    if(file_exists("submissions.csv")){
        echo "<table><tr><th>Name</th><th>Email</th><th>Phone</th><th>Course</th><th>Message</th><th>Date</th></tr>";
        $rows = array_map('str_getcsv', file('submissions.csv'));
        foreach($rows as $row){
            echo "<tr>";
            foreach($row as $col) echo "<td>".htmlspecialchars($col)."</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No submissions yet.</p>";
    }
    ?>
<?php elseif(isset($_GET['admin_login'])): ?>
    <h2>Admin Login</h2>
    <form method="post">
        <label>Username</label><input type="text" name="admin_user" required>
        <label>Password</label><input type="password" name="admin_pass" required>
        <input type="submit" class="btn" value="Login">
    </form>
<?php else: ?>
    <h2 id="enroll">Enroll Now</h2>
    <form method="post">
        <label>Name</label><input type="text" name="name" required>
        <label>Email</label><input type="email" name="email" required>
        <label>Phone</label><input type="tel" name="phone" required>
        <label>Course</label>
        <select name="course" required>
            <option value="Web Development">Web Development</option>
            <option value="Python Programming">Python Programming</option>
            <option value="Data Science">Data Science</option>
        </select>
        <label>Message</label><textarea name="message" rows="4"></textarea>
        <input type="submit" class="btn" value="Submit">
    </form>

    <h2 id="admin">Admin Login</h2>
    <form method="get">
        <input type="hidden" name="admin_login" value="1">
        <input type="submit" class="btn" value="Go to Admin Login">
    </form>
<?php endif; ?>

</div>

<footer>
<p>© IT Coaching Institute</p>
</footer>

</body>
</html>
