# Task Manager

## Deskripsi Singkat
Task Manager adalah aplikasi berbasis web yang dirancang untuk memfasilitasi pengelolaan tugas antara guru dan siswa. Guru dapat menambahkan, mengedit, dan menghapus tugas, sementara siswa dapat melihat dan mengerjakan tugas yang diberikan kepada mereka.

## Cara Kerja Sistem
Sistem ini bekerja dengan cara berikut:
1. **Autentikasi Pengguna**: Pengguna harus login untuk mengakses sistem. Terdapat dua jenis pengguna, yaitu `guru` dan `siswa`.
2. **Manajemen Tugas**:
   - **Guru** dapat menambahkan tugas baru, mengedit tugas yang ada, dan menghapus tugas. Tugas dapat diberikan kepada semua siswa atau siswa tertentu.
   - **Siswa** dapat melihat daftar tugas yang harus dikerjakan dan mengirimkan hasil pekerjaan mereka.
3. **Notifikasi**: Sistem memberikan notifikasi kepada siswa jika ada tugas yang jatuh tempo hari ini atau besok.

## Cara Penggunaan
### Instalasi
1. Pastikan Anda memiliki server web dan database MySQL yang berjalan.
2. Clone repositori ini:
   ```bash
   git clone [URL repositori]
   ```
3. Konfigurasi database di `config/config.php` dengan kredensial database Anda.
4. Import file `task_manager.sql` ke dalam database MySQL Anda.

### Menjalankan Sistem
1. Jalankan server web Anda dan akses aplikasi melalui browser.
2. Login dengan akun yang sudah terdaftar atau buat akun baru melalui halaman registrasi.

### Fitur Utama
- **Tambah Tugas**: Guru dapat menambahkan tugas baru dengan mengisi form yang tersedia.
- **Edit Tugas**: Guru dapat mengedit tugas yang sudah ada.
- **Hapus Tugas**: Guru dapat menghapus tugas yang tidak diperlukan lagi.
- **Kerjakan Tugas**: Siswa dapat melihat detail tugas dan mengirimkan hasil pekerjaan mereka.
- **Profil Pengguna**: Pengguna dapat melihat dan mengedit profil mereka.

## Struktur Database
- **users**: Menyimpan data pengguna termasuk nama, email, password, dan peran.
- **task**: Menyimpan data tugas yang dibuat oleh guru.
- **student_task**: Menyimpan data tugas yang diberikan kepada siswa dan status pengerjaannya.

## Kontribusi
Jika Anda ingin berkontribusi pada proyek ini, silakan buat pull request atau hubungi kami melalui [informasi kontak] yang dapat diakses di https://github.com/kadek889 .

## Tujuan
Proyek ini dibuat sebagai bentuk penugasan akhir satu mata kulaih di kampus kami "Universitas Dipa Makassar". Hubungi kontak untuk detail lebih lanjut.
