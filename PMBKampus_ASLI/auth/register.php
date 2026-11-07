<?php
require_once "../config/koneksi.php";
session_start();
$error = "";
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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama = trim($_POST["nama"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($nama === "" || $email === "" || $password === "" || $jurusan === "") {
        $error = "Semua data wajib diisi, termasuk pilihan jurusan.";
    } elseif (!in_array($jurusan, $daftarJurusan, true)) {
        $error = "Pilihan jurusan tidak valid.";
    } else {
        $cek = $conn->prepare("SELECT id FROM users WHERE email=?");
        $cek->bind_param("s", $email);
        $cek->execute();
        $result = $cek->get_result();

        if ($result->num_rows > 0) {
            $error = "Email sudah terdaftar.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (nama,email,password,role) VALUES (?,?,?,'user')");
            $stmt->bind_param("sss", $nama, $email, $hash);
            if ($stmt->execute()) {
                $user_id = $stmt->insert_id;
                $nomor = "PMB" . date("Y") . str_pad($user_id, 4, "0", STR_PAD_LEFT);
                $ins = $conn->prepare("INSERT INTO calon_maba (user_id,nomor_test,nama_lengkap,jurusan,status) VALUES (?,?,?,?,'Belum Test')");
                $ins->bind_param("isss", $user_id, $nomor, $nama, $jurusan);
                if ($ins->execute()) {
                    header("Location: login.php?success=1");
                    exit;
                }
                $error = "Akun dibuat, tetapi data pendaftaran gagal disimpan. Hubungi admin.";
            } else {
                $error = "Registrasi gagal.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registrasi PMB</title><link rel="stylesheet" href="../css/style.css">
</head>
<body class="portal-body">
<div class="auth-wrap">
<div class="form-card">
    <div class="brand">SELMA<span>UB</span></div>
    <h2>Registrasi Calon Maba</h2>
    <p class="muted">Buat akun dan pilih program studi yang kamu minati.</p>
    <?php if($error): ?><div class="alert danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post">
        <label>Nama Lengkap</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($_POST["nama"] ?? "") ?>" required>
        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($_POST["email"] ?? "") ?>" required>
        <label>Jurusan / Program Studi</label>
        <select name="jurusan" required>
            <option value="">Pilih jurusan yang diminati</option>
            <?php foreach ($daftarJurusan as $namaJurusan): ?>
            <option value="<?= htmlspecialchars($namaJurusan) ?>" <?= $jurusan === $namaJurusan ? "selected" : "" ?>><?= htmlspecialchars($namaJurusan) ?></option>
            <?php endforeach; ?>
        </select>
        <label>Password</label>
        <input type="password" name="password" minlength="6" required>
        <button class="btn primary full" type="submit">Daftar</button>
    </form>
    <p class="bottom-link">Sudah punya akun? <a href="login.php">Login</a></p>
</div>
</div>
</body>
</html>
