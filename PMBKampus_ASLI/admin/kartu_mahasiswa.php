<?php
require_once "../config/koneksi.php";
require_once "../config/auth.php";
require_role("admin");
$id = (int)($_GET["id"] ?? 0);
$stmt = $conn->prepare("SELECT d.id AS daftar_id,d.tanggal_daftar_ulang,d.status AS status_daftar,d.nim,d.foto_diri,c.nomor_test,c.nama_lengkap,c.asal_sekolah,c.jurusan,c.tempat_lahir,c.tanggal_lahir,c.jenis_kelamin,u.email FROM daftar_ulang d JOIN calon_maba c ON c.id=d.calon_maba_id JOIN users u ON u.id=c.user_id WHERE d.id=? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$m = $stmt->get_result()->fetch_assoc();
if (!$m || strtolower($m["status_daftar"]) !== "selesai") {
    http_response_code(404);
    exit("Kartu hanya tersedia untuk mahasiswa yang sudah menyelesaikan daftar ulang.");
}
function e($v) { return htmlspecialchars((string)($v ?? "-"), ENT_QUOTES, "UTF-8"); }
$initials = strtoupper(substr($m["nama_lengkap"], 0, 1));
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Kartu Mahasiswa Baru - <?= e($m["nama_lengkap"]) ?></title><link rel="stylesheet" href="../css/style.css">
<style>
body{background:#f1f3f7}.print-actions{max-width:820px;margin:25px auto;display:flex;justify-content:space-between;align-items:center;gap:10px}.card-paper{width:min(820px,calc(100% - 32px));margin:0 auto 40px;background:#fff;border:1px solid #d8dee8;border-radius:14px;overflow:hidden;box-shadow:0 14px 40px #1c2c4515}
.card-head{padding:24px 30px;background:#17365a;color:#fff;display:flex;align-items:center;gap:15px}.seal{width:54px;height:54px;border:1px solid #d7bd76;border-radius:50%;display:grid;place-items:center;color:#e5cb84;font-size:28px}.card-head small{display:block;color:#d7bd76;font-size:10px;letter-spacing:2px;margin-bottom:5px}.card-head h1{font:700 23px Georgia,serif;margin:0}.card-head p{margin:6px 0 0;color:#dce4ee;font-size:11px}
.card-body{display:grid;grid-template-columns:1fr 150px;gap:28px;padding:30px}.card-label{font-size:10px;letter-spacing:2px;color:#a17a23;font-weight:700}.student-name{font:700 27px Georgia,serif;color:#17365a;margin:10px 0 20px}.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:17px}.info-grid small{display:block;color:#8b94a2;font-size:10px;margin-bottom:5px}.info-grid b{font-size:13px;color:#27364a;overflow-wrap:anywhere}.photo-placeholder{height:178px;border:1px solid #dce2e9;background:linear-gradient(145deg,#edf1f5,#fafbfc);display:flex;align-items:center;justify-content:center;flex-direction:column;color:#17365a}.photo-placeholder strong{font:700 55px Georgia,serif}.photo-placeholder span{font-size:9px;letter-spacing:1px;color:#8b94a2;margin-top:8px}.nim-box{margin-top:20px;padding:15px;background:#f7f3e8;border-left:3px solid #b18a31}.nim-box small{display:block;color:#8b7132;font-size:10px;letter-spacing:1px}.nim-box b{font-size:22px;letter-spacing:2px;color:#17365a}.card-foot{padding:15px 30px;background:#fafbfc;border-top:1px solid #edf0f4;display:flex;justify-content:space-between;gap:12px;color:#8b94a2;font-size:10px}
@media(max-width:600px){.card-body{grid-template-columns:1fr;padding:20px}.photo-placeholder{width:130px;height:155px;grid-row:1}.card-head{padding:20px}.card-head h1{font-size:19px}.student-name{font-size:23px}.info-grid{grid-template-columns:1fr 1fr}.card-foot{padding:15px 20px;flex-wrap:wrap}}
@media print{@page{size:A4;margin:15mm}body{background:#fff}.print-actions{display:none}.card-paper{width:100%;margin:0;box-shadow:none;border:1px solid #ccd3dd;break-inside:avoid}.card-head{-webkit-print-color-adjust:exact;print-color-adjust:exact}.nim-box,.card-foot,.photo-placeholder{-webkit-print-color-adjust:exact;print-color-adjust:exact}}
</style></head><body>
<div class="print-actions"><a class="btn secondary" href="daftar_ulang.php">← Kembali ke Daftar Ulang</a><button class="btn primary" onclick="window.print()">Cetak / Simpan PDF</button></div>
<section class="card-paper">
<header class="card-head"><div class="seal">✦</div><div><small>PORTAL PENERIMAAN MAHASISWA BARU</small><h1>KARTU MAHASISWA BARU</h1><p>Dokumen identitas awal mahasiswa yang telah menyelesaikan daftar ulang</p></div></header>
<div class="card-body"><div>
<div class="card-label">IDENTITAS MAHASISWA</div><h2 class="student-name"><?= e($m["nama_lengkap"]) ?></h2>
<div class="info-grid">
<div><small>NOMOR TEST</small><b><?= e($m["nomor_test"]) ?></b></div>
<div><small>ASAL SEKOLAH</small><b><?= e($m["asal_sekolah"] ?: "-") ?></b></div>
<div><small>JURUSAN / PROGRAM STUDI</small><b><?= e($m["jurusan"] ?: "-") ?></b></div>
<div><small>TEMPAT, TANGGAL LAHIR</small><b><?= e(trim(($m["tempat_lahir"] ?: "-") . ", " . ($m["tanggal_lahir"] ?: "-"))) ?></b></div>
<div><small>JENIS KELAMIN</small><b><?= e($m["jenis_kelamin"] ?: "-") ?></b></div>
<div><small>EMAIL</small><b><?= e($m["email"]) ?></b></div>
<div><small>TANGGAL DAFTAR ULANG</small><b><?= e($m["tanggal_daftar_ulang"]) ?></b></div>
</div>
<div class="nim-box"><small>NOMOR INDUK MAHASISWA (NIM)</small><b><?= e($m["nim"]) ?></b></div>
</div><div class="photo-placeholder"><?php if (!empty($m["foto_diri"])): ?><img src="../uploads/daftar_ulang/<?= rawurlencode($m["foto_diri"]) ?>" alt="Foto mahasiswa" style="width:100%;height:100%;object-fit:cover"><?php else: ?><strong><?= e($initials) ?></strong><span>IDENTITAS MAHASISWA</span><?php endif; ?></div></div>
<footer class="card-foot"><span>Status: DAFTAR ULANG SELESAI</span><span>SELMA UB · Tahun Akademik 2026/2027</span></footer>
</section></body></html>