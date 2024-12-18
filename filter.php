<div class="mb-3 d-flex gap-3">
    <div class="w-50">
        <label for="filterCategory" class="form-label">Filter Kategori</label>
        <select id="filterCategory" class="form-select">
            <option value="">Semua Kategori</option>
            <option value="tugas">Tugas</option>
            <option value="project">Project</option>
        </select>
    </div>
    <div class="w-50">
        <label for="filterPriority" class="form-label">Filter Prioritas</label>
        <select id="filterPriority" class="form-select">
            <option value="">Semua Prioritas</option>
            <option value="Tinggi">Tinggi</option>
            <option value="Sedang">Sedang</option>
            <option value="Rendah">Rendah</option>
        </select>
    </div>
    <div class="w-50">
        <label for="filterStatus" class="form-label">Filter Status</label>
        <select id="filterStatus" class="form-select">
            <option value="">Semua Status</option>
            <option value="Belum Dikerjakan">Belum Dikerjakan</option>
            <option value="Selesai">Selesai</option>
        </select>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const filterStatus = document.getElementById('filterStatus');
        const filterCategory = document.getElementById('filterCategory');
        const filterPriority = document.getElementById('filterPriority');
        const taskTable = document.getElementById('taskTable');

        function filterTasks() {
            const category = filterCategory.value;
            const priority = filterPriority.value;
            const status = filterStatus.value;
            
            Array.from(taskTable.rows).forEach(row => {
                const taskCategory = row.getAttribute('data-category');
                const taskPriority = row.getAttribute('data-priority');
                const taskStatus = row.getAttribute('data-status');
                const matchesCategory = !category || taskCategory === category;
                const matchesPriority = !priority || taskPriority === priority;
                const matchesStatus = !status || taskStatus === status;

                row.style.display = matchesCategory && matchesPriority && matchesStatus ? '' : 'none';
            });
        }

        filterCategory.addEventListener('change', filterTasks);
        filterPriority.addEventListener('change', filterTasks);
        filterStatus.addEventListener('change', filterTasks);
    });
</script> 