<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/sidebar.css" rel="stylesheet">
    <title>Sidebar Responsif</title>
</head>
<body>
    <button id="toggle-btn">&#9776;</button>
    <div id="sidebar" class="d-flex flex-column">
        <h6 class="p-3"></h6>
        <nav class="nav flex-column px-3">
            <h4><a href="#" class="nav-link">Task Manager</a></h4>
            <a href="index.php" class="nav-link">Dashboard</a>
            <a href="daftar_tugas.php" class="nav-link">Daftar Tugas</a>
<?php if ($userRole === 'guru'): ?>
            <a href="tugas/add_task.php" class="nav-link">Tambah Tugas</a>
<?php endif; ?>
            <a href="profile.php" class="nav-link">Profil</a>
            <a href="logout.php" class="nav-link text-danger">Logout</a>
        </nav>
    </div>

    <script>
        const toggleBtn = document.getElementById('toggle-btn');
        const sidebar = document.getElementById('sidebar');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
        });
    </script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
