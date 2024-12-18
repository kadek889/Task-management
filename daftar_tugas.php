<?php
include 'config/session_config.php';
include 'config/config.php';

// Ambil data dari tabel student_task
$user_id = $_SESSION['user_id']; // Pastikan user_id disimpan dalam sesi saat login
$sql = "SELECT st.id, u.name, st.submission_file, st.submission_text, st.status 
        FROM student_task st 
        JOIN users u ON st.student_id = u.id 
        WHERE st.student_id = $user_id"; // Menambahkan kondisi untuk ID pengguna
$result = mysqli_query($conn, $sql);
$tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Cek jika ada permintaan hapus
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    // Hapus file di folder
    $query = "SELECT submission_file FROM student_task WHERE id = $id"; // Ganti dengan nama tabel yang sesuai
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    if (isset($row['submission_file']) && file_exists('uploads/' . $row['submission_file'])) {
        unlink('uploads/' . $row['submission_file']);
    }

    // Hapus data di database
    $query = "DELETE FROM student_task WHERE id = $id"; // Ganti dengan nama tabel yang sesuai
    mysqli_query($conn, $query);
    header('Location: daftar_tugas.php'); // Redirect kembali ke halaman daftar tugas
    exit; // Pastikan untuk menghentikan eksekusi setelah redirect
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/sidebar.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/loading.css">
    <title>Daftar Tugas</title>
</head>
<body onload="document.body.classList.add('loaded');"></body>
    <?php include 'sidebar.php'; ?>
    <div class="container mt-5">
    <h1 class="mb-4">Daftar Tugas</h1>
                <?php include 'filter.php'; ?>
                <table class="table table-bordered" id="taskTable">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Submission File</th>
                            <th>Submission Text</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="taskTable">
                        <?php
                        // Logika untuk filter
                        $search = isset($_GET['search']) ? $_GET['search'] : '';
                        $sql = "SELECT st.id, u.name, st.submission_file, st.submission_text, st.status 
                                FROM student_task st 
                                JOIN users u ON st.student_id = u.id 
                                WHERE u.name LIKE '%$search%'"; // Menambahkan filter pencarian
                        $result = mysqli_query($conn, $sql);
                        $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);

                        foreach ($tasks as $task): ?>
                        <tr data-category="<?php echo htmlspecialchars($task['category'] ?? ''); ?>" 
                            data-priority="<?php echo htmlspecialchars($task['priority'] ?? ''); ?>" 
                            data-status="<?php echo htmlspecialchars($task['status'] ?? ''); ?>">
                            <td><?php echo htmlspecialchars($task['name'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($task['submission_file'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($task['submission_text'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($task['status'] ?? '-'); ?></td>
                            <td>
                                <a href="uploads/<?php echo htmlspecialchars($task['submission_file']); ?>" class="btn btn-primary btn-sm" target="_blank">Unduh</a>
                                <a href="?hapus=<?php echo $task['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data ini?');">Hapus</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html> 