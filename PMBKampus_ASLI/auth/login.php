<?php
require_once "../config/koneksi.php";
session_start();
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user"] = [
            "id" => $user["id"],
            "nama" => $user["nama"],
            "email" => $user["email"],
            "role" => $user["role"]
        ];
        header("Location: " . ($user["role"] === "admin" ? "../admin/dashboard.php" : "../user/dashboard.php"));
        exit;
    }
    $error = "Email atau password salah.";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login PMB</title><link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="auth-wrap">
<div class="form-card">
    <div class="brand">SELMA<span>UB</span></div>
    <h2>Login</h2>
    <p class="muted">Masuk ke sistem PMB.</p>
    <?php if(isset($_GET["success"])): ?><div class="alert success">Registrasi berhasil. Silakan login.</div><?php endif; ?>
    <?php if($error): ?><div class="alert danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post">
        <label>Email</label><input type="email" name="email" required>
        <label>Password</label><input type="password" name="password" required>
        <button class="btn primary full" type="submit">Login</button>
    </form>
    <p class="bottom-link">Belum punya akun? <a href="register.php">Registrasi</a></p>
    <p class="hint">Admin demo: admin@pmb.test / admin123</p>
</div>
</div>
</body>
</html>