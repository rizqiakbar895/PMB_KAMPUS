<?php
require_once "../config/koneksi.php"; require_once "../config/auth.php"; require_role("admin");
$total=$conn->query("SELECT COUNT(*) c FROM calon_maba")->fetch_assoc()["c"];
$lulus=$conn->query("SELECT COUNT(*) c FROM calon_maba WHERE status='Lulus'")->fetch_assoc()["c"];
$ulang=$conn->query("SELECT COUNT(*) c FROM daftar_ulang")->fetch_assoc()["c"];
$soal=$conn->query("SELECT COUNT(*) c FROM soal")->fetch_assoc()["c"];
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Admin PMB</title><link rel="stylesheet" href="../css/style.css"></head><body>
<div class="layout"><aside class="sidebar"><div class="brand">PMB<span>Admin</span></div><a class="active" href="dashboard.php">Dashboard</a><a href="calon_maba.php">Data Calon Maba</a><a href="soal.php">Data Soal</a><a href="hasil_test.php">Hasil Test</a><a href="daftar_ulang.php">Daftar Ulang</a><a href="pengumuman.php">Pengumuman</a><a href="../auth/logout.php">Logout</a></aside>
<main class="main"><h1>Dashboard Admin</h1><p class="muted">Kontrol dan monitor data PMB.</p><div class="stats"><div class="stat"><span>Calon Maba</span><strong><?= $total ?></strong></div><div class="stat"><span>Lulus</span><strong><?= $lulus ?></strong></div><div class="stat"><span>Daftar Ulang</span><strong><?= $ulang ?></strong></div><div class="stat"><span>Soal Test</span><strong><?= $soal ?></strong></div></div>
<div class="card"><h2>Menu Admin</h2><div class="admin-grid"><a href="calon_maba.php">👤 Kelola Calon Maba</a><a href="soal.php">📝 Kelola Soal Test</a><a href="hasil_test.php">📊 Verifikasi Hasil Test</a><a href="daftar_ulang.php">🎓 Lihat Daftar Ulang & Cetak Kartu</a><a href="pengumuman.php">📢 Kelola Pengumuman PMB</a></div></div></main></div></body></html>