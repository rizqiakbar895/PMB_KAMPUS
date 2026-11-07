<?php
require_once "../config/koneksi.php"; require_once "../config/auth.php"; require_role("user");
$stmt=$conn->prepare("SELECT nomor_test,nama_lengkap,status FROM calon_maba WHERE user_id=?"); $stmt->bind_param("i",$_SESSION["user"]["id"]); $stmt->execute(); $m=$stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Nomor Test</title><link rel="stylesheet" href="../css/style.css"></head><body>
<div class="layout"><aside class="sidebar"><div class="brand">SELMA<span>UB</span></div><a href="dashboard.php">Dashboard</a><a href="data_diri.php">Data Diri</a><a class="active" href="nomor_test.php">Nomor Test</a><a href="test.php">Tes Masuk</a><a href="hasil.php">Hasil Test</a><a href="daftar_ulang.php">Daftar Ulang</a><a href="../auth/logout.php">Logout</a></aside>
<main class="main"><h1>Nomor Test</h1><div class="number-card"><p>Nomor test kamu</p><strong><?= htmlspecialchars($m["nomor_test"]) ?></strong><span><?= htmlspecialchars($m["nama_lengkap"]) ?></span></div><div class="card"><b>Status:</b> <?= htmlspecialchars($m["status"]) ?></div></main></div></body></html>