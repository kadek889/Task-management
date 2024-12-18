<?php
include 'config/session_config.php';
include 'config/config.php';

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'siswa') {
    echo "<script>
            alert('Anda bukan siswa');
            window.location.href = 'index.php';
          </script>";
    exit();
}

$student_id = $_SESSION['user_id'];
$assignment_id = $_GET['id'] ?? null;

if ($assignment_id) {
    // Ambil detail tugas
    $query = "SELECT * FROM task WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $assignment_id);
    $stmt->execute();
    $task = $stmt->get_result()->fetch_assoc();

    // Periksa apakah form telah disubmit
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $submission_text = $_POST['submission_text'] ?? '';
        $submission_file = $_FILES['submission_file']['name'] ?? null;

        // Proses upload file jika ada
        if ($submission_file) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($submission_file);
            move_uploaded_file($_FILES['submission_file']['tmp_name'], $target_file);
        }

        // Simpan jawaban ke database
        $query = "UPDATE student_task SET submission_file = ?, submission_text = ?, status = 'Selesai' WHERE assignment_id = ? AND student_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssii", $submission_file, $submission_text, $assignment_id, $student_id);
        $stmt->execute();

        echo "Tugas berhasil disubmit!";
    }
} else {
    echo "Tugas tidak ditemukan.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/loading.css">
    <title>Kerjakan Tugas</title>
</head>
<body class="bg-light" onload="document.body.classList.add('loaded');">
<div class="container mt-5">
    <h1 class="mb-4">Kerjakan Tugas: <?= htmlspecialchars($task['title']) ?></h1>
    <p><?= htmlspecialchars($task['description']) ?></p>
    <form action="" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="submission_text" class="form-label">Deskripsi:</label>
            <textarea name="submission_text" id="submission_text" class="form-control" rows="5"></textarea>
        </div>
        <div class="mb-3">
            <label for="submission_file" class="form-label">Unggah File:</label>
            <input type="file" name="submission_file" id="submission_file" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
</body>
</html> 