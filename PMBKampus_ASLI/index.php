<?php
session_start();
$isLoggedIn = !empty($_SESSION["user"]);
$isAdmin = $isLoggedIn && ($_SESSION["user"]["role"] ?? "") === "admin";
require_once __DIR__ . "/config/koneksi.php";
$pengumuman = false;
try {
    $pengumuman = $conn->query("SELECT judul,kategori,isi,tanggal_publikasi FROM pengumuman WHERE status='Publik' ORDER BY tanggal_publikasi DESC, id DESC LIMIT 6");
} catch (Throwable $e) {
    $pengumuman = false;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SELMA UB — Penerimaan Mahasiswa Baru</title>
<link rel="stylesheet" href="css/style.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
</head>
<body class="portal-body">
<header class="portal-header">
  <a class="portal-brand" href="index.php">
    <span class="brand-seal">✦</span>
    <span><b>SELMA UB</b><small>SELEKSI MASUK UNIVERSITAS BRAWIJAYA</small></span>
  </a>
  <nav class="portal-nav">
    <a href="#beranda">Beranda</a>
    <a href="#jurusan">Jurusan</a>
    <a href="#pengumuman">Info PMB</a>
    <a href="#alur">Alur</a>
    <a href="#keunggulan">Keunggulan</a>
    <?php if ($isLoggedIn): ?>
      <a href="<?= $isAdmin ? 'admin/dashboard.php' : 'user/dashboard.php' ?>">Dashboard</a>
      <a href="auth/logout.php">Keluar</a>
    <?php else: ?>
      <a href="auth/login.php">Masuk</a>
      <a class="nav-cta" href="auth/register.php">Daftar Sekarang</a>
    <?php endif; ?>
  </nav>
</header>

<main>
<section class="portal-hero" id="beranda">
  <div class="hero-copy">
    <div class="eyebrow"><span></span> PENERIMAAN MAHASISWA BARU</div>
    <h1>Mulai langkahmu.<br><em>Bangun masa depan.</em></h1>
    <p>Daftar secara daring, ikuti tahapan seleksi, lihat hasil tes, dan selesaikan daftar ulang dalam satu portal PMB.</p>
    <div class="hero-actions">
      <?php if ($isLoggedIn): ?>
        <a class="portal-btn primary" href="<?= $isAdmin ? 'admin/dashboard.php' : 'user/dashboard.php' ?>">Buka Dashboard <span>→</span></a>
      <?php else: ?>
        <a class="portal-btn primary" href="auth/register.php">Mulai Registrasi <span>→</span></a>
        <a class="portal-btn outline" href="auth/login.php">Masuk ke Akun</a>
      <?php endif; ?>
    </div>
    <div class="hero-note"><span class="note-check">✓</span> Proses pendaftaran praktis, transparan, dan dapat dipantau secara daring.</div>
  </div>
  <div class="hero-visual" aria-label="Foto gedung kampus">
    <img src="assets/images/kampus.jpg" alt="Gedung kampus dikelilingi pepohonan" class="campus-photo">
    <div class="campus-photo-shade"></div>
    <div class="campus-label"><small>SELAMAT DATANG DI</small><b>Perjalanan Akademikmu</b></div>
    <div class="visual-caption"><span>01</span><div><b>JALUR PENERIMAAN</b><small>Seleksi dan daftar ulang terintegrasi</small></div></div>
  </div>
</section>


<section class="majors-section" id="jurusan">
  <div class="section-kicker">PROGRAM STUDI</div>
  <div class="majors-heading">
    <div><h2>Pilih jurusan untuk masa depanmu.</h2><p>Kenali bidang yang sesuai dengan minat dan rencana kariermu.</p></div>
    <a class="majors-link" href="auth/register.php">Daftar sekarang →</a>
  </div>
  <div class="majors-grid">
    <article class="major-card">
      <div class="major-symbol">01</div>
      <h3>Teknik Informatika</h3>
      <p>Pemrograman, aplikasi, kecerdasan buatan, dan teknologi digital.</p>
      <a href="auth/register.php">Pilih jurusan <span>→</span></a>
    </article>
    <article class="major-card">
      <div class="major-symbol">02</div>
      <h3>Sistem Informasi</h3>
      <p>Pengembangan sistem informasi dan solusi teknologi untuk organisasi.</p>
      <a href="auth/register.php">Pilih jurusan <span>→</span></a>
    </article>
    <article class="major-card">
      <div class="major-symbol">03</div>
      <h3>Manajemen</h3>
      <p>Strategi bisnis, kepemimpinan, dan pengelolaan organisasi.</p>
      <a href="auth/register.php">Pilih jurusan <span>→</span></a>
    </article>
    <article class="major-card">
      <div class="major-symbol">04</div>
      <h3>Akuntansi</h3>
      <p>Keuangan, pelaporan, audit, dan analisis bisnis.</p>
      <a href="auth/register.php">Pilih jurusan <span>→</span></a>
    </article>
    <article class="major-card">
      <div class="major-symbol">05</div>
      <h3>Ilmu Komunikasi</h3>
      <p>Media, komunikasi digital, hubungan masyarakat, dan kreativitas.</p>
      <a href="auth/register.php">Pilih jurusan <span>→</span></a>
    </article>
    <article class="major-card">
      <div class="major-symbol">06</div>
      <h3>Ilmu Hukum</h3>
      <p>Dasar hukum, analisis kasus, dan pemahaman sistem peraturan.</p>
      <a href="auth/register.php">Pilih jurusan <span>→</span></a>
    </article>
    <article class="major-card">
      <div class="major-symbol">07</div>
      <h3>Psikologi</h3>
      <p>Perilaku manusia, proses berpikir, dan dinamika sosial.</p>
      <a href="auth/register.php">Pilih jurusan <span>→</span></a>
    </article>
    <article class="major-card">
      <div class="major-symbol">08</div>
      <h3>Teknik Industri</h3>
      <p>Sistem kerja, proses produksi, dan peningkatan efisiensi.</p>
      <a href="auth/register.php">Pilih jurusan <span>→</span></a>
    </article>
  </div>
  <p class="majors-note">Daftar jurusan di atas merupakan contoh dan dapat disesuaikan dengan program studi yang tersedia di kampus.</p>
</section>


<section class="news-section" id="pengumuman">
  <div class="section-kicker">INFO TERBARU</div>
  <div class="majors-heading">
    <div><h2>Berita & Pengumuman</h2><p>Informasi penting seputar penerimaan mahasiswa baru.</p></div>
    <?php if ($isAdmin): ?><a class="majors-link" href="admin/pengumuman.php">Kelola pengumuman →</a><?php endif; ?>
  </div>
  <div class="news-grid">
    <?php if ($pengumuman && $pengumuman->num_rows > 0): ?>
      <?php while ($berita = $pengumuman->fetch_assoc()): ?>
        <article class="news-card">
          <div class="news-meta"><span><?= htmlspecialchars($berita["kategori"]) ?></span><time><?= htmlspecialchars(date("d M Y", strtotime($berita["tanggal_publikasi"]))) ?></time></div>
          <h3><?= htmlspecialchars($berita["judul"]) ?></h3>
          <p><?= nl2br(htmlspecialchars(mb_strimwidth($berita["isi"], 0, 190, "..."))) ?></p>
        </article>
      <?php endwhile; ?>
    <?php else: ?>
      <article class="news-card news-empty"><div class="news-meta"><span>Informasi PMB</span><span>Contoh</span></div><h3>Informasi pendaftaran</h3><p>Pengumuman resmi akan ditampilkan di sini setelah admin menerbitkannya melalui dashboard.</p></article>
      <article class="news-card news-empty"><div class="news-meta"><span>Jadwal Seleksi</span><span>Contoh</span></div><h3>Pantau jadwal seleksi</h3><p>Periksa portal secara berkala agar tidak melewatkan tahapan pendaftaran.</p></article>
      <article class="news-card news-empty"><div class="news-meta"><span>Tips</span><span>Contoh</span></div><h3>Lengkapi data diri</h3><p>Pastikan data yang dimasukkan sesuai dan lengkap sebelum mengikuti tes.</p></article>
    <?php endif; ?>
  </div>
</section>

<section class="info-section" id="persyaratan">
  <div class="section-kicker">PERSIAPAN PENDAFTARAN</div>
  <div class="majors-heading"><div><h2>Siapkan sebelum mendaftar.</h2><p>Checklist umum agar proses registrasi lebih lancar.</p></div></div>
  <div class="requirements-grid">
    <article><span>01</span><div><h3>Identitas diri</h3><p>Siapkan nama lengkap dan alamat email aktif.</p></div></article>
    <article><span>02</span><div><h3>Informasi sekolah</h3><p>Siapkan nama asal sekolah dan data diri yang dibutuhkan.</p></div></article>
    <article><span>03</span><div><h3>Pilihan program studi</h3><p>Pelajari jurusan yang tersedia dan pilih sesuai minat.</p></div></article>
    <article><span>04</span><div><h3>Pantau akun PMB</h3><p>Simpan informasi login dan cek status pendaftaran secara berkala.</p></div></article>
  </div>
  <p class="info-disclaimer">Persyaratan ini merupakan panduan contoh untuk proyek sekolah. Sesuaikan dengan ketentuan kampus yang sebenarnya.</p>
</section>

<section class="faq-section" id="faq">
  <div class="section-kicker">BANTUAN CALON MAHASISWA</div>
  <h2>Pertanyaan yang sering diajukan</h2>
  <div class="faq-list">
    <details><summary>Bagaimana cara mendaftar PMB?</summary><p>Pilih Daftar Sekarang, isi nama, email, password, dan jurusan. Setelah akun dibuat, login untuk melengkapi data diri.</p></details>
    <details><summary>Di mana saya melihat nomor tes?</summary><p>Login ke akun calon mahasiswa, lalu buka menu Nomor Test atau Dashboard.</p></details>
    <details><summary>Bagaimana cara melihat hasil tes?</summary><p>Setelah tes dinilai, buka menu Hasil Test untuk melihat status kelulusan.</p></details>
    <details><summary>Kapan kartu mahasiswa baru bisa dicetak?</summary><p>Admin dapat mencetak kartu dari menu Daftar Ulang setelah status daftar ulang calon mahasiswa tercatat selesai.</p></details>
    <details><summary>Bagaimana jika ada informasi pendaftaran terbaru?</summary><p>Lihat bagian Berita & Pengumuman di halaman utama PMB. Informasi yang berstatus Publik akan ditampilkan di sana.</p></details>
  </div>
</section>

<section class="portal-steps" id="alur">
  <div class="section-kicker">LANGKAH PENDAFTARAN</div>
  <div class="steps-heading"><h2>Alur PMB yang mudah diikuti</h2><p>Ikuti setiap tahap hingga resmi menjadi mahasiswa baru.</p></div>
  <div class="portal-step-grid">
    <article><span>01</span><div class="step-icon">✎</div><h3>Registrasi</h3><p>Buat akun dan lengkapi data calon mahasiswa.</p></article>
    <article><span>02</span><div class="step-icon">▤</div><h3>Nomor Tes</h3><p>Dapatkan nomor tes secara otomatis.</p></article>
    <article><span>03</span><div class="step-icon">✓</div><h3>Tes & Hasil</h3><p>Kerjakan tes dan pantau pengumuman kelulusan.</p></article>
    <article><span>04</span><div class="step-icon">▣</div><h3>Daftar Ulang</h3><p>Selesaikan daftar ulang dan dapatkan NIM.</p></article>
  </div>
</section>

<section class="portal-bottom" id="keunggulan">
  <div><div class="section-kicker">PORTAL PMB</div><h2>Satu portal untuk setiap tahap.</h2></div>
  <p>Informasi pendaftaran, hasil seleksi, daftar ulang, dan kartu mahasiswa baru tersusun dalam satu sistem yang mudah digunakan.</p>
  <?php if (!$isLoggedIn): ?><a class="portal-btn light" href="auth/register.php">Daftar Sekarang <span>→</span></a><?php endif; ?>
</section>
</main>
<footer class="portal-footer"><span>© 2026 SELMA UB</span><span>Portal Penerimaan Mahasiswa Baru</span></footer>
</body>
</html>