<?php
session_start();
include 'config.php'; 

$message = "";
$validToken = false;

$email = $_GET['email'] ?? '';
$token = $_GET['token'] ?? '';

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("
        SELECT reset_token, reset_expires, NOW() as server_time FROM users WHERE Email = :email
    ");
    $stmt->execute([':email' => $email]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$userData) {
        $message = "<div class='alert alert-danger'>The email in the link does not exist.</div>";
    } else {
        $dbToken = $userData['reset_token'];
        $dbExpires = $userData['reset_expires'];
        $serverTime = $userData['server_time'];

        if ($dbToken === null) {
             $message = "<div class='alert alert-danger'>This link has already been used. Please create a new request.</div>";
        }
        elseif ($dbToken !== $token) {
            $message = "<div class='alert alert-danger'>
                            Invalid link.<br>
                            You may have requested a reset multiple times.<br>
                            Please check <b>latest email</b>.
                        </div>";
        }
        elseif (strtotime($dbExpires) < strtotime($serverTime)) {
            $message = "<div class='alert alert-danger'>
                            The link has expired.<br>
                            Expires at: $dbExpires <br>
                            Server time: $serverTime <br>
                            Please try again.
                        </div>";
        }
        else {
            $validToken = true;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
        $newPass = $_POST['new_pass'];
        $hashedPass = password_hash($newPass, PASSWORD_DEFAULT); 
        
        $update = $pdo->prepare("
            UPDATE users 
            SET Password = :pass, 
                reset_token = NULL, 
                reset_expires = NULL 
            WHERE Email = :email
        ");
        $update->execute([':pass' => $hashedPass, ':email' => $email]);

        $message = "<div class='alert alert-success'>
                        Password changed successfully!<br>
                        <a href='templates/login.html.php' class='alert-link'>Click here to log in</a>
                    </div>";
        $validToken = false;
    }

} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #2E3D54; color: white; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .card { background-color: #1c2633; border: none; color: white; }
        .form-control { background-color: #2b3a4a; border: 1px solid #4a5c70; color: white; }
        .form-control:focus { background-color: #35475a; color: white; border-color: #6c757d; }
    </style>
</head>
<body>

<div class="card p-4 shadow-lg" style="width: 400px;">
    <h3 class="text-center mb-3">New Password</h3>
    
    <?= $message ?>

    <?php if ($validToken): ?>
    <form action="" method="POST">
        <div class="mb-3">
            <label class="form-label">Enter new password:</label>
            <input type="password" name="new_pass" class="form-control" placeholder="New Password" required minlength="6">
        </div>
        <button type="submit" class="btn btn-success w-100 fw-bold">Update Password</button>
    </form>
    <?php endif; ?>
</div>

</body>
</html>