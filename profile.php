<?php
include 'config/session_config.php';
include 'config/config.php';

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data pengguna dari database
$stmt = $conn->prepare("SELECT name, email, profile_picture, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    $error = "Pengguna tidak ditemukan.";
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/loading.css">
    <title>Profil Pengguna</title>
</head>
<body class="bg-light" onload="document.body.classList.add('loaded');">
<?php include 'sidebar.php'; ?>
<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">
            <h3 class="card-title text-center">Profil Pengguna</h3>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php else: ?>
                <div class="text-center mb-4">
                    <img src="<?= htmlspecialchars($user['profile_picture']) ?>" 
                         alt="Foto Profil" 
                         class="rounded-circle" 
                         style="width: 150px; height: 150px; object-fit: cover;">
                </div>
                <table class="table">
                    <tr>
                        <th>Nama Lengkap:</th>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                    </tr>
                    <tr>
                        <th>Bergabung Sejak:</th>
                        <td><?= htmlspecialchars(date("d M Y", strtotime($user['created_at']))) ?></td>
                    </tr>
                </table>
                <div class="text-center mt-3">
                    <a href="edit_profile.php" class="btn btn-warning">Edit Profil</a>
                    <a href="logout.php" class="btn btn-danger">Logout</a>
                </div>
                <div class="text-center mt-3">
                    <a href="delete_account.php" class="btn btn-danger"
                       onclick="return confirm('Apakah Anda yakin ingin menghapus akun ini? Semua data Anda akan hilang.');">
                       Hapus Akun
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
