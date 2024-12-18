<?php
include 'config/session_config.php';
include 'config/config.php';
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Hapus akun pengguna dari database
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    // Hapus sesi pengguna dan logout
    session_destroy();
    header('Location: register.php?message=Akun Anda berhasil dihapus.');
    exit();
} else {
    echo "Gagal menghapus akun: " . $stmt->error;
}
$stmt->close();
?>
