<?php
include 'config/session_config.php';
include 'config/config.php';

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$error = null;
$success = null;

// Ambil data pengguna dari database
$stmt = $conn->prepare("SELECT name, email, profile_picture FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    $error = "Pengguna tidak ditemukan.";
}
$stmt->close();

// Perbarui data pengguna jika form dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $profile_picture = $user['profile_picture']; // Default to current picture
    $password_changed = false;

    // Periksa apakah file baru diunggah
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'profile/';
        $file_name = uniqid() . '_' . basename($_FILES['profile_picture']['name']);
        $target_file = $upload_dir . $file_name;

        // Validasi file gambar
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($file_type, $allowed_types)) {
            // Hapus gambar lama jika ada
            if (!empty($user['profile_picture']) && file_exists($user['profile_picture'])) {
                unlink($user['profile_picture']); // Menghapus file lama
            }
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
                $profile_picture = $target_file; // Update path gambar baru
            } else {
                $error = "Gagal mengunggah gambar.";
            }
        } else {
            $error = "Format file tidak valid. Hanya JPG, JPEG, PNG, atau GIF yang diperbolehkan.";
        }
    }

    // Periksa apakah password diubah
    if (!empty($_POST['current_password']) && !empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password !== $confirm_password) {
            $error = "Password baru dan konfirmasi password tidak cocok.";
        } else {
            // Verifikasi password lama
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->bind_result($hashed_password);
            $stmt->fetch();
            $stmt->close();

            if (password_verify($current_password, $hashed_password)) {
                // Update password baru
                $new_hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $new_hashed_password, $user_id);
                if ($stmt->execute()) {
                    $password_changed = true;
                }
                $stmt->close();
            } else {
                $error = "Password lama salah.";
            }
        }
    }

    if (!$error) {
        // Update data pengguna di database
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, profile_picture = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $email, $profile_picture, $user_id);

        if ($stmt->execute()) {
            echo "<script>
                alert('Profil berhasil diperbarui.');
                window.location.href = 'profile.php';
            </script>";
            exit(); // Pastikan tidak ada output lain
        } else {
            $error = "Gagal memperbarui profil: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <title>Edit Profil</title>
    <link rel="stylesheet" href="assets/css/loading.css">
</head>
<body class="bg-light" onload="document.body.classList.add('loaded');">
<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">
            <h3 class="card-title text-center">Edit Profil</h3>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php elseif ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3 text-center">
                    <img src="<?= htmlspecialchars($user['profile_picture']) ?>" 
                         alt="Foto Profil" 
                         class="rounded-circle mb-3" 
                         style="width: 150px; height: 150px; object-fit: cover;">
                    <input type="file" class="form-control" name="profile_picture" accept="image/*">
                </div>
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="name" name="name" 
                           value="<?= htmlspecialchars($user['name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                <hr>
                <h5 class="mb-3">Ubah Password</h5>
                <div class="mb-3">
                    <label for="current_password" class="form-label">Password Lama</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="current_password" name="current_password">
                        <button type="button" class="btn btn-outline-secondary toggle-password" data-target="current_password">👁️</button>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="new_password" class="form-label">Password Baru</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="new_password" name="new_password">
                        <button type="button" class="btn btn-outline-secondary toggle-password" data-target="new_password">👁️</button>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                        <button type="button" class="btn btn-outline-secondary toggle-password" data-target="confirm_password">👁️</button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
                <a href="profile.php" class="btn btn-secondary w-100 mt-2">Batal</a>
            </form>
        </div>
    </div>
</div>

<script src="assets/js/password-toggle.js"></script>
</body>
</html>
