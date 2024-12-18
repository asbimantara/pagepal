<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
        $password = $_POST['password'];

        $user = $database->users->findOne(['email' => $email]);

        if ($user && password_verify($password, $user->password)) {
            $_SESSION['user_id'] = (string)$user->_id;
            $_SESSION['user_name'] = $user->name;
            header("Location: ../index.php");
            exit();
        } else {
            $error = "Email atau password salah!";
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PagePal</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <img src="https://cdn-icons-png.flaticon.com/512/2232/2232688.png" 
                     alt="PagePal Logo" 
                     class="auth-logo">
                <h1>Masuk ke PagePal</h1>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-actions">
                    <button type="submit">Masuk</button>
                </div>

                <p class="auth-links">
                    Belum punya akun? <a href="register.php">Daftar di sini</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html> 