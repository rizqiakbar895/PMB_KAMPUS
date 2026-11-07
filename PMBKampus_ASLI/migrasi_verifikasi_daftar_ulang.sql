-- Jalankan SATU KALI di phpMyAdmin > database pmb > tab SQL.
-- Backup database sebelum menjalankan migrasi.
-- Alur baru: hasil tes menunggu verifikasi admin; daftar ulang perlu data/foto dan persetujuan admin.

ALTER TABLE hasil_test
  MODIFY status VARCHAR(40) NOT NULL DEFAULT 'Menunggu Verifikasi';

ALTER TABLE calon_maba
  MODIFY status VARCHAR(40) DEFAULT 'Belum Test';

ALTER TABLE daftar_ulang
  MODIFY nim VARCHAR(30) NULL,
  MODIFY status VARCHAR(40) DEFAULT 'Menunggu Verifikasi',
  ADD COLUMN nama_lengkap VARCHAR(100) NULL AFTER nim,
  ADD COLUMN nik VARCHAR(30) NULL AFTER nama_lengkap,
  ADD COLUMN alamat TEXT NULL AFTER nik,
  ADD COLUMN no_hp VARCHAR(30) NULL AFTER alamat,
  ADD COLUMN foto_diri VARCHAR(255) NULL AFTER no_hp;

-- File foto diunggah ke folder uploads/daftar_ulang oleh aplikasi.
