<?php
include 'config.php';

$pdo = new PDO($dsn, $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameInput = isset($_POST['Username']) ? trim($_POST['Username']) : '';
    $emailInput    = isset($_POST['Email']) ? trim($_POST['Email']) : '';
    $passwordInput = isset($_POST['Password']) ? $_POST['Password'] : '';

    if ($usernameInput && $emailInput && $passwordInput) {
        $passwordHash = password_hash($passwordInput, PASSWORD_DEFAULT);
        $role = 'user';
        $avatar = 'default_avatar.png';

        try {
            $sql = "INSERT INTO users (username, email, password, role, avatar) 
                    VALUES (:username, :email, :password, :role, :avatar)"; 
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':username' => $usernameInput,
                ':email'    => $emailInput,
                ':password' => $passwordHash,
                ':role'     => $role,
                ':avatar'   => $avatar
            ]);

            header('Location: index.php');
            exit;

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                echo "Username or Email already exists!";
            } else {
                echo "Error: " . $e->getMessage();
            }
        }
    } else {
        echo "Please fill in all the information completely.";
    }
}
?>