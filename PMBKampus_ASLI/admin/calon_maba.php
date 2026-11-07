<?php
require_once "../config/koneksi.php"; require_once "../config/auth.php"; require_role("admin");
if(isset($_GET["hapus"])){ $id=(int)$_GET["hapus"]; $s=$conn->prepare("DELETE FROM calon_maba WHERE id=?"); $s->bind_param("i",$id); $s->execute(); header("Location: calon_maba.php"); exit; }
if($_SERVER["REQUEST_METHOD"]==="POST"){
$id=(int)$_POST["id"]; $nama=$_POST["nama"]; $nik=$_POST["nik"]; $tempat=$_POST["tempat"]; $tgl=$_POST["tgl"]; $jk=$_POST["jk"]; $alamat=$_POST["alamat"]; $sekolah=$_POST["sekolah"]; $jurusan=$_POST["jurusan"]; $status=$_POST["status"];
$s=$conn->prepare("UPDATE calon_maba SET nama_lengkap=?,nik=?,tempat_lahir=?,tanggal_lahir=?,jenis_kelamin=?,alamat=?,asal_sekolah=?,jurusan=?,status=? WHERE id=?");
$s->bind_param("sssssssssi",$nama,$nik,$tempat,$tgl,$jk,$alamat,$sekolah,$jurusan,$status,$id); $s->execute(); header("Location: calon_maba.php"); exit;
}
$edit=null;if(isset($_GET["edit"])){ $id=(int)$_GET["edit"]; $s=$conn->prepare("SELECT * FROM calon_maba WHERE id=?");$s->bind_param("i",$id);$s->execute();$edit=$s->get_result()->fetch_assoc(); }
$data=$conn->query("SELECT * FROM calon_maba ORDER BY id DESC");
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Data Maba</title><link rel="stylesheet" href="../css/style.css"></head><body>
<div class="layout"><aside class="sidebar"><div class="brand">PMB<span>Admin</span></div><a href="dashboard.php">Dashboard</a><a class="active" href="calon_maba.php">Data Calon Maba</a><a href="soal.php">Data Soal</a><a href="hasil_test.php">Hasil Test</a><a href="daftar_ulang.php">Daftar Ulang</a><a href="../auth/logout.php">Logout</a></aside>
<main class="main"><h1>Data Calon Maba</h1><p class="muted">CRUD data calon mahasiswa.</p>
<?php if($edit): ?><div class="card"><h2>Edit Data Maba</h2><form method="post" class="grid-form"><input type="hidden" name="id" value="<?=$edit["id"]?>">
<label>Nama Lengkap<input name="nama" value="<?=htmlspecialchars($edit["nama_lengkap"])?>" required></label><label>NIK<input name="nik" value="<?=htmlspecialchars($edit["nik"]??"")?>"></label>
<label>Tempat Lahir<input name="tempat" value="<?=htmlspecialchars($edit["tempat_lahir"]??"")?>"></label><label>Tanggal Lahir<input type="date" name="tgl" value="<?=htmlspecialchars($edit["tanggal_lahir"]??"")?>"></label>
<label>Jenis Kelamin<select name="jk"><option value="">Pilih</option><option <?=($edit["jenis_kelamin"]??"")==="Laki-laki"?"selected":""?>>Laki-laki</option><option <?=($edit["jenis_kelamin"]??"")==="Perempuan"?"selected":""?>>Perempuan</option></select></label>
<label>Asal Sekolah<input name="sekolah" value="<?=htmlspecialchars($edit["asal_sekolah"]??"")?>"></label>
<label>Jurusan<select name="jurusan"><option value="">Pilih jurusan</option><?php foreach(["Teknik Informatika","Sistem Informasi","Manajemen","Akuntansi","Ilmu Komunikasi","Ilmu Hukum","Psikologi","Teknik Industri"] as $j): ?><option value="<?=htmlspecialchars($j)?>" <?=($edit["jurusan"]??"")===$j?"selected":""?>><?=htmlspecialchars($j)?></option><?php endforeach;?></select></label>
<label>Status<select name="status"><?php foreach(["Belum Test","Lulus","Tidak Lulus"] as $x): ?><option <?=($edit["status"]===$x)?"selected":""?>><?=$x?></option><?php endforeach;?></select></label>
<label class="wide">Alamat<textarea name="alamat"><?=htmlspecialchars($edit["alamat"]??"")?></textarea></label><div class="wide"><button class="btn primary">Simpan</button> <a class="btn secondary" href="calon_maba.php">Batal</a></div></form></div><?php endif;?>
<div class="card table-wrap"><table><tr><th>No</th><th>Nomor Test</th><th>Nama</th><th>Asal Sekolah</th><th>Jurusan</th><th>Status</th><th>Aksi</th></tr><?php $n=1;while($r=$data->fetch_assoc()):?><tr><td><?=$n++?></td><td><?=htmlspecialchars($r["nomor_test"])?></td><td><?=htmlspecialchars($r["nama_lengkap"])?></td><td><?=htmlspecialchars($r["asal_sekolah"]??"-")?></td><td><?=htmlspecialchars($r["jurusan"]??"-")?></td><td><?=$r["status"]?></td><td><a class="edit-link" href="?edit=<?=$r["id"]?>">Edit</a> | <a class="danger-link" href="?hapus=<?=$r["id"]?>" onclick="return confirm('Hapus data?')">Hapus</a></td></tr><?php endwhile;?></table></div>
</main></div></body></html>