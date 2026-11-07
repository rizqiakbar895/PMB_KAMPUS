
<?php
require_once "../config/koneksi.php";
require_once "../config/auth.php";
require_role("user");

$userId = (int) ($_SESSION["user"]["id"] ?? 0);
$namaAkun = $_SESSION["user"]["nama"] ?? "Calon Mahasiswa";
$msg = "";
$error = "";

// Cari data calon mahasiswa
$stmt = $conn->prepare(
    "SELECT * FROM calon_maba WHERE user_id = ? LIMIT 1"
);
$stmt->bind_param("i", $userId);
$stmt->execute();
$m = $stmt->get_result()->fetch_assoc();

// Jika data calon mahasiswa belum ada, buat data awal
if (!$m) {
    $nomorTest = "PMB" . date("Y")
        . str_pad((string)$userId, 4, "0", STR_PAD_LEFT);

    $insert = $conn->prepare(
        "INSERT INTO calon_maba
         (user_id, nomor_test, nama_lengkap, status)
         VALUES (?, ?, ?, 'Belum Test')"
    );
    $insert->bind_param("iss", $userId, $nomorTest, $namaAkun);

    if ($insert->execute()) {
        $stmt = $conn->prepare(
            "SELECT * FROM calon_maba WHERE user_id = ? LIMIT 1"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $m = $stmt->get_result()->fetch_assoc();
    } else {
        $error = "Data awal gagal dibuat. Periksa database PMB.";
    }
}

// Simpan perubahan data diri
if ($_SERVER["REQUEST_METHOD"] === "POST" && $m) {
    $nama = trim($_POST["nama"] ?? "");
    $nik = trim($_POST["nik"] ?? "");
    $tempat = trim($_POST["tempat"] ?? "");
    $tgl = trim($_POST["tanggal_lahir"] ?? "");
    $jk = trim($_POST["jk"] ?? "");
    $alamat = trim($_POST["alamat"] ?? "");
    $sekolah = trim($_POST["sekolah"] ?? "");
    $jurusan = trim($_POST["jurusan"] ?? "");

    $daftarJurusan = [
        "Teknik Informatika",
        "Sistem Informasi",
        "Manajemen",
        "Akuntansi",
        "Ilmu Komunikasi",
        "Ilmu Hukum",
        "Psikologi",
        "Teknik Industri"
    ];

    if ($nama === "" || !in_array($jurusan, $daftarJurusan, true)) {
        $error = "Nama lengkap dan jurusan wajib diisi dengan benar.";
    } else {
        $tglDB = $tgl !== "" ? $tgl : null;

        $up = $conn->prepare(
            "UPDATE calon_maba
             SET nama_lengkap = ?, nik = ?, tempat_lahir = ?,
                 tanggal_lahir = ?, jenis_kelamin = ?, alamat = ?,
                 asal_sekolah = ?, jurusan = ?
             WHERE user_id = ?"
        );

        $up->bind_param(
            "ssssssssi",
            $nama,
            $nik,
            $tempat,
            $tglDB,
            $jk,
            $alamat,
            $sekolah,
            $jurusan,
            $userId
        );

        if ($up->execute()) {
            $msg = "Data diri berhasil disimpan.";

            // Ambil kembali data terbaru dari database
            $stmt = $conn->prepare(
                "SELECT * FROM calon_maba WHERE user_id = ? LIMIT 1"
            );
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $m = $stmt->get_result()->fetch_assoc();
        } else {
            $error = "Data gagal disimpan. Silakan coba lagi.";
        }
    }
}

function e($value) {
    return htmlspecialchars((string)($value ?? ""), ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Diri - SELMA UB</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="layout">

    <aside class="sidebar">
        <div class="brand">SELMA<span>UB</span></div>
        <a href="dashboard.php">Dashboard</a>
        <a class="active" href="data_diri.php">Data Diri</a>
        <a href="nomor_test.php">Nomor Test</a>
        <a href="test.php">Tes Masuk</a>
        <a href="hasil.php">Hasil Test</a>
        <a href="daftar_ulang.php">Daftar Ulang</a>
        <a href="../index.php#pengumuman">Pengumuman PMB</a>
        <a href="../auth/logout.php">Logout</a>
    </aside>

    <main class="main">
        <h1>Data Diri</h1>
        <p class="muted">Lengkapi data calon mahasiswa.</p>

        <div class="card">

            <?php if ($msg): ?>
                <div class="alert success"><?= e($msg) ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert danger"><?= e($error) ?></div>
            <?php endif; ?>

            <?php if ($m): ?>
            <form method="post" class="grid-form">

                <label>
                    Nama Lengkap
                    <input
                        type="text"
                        name="nama"
                        value="<?= e($m["nama_lengkap"] ?? $namaAkun) ?>"
                        required
                    >
                </label>

                <label>
                    NIK
                    <input type="text" name="nik"
                        value="<?= e($m["nik"] ?? "") ?>">
                </label>

                <label>
                    Tempat Lahir
                    <input type="text" name="tempat"
                        value="<?= e($m["tempat_lahir"] ?? "") ?>">
                </label>

                <label>
                    Tanggal Lahir
                    <input type="date" name="tanggal_lahir"
                        value="<?= e($m["tanggal_lahir"] ?? "") ?>">
                </label>

                <label>
                    Jenis Kelamin
                    <select name="jk">
                        <option value="">Pilih</option>
                        <option value="Laki-laki"
                            <?= ($m["jenis_kelamin"] ?? "") === "Laki-laki" ? "selected" : "" ?>>
                            Laki-laki
                        </option>
                        <option value="Perempuan"
                            <?= ($m["jenis_kelamin"] ?? "") === "Perempuan" ? "selected" : "" ?>>
                            Perempuan
                        </option>
                    </select>
                </label>

                <label>
                    Asal Sekolah
                    <input type="text" name="sekolah"
                        value="<?= e($m["asal_sekolah"] ?? "") ?>">
                </label>

                <label>
                    Jurusan / Program Studi
                    <select name="jurusan" required>
                        <option value="">Pilih jurusan</option>
                        <?php
                        $jurusanTerpilih = $m["jurusan"] ?? "";
                        $daftarJurusan = [
                            "Teknik Informatika",
                            "Sistem Informasi",
                            "Manajemen",
                            "Akuntansi",
                            "Ilmu Komunikasi",
                            "Ilmu Hukum",
                            "Psikologi",
                            "Teknik Industri"
                        ];

                        foreach ($daftarJurusan as $j):
                        ?>
                            <option value="<?= e($j) ?>"
                                <?= $jurusanTerpilih === $j ? "selected" : "" ?>>
                                <?= e($j) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label class="wide">
                    Alamat
                    <textarea name="alamat" rows="4"><?= e($m["alamat"] ?? "") ?></textarea>
                </label>

                <div class="wide">
                    <button type="submit" class="btn primary">
                        Simpan Data
                    </button>
                </div>

            </form>
            <?php endif; ?>

        </div>
    </main>
</div>
</body>
</html>
