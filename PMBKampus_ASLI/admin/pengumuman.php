<?php
require_once "../config/koneksi.php";
require_once "../config/auth.php";
require_role("admin");

$pesan = "";
if (isset($_GET["hapus"])) {
    $id = (int)$_GET["hapus"];
    $stmt = $conn->prepare("DELETE FROM pengumuman WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: pengumuman.php?msg=deleted");
    exit;
}

$edit = null;
if (isset($_GET["edit"])) {
    $id = (int)$_GET["edit"];
    $stmt = $conn->prepare("SELECT * FROM pengumuman WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit = $stmt->get_result()->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int)($_POST["id"] ?? 0);
    $judul = trim($_POST["judul"] ?? "");
    $kategori = trim($_POST["kategori"] ?? "Informasi PMB");
    $isi = trim($_POST["isi"] ?? "");
    $tanggal = $_POST["tanggal_publikasi"] ?? date("Y-m-d");
    $status = ($_POST["status"] ?? "Draft") === "Publik" ? "Publik" : "Draft";

    if ($judul === "" || $isi === "" || $tanggal === "") {
        $pesan = "Judul, isi, dan tanggal wajib diisi.";
    } elseif ($id > 0) {
        $stmt = $conn->prepare("UPDATE pengumuman SET judul=?,kategori=?,isi=?,tanggal_publikasi=?,status=? WHERE id=?");
        $stmt->bind_param("sssssi", $judul, $kategori, $isi, $tanggal, $status, $id);
        $stmt->execute();
        header("Location: pengumuman.php?msg=saved");
        exit;
    } else {
        $stmt = $conn->prepare("INSERT INTO pengumuman (judul,kategori,isi,tanggal_publikasi,status) VALUES (?,?,?,?,?)");
        $stmt->bind_param("sssss", $judul, $kategori, $isi, $tanggal, $status);
        $stmt->execute();
        header("Location: pengumuman.php?msg=created");
        exit;
    }
}
if (isset($_GET["msg"])) {
    $pesan = ["created"=>"Pengumuman berhasil ditambahkan.", "saved"=>"Pengumuman berhasil diperbarui.", "deleted"=>"Pengumuman berhasil dihapus."][$_GET["msg"]] ?? "";
}
$list = $conn->query("SELECT * FROM pengumuman ORDER BY tanggal_publikasi DESC, id DESC");
function h($v) { return htmlspecialchars((string)($v ?? ""), ENT_QUOTES, "UTF-8"); }
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Pengumuman PMB</title><link rel="stylesheet" href="../css/style.css"></head><body>
<div class="layout"><aside class="sidebar"><div class="brand">PMB<span>Admin</span></div><a href="dashboard.php">Dashboard</a><a href="calon_maba.php">Data Calon Maba</a><a href="soal.php">Data Soal</a><a href="hasil_test.php">Hasil Test</a><a href="daftar_ulang.php">Daftar Ulang</a><a class="active" href="pengumuman.php">Pengumuman</a><a href="../auth/logout.php">Logout</a></aside>
<main class="main"><h1>Pengumuman & Berita PMB</h1><p class="muted">Kelola informasi yang akan ditampilkan pada halaman utama calon mahasiswa.</p>
<?php if ($pesan): ?><div class="alert <?= strpos($pesan,"wajib")!==false ? "danger":"success" ?>"><?= h($pesan) ?></div><?php endif; ?>
<div class="card"><h2><?= $edit ? "Edit Pengumuman" : "Tambah Pengumuman" ?></h2><form method="post" class="grid-form">
<input type="hidden" name="id" value="<?= (int)($edit["id"] ?? 0) ?>">
<label class="wide">Judul<input name="judul" maxlength="180" value="<?= h($edit["judul"] ?? "") ?>" required></label>
<label>Kategori<select name="kategori"><?php foreach(["Informasi PMB","Jadwal Seleksi","Pengumuman Hasil","Beasiswa","Kegiatan Kampus"] as $k): ?><option <?= ($edit["kategori"] ?? "Informasi PMB")===$k ? "selected":"" ?>><?= h($k) ?></option><?php endforeach; ?></select></label>
<label>Tanggal Publikasi<input type="date" name="tanggal_publikasi" value="<?= h($edit["tanggal_publikasi"] ?? date("Y-m-d")) ?>" required></label>
<label>Status<select name="status"><option <?= ($edit["status"] ?? "Draft")==="Draft"?"selected":"" ?>>Draft</option><option <?= ($edit["status"] ?? "")==="Publik"?"selected":"" ?>>Publik</option></select></label>
<label class="wide">Isi Pengumuman<textarea name="isi" rows="5" required><?= h($edit["isi"] ?? "") ?></textarea></label>
<div class="wide"><button class="btn primary" type="submit"><?= $edit ? "Simpan Perubahan" : "Terbitkan Pengumuman" ?></button> <?php if($edit): ?><a class="btn secondary" href="pengumuman.php">Batal</a><?php endif; ?></div>
</form></div>
<div class="card table-wrap"><h2>Daftar Pengumuman</h2><table><tr><th>Judul</th><th>Kategori</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr>
<?php while($r=$list->fetch_assoc()): ?><tr><td><?= h($r["judul"]) ?></td><td><?= h($r["kategori"]) ?></td><td><?= h($r["tanggal_publikasi"]) ?></td><td><span class="status"><?= h($r["status"]) ?></span></td><td><a class="edit-link" href="?edit=<?= (int)$r["id"] ?>">Edit</a> | <a class="danger-link" href="?hapus=<?= (int)$r["id"] ?>" onclick="return confirm('Hapus pengumuman ini?')">Hapus</a></td></tr><?php endwhile; ?>
</table></div></main></div></body></html>