CREATE DATABASE IF NOT EXISTS pmb;
USE pmb;
DROP TABLE IF EXISTS daftar_ulang;
DROP TABLE IF EXISTS hasil_test;
DROP TABLE IF EXISTS soal;
DROP TABLE IF EXISTS calon_maba;
DROP TABLE IF EXISTS users;
CREATE TABLE users (
 id INT AUTO_INCREMENT PRIMARY KEY,
 nama VARCHAR(100) NOT NULL,
 email VARCHAR(100) NOT NULL UNIQUE,
 password VARCHAR(255) NOT NULL,
 role ENUM('admin','user') NOT NULL DEFAULT 'user',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE calon_maba (
 id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NOT NULL,
 nomor_test VARCHAR(30) NOT NULL UNIQUE,
 nama_lengkap VARCHAR(100) NOT NULL,
 nik VARCHAR(30),
 tempat_lahir VARCHAR(100),
 tanggal_lahir DATE,
 jenis_kelamin VARCHAR(20),
 alamat TEXT,
 asal_sekolah VARCHAR(150),
 jurusan VARCHAR(100),
 status VARCHAR(30) DEFAULT 'Belum Test',
 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE soal (
 id INT AUTO_INCREMENT PRIMARY KEY,
 pertanyaan TEXT NOT NULL,
 pilihan_a VARCHAR(255) NOT NULL,
 pilihan_b VARCHAR(255) NOT NULL,
 pilihan_c VARCHAR(255) NOT NULL,
 pilihan_d VARCHAR(255) NOT NULL,
 jawaban CHAR(1) NOT NULL
);

CREATE TABLE hasil_test (
 id INT AUTO_INCREMENT PRIMARY KEY,
 calon_maba_id INT NOT NULL,
 jumlah_benar INT NOT NULL,
 nilai INT NOT NULL,
 status VARCHAR(30) NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (calon_maba_id) REFERENCES calon_maba(id) ON DELETE CASCADE
);

CREATE TABLE daftar_ulang (
 id INT AUTO_INCREMENT PRIMARY KEY,
 calon_maba_id INT NOT NULL,
 tanggal_daftar_ulang DATE NOT NULL,
 status VARCHAR(30) DEFAULT 'Selesai',
 nim VARCHAR(30) NOT NULL,
 FOREIGN KEY (calon_maba_id) REFERENCES calon_maba(id) ON DELETE CASCADE
);

INSERT INTO users (nama,email,password,role) VALUES
('Administrator','admin@pmb.test','$2y$12$hgow1gMtEHZ8T5j3nfxpHOEy7uNreGKYjf4XtJ1v6lpWK5Lq82zQC','admin');

INSERT INTO soal (pertanyaan,pilihan_a,pilihan_b,pilihan_c,pilihan_d,jawaban) VALUES
('Berapakah hasil dari 10 + 5?','10','15','20','25','B'),
('Ibukota Indonesia adalah...','Bandung','Surabaya','Jakarta','Medan','C'),
('HTML digunakan untuk...','Mengatur struktur halaman web','Mengolah database','Membuat sistem operasi','Mengedit video','A'),
('PHP merupakan bahasa yang banyak digunakan untuk...','Pemrograman web','Desain grafis','Editing audio','Menggambar 3D','A'),
('CSS digunakan untuk...','Mengatur tampilan halaman web','Menyimpan database','Membuat server','Mengirim email','A');
