<?php
include 'config/session_config.php';
include 'config/config.php';

if ($isLoggedIn) {
    $user_id = $_SESSION['user_id'];

    // Query untuk mengambil data tugas berdasarkan role pengguna
    if ($userRole == 'guru') {
        $query = "SELECT id, title, description, category, priority, deadline FROM task WHERE teacher_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
    } else if ($userRole == 'siswa') {
        // Query untuk mengambil data tugas yang belum dikerjakan
        $query = "SELECT t.id, t.title, t.description, t.category, t.priority, t.deadline 
                  FROM task t 
                  LEFT JOIN student_task st ON t.id = st.assignment_id 
                  WHERE st.student_id = ? AND (st.status IS NULL OR st.status = 'Belum Dikerjakan')";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
    } else {
        $query = "SELECT id, title, description, category, priority, deadline FROM task";
        $stmt = $conn->prepare($query);
    }

    $stmt->execute();
    $result = $stmt->get_result(); // Hasil query dimasukkan ke dalam variabel $result

    // Ambil semua tugas dari database
    $assignments = $result->fetch_all(MYSQLI_ASSOC);

    // Inisialisasi $tasks dengan data yang sama dengan $assignments untuk contoh ini
    $tasks = $assignments; // Pastikan ini diadaptasi sesuai kebutuhan logika aplikasi Anda
} else {
    $result = null; // Jika pengguna belum login, tidak ada data tugas yang diambil
    $assignments = [];
    $tasks = []; // Inisialisasi $tasks sebagai array kosong jika belum login
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/loading.css">
</head>
<body onload="document.body.classList.add('loaded');"></body>
<?php include 'sidebar.php'; ?>
<div class="container mt-5">
    <h1 class="mb-4">Dashboard</h1>
    <?php if ($_SESSION['role'] == 'guru'): ?>
     <h4>TUGAS YANG DIBERIKAN</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tugas</th>
                <th>Kategori</th>
                <th>Prioritas</th>
                <th>Deadline</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="taskTable">
            <?php foreach ($assignments as $task): ?>
                <tr>
                    <td><?= htmlspecialchars($task['title']) ?></td>
                    <td><?= htmlspecialchars($task['category']) ?></td>
                    <td><?= htmlspecialchars($task['priority']) ?></td>
                    <td><?= htmlspecialchars($task['deadline']) ?></td>
                    <td>
                        <?php if ($userRole == 'guru'): ?>
                            <a href="../tugas/edit_task.php?id=<?= $task['id'] ?>" class="btn btn-warning">Edit</a>
                            <a href="delete_task.php?id=<?= $task['id'] ?>" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus tugas ini?')">Hapus</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
    <div class="section">
        <h4>Daftar Tugas yang harus dikerjakan</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Deskripsi</th>
                    <th>Deadline</th>
                    <th>Detail</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($tasks) > 0): ?>
                    <?php foreach ($tasks as $task): ?>
                        <tr>
                            <td><?= htmlspecialchars($task['title']) ?></td>
                            <td><?= htmlspecialchars($task['description']) ?></td>
                            <td><?= htmlspecialchars($task['deadline']) ?></td>
                            <td>
                                <!-- Tombol Kerjakan -->
                                <a href="work_on_task.php?id=<?= $task['id'] ?>" class="btn btn-primary">Selengkapnya</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">Belum ada tugas yang diberikan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.js"></script>
<script src="assets/js/dashboard.js"></script>
</body>
</html>
