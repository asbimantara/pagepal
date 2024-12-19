<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Cek email sudah terdaftar
    $existingUser = $database->users->findOne(['email' => $email]);
    
    if ($existingUser) {
        $error = "Email sudah terdaftar!";
    } else {
        $result = $database->users->insertOne([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'created_at' => new MongoDB\BSON\UTCDateTime(),
            'books' => []
        ]);

        if ($result->getInsertedCount() > 0) {
            $_SESSION['user_id'] = (string)$result->getInsertedId();
            $_SESSION['user_name'] = $name;
            header("Location: login.php");
            exit();
        } else {
            $error = "Gagal mendaftar. Silakan coba lagi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - PagePal</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <img src="https://cdn-icons-png.flaticon.com/512/2232/2232688.png" 
                     alt="PagePal Logo" 
                     class="auth-logo"
                     onerror="this.onerror=null; this.src='../assets/images/logo.png';">
                <h1>Daftar PagePal</h1>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-actions">
                    <button type="submit">Daftar Sekarang</button>
                </div>

                <p class="auth-links">
                    Sudah punya akun? <a href="login.php">Masuk di sini</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html> 