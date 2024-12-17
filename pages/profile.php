<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user = $database->users->findOne(['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])]);

// Handle form submission untuk update profil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updates = [
        'name' => htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8'),
        'email' => filter_var($_POST['email'], FILTER_SANITIZE_EMAIL)
    ];

    // Jika password baru diisi
    if (!empty($_POST['new_password'])) {
        if (password_verify($_POST['current_password'], $user->password)) {
            $updates['password'] = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        } else {
            $error = "Password saat ini tidak sesuai";
        }
    }

    if (!isset($error)) {
        try {
            $result = $database->users->updateOne(
                ['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])],
                ['$set' => $updates]
            );

            if ($result->getModifiedCount() > 0) {
                $success = "Profil berhasil diperbarui";
                $_SESSION['user_name'] = $updates['name'];
                $user = $database->users->findOne(['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])]);
            }
        } catch (Exception $e) {
            $error = "Gagal memperbarui profil";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PagePal - Profile</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/form.css">
    <link rel="stylesheet" href="../assets/css/profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include '../layouts/header.php'; ?>

    <div class="container">
        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-picture-container">
                    <img src="<?php echo !empty($user->profile_picture) ? '../' . $user->profile_picture : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png'; ?>" 
                         alt="Profile Picture" 
                         class="profile-picture">
                    <label for="profile-upload" class="upload-icon">
                        <i class="fas fa-camera"></i>
                    </label>
                    <input type="file" id="profile-upload" class="profile-upload" accept="image/*" style="display: none;">
                </div>
                <h1><?php echo htmlspecialchars($user->name); ?></h1>
                <p class="join-date">
                    <i class="fas fa-calendar-alt"></i>
                    Bergabung sejak <?php echo $user->created_at->toDateTime()->format('d M Y'); ?>
                </p>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST" class="profile-form" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" id="name" name="name" 
                           value="<?php echo htmlspecialchars($user->name); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars($user->email); ?>" required>
                </div>

                <div class="password-section">
                    <h2>
                        <i class="fas fa-lock"></i>
                        Ubah Password
                    </h2>
                    <div class="form-group">
                        <label for="current_password">Password Saat Ini</label>
                        <input type="password" id="current_password" name="current_password">
                    </div>

                    <div class="form-group">
                        <label for="new_password">Password Baru</label>
                        <input type="password" id="new_password" name="new_password">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <?php include '../layouts/footer.php'; ?>
    
    <script>
        document.getElementById('profile-upload').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validasi tipe file
                if (!file.type.startsWith('image/')) {
                    alert('Mohon upload file gambar');
                    return;
                }
                
                // Preview gambar
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelector('.profile-picture').src = e.target.result;
                }
                reader.readAsDataURL(file);
                
                // Upload gambar
                const formData = new FormData();
                formData.append('profile_picture', file);
                
                fetch('upload_profile.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Tampilkan pesan sukses jika perlu
                        console.log('Foto profil berhasil diupload');
                    } else {
                        alert('Gagal mengupload foto profil');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengupload foto');
                });
            }
        });
    </script>
</body>
</html> 