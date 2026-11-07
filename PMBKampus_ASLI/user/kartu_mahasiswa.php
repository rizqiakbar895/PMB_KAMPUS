<?php
require_once "../config/koneksi.php";
require_once "../config/auth.php";
require_role("user");

$userId = (int)($_SESSION["user"]["id"] ?? 0);

/*
 * Kartu hanya dapat dilihat oleh pemilik akun yang:
 * 1. memiliki data daftar ulang;
 * 2. daftar ulangnya sudah disetujui admin (status Selesai);
 * 3. sudah memiliki NIM.
 */
$stmt = $conn->prepare(
    "SELECT
        d.id AS daftar_id,
        d.tanggal_daftar_ulang,
        d.status AS status_daftar,
        d.nim,
        d.foto_diri,
        d.nama_lengkap AS nama_daftar_ulang,
        d.nik,
        d.alamat,
        d.no_hp,
        c.nomor_test,
        c.nama_lengkap,
        c.asal_sekolah,
        c.jurusan,
        c.tempat_lahir,
        c.tanggal_lahir,
        c.jenis_kelamin,
        u.email
     FROM daftar_ulang d
     INNER JOIN calon_maba c ON c.id = d.calon_maba_id
     INNER JOIN users u ON u.id = c.user_id
     WHERE c.user_id = ?
       AND d.status = 'Selesai'
       AND d.nim IS NOT NULL
       AND d.nim <> ''
     ORDER BY d.id DESC
     LIMIT 1"
);
$stmt->bind_param("i", $userId);
$stmt->execute();
$m = $stmt->get_result()->fetch_assoc();

function e($value) {
    return htmlspecialchars((string)($value ?? "-"), ENT_QUOTES, "UTF-8");
}

if (!$m) {
    // Ambil status daftar ulang agar mahasiswa mendapat penjelasan yang tepat.
    $check = $conn->prepare(
        "SELECT d.status
         FROM daftar_ulang d
         INNER JOIN calon_maba c ON c.id = d.calon_maba_id
         WHERE c.user_id = ?
         ORDER BY d.id DESC
         LIMIT 1"
    );
    $check->bind_param("i", $userId);
    $check->execute();
    $state = $check->get_result()->fetch_assoc();
    $status = $state["status"] ?? "";
} else {
    $status = "Selesai";
}

