<?php
include '../config/session_config.php';
include '../config/config.php';

// Menggunakan operator ternary untuk pengecekan akses halaman
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guru') {
    header('Location: ../login.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $deadline = trim($_POST['deadline']);
    $user_id = $_SESSION['user_id'];

    // Validasi input
    if (empty($title) || empty($category) || empty($deadline)) {
        $error = 'Harap isi semua bidang yang wajib!';
    } else {
        // Simpan tugas ke tabel task
        $conn->begin_transaction(); // Mulai transaksi
        try {
            $stmt = $conn->prepare("
                INSERT INTO task (teacher_id, title, description, category, deadline) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("issss", $user_id, $title, $description, $category, $deadline);
            $stmt->execute();

            // Ambil ID tugas yang baru dibuat
            $task_id = $stmt->insert_id;

            if (isset($_POST['assign_all'])) {
                // Ambil semua ID siswa dari tabel users
                $student_ids = $conn->query("SELECT id FROM users WHERE role = 'siswa'");
            } else {
                // Gunakan array assign_to dari POST jika tidak memilih semua siswa
                $student_ids = array_map(function($id) { return ['id' => $id]; }, $_POST['assign_to']);
            }

            // Simpan ke tabel student_task
            $stmt = $conn->prepare("
                INSERT INTO student_task (assignment_id, student_id) 
                VALUES (?, ?)
            ");
            foreach ($student_ids as $student) {
                $stmt->bind_param("ii", $task_id, $student['id']);
                $stmt->execute();
            }

            $conn->commit(); // Commit transaksi
            header('Location: ../index.php');
            exit();
        } catch (Exception $e) {
            $conn->rollback(); // Rollback transaksi jika terjadi kesalahan
            $error = 'Gagal menambahkan tugas. Silakan coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/loading.css">
    <title>Tambah Tugas</title>
</head>
<body onload="document.body.classList.add('loaded');"></body>
<div class="container mt-5">
    <h1 class="mb-4">Tambah Tugas Baru</h1>
    <a href="../index.php" class="btn btn-secondary mb-3">Kembali ke Dashboard</a>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="title" class="form-label">Nama Tugas *</label>
            <input type="text" id="title" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Deskripsi</label>
            <textarea id="description" name="description" class="form-control" rows="3"></textarea>
        </div>
        <div class="mb-3">
            <label for="category" class="form-label">Kategori *</label>
            <select id="category" name="category" class="form-select" required>
                <option value="">Pilih Kategori</option>
                <option value="tugas">Tugas</option>
                <option value="project">Project</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="deadline" class="form-label">Deadline *</label>
            <input type="datetime-local" id="deadline" name="deadline" class="form-control" required>
        </div>
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="assign_all" name="assign_all">
                <label class="form-check-label" for="assign_all">
                    Berikan tugas ini kepada semua siswa
                </label>
            </div>
        </div>
        <div class="mb-3">
            <label for="assign_to" class="form-label">Pilih Siswa *</label>
            <select id="assign_to" name="assign_to[]" class="form-select" multiple required>
                <?php
                // Ambil daftar siswa dari database
                $students = $conn->query("SELECT id, name FROM users WHERE role = 'siswa'");
                while ($student = $students->fetch_assoc()):
                ?>
                    <option value="<?= $student['id'] ?>"><?= htmlspecialchars($student['name']) ?></option>
                <?php endwhile; ?>
            </select>
            <small class="text-muted">Tahan CTRL untuk memilih beberapa siswa.</small>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Tugas</button>
    </form>
</div>
</body>
</html>
