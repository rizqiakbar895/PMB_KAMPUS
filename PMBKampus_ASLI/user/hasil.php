<?php
require_once "../config/koneksi.php"; require_once "../config/auth.php"; require_role("user");
$stmt=$conn->prepare("SELECT h.*,c.nomor_test FROM hasil_test h JOIN calon_maba c ON c.id=h.calon_maba_id WHERE c.user_id=? ORDER BY h.id DESC LIMIT 1");
$stmt->bind_param("i",$_SESSION["user"]["id"]); $stmt->execute(); $h=$stmt->get_result()->fetch_assoc();
function e($v){return htmlspecialchars((string)($v??"-"),ENT_QUOTES,"UTF-8");}
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Hasil Tes</title><link rel="stylesheet" href="../css/style.css">
<style>.result-panel{max-width:680px;padding:30px;border-radius:12px;background:#fff;border:1px solid #eee7d8}.result-score{font:700 48px Georgia,serif;color:#17365a;margin:12px 0}.pending{padding:14px 17px;background:#fbf5e6;border-left:4px solid #b18a31;border-radius:6px;color:#765a19}.approved{padding:14px 17px;background:#eaf6ed;border-left:4px solid #33834b;border-radius:6px;color:#24663a}</style></head><body><div class="layout">
<aside class="sidebar"><div class="brand">SELMA<span>UB</span></div><a href="dashboard.php">Dashboard</a><a href="data_diri.php">Data Diri</a><a href="nomor_test.php">Nomor Test</a><a href="test.php">Tes Masuk</a><a class="active" href="hasil.php">Hasil Test</a><a href="daftar_ulang.php">Daftar Ulang</a><a href="../auth/logout.php">Logout</a></aside>
<main class="main"><h1>Hasil Tes Masuk</h1>
<?php if(!$h): ?><div class="card">Kamu belum mengerjakan tes masuk.</div>
<?php else: ?><section class="result-panel"><span class="muted">Nomor Tes: <?= e($h["nomor_test"]) ?></span><h2>Hasil Seleksi</h2><div class="result-score"><?= (int)$h["nilai"] ?></div><p>Jawaban benar: <?= (int)$h["jumlah_benar"] ?></p>
<?php if($h["status"]==="Menunggu Verifikasi"): ?><div class="pending"><b>Menunggu Verifikasi Admin</b><br>Nilai tes kamu sudah tersimpan. Admin akan memeriksa hasil dan menetapkan keputusan kelulusan.</div>
<?php elseif($h["status"]==="Lulus"): ?><div class="approved"><b>Selamat, kamu dinyatakan LULUS!</b><br>Silakan lanjutkan proses daftar ulang.</div><p><a class="btn primary" href="daftar_ulang.php">Lanjut Daftar Ulang</a></p>
<?php elseif($h["status"]==="Tidak Lulus"): ?><div class="pending"><b>Belum Lulus</b><br>Keputusan ini telah diverifikasi oleh admin.</div>
<?php else: ?><div class="pending">Status hasil: <?= e($h["status"]) ?></div><?php endif; ?>
</section><?php endif; ?></main></div></body></html>