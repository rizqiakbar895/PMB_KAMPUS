<?php
require_once "../config/koneksi.php";
require_once "../config/auth.php";
require_role("user");
$userId=(int)($_SESSION["user"]["id"]??0);
$stmt=$conn->prepare("SELECT id,status FROM calon_maba WHERE user_id=? LIMIT 1");
$stmt->bind_param("i",$userId); $stmt->execute(); $m=$stmt->get_result()->fetch_assoc();
if(!$m){ exit("Data pendaftaran belum ditemukan. Lengkapi data diri terlebih dahulu."); }

$latest=$conn->prepare("SELECT status FROM hasil_test WHERE calon_maba_id=? ORDER BY id DESC LIMIT 1");
$latest->bind_param("i",$m["id"]); $latest->execute(); $existing=$latest->get_result()->fetch_assoc();

if($_SERVER["REQUEST_METHOD"]==="POST"){
    if($existing){ exit("Jawaban tes sudah dikirim. Silakan tunggu verifikasi admin."); }
    $soal=$conn->query("SELECT * FROM soal ORDER BY id");
    $benar=0; $total=$soal->num_rows;
    if($total===0) exit("Soal tes belum tersedia.");
    while($s=$soal->fetch_assoc()){
        $jawaban=strtoupper($_POST["jawaban"][$s["id"]]??"");
        if($jawaban===$s["jawaban"]) $benar++;
    }
    $nilai=(int)round(($benar/$total)*100);
    $status="Menunggu Verifikasi";
    $ins=$conn->prepare("INSERT INTO hasil_test (calon_maba_id,jumlah_benar,nilai,status) VALUES (?,?,?,?)");
    $ins->bind_param("iiis",$m["id"],$benar,$nilai,$status); $ins->execute();
    $up=$conn->prepare("UPDATE calon_maba SET status='Menunggu Verifikasi' WHERE id=?");
    $up->bind_param("i",$m["id"]); $up->execute();
    header("Location: hasil.php?submitted=1"); exit;
}
$soal=$conn->query("SELECT * FROM soal ORDER BY id");
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Tes Masuk</title><link rel="stylesheet" href="../css/style.css">
<style>
.question{margin:18px 0;padding:24px}.question-title{display:block;font-size:16px;line-height:1.7;color:#17365a;margin-bottom:18px}
.answer-list{display:grid;grid-template-columns:1fr;gap:10px}
.answer-option{display:flex;align-items:flex-start;gap:11px;padding:13px 15px;border:1px solid #e7e1d4;border-radius:8px;background:#fff;cursor:pointer;line-height:1.5}
.answer-option:hover{border-color:#b18a31;background:#fbf8ef}
.answer-option input{margin-top:3px;accent-color:#17365a}
.answer-letter{font-weight:700;color:#a17a23;min-width:22px}
.test-note{padding:15px 18px;background:#f7f2e5;border-left:3px solid #b18a31;border-radius:6px;color:#66552d}
</style></head><body><div class="layout">
<aside class="sidebar"><div class="brand">SELMA<span>UB</span></div><a href="dashboard.php">Dashboard</a><a href="data_diri.php">Data Diri</a><a href="nomor_test.php">Nomor Test</a><a class="active" href="test.php">Tes Masuk</a><a href="hasil.php">Hasil Test</a><a href="daftar_ulang.php">Daftar Ulang</a><a href="../auth/logout.php">Logout</a></aside>
<main class="main"><h1>Tes Masuk</h1><p class="muted">Pilih satu jawaban yang paling tepat untuk setiap soal.</p>
<?php if($existing): ?><div class="card test-note"><b>Jawaban sudah dikirim.</b><br>Status tes kamu: <?= htmlspecialchars($existing["status"]) ?>. Tunggu pemeriksaan dan keputusan admin.</div>
<?php else: ?><div class="test-note">Nilai tes akan diperiksa oleh admin. Hasil kelulusan belum resmi sampai diverifikasi.</div>
<form method="post"><?php $no=1; while($s=$soal->fetch_assoc()): ?>
<section class="card question"><span class="question-title"><b><?= $no++ ?>. <?= htmlspecialchars($s["pertanyaan"]) ?></b></span>
<div class="answer-list">
<label class="answer-option"><input type="radio" name="jawaban[<?= (int)$s["id"] ?>]" value="A" required><span class="answer-letter">A.</span><span><?= htmlspecialchars($s["pilihan_a"]) ?></span></label>
<label class="answer-option"><input type="radio" name="jawaban[<?= (int)$s["id"] ?>]" value="B"><span class="answer-letter">B.</span><span><?= htmlspecialchars($s["pilihan_b"]) ?></span></label>
<label class="answer-option"><input type="radio" name="jawaban[<?= (int)$s["id"] ?>]" value="C"><span class="answer-letter">C.</span><span><?= htmlspecialchars($s["pilihan_c"]) ?></span></label>
<label class="answer-option"><input type="radio" name="jawaban[<?= (int)$s["id"] ?>]" value="D"><span class="answer-letter">D.</span><span><?= htmlspecialchars($s["pilihan_d"]) ?></span></label>
</div></section><?php endwhile; ?>
<button class="btn primary" type="submit" onclick="return confirm('Kirim jawaban tes sekarang? Jawaban tidak dapat diubah setelah dikirim.')">Kirim Jawaban</button></form><?php endif; ?>
</main></div></body></html>