<?php
include 'config/session_config.php';
include 'config/config.php';

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Periksa apakah ID tugas diberikan
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ../index.php?error=invalid_task_id');
    exit();
}

$task_id = intval($_GET['id']);

// Periksa apakah tugas milik pengguna
$stmt = $conn->prepare("SELECT id FROM task WHERE id = ? AND teacher_id = ?");
$stmt->bind_param("ii", $task_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Jika tugas tidak ditemukan atau tidak milik pengguna
    header('Location: ../index.php?error=task_not_found');
    exit();
}

// Hapus tugas
$stmt = $conn->prepare("DELETE FROM task WHERE id = ? AND teacher_id = ?");
$stmt->bind_param("ii", $task_id, $user_id);

if ($stmt->execute()) {
    // Redirect dengan pesan sukses
    header('Location: ../index.php?success=task_deleted');
} else {
    // Redirect dengan pesan error
    header('Location: ../index.php?error=delete_failed');
}

$stmt->close();
$conn->close();
?>
