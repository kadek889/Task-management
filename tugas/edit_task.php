<?php
include '../config/session_config.php';
include '../config/config.php';

// Pengecekan akses halaman
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guru') {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$error = null;
$success = null;

// Periksa apakah ID tugas tersedia
$task_id = intval($_GET['id']);

// Ambil data tugas berdasarkan ID
$stmt = $conn->prepare("SELECT * FROM task WHERE id = ? AND teacher_id = ?");
$stmt->bind_param("ii", $task_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $error = "Tugas tidak ditemukan atau Anda tidak memiliki akses.";
} else {
    $task = $result->fetch_assoc();
}
$stmt->close();

// Proses pembaruan tugas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $category = htmlspecialchars($_POST['category']);
    $priority = $_POST['priority'];
    $deadline = $_POST['deadline'];

    // Validasi data
    if (empty($title) || empty($description) || empty($category) || empty($priority) || empty($deadline)) {
        $error = "Semua kolom wajib diisi.";
    } else {
        // Update tugas di database
        $stmt = $conn->prepare("UPDATE task SET title = ?, description = ?, category = ?, priority = ?, deadline = ? WHERE id = ? AND teacher_id = ?");
        $stmt->bind_param("ssssssi", $title, $description, $category, $priority, $deadline, $task_id, $user_id);

        if ($stmt->execute()) {
            $success = "Tugas berhasil diperbarui.";
            // Redirect ke dashboard setelah beberapa detik
            echo "<script>
                    alert('Tugas berhasil diperbarui.');
                    window.location.href = '../index.php';
                  </script>";
            exit();
        } else {
            $error = "Gagal memperbarui tugas: " . $stmt->error;
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Edit Tugas</title>
    <link rel="stylesheet" href="../assets/css/loading.css">
</head>
<body class="bg-light" onload="document.body.classList.add('loaded');"></body>
<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">
            <h3 class="card-title text-center">Edit Tugas</h3>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php elseif ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="title" class="form-label">Nama Tugas</label>
                    <input type="text" class="form-control" id="title" name="title" 
                           value="<?= htmlspecialchars($task['title']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Deskripsi Tugas</label>
                    <textarea class="form-control" id="description" name="description" required><?= htmlspecialchars($task['description']) ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="category" class="form-label">Kategori</label>
                    <select class="form-select" id="category" name="category" required>
                        <option value="Tugas" <?= $task['category'] === 'tugas' ? 'selected' : '' ?>>Tugas</option>
                        <option value="Project" <?= $task['category'] === 'project' ? 'selected' : '' ?>>Project</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="priority" class="form-label">Prioritas</label>
                    <select class="form-select" id="priority" name="priority" required>
                        <option value="Tinggi" <?= $task['priority'] === 'Tinggi' ? 'selected' : '' ?>>Tinggi</option>
                        <option value="Sedang" <?= $task['priority'] === 'Sedang' ? 'selected' : '' ?>>Sedang</option>
                        <option value="Rendah" <?= $task['priority'] === 'Rendah' ? 'selected' : '' ?>>Rendah</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="deadline" class="form-label">Deadline</label>
                    <input type="datetime-local" class="form-control" id="deadline" name="deadline" 
                           value="<?= htmlspecialchars($task['deadline']) ?>" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
                <a href="../index.php" class="btn btn-secondary w-100 mt-2">Batal</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