$namaKartu = $m["nama_daftar_ulang"] ?: $m["nama_lengkap"];
$initials = strtoupper(substr(trim($namaKartu), 0, 1));
$photoPath = !empty($m["foto_diri"])
    ? "../uploads/daftar_ulang/" . rawurlencode($m["foto_diri"])
    : "";
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kartu Mahasiswa - SELMA UB</title>
<link rel="stylesheet" href="../css/style.css">
<style>
body{background:#f5f3eb}
.card-page{max-width:980px;margin:0 auto;padding:28px}
.card-actions{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:20px;flex-wrap:wrap}
.student-card{max-width:820px;margin:0 auto;background:#fff;border:1px solid #e4dfd2;border-radius:15px;overflow:hidden;box-shadow:0 16px 45px rgba(23,54,90,.10)}
.card-head{padding:25px 30px;background:#17365a;color:#fff;display:flex;align-items:center;gap:16px}
.seal{width:55px;height:55px;flex:0 0 55px;border:1px solid #d7bd76;border-radius:50%;display:grid;place-items:center;color:#e5cb84;font-size:28px}
.card-head small{display:block;color:#e5cb84;font-size:10px;letter-spacing:2px;margin-bottom:6px}
.card-head h1{font:700 23px Georgia,serif;margin:0}
.card-head p{font-size:11px;color:#dce4ee;margin:6px 0 0}
.card-content{display:grid;grid-template-columns:1fr 155px;gap:28px;padding:30px}
.eyebrow{font-size:10px;letter-spacing:2px;color:#a17a23;font-weight:700}
.student-name{font:700 27px Georgia,serif;color:#17365a;margin:10px 0 20px}
.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:17px}
.info-grid small{display:block;color:#8b94a2;font-size:10px;margin-bottom:5px}
.info-grid b{font-size:13px;color:#27364a;overflow-wrap:anywhere}
.photo{height:185px;border:1px solid #dce2e9;background:#f3f1e9;display:flex;align-items:center;justify-content:center;overflow:hidden;color:#17365a}
.photo img{width:100%;height:100%;object-fit:cover}
.photo strong{font:700 55px Georgia,serif}
.nim-box{margin-top:22px;padding:15px;background:#f7f3e8;border-left:3px solid #b18a31}
.nim-box small{display:block;color:#8b7132;font-size:10px;letter-spacing:1px}
.nim-box b{font-size:22px;letter-spacing:2px;color:#17365a}
.card-foot{padding:15px 30px;background:#fafbfc;border-top:1px solid #edf0f4;display:flex;justify-content:space-between;gap:12px;color:#8b94a2;font-size:10px}
.waiting-box{max-width:700px;margin:35px auto;padding:28px;background:#fff;border:1px solid #e8e1d2;border-radius:12px}
.waiting-box h2{font:700 23px Georgia,serif;color:#17365a;margin-top:0}
.waiting-box p{color:#687383;line-height:1.7;font-size:13px}
@media(max-width:650px){.card-page{padding:16px}.card-content{grid-template-columns:1fr;padding:20px}.photo{width:135px;height:165px;grid-row:1}.card-head{padding:20px}.card-head h1{font-size:19px}.student-name{font-size:23px}.info-grid{grid-template-columns:1fr 1fr}.card-foot{padding:15px 20px;flex-wrap:wrap}}
@media print{@page{size:A4;margin:15mm}.no-print{display:none!important}body{background:#fff}.card-page{padding:0}.student-card{width:100%;margin:0;box-shadow:none;border:1px solid #ccd3dd;break-inside:avoid}.card-head{-webkit-print-color-adjust:exact;print-color-adjust:exact}.nim-box,.card-foot,.photo{-webkit-print-color-adjust:exact;print-color-adjust:exact}}
</style>
</head>
<body>
<div class="layout">
<aside class="sidebar no-print">
    <div class="brand">SELMA<span>UB</span></div>
    <a href="dashboard.php">Dashboard</a>
    <a href="data_diri.php">Data Diri</a>
    <a href="nomor_test.php">Nomor Test</a>
    <a href="test.php">Tes Masuk</a>
    <a href="hasil.php">Hasil Test</a>
    <a href="daftar_ulang.php">Daftar Ulang</a>
    <a class="active" href="kartu_mahasiswa.php">Kartu Mahasiswa</a>
    <a href="../index.php#pengumuman">Pengumuman PMB</a>
    <a href="../auth/logout.php">Logout</a>
</aside>
<main class="main">
<div class="card-page">
<?php if (!$m): ?>
    <section class="waiting-box">
        <div class="eyebrow">KARTU MAHASISWA</div>
        <h2>Kartu belum tersedia</h2>
        <?php if ($status === "Menunggu Verifikasi"): ?>
            <p>Data daftar ulang kamu sedang diperiksa admin. Tombol cetak kartu akan tersedia setelah daftar ulang disetujui dan NIM diterbitkan.</p>
        <?php elseif ($status === "Perlu Perbaikan"): ?>
            <p>Data daftar ulang perlu diperbaiki. Silakan buka menu Daftar Ulang, perbarui data dan foto, lalu kirim kembali.</p>
        <?php else: ?>
            <p>Kartu mahasiswa baru tersedia setelah kamu dinyatakan lulus tes, menyelesaikan daftar ulang, dan admin menyetujui berkas.</p>
        <?php endif; ?>
        <a class="btn primary" href="daftar_ulang.php">Buka Daftar Ulang</a>
    </section>
<?php else: ?>
    <div class="card-actions no-print">
        <a class="btn secondary" href="dashboard.php">← Kembali ke Dashboard</a>
        <button class="btn primary" onclick="window.print()">Cetak / Simpan PDF</button>
    </div>
    <section class="student-card">
        <header class="card-head">
            <div class="seal">✦</div>
            <div>
                <small>PORTAL PENERIMAAN MAHASISWA BARU</small>
                <h1>KARTU MAHASISWA BARU</h1>
                <p>Identitas mahasiswa yang telah menyelesaikan proses daftar ulang</p>
            </div>
        </header>
        <div class="card-content">
            <div>
                <div class="eyebrow">IDENTITAS MAHASISWA</div>
                <h2 class="student-name"><?= e($namaKartu) ?></h2>
                <div class="info-grid">
                    <div><small>NOMOR TES</small><b><?= e($m["nomor_test"]) ?></b></div>
                    <div><small>ASAL SEKOLAH</small><b><?= e($m["asal_sekolah"]) ?></b></div>
                    <div><small>PROGRAM STUDI</small><b><?= e($m["jurusan"]) ?></b></div>
                    <div><small>TEMPAT, TANGGAL LAHIR</small><b><?= e(trim(($m["tempat_lahir"] ?: "-") . ", " . ($m["tanggal_lahir"] ?: "-"))) ?></b></div>
                    <div><small>JENIS KELAMIN</small><b><?= e($m["jenis_kelamin"]) ?></b></div>
                    <div><small>EMAIL</small><b><?= e($m["email"]) ?></b></div>
                    <div><small>TANGGAL DAFTAR ULANG</small><b><?= e($m["tanggal_daftar_ulang"]) ?></b></div>
                </div>
                <div class="nim-box">
                    <small>NOMOR INDUK MAHASISWA (NIM)</small>
                    <b><?= e($m["nim"]) ?></b>
                </div>
            </div>
            <div class="photo">
                <?php if ($photoPath): ?>
                    <img src="<?= e($photoPath) ?>" alt="Foto mahasiswa">
                <?php else: ?>
                    <strong><?= e($initials) ?></strong>
                <?php endif; ?>
            </div>
        </div>
        <footer class="card-foot">
            <span>Status: DAFTAR ULANG SELESAI</span>
            <span>SELMA UB · Tahun Akademik 2026/2027</span>
        </footer>
    </section>
<?php endif; ?>
</div>
</main>
</div>
</body>
</html>
