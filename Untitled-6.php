<?php
// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$csv_file = 'submissions.csv';
$success = false;

// Form submission
if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['submit_form'])){
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $course = htmlspecialchars($_POST['course']);
    $message = htmlspecialchars($_POST['message']);

    // Save to CSV
    $data = [$name, $email, $phone, $course, $message, date('Y-m-d H:i:s')];
    $fp = fopen($csv_file, 'a');
    fputcsv($fp, $data);
    fclose($fp);

    // Send email via PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'codingonly55@gmail.com'; // your Gmail
        $mail->Password   = 'passwordgmail';   // Gmail App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom($email, $name);
        $mail->addAddress('info@itcoaching.com', 'IT Coaching');

        $mail->isHTML(false);
        $mail->Subject = "New Enrollment: $name";
        $mail->Body    = "Name: $name\nEmail: $email\nPhone: $phone\nCourse: $course\nMessage: $message";

        $mail->send();
        $success = true;
    } catch (Exception $e) {
        $success = false;
    }
}

// CSV download
if(isset($_GET['download_csv']) && $_GET['download_csv']=='1'){
    if(file_exists($csv_file)){
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="submissions.csv"');
        readfile($csv_file);
        exit;
    }
}

// Admin Panel
$show_admin = false;
$admin_password = "admin123"; // change this
$submissions = [];

