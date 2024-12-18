document.addEventListener('DOMContentLoaded', function() {
    // Fungsi untuk mengecek deadline
    function checkDeadlines() {
        const rows = document.querySelectorAll('#taskTable tr');
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        
        rows.forEach(row => {
            const deadlineCell = row.querySelector('td:nth-child(4)');
            if (deadlineCell) {
                const deadlineDate = new Date(deadlineCell.textContent);
                
                // Reset jam ke 00:00:00 untuk perbandingan yang akurat
                deadlineDate.setHours(0, 0, 0, 0);
                today.setHours(0, 0, 0, 0);
                tomorrow.setHours(0, 0, 0, 0);

                // Jika deadline adalah hari ini atau besok
                if (deadlineDate.getTime() === today.getTime() || 
                    deadlineDate.getTime() === tomorrow.getTime()) {
                    
                    const taskTitle = row.querySelector('td:first-child').textContent;
                    const isToday = deadlineDate.getTime() === today.getTime();
                    
                    showNotification(taskTitle, isToday);
                }
            }
        });
    }

    // Fungsi untuk menampilkan notifikasi
    function showNotification(taskTitle, isToday) {
        // Buat elemen notifikasi
        const notification = document.createElement('div');
        notification.className = 'alert alert-warning alert-dismissible fade show';
        notification.role = 'alert';
        
        // Atur pesan notifikasi
        const message = isToday ? 
            `Tugas "${taskTitle}" jatuh tempo hari ini!` :
            `Tugas "${taskTitle}" akan jatuh tempo besok!`;
        
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

        // Tambahkan notifikasi ke halaman
        const container = document.querySelector('.container');
        container.insertBefore(notification, container.firstChild);
    }

    // Jalankan pengecekan saat halaman dimuat
    checkDeadlines();
});
    document.addEventListener('DOMContentLoaded', () => {
        const filterCategory = document.getElementById('filterCategory');
        const filterPriority = document.getElementById('filterPriority');
        const taskTable = document.getElementById('taskTable');

        function filterTasks() {
            const category = filterCategory.value;
            const priority = filterPriority.value;
            
            Array.from(taskTable.rows).forEach(row => {
                const taskCategory = row.getAttribute('data-category');
                const taskPriority = row.getAttribute('data-priority');
                const matchesCategory = !category || taskCategory === category;
                const matchesPriority = !priority || taskPriority === priority;

                row.style.display = matchesCategory && matchesPriority ? '' : 'none';
            });
        }

        filterCategory.addEventListener('change', filterTasks);
        filterPriority.addEventListener('change', filterTasks);
    });
    function confirmDelete() {
        return confirm("Apakah Anda yakin ingin menghapus tugas ini?");
    }
// Fungsi konfirmasi hapus yang sebelumnya belum ada
function confirmDelete() {
    return confirm('Apakah Anda yakin ingin menghapus tugas ini?');
} 