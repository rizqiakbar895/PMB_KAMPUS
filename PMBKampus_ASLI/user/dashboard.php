
<?php
require_once "../config/koneksi.php";
require_once "../config/auth.php";
require_role("user");

// Ambil data calon mahasiswa berdasarkan akun yang login
$userId = (int) ($_SESSION["user"]["id"] ?? 0);

$stmt = $conn->prepare(
    "SELECT * FROM calon_maba WHERE user_id = ? LIMIT 1"
);
$stmt->bind_param("i", $userId);
$stmt->execute();

$result = $stmt->get_result();
$maba = $result->fetch_assoc();

// Nilai default jika data calon mahasiswa belum tersedia
if (!$maba) {
    $maba = [
        "nomor_test" => "Belum tersedia",
        "status" => "Belum Terdaftar"
    ];
}

// Cek apakah mahasiswa sudah menyelesaikan daftar ulang dan memperoleh NIM
$kartuStmt = $conn->prepare(
    "SELECT d.id
     FROM daftar_ulang d
     INNER JOIN calon_maba c ON c.id = d.calon_maba_id
     WHERE c.user_id = ? AND d.status = 'Selesai'
       AND d.nim IS NOT NULL AND d.nim <> ''
     ORDER BY d.id DESC LIMIT 1"
);
$kartuStmt->bind_param("i", $userId);
$kartuStmt->execute();
$kartuTersedia = (bool)$kartuStmt->get_result()->fetch_assoc();

// Ambil pengumuman terbaru
$pengumuman = false;

try {
    $pengumuman = $conn->query(
        "SELECT judul, kategori, isi, tanggal_publikasi
         FROM pengumuman
         WHERE status = 'Publik'
         ORDER BY tanggal_publikasi DESC, id DESC
         LIMIT 3"
    );
} catch (Throwable $e) {
    $pengumuman = false;
}

// Amankan data untuk ditampilkan
$namaUser = htmlspecialchars(
    $_SESSION["user"]["nama"] ?? "Calon Mahasiswa",
    ENT_QUOTES,
    "UTF-8"
);

$nomorTest = htmlspecialchars(
    $maba["nomor_test"] ?? "Belum tersedia",
    ENT_QUOTES,
    "UTF-8"
);

$status = htmlspecialchars(
    $maba["status"] ?? "Belum Terdaftar",
    ENT_QUOTES,
    "UTF-8"
);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User - SELMA UB</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="layout">

    <aside class="sidebar">
        <div class="brand">SELMA<span>UB</span></div>
        <a class="active" href="dashboard.php">Dashboard</a>
        <a href="data_diri.php">Data Diri</a>
        <a href="nomor_test.php">Nomor Test</a>
        <a href="test.php">Tes Masuk</a>
        <a href="hasil.php">Hasil Test</a>
        <a href="daftar_ulang.php">Daftar Ulang</a>
        <a href="kartu_mahasiswa.php">Kartu Mahasiswa</a>
        <a href="../index.php#pengumuman">Pengumuman PMB</a>
        <a href="../auth/logout.php">Logout</a>
    </aside>

    <main class="main">

        <div class="topbar">
            <div>
                <h1>Halo, <?= $namaUser ?> 👋</h1>
                <p class="muted">
                    Selamat datang di dashboard PMB.
                </p>
            </div>
        </div>

        <div class="stats">
            <div class="stat">
                <span>Nomor Test</span>
                <strong><?= $nomorTest ?></strong>
            </div>

            <div class="stat">
                <span>Status</span>
                <strong><?= $status ?></strong>
            </div>
        </div>

        <div class="card">
            <h2>Alur Pendaftaran</h2>

            <div class="steps">
                <div class="step done">
                    <b>1</b><span>Registrasi</span>
                </div>
                <div class="step">
                    <b>2</b><span>Data Diri</span>
                </div>
                <div class="step">
                    <b>3</b><span>Tes</span>
                </div>
                <div class="step">
                    <b>4</b><span>Hasil</span>
                </div>
                <div class="step">
                    <b>5</b><span>Daftar Ulang</span>
                </div>
            </div>
        </div>

        <div class="card quick-actions-card">
            <h2>Akses Cepat</h2>

            <div class="admin-grid">
                <a href="data_diri.php">✍️ Lengkapi Data Diri</a>
                <a href="nomor_test.php">🎫 Lihat Nomor Tes</a>
                <a href="test.php">📝 Kerjakan Tes Masuk</a>
                <a href="hasil.php">📊 Cek Hasil Tes</a>
                <a href="daftar_ulang.php">🎓 Proses Daftar Ulang</a>
                <?php if ($kartuTersedia): ?>
                    <a href="kartu_mahasiswa.php">🪪 Lihat / Cetak Kartu Mahasiswa</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <h2>Info & Pengumuman Terbaru</h2>

            <?php if ($pengumuman && $pengumuman->num_rows > 0): ?>
                <div class="dashboard-news-list">

                    <?php while ($news = $pengumuman->fetch_assoc()): ?>
                        <article>
                            <div>
                                <span>
                                    <?= htmlspecialchars($news["kategori"] ?? "Informasi") ?>
                                    ·
                                    <?= htmlspecialchars(
                                        date("d M Y", strtotime($news["tanggal_publikasi"]))
                                    ) ?>
                                </span>

                                <h3>
                                    <?= htmlspecialchars($news["judul"]) ?>
                                </h3>

                                <p>
                                    <?= nl2br(htmlspecialchars(
                                        mb_strimwidth(
                                            $news["isi"],
                                            0,
                                            180,
                                            "..."
                                        )
                                    )) ?>
                                </p>
                            </div>
                        </article>
                    <?php endwhile; ?>

                </div>
            <?php else: ?>
                <p class="muted">
                    Belum ada pengumuman terbaru.
                    Informasi yang diterbitkan admin akan muncul di sini.
                </p>
            <?php endif; ?>

            <a class="edit-link" href="../index.php#pengumuman">
                Lihat semua informasi di halaman utama →
            </a>
        </div>

    </main>
</div>
</body>
</html>
