<?php
require_once "../config/koneksi.php"; require_once "../config/auth.php"; require_role("user");
$userId=(int)($_SESSION["user"]["id"]??0);
$q=$conn->prepare("SELECT * FROM calon_maba WHERE user_id=? LIMIT 1");$q->bind_param("i",$userId);$q->execute();$m=$q->get_result()->fetch_assoc();
if(!$m) exit("Data pendaftaran tidak ditemukan.");
$q=$conn->prepare("SELECT * FROM hasil_test WHERE calon_maba_id=? ORDER BY id DESC LIMIT 1");$q->bind_param("i",$m["id"]);$q->execute();$hasil=$q->get_result()->fetch_assoc();
$q=$conn->prepare("SELECT * FROM daftar_ulang WHERE calon_maba_id=? ORDER BY id DESC LIMIT 1");$q->bind_param("i",$m["id"]);$q->execute();$du=$q->get_result()->fetch_assoc();
$msg="";$error="";
function e($v){return htmlspecialchars((string)($v??""),ENT_QUOTES,"UTF-8");}
if($_SERVER["REQUEST_METHOD"]==="POST" && $hasil && $hasil["status"]==="Lulus" && (!$du || $du["status"]==="Perlu Perbaikan")){
 $nama=trim($_POST["nama_lengkap"]??"");$nik=trim($_POST["nik"]??"");$alamat=trim($_POST["alamat"]??"");$hp=trim($_POST["no_hp"]??"");
 if($nama===""||$nik===""||$alamat===""||$hp==="") $error="Semua data wajib diisi.";
 elseif(!isset($_FILES["foto_diri"])||$_FILES["foto_diri"]["error"]!==UPLOAD_ERR_OK) $error="Foto diri wajib diunggah.";
 else{
  $file=$_FILES["foto_diri"];
  $finfo=new finfo(FILEINFO_MIME_TYPE);$mime=$finfo->file($file["tmp_name"]);
  $allowed=["image/jpeg"=>"jpg","image/png"=>"png","image/webp"=>"webp"];
  if(!isset($allowed[$mime])) $error="Format foto harus JPG, PNG, atau WEBP.";
  elseif($file["size"]>2*1024*1024) $error="Ukuran foto maksimal 2 MB.";
  else{
   $dir=__DIR__."/../uploads/daftar_ulang";
   if(!is_dir($dir)) mkdir($dir,0755,true);
   $filename="du_".$m["id"]."_".bin2hex(random_bytes(6)).".".$allowed[$mime];
   if(!move_uploaded_file($file["tmp_name"],$dir."/".$filename)) $error="Foto gagal disimpan.";
   else{
    $nim="";$status="Menunggu Verifikasi";
    if($du && $du["status"]==="Perlu Perbaikan"){
     $ins=$conn->prepare("UPDATE daftar_ulang SET tanggal_daftar_ulang=CURDATE(),status=?,nim=NULL,nama_lengkap=?,nik=?,alamat=?,no_hp=?,foto_diri=? WHERE id=?");
     $ins->bind_param("sssssi",$status,$nama,$nik,$alamat,$hp,$filename,$du["id"]);
    } else {
     $ins=$conn->prepare("INSERT INTO daftar_ulang (calon_maba_id,tanggal_daftar_ulang,status,nim,nama_lengkap,nik,alamat,no_hp,foto_diri) VALUES (?,CURDATE(),?,?, ?,?,?,?,?)");
     $ins->bind_param("isssssss",$m["id"],$status,$nim,$nama,$nik,$alamat,$hp,$filename);
    }
    if($ins->execute()){$msg="Formulir daftar ulang berhasil dikirim. Tunggu verifikasi admin.";$du=["status"=>$status,"foto_diri"=>$filename];}
    else{$error="Gagal menyimpan data daftar ulang: ".$conn->error;@unlink($dir."/".$filename);}
   }
  }
 }
}
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Daftar Ulang</title><link rel="stylesheet" href="../css/style.css"><style>.du-form{max-width:850px}.du-form label{display:flex;flex-direction:column;gap:7px;font-size:12px;font-weight:700;color:#17365a}.du-form input,.du-form textarea{width:100%;padding:12px;border:1px solid #ded8ca;border-radius:7px}.du-grid{display:grid;grid-template-columns:1fr 1fr;gap:17px}.du-grid .wide{grid-column:1/-1}.photo-hint{font-size:11px;color:#858585;font-weight:400}.notice-box{padding:18px;border-radius:8px;background:#f8f3e7;border-left:4px solid #b18a31}@media(max-width:650px){.du-grid{grid-template-columns:1fr}.du-grid .wide{grid-column:auto}}</style></head><body><div class="layout">
<aside class="sidebar"><div class="brand">SELMA<span>UB</span></div><a href="dashboard.php">Dashboard</a><a href="data_diri.php">Data Diri</a><a href="nomor_test.php">Nomor Test</a><a href="test.php">Tes Masuk</a><a href="hasil.php">Hasil Test</a><a class="active" href="daftar_ulang.php">Daftar Ulang</a><a href="../auth/logout.php">Logout</a></aside>
<main class="main"><h1>Daftar Ulang</h1><p class="muted">Lengkapi kembali identitas dan unggah foto diri untuk proses verifikasi.</p>
<?php if($msg): ?><div class="alert success"><?= e($msg) ?></div><?php endif; ?><?php if($error): ?><div class="alert danger"><?= e($error) ?></div><?php endif; ?>
<?php if(!$hasil || $hasil["status"]!=="Lulus"): ?><div class="card">Formulir daftar ulang akan tersedia setelah hasil tes kamu dinyatakan <b>Lulus</b> oleh admin.</div>
<?php elseif($du && $du["status"]!=="Perlu Perbaikan"): ?><div class="card"><div class="notice-box"><b>Status daftar ulang: <?= e($du["status"]) ?></b><br><?php if($du["status"]==="Menunggu Verifikasi"): ?>Data dan foto diri sudah dikirim. Admin sedang memeriksa berkas kamu.<?php elseif($du["status"]==="Selesai"): ?>Daftar ulang disetujui. NIM kamu: <b><?= e($du["nim"]) ?></b>
<br><br><a class="btn primary" href="kartu_mahasiswa.php">🪪 Lihat / Cetak Kartu Mahasiswa</a><?php else: ?>Silakan hubungi admin untuk informasi perbaikan data.<?php endif; ?></div>
<?php if(!empty($du["foto_diri"])): ?><p><img src="../uploads/daftar_ulang/<?= e($du["foto_diri"]) ?>" alt="Foto daftar ulang" style="margin-top:15px;width:130px;height:160px;object-fit:cover;border-radius:8px"></p><?php endif; ?>
</div>
<?php else: ?><div class="card du-form"><h2>Formulir Daftar Ulang</h2><p class="muted">Pastikan data yang kamu isi benar. Foto harus JPG, PNG, atau WEBP maksimal 2 MB.</p>
<form method="post" enctype="multipart/form-data"><div class="du-grid">
<label>Nama Lengkap<input name="nama_lengkap" value="<?= e($m["nama_lengkap"]) ?>" required></label>
<label>NIK<input name="nik" value="<?= e($m["nik"]) ?>" required></label>
<label class="wide">Nomor HP Aktif<input name="no_hp" type="tel" required placeholder="Contoh: 081234567890"></label>
<label class="wide">Alamat Lengkap<textarea name="alamat" rows="3" required><?= e($m["alamat"]) ?></textarea></label>
<label class="wide">Foto Diri<input type="file" name="foto_diri" accept="image/jpeg,image/png,image/webp" required><span class="photo-hint">Gunakan foto diri terbaru yang jelas. Maksimal 2 MB.</span></label>
</div><button class="btn primary" type="submit" style="margin-top:18px">Kirim Data Daftar Ulang</button></form></div><?php endif; ?>
</main></div></body></html>