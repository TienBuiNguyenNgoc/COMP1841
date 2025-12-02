<?php
session_start();
include '../config.php';

$pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (empty($_SESSION['Role']) || $_SESSION['Role'] !== 'admin') {
    die("Access denied.");
}

$id = $_GET['contactID'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM contacts WHERE contactID = :contactID");
$stmt->execute([':contactID' => $id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Message not found!");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $reply = trim($_POST['reply_message']);

    $update = $pdo->prepare("
        UPDATE contacts
        SET reply_message = :reply, reply_date = NOW()
        WHERE contactID = :contactID
    ");

    $update->execute([
        ':reply' => $reply,
        ':contactID'    => $id
    ]);

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'boscortg@gmail.com';  
        $mail->Password   = 'bdxa fbdf rqvz vixd'; 
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('Your@gmail.com', 'Admin');
        $mail->addAddress($data['sender_email']);

        $mail->Subject = "Reply to your message";
        $mail->Body    = $reply;

        $mail->send();

    } catch (Exception $e) {
        echo "Email failed: " . $mail->ErrorInfo;
        exit;
    }

    header("Location: ../admin/manage_contact.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reply Message</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #0F1C2C; 
            color: #fff;
        }
        .underline-nav {
            display: flex;
            gap: 30px;
        }
        .underline-nav a {
            position: relative;
            color: #fff;
            text-decoration: none;
            font-size: 18px;
            padding: 10px 0;
            transition: .5s ease;
        }
        .underline-nav a:hover {
            color: #FF004D; 
        }
        .underline-nav a::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 3px;
            background-color: #FF004D;
            transition: width 0.3s ease;
        }
        .underline-nav a:hover::after {
            width: 100%;
        } 
        .message-container {
            background-color: #2E3D54;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<nav class="underline-nav px-4" style="background-color: #2E3D54; align-items:center;">
    <img src="../img_bg/Logo.png" alt="Logo" width="200" height="40">
    <a href="../index.php">Home</a>
    <a href="../admin/manage_contact.php">Back to Messages</a>
</nav>

<div class="container mt-5">
    <h3 class="mb-4 text-warning">Reply to Message #<?= htmlspecialchars($data['contactID']) ?></h3>
    
    <div class="message-container">
        <p class="text-info mb-1"><strong><i class="bi bi-person-fill"></i> Sender:</strong> <?= htmlspecialchars($data['sender_email']) ?></p>

        <p class="text-light mt-3 mb-1"><strong>Original Message:</strong></p>
        <p class="text-white ps-3 border-start border-light border-3"><?= nl2br(htmlspecialchars($data['message'])) ?></p>

        <?php if (!empty($data['image'])): ?>
            <p class="mb-0"><strong>Attached Image:</strong></p>
            <img src="../upload/<?= htmlspecialchars($data['image']) ?>" width="150" height="100" style="object-fit:cover;" class="img-fluid rounded mt-2 border border-secondary">
        <?php endif; ?>
    </div>

    <h4 class="text-light mb-3">Send Your Reply</h4>
    <form method="POST">
        <div class="mb-3">
            <label for="reply_message" class="form-label">Reply Message:</label>
            <textarea name="reply_message" id="reply_message" class="form-control bg-dark text-white border-secondary" required rows="6"></textarea>
        </div>
        
        <a href="../admin/manage_contact.php" class="btn btn-secondary me-2">Cancel</a>
        <button type="submit" class="btn btn-primary"><i class="bi bi-send-fill"></i> Send Reply</button>
    </form>
</div>

</body>
</html>
