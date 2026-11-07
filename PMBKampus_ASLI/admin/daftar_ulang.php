<?php
require_once "../config/koneksi.php"; require_once "../config/auth.php"; require_role("admin");
$notice="";
if($_SERVER["REQUEST_METHOD"]==="POST"){
 $id=(int)($_POST["id"]??0);$aksi=$_POST["aksi"]??"";
 if($id>0 && $aksi==="approve"){
  $q=$conn->prepare("SELECT calon_maba_id,status FROM daftar_ulang WHERE id=? LIMIT 1");$q->bind_param("i",$id);$q->execute();$d=$q->get_result()->fetch_assoc();
  if($d && $d["status"]==="Menunggu Verifikasi"){
   $nim="26".str_pad((string)$d["calon_maba_id"],6,"0",STR_PAD_LEFT);
   $u=$conn->prepare("UPDATE daftar_ulang SET status='Selesai',nim=? WHERE id=?");$u->bind_param("si",$nim,$id);
   if($u->execute())$notice="Daftar ulang disetujui. NIM berhasil diterbitkan.";
  }
 }
 if($id>0 && $aksi==="reject"){
  $u=$conn->prepare("UPDATE daftar_ulang SET status='Perlu Perbaikan' WHERE id=? AND status='Menunggu Verifikasi'");$u->bind_param("i",$id);$u->execute();$notice="Berkas ditandai perlu perbaikan.";
 }
}
$data=$conn->query("SELECT d.*,c.nomor_test,c.jurusan FROM daftar_ulang d JOIN calon_maba c ON c.id=d.calon_maba_id ORDER BY d.id DESC");
function e($v){return htmlspecialchars((string)($v??"-"),ENT_QUOTES,"UTF-8");}
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Verifikasi Daftar Ulang</title><link rel="stylesheet" href="../css/style.css"><style>.student-photo{width:66px;height:82px;object-fit:cover;border-radius:6px;border:1px solid #e5dfd1}.actions{display:flex;gap:6px;flex-wrap:wrap}.actions button{border:0;border-radius:6px;padding:8px 10px;cursor:pointer;font-weight:700}.yes{background:#e8f4e9;color:#27643a}.no{background:#fbeaea;color:#9d3030}</style></head><body><div class="layout">
<aside class="sidebar"><div class="brand">PMB<span>Admin</span></div><a href="dashboard.php">Dashboard</a><a href="calon_maba.php">Data Calon Maba</a><a href="soal.php">Data Soal</a><a href="hasil_test.php">Verifikasi Hasil Tes</a><a class="active" href="daftar_ulang.php">Daftar Ulang</a><a href="pengumuman.php">Pengumuman</a><a href="../auth/logout.php">Logout</a></aside>
<main class="main"><h1>Verifikasi Daftar Ulang</h1><p class="muted">Periksa data dan foto diri. NIM diterbitkan setelah berkas disetujui.</p>
<?php if($notice): ?><div class="alert success"><?= e($notice) ?></div><?php endif; ?>
<div class="card table-wrap"><table><tr><th>No</th><th>Nomor Tes</th><th>Nama</th><th>Jurusan</th><th>NIK</th><th>No. HP</th><th>Alamat</th><th>Foto</th><th>Status</th><th>NIM</th><th>Aksi</th></tr>
<?php $no=1;while($r=$data->fetch_assoc()): ?><tr><td><?= $no++ ?></td><td><?= e($r["nomor_test"]) ?></td><td><?= e($r["nama_lengkap"]) ?></td><td><?= e($r["jurusan"]) ?></td><td><?= e($r["nik"]??"-") ?></td><td><?= e($r["no_hp"]??"-") ?></td><td><?= e($r["alamat"]??"-") ?></td><td><?php if(!empty($r["foto_diri"])): ?><a target="_blank" href="../uploads/daftar_ulang/<?= rawurlencode($r["foto_diri"]) ?>"><img class="student-photo" src="../uploads/daftar_ulang/<?= rawurlencode($r["foto_diri"]) ?>" alt="Foto mahasiswa"></a><?php else: ?>-<?php endif; ?></td><td><?= e($r["status"]) ?></td><td><b><?= e($r["nim"] ?: "Belum terbit") ?></b></td><td>
<?php if($r["status"]==="Menunggu Verifikasi"): ?><form method="post" class="actions"><input type="hidden" name="id" value="<?= (int)$r["id"] ?>"><button class="yes" name="aksi" value="approve" onclick="return confirm('Setujui berkas dan terbitkan NIM?')">Setujui</button><button class="no" name="aksi" value="reject" onclick="return confirm('Tandai berkas perlu perbaikan?')">Perbaikan</button></form><?php else: ?>-<?php endif; ?>
</td><td><?php if($r["status"]==="Selesai"): ?><a class="btn primary" href="kartu_mahasiswa.php?id=<?= (int)$r["id"] ?>">Cetak Kartu</a><?php else: ?>Menunggu<?php endif; ?></td></tr><?php endwhile; ?></table></div></main></div></body></html>