if(isset($_POST['admin_password']) && $_POST['admin_password'] === $admin_password){
    $show_admin = true;
    if(file_exists($csv_file)){
        $submissions = array_map('str_getcsv', file($csv_file));
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>IT Coaching Institute</title>
<style>
body{font-family:Arial,sans-serif;margin:0;background:#f7fafc;color:#111;}
.container{max-width:1100px;margin:auto;padding:20px;}
header{background:#fff;padding:10px 20px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 2px 6px rgba(0,0,0,0.1);position:sticky;top:0;z-index:50;}
.logo{font-weight:bold;color:#0b76ef;font-size:24px;}
nav ul{list-style:none;display:flex;gap:15px;padding:0;margin:0;}
nav ul li a{text-decoration:none;color:#111;}
.nav-buttons a{background:#0b76ef;color:#fff;padding:6px 10px;border-radius:6px;text-decoration:none;margin-left:6px;}
.hero{background:#e0f2fe;padding:40px;margin:20px 0;border-radius:10px;text-align:center;}
.features{display:flex;gap:10px;margin-top:15px;flex-wrap:wrap;justify-content:center;}
.card{background:#fff;padding:15px;border-radius:8px;flex:1;min-width:220px;box-shadow:0 2px 6px rgba(0,0,0,0.1);}
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:15px;margin-top:15px;}
.course-card{background:#fff;padding:15px;border-radius:8px;box-shadow:0 2px 6px rgba(0,0,0,0.1);}
.tag{font-size:12px;color:#0b76ef;background:#dbeafe;padding:2px 6px;border-radius:999px;}
.people{display:flex;gap:10px;margin-top:10px;flex-wrap:wrap;justify-content:center;}
.person{background:#fff;padding:10px;border-radius:8px;display:flex;align-items:center;gap:10px;box-shadow:0 2px 6px rgba(0,0,0,0.1);}
.avatar{width:50px;height:50px;background:#0b76ef;color:#fff;font-weight:bold;display:flex;align-items:center;justify-content:center;border-radius:8px;}
table{width:100%;border-collapse:collapse;margin-top:10px;}
th,td{padding:10px;border:1px solid #ccc;}
th{background:#0b76ef;color:#fff;}
form{display:grid;gap:10px;margin-top:10px;}
input,select,textarea,button{padding:10px;border-radius:6px;border:1px solid #ccc;width:100%;}
button{background:#0b76ef;color:#fff;border:none;cursor:pointer;transition:0.3s;}
button:hover{background:#095ab5;}
footer{padding:15px;text-align:center;background:#fff;margin-top:20px;}
.success-msg{color:green;margin-bottom:10px;}
.admin-table{margin-top:15px;border:1px solid #ccc;}
.admin-table th, .admin-table td{border:1px solid #ccc;padding:8px;}
.download-btn{background:green;color:#fff;padding:6px 12px;border-radius:6px;text-decoration:none;}
.whatsapp-float{position:fixed;width:60px;height:60px;bottom:20px;right:20px;background:#25D366;color:#fff;border-radius:50%;text-align:center;font-size:30px;box-shadow:2px 2px 5px rgba(0,0,0,0.3);z-index:100;}
.whatsapp-float i{margin-top:14px;}
@media(max-width:768px){
  nav ul{display:none;}
  .features{flex-direction:column;}
  .people{flex-direction:column;}
}
</style>
</head>
<body>

<?php if($show_admin): ?>
<div class="container">
  <h2>Admin Panel - Submissions</h2>
  <a class="download-btn" href="?download_csv=1">Download CSV</a>
  <?php if(empty($submissions)): ?>
    <p>No submissions yet.</p>
  <?php else: ?>
  <table class="admin-table">
    <tr><th>Name</th><th>Email</th><th>Phone</th><th>Course</th><th>Message</th><th>Submitted At</th></tr>
    <?php foreach($submissions as $row): ?>
      <tr>
        <td><?= htmlspecialchars($row[0]) ?></td>
        <td><?= htmlspecialchars($row[1]) ?></td>
        <td><?= htmlspecialchars($row[2]) ?></td>
        <td><?= htmlspecialchars($row[3]) ?></td>
        <td><?= htmlspecialchars($row[4]) ?></td>
        <td><?= $row[5] ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
  <?php endif; ?>
</div>
<?php else: ?>

<header>
  <div class="container" style="display:flex;align-items:center;justify-content:space-between;">
    <div class="brand">
      <div class="logo">IT</div>
      <div>IT Coaching Institute</div>
    </div>
    <nav>
      <ul>
        <li><a href="#courses">Courses</a></li>
        <li><a href="#faculty">Faculty</a></li>
        <li><a href="#schedule">Schedule</a></li>
        <li><a href="#contact">Contact</a></li>
      </ul>
    </nav>
    <div class="nav-buttons">
      <a href="tel:+911234567890">Call</a>
      <a href="mailto:codingonly55@gmail.com">Email</a>
      <a href="#contact">Enroll</a>
    </div>
  </div>
</header>

<main class="container">
  <section class="hero">
    <h1>Master IT Skills with Us</h1>
    <p>Expert faculty • Small batches • Online & Classroom • Personalized support</p>
    <div class="features">
      <div class="card"><strong>Classroom & Online</strong><p>Learn live or at your own pace.</p></div>
      <div class="card"><strong>Projects & Labs</strong><p>Hands-on coding & real projects.</p></div>
    </div>
  </section>

  <section id="courses">
    <h2>Courses</h2>
    <div class="grid">
      <div class="course-card"><div class="tag">Classroom</div><h3>Web Development</h3><p>HTML, CSS, JS, PHP & MySQL</p><div>₹12,000 • 3 months</div></div>
      <div class="course-card"><div class="tag">Online</div><h3>Python & Data Science</h3><p>Python, Pandas, ML & AI basics</p><div>₹10,000 • 3 months</div></div>
      <div class="course-card"><div class="tag">Crash</div><h3>Cybersecurity</h3><p>Network security & ethical hacking basics</p><div>₹8,000 • 1 month</div></div>
    </div>
  </section>

  <section id="faculty">
    <h2>Faculty</h2>
    <div class="people">
      <div class="person"><div class="avatar">RS</div><div><strong>Ravi Sharma</strong><div>Web Dev • 8+ yrs</div></div></div>
      <div class="person"><div class="avatar">AP</div><div><strong>Anita Patel</strong><div>Python & Data Sci • 7+ yrs</div></div></div>
    </div>
  </section>

  <section id="schedule">
    <h2>Schedule</h2>
    <table>
      <tr><th>Course</th><th>Days</th><th>Time</th><th>Mode</th></tr>
      <tr><td>Web Development</td><td>Mon, Wed, Fri</td><td>6-8 PM</td><td>Classroom/Online</td></tr>
      <tr><td>Python & Data Science</td><td>Tue, Thu</td><td>7-9 PM</td><td>Online</td></tr>
      <tr><td>Cybersecurity</td><td>Sat, Sun</td><td>9 AM-12 PM</td><td>Classroom</td></tr>
    </table>
  </section>

  <section id="contact">
    <h2>Contact & Enroll</h2>
    <?php if($success) echo "<div class='success-msg'>Your submission has been received!</div>"; ?>
    <form method="post">
      <input type="hidden" name="submit_form" value="1">
      <input type="text" name="name" placeholder="Full Name" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="tel" name="phone" placeholder="Phone" required>
      <select name="course">
        <option>Web Development</option>
        <option>Python & Data Science</option>
        <option>Cybersecurity</option>
      </select>
      <textarea name="message" rows="4" placeholder="Message"></textarea>
      <button type="submit">Submit</button>
    </form>
  </section>
</main>

<a href="https://wa.me/911234567890" target="_blank" class="whatsapp-float"><i>💬</i></a>

<footer>
  <p>© IT Coaching Institute • All rights reserved</p>
</footer>

<div style="position:fixed;bottom:10px;right:10px;background:#fff;padding:10px;border-radius:6px;box-shadow:0 2px 6px rgba(0,0,0,0.2);">
  <form method="post">
    <input type="password" name="admin_password" placeholder="Admin Password">
    <button type="submit">Login</button>
  </form>
</div>

<?php endif; ?>
</body>
</html>
