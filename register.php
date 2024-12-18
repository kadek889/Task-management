<?php
include 'config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $profile_picture = 'profile/lutung.jpg'; // Default profile picture

    // Proses file gambar jika diunggah
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'profile/';
        $filename = time() . '_' . basename($_FILES['profile_picture']['name']);
        $target_file = $upload_dir . $filename;

        // Validasi dan pindahkan file
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (in_array($file_type, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
                $profile_picture = $target_file;
            } else {
                $error = "Gagal mengunggah gambar.";
            }
        } else {
            $error = "Format file tidak didukung. Gunakan JPG, PNG, atau GIF.";
        }
    }

    // Masukkan data ke database jika tidak ada error
    if (!isset($error)) {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, profile_picture) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $password, $profile_picture);

        if ($stmt->execute()) {
            header('Location: login.php');
            exit();
        } else {
            $error = "Registrasi gagal: " . $stmt->error;
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
    <title>Register</title>
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 500px;">
        <div class="card-body">
            <h3 class="card-title text-center">Register</h3>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST" action="" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="name" class="form-label">Nama Lengkap</label>
        <input type="text" class="form-control" id="name" name="name" required>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <div class="mb-3">
        <label for="confirm_password" class="form-label">Konfirmasi Password</label>
        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
    </div>
    <div class="mb-3">
        <label for="profile_picture" class="form-label">Foto Profil</label>
        <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
    </div>
    <button type="submit" class="btn btn-primary w-100">Daftar</button>
    <p class="text-center mt-3">Sudah punya akun? <a href="login.php">Login</a></p>
</form>

        </div>
    </div>
</div>
</body>
</html>
