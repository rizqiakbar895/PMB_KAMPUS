-- Jalankan sekali di phpMyAdmin > database pmb > tab SQL.
-- Tidak menghapus data yang sudah ada.
CREATE TABLE IF NOT EXISTS pengumuman (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(180) NOT NULL,
    kategori VARCHAR(60) NOT NULL DEFAULT 'Informasi PMB',
    isi TEXT NOT NULL,
    tanggal_publikasi DATE NOT NULL,
    status ENUM('Draft','Publik') NOT NULL DEFAULT 'Draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO pengumuman (judul, kategori, isi, tanggal_publikasi, status)
SELECT 'Pendaftaran PMB telah dibuka', 'Informasi PMB',
       'Silakan membuat akun, memilih program studi, dan melengkapi data diri melalui portal PMB.',
       CURDATE(), 'Publik'
WHERE NOT EXISTS (SELECT 1 FROM pengumuman WHERE judul='Pendaftaran PMB telah dibuka');
