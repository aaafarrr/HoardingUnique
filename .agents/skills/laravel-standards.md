# STANDAR PENGEMBANGAN LARAVEL (PROJECT SKILLS)

## PERAN AGEN
Anda bertindak sebagai Senior Backend Engineer Laravel. Tugas Anda adalah menghasilkan kode yang aman, efisien, dan siap digunakan oleh banyak orang secara konsisten.

## ATURAN ARSITEKTUR KODE
Setiap kali Anda membuat atau memodifikasi kode di project ini, wajib mematuhi struktur berikut:
1. Thin Controllers: Controller hanya boleh menerima input request dan mengembalikan response/view.
2. Service Layer Pattern: Semua logika bisnis, proses data, dan query kompleks wajib ditaruh di dalam Service Class terpisah di folder `app/Services/`.
3. Form Request Validation: Gunakan `php artisan make:request` untuk validasi input. Jangan menulis logika validasi langsung di Controller.
4. Pendekatan Vanilla: Prioritaskan fitur dan library bawaan Laravel. Jangan menginstal package pihak ketiga (vendor) tambahan kecuali diinstruksikan.

## INSTRUKSI EKSEKUSI
* Sebelum membuat file baru, periksa apakah file serupa sudah ada untuk menghindari duplikasi.
* Jika ada error setelah Anda menulis kode, periksa file log di `storage/logs/laravel.log` sebelum mencoba memperbaikinya.

## LOGIKA PEMBAGIAN PROSES (SERVICE PATTERN)
Untuk memastikan kode bersih, ikuti pedoman pemisahan proses berikut saat menulis kode baru:

### 1. KAPAN MEMBUAT FILE SERVICE? (SYARAT)
Buat file service baru jika tugas yang diminta **melibatkan lebih dari satu langkah kompleks**. Contohnya:
* Menggabungkan data dari database, API Eksternal, dan file Log.
* Melakukan kalkulasi atau validasi data yang memerlukan beberapa variabel input.
* Mengupdate banyak tabel yang saling terkait (Relasi).
* Memerlukan validasi data ganda (User & Data Transaksi).

**Format Nama File Service:**
`InputValidationService.php` (Fokus pada validasi/aturan input)
`OrderProcessingService.php` (Fokus pada logika bisnis)
`DataSynchronizationService.php` (Fokus pada proses gabungan data)

### 2. KAPAN TIDAK PERLU SERVICE? (PENGECUALIAN)
Anda **boleh langsung menulis kode di Controller** jika tugas sangat sederhana, yaitu:
* Hanya mengambil data dari database dengan satu tabel (`User::all()`).
* Query sederhana menggunakan Eloquent (`User::where('id', $id)->first()`).
* Tugas hanya memakan 1-2 baris kode logis.