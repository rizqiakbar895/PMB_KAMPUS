-- Jalankan sekali di phpMyAdmin pada database pmb yang sedang dipakai.
-- Tidak menghapus data lama.
ALTER TABLE calon_maba ADD COLUMN jurusan VARCHAR(100) NULL AFTER asal_sekolah;
