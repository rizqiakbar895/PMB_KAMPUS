SELMA UB - REDESAIN PORTAL + CETAK KARTU MAHASISWA BARU

FITUR BARU
1. Landing page publik dengan gaya portal penerimaan universitas, responsif.
2. Admin > Daftar Ulang menampilkan tombol "Cetak Kartu" untuk status daftar ulang Selesai.
3. Kartu menampilkan nama, nomor test, asal sekolah, data lahir, email, tanggal daftar ulang, dan NIM.
4. Tombol "Cetak / Simpan PDF" membuka dialog print browser.

CARA MEMASANG
1. Backup folder PMB lama terlebih dahulu.
2. Extract ZIP ini. File project ada langsung di folder hasil extract.
3. Copy/replace index.php, css/style.css, admin/daftar_ulang.php, admin/dashboard.php.
4. Tambahkan file baru admin/kartu_mahasiswa.php ke folder admin project yang sedang dipakai.
5. Jangan import database.sql lagi jika database pmb sudah berjalan. Perubahan ini memakai tabel yang sudah ada.
6. Buka http://localhost/NAMA_FOLDER/ untuk melihat landing page.
7. Login admin, buka Daftar Ulang, lalu klik Cetak Kartu pada baris dengan status Selesai.
8. Pada halaman kartu klik Cetak / Simpan PDF. Pilih printer atau "Save as PDF".

CATATAN
- Kartu hanya tersedia untuk entri daftar_ulang dengan status "Selesai".
- Desain memakai identitas generik SELMA UB sebagai inspirasi portal universitas, bukan logo resmi universitas tertentu.

TEMA SERAGAM
CSS telah diperbarui agar dashboard user, dashboard admin, halaman login/registrasi,
dan komponen umum memakai palet yang konsisten dengan halaman utama: navy, emas, dan ivory.
Ganti file css/style.css pada project yang sedang digunakan, atau gunakan seluruh project
ini setelah melakukan backup.

FOTO KAMPUS
Foto kampus yang diberikan telah dipasang pada hero halaman utama.
Lokasi gambar: assets/images/kampus.jpg
Untuk mengganti foto lagi, ganti file tersebut dengan foto baru dan gunakan nama yang sama.

FITUR JURUSAN
- Halaman utama memiliki section Program Studi.
- Form registrasi mewajibkan calon mahasiswa memilih jurusan.
- Jurusan tersimpan pada calon_maba dan bisa diubah melalui Data Diri.
- Admin dapat melihat/mengedit jurusan calon mahasiswa.
- Jurusan tampil pada daftar ulang dan kartu mahasiswa baru.

PENTING UNTUK DATABASE YANG SUDAH ADA
Jangan import database.sql lagi karena itu akan menghapus tabel lama (DROP TABLE).
Buka phpMyAdmin > pilih database pmb > tab SQL, lalu jalankan isi file:
tambah_kolom_jurusan.sql
Jalankan hanya satu kali. Jika kolom jurusan sudah ada, jangan jalankan ulang.
Setelah itu replace file project sesuai struktur ZIP atau salin file yang berubah.

FITUR TAMBAHAN ALA WEBSITE KAMPUS
- Bagian Berita & Pengumuman dinamis pada landing page.
- Admin dapat membuat, mengedit, menghapus, dan menerbitkan pengumuman.
- Status Draft tidak ditampilkan kepada calon mahasiswa; status Publik ditampilkan.
- Dashboard calon mahasiswa memiliki akses cepat dan 3 info terbaru.
- Bagian Persiapan Pendaftaran/Checklist.
- Bagian FAQ dengan pertanyaan umum yang bisa dibuka-tutup.
- Menu admin baru: Pengumuman.

CARA UPDATE DATABASE
1. Backup database pmb.
2. Buka phpMyAdmin > database pmb > SQL.
3. Jalankan isi file tambah_fitur_pmb.sql satu kali.
4. Jangan import database.sql ulang karena berisi DROP TABLE.
5. Salin/replace file dari ZIP ini ke project lama dengan struktur folder yang sama.
6. Login admin, buka menu Pengumuman, buat informasi dan pilih status Publik.
7. Buka halaman utama; pengumuman akan tampil otomatis.

Catatan: Jadwal/persyaratan di bagian landing page adalah konten contoh untuk proyek sekolah,
silakan sesuaikan dengan kebijakan kampus yang sebenarnya.

UPDATE DESAIN
- Tampilan dashboard, sidebar, kartu statistik, tombol, tabel, dan formulir dipoles dengan aksen navy-emas-cream.
- Efek hover ringan dan dekorasi halus ditambahkan agar tampilan lebih hidup tanpa mengubah fungsi PHP.
Untuk memasang: backup folder project lama, extract ZIP ini, lalu salin folder project ke htdocs. Jangan import ulang database.sql jika database lama sudah berisi data.

FITUR VERIFIKASI TES DAN DAFTAR ULANG
1. Backup database pmb terlebih dahulu.
2. Buka phpMyAdmin > database pmb > SQL.
3. Jalankan file migrasi_verifikasi_daftar_ulang.sql SATU KALI.
4. Replace file project dengan versi ZIP ini, pertahankan konfigurasi koneksi database.
5. Buat folder uploads/daftar_ulang jika belum ada (sudah disertakan pada ZIP).
6. Mahasiswa mengirim tes -> status Menunggu Verifikasi.
7. Admin membuka menu Hasil Test, lalu memilih Setujui Lulus atau Tidak Lulus.
8. Jika Lulus, mahasiswa mengisi formulir daftar ulang dan mengunggah foto diri (JPG/PNG/WEBP maks 2 MB).
9. Admin membuka menu Daftar Ulang, memeriksa berkas, lalu Setujui untuk menerbitkan NIM dan membuka fitur Cetak Kartu.

Catatan:
- Hasil tes yang dikirim setelah pembaruan akan menunggu verifikasi admin.
- Hasil tes lama tidak otomatis berubah; admin perlu meninjau data lama secara manual jika diperlukan.
- Pastikan ekstensi fileinfo PHP aktif agar validasi tipe foto berjalan.
- Jangan import database.sql karena file itu menghapus tabel lama.

FITUR KARTU MAHASISWA DI AREA MAHASISWA
- Menu Kartu Mahasiswa tersedia pada sidebar mahasiswa.
- Mahasiswa dapat melihat/mencetak kartu sendiri setelah daftar ulang disetujui admin dan NIM diterbitkan.
- Halaman kartu memeriksa user_id akun yang login; mahasiswa tidak dapat membuka kartu akun lain dengan mengganti URL.
- Admin tetap memverifikasi data daftar ulang, sedangkan akses cetak kartu tersedia di area mahasiswa.
- Tidak memerlukan perubahan database tambahan.
