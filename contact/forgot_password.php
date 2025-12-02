<?php
session_start();
include '../config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    try {
        $pdo = new PDO($dsn, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT UserID, Username FROM users WHERE Email = :email");
        $stmt->execute([':email' => $email]);
        $userFound = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userFound) {
            $token = bin2hex(random_bytes(32));
            $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));

            $update = $pdo->prepare("UPDATE users SET reset_token = :token, reset_expires = :expires WHERE Email = :email");
            $update->execute([':token' => $token, ':expires' => $expires, ':email' => $email]);

            $projectFolder = "CourseworkCOMP1841"; 
            $resetLink = "http://localhost/$projectFolder/reset_password.php?email=$email&token=$token";

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'boscortg@gmail.com'; 
                $mail->Password   = 'bdxa fbdf rqvz vixd'; 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('your_email@gmail.com', 'Admin Support');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Reset Password Request';
                $mail->Body    = "Click here to reset password: <a href='$resetLink'>$resetLink</a>";

                $mail->send();
                $message = "<div class='alert alert-success'>Check your email for the reset link!</div>";
            } catch (Exception $e) {
                $message = "<div class='alert alert-danger'>Mailer Error: {$mail->ErrorInfo}</div>";
            }
        } else {
            $message = "<div class='alert alert-danger'>Email not found!</div>";
        }
    } catch (PDOException $e) {
        $message = "DB connection failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body { background-color: #2E3D54; color: white; display: flex; align-items: center; justify-content: center; height: 100vh; }</style>
</head>
<body>
<div class="card p-4 shadow-lg" style="width: 400px; background-color: #1c2633; border:none;">
    <h3 class="text-center mb-3">Forgot Password</h3>
    <?= $message ?>
    <form action="" method="POST">
        <div class="mb-3">
            <input type="email" name="email" class="form-control" required placeholder="Enter email">
        </div>
        <button type="submit" class="btn btn-primary w-100">Send Link</button>
        <div class="text-center mt-3">
            <a href="../templates/login.html.php" class="text-secondary">Back to Login</a>
        </div>
    </form>
</div>
</body>
</html>