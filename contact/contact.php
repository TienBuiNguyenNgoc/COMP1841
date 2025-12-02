<?php
session_start();

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include '../config.php'; 
$pdo = new PDO($dsn, $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_SESSION["Email"])) {
        die("You are not logged in.");
    }

    $senderEmail = $_SESSION["Email"];
    $stmtUser = $pdo->prepare("SELECT userID FROM users WHERE email = :email");
    $stmtUser->execute([':email' => $senderEmail]);
    $currentUser = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if ($currentUser) {
        $userID = $currentUser['userID'];

        $to      = $_POST["to"];
        $message = $_POST["message"];
        $imageName = uploadImage($_FILES['image']);

        sendEmail($to, $message, $imageName);
        saveToDatabase($pdo, $userID, $senderEmail, $message, $imageName);

        header('Location:../index.php');
        exit();
    } else {
        echo "DB connection failed: ";
    }
}

function uploadImage($file)
{
    if (empty($file['name'])) {
        return null; 
    }

    $uploadDir = '../upload/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $imageName = time() . "_" . basename($file['name']);
    $targetPath = $uploadDir . $imageName;

    if (move_uploaded_file($file["tmp_name"], $targetPath)) {
        return $imageName;
    }
    return null;
}

function sendEmail($to, $message, $imageName)
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'boscortg@gmail.com'; 
        $mail->Password   = 'bdxa fbdf rqvz vixd'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
        $mail->Port       = 587;

        $mail->setFrom('boscortg@gmail.com', 'Admin System'); 
        $mail->addAddress($to);

        if ($imageName) {
            $mail->addAttachment("../upload/" . $imageName);
        }

        $mail->isHTML(true); 
        $mail->Subject = "New Message Notification";
        $mail->Body    = nl2br($message); 
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function saveToDatabase($pdo, $userID, $email, $message, $image)
{
    $sql = "INSERT INTO contacts (userID, sender_email, message, image, sent_at) 
            VALUES (:userID, :email, :message, :image, NOW())";
            
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':userID'  => $userID,  
        ':email'   => $email,
        ':message' => $message,
        ':image'   => $image
    ]);
}
?>