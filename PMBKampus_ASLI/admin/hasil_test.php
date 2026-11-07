<?php
require_once "../config/koneksi.php"; require_once "../config/auth.php"; require_role("admin");
$notice="";
if($_SERVER["REQUEST_METHOD"]==="POST"){
 $id=(int)($_POST["hasil_id"]??0); $decision=$_POST["keputusan"]??"";
 if($id>0 && in_array($decision,["Lulus","Tidak Lulus"],true)){
  $conn->begin_transaction();
  try{
   $q=$conn->prepare("SELECT calon_maba_id FROM hasil_test WHERE id=? FOR UPDATE");
   $q->bind_param("i",$id);$q->execute();$row=$q->get_result()->fetch_assoc();
   if(!$row) throw new Exception("Hasil tes tidak ditemukan.");
   $u=$conn->prepare("UPDATE hasil_test SET status=? WHERE id=?");
   $u->bind_param("si",$decision,$id);$u->execute();
   $u=$conn->prepare("UPDATE calon_maba SET status=? WHERE id=?");
   $u->bind_param("si",$decision,$row["calon_maba_id"]);$u->execute();
   $conn->commit();$notice="Keputusan kelulusan berhasil disimpan.";
  }catch(Throwable $e){$conn->rollback();$notice="Gagal menyimpan keputusan: ".$e->getMessage();}
 }
}
$data=$conn->query("SELECT h.id hasil_id,c.nomor_test,c.nama_lengkap,c.jurusan,h.jumlah_benar,h.nilai,h.status,h.created_at FROM hasil_test h JOIN calon_maba c ON c.id=h.calon_maba_id ORDER BY h.id DESC");
function e($v){return htmlspecialchars((string)($v??"-"),ENT_QUOTES,"UTF-8");}
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Verifikasi Hasil Tes</title><link rel="stylesheet" href="../css/style.css"><style>.review-actions{display:flex;gap:7px;flex-wrap:wrap}.review-actions button{border:0;border-radius:6px;padding:9px 12px;cursor:pointer;font-weight:700}.approve{background:#e8f4e9;color:#27643a}.reject{background:#fbeaea;color:#9d3030}.waiting{color:#966e17;font-weight:700}</style></head><body><div class="layout">
<aside class="sidebar"><div class="brand">PMB<span>Admin</span></div><a href="dashboard.php">Dashboard</a><a href="calon_maba.php">Data Calon Maba</a><a href="soal.php">Data Soal</a><a class="active" href="hasil_test.php">Verifikasi Hasil Tes</a><a href="daftar_ulang.php">Daftar Ulang</a><a href="pengumuman.php">Pengumuman</a><a href="../auth/logout.php">Logout</a></aside>
<main class="main"><h1>Verifikasi Hasil Tes</h1><p class="muted">Periksa nilai tes, lalu tetapkan keputusan kelulusan resmi.</p>
<?php if($notice): ?><div class="alert success"><?= e($notice) ?></div><?php endif; ?>
<div class="card table-wrap"><table><tr><th>No</th><th>Nomor Tes</th><th>Nama</th><th>Jurusan</th><th>Benar</th><th>Nilai</th><th>Status</th><th>Verifikasi</th></tr>
<?php $no=1;while($r=$data->fetch_assoc()): ?><tr><td><?= $no++ ?></td><td><?= e($r["nomor_test"]) ?></td><td><?= e($r["nama_lengkap"]) ?></td><td><?= e($r["jurusan"]) ?></td><td><?= (int)$r["jumlah_benar"] ?></td><td><b><?= (int)$r["nilai"] ?></b></td><td><?= e($r["status"]) ?></td><td>
<?php if($r["status"]==="Menunggu Verifikasi"): ?><form method="post" class="review-actions"><input type="hidden" name="hasil_id" value="<?= (int)$r["hasil_id"] ?>"><button class="approve" name="keputusan" value="Lulus" onclick="return confirm('Tetapkan mahasiswa ini LULUS?')">Setujui Lulus</button><button class="reject" name="keputusan" value="Tidak Lulus" onclick="return confirm('Tetapkan mahasiswa ini TIDAK LULUS?')">Tidak Lulus</button></form><?php else: ?><span><?= e($r["status"]) ?></span><?php endif; ?>
</td></tr><?php endwhile; ?></table></div></main></div></body></html>