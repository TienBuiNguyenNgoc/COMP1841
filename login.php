<?php
session_start();
include 'config.php';
$pdo = new PDO($dsn, $user, $pass);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $UsernameInput = trim($_POST['Username']);
    $PasswordInput = $_POST['Password'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    
    $stmt->execute([':username' => $UsernameInput]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($PasswordInput, $user['password'])) {
        $_SESSION['userID'] = $user['userID']; 
        $_SESSION['Username'] = $user['username'];
        $_SESSION['Email']    = $user['email'];
        $_SESSION['Avatar']   = $user['avatar'] ?? '';
        $_SESSION['Role']     = $user['role'];

        header('Location: index.php');
        exit;
    } else {
        echo "<p style='color:red'>Incorrect username or password.</p>";
    }
}
?>