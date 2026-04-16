<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lupa Password</title>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: Arial, Helvetica, sans-serif;
}

body {
  background: linear-gradient(135deg, #4facfe, #00f2fe);
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
}

.container {
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}

.form-box {
  width: 400px;
  background: rgba(255,255,255,0.95);
  padding: 35px 30px;
  border-radius: 18px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.15);
  text-align: center;
}

.form-box h2 {
  margin-bottom: 10px;
  color: #333;
}

.form-box p {
  font-size: 14px;
  color: #666;
  margin-bottom: 25px;
}

.input-group {
  text-align: left;
  margin-bottom: 20px;
}

.input-group label {
  font-size: 13px;
  color: #333;
  margin-bottom: 5px;
  display: block;
}

.input-group input {
  width: 100%;
  padding: 10px;
  border-radius: 8px;
  border: 1px solid #ccc;
  outline: none;
  transition: 0.3s;
}

.input-group input:focus {
  border-color: #4facfe;
}

.btn {
  width: 100%;
  padding: 12px;
  border: none;
  border-radius: 10px;
  background: #4facfe;
  color: white;
  font-size: 15px;
  cursor: pointer;
  transition: 0.3s;
}

.btn:hover {
  background: #00c6ff;
}

.link {
  margin-top: 15px;
  font-size: 14px;
}

.link a {
  text-decoration: none;
  color: #4facfe;
}

.link a:hover {
  text-decoration: underline;
}

.alert {
  background: #ffdddd;
  color: #a00;
  padding: 10px;
  border-radius: 8px;
  margin-bottom: 15px;
  font-size: 14px;
}

.success {
  background: #ddffdd;
  color: #060;
}
</style>

</head>
<body>

<div class="container">
  <div class="form-box">

    <h2>Lupa Password</h2>
    <p>Masukkan username untuk reset password</p>

    <!-- ALERT -->
    <?php 
    if(isset($_GET['error'])) {
      echo '<div class="alert">Username tidak ditemukan!</div>';
    }

    if(isset($_GET['success'])) {
      echo '<div class="alert success">Silakan buat password baru</div>';
    }
    ?>

    <!-- FORM -->
    <form action="proses_lupa_password.php" method="POST">
      
      <div class="input-group">
        <label>Username</label>
        <input type="text" name="username" placeholder="Masukkan username" required>
      </div>

      <button type="submit" class="btn">Reset Password</button>

    </form>

    <div class="link">
      <a href="login_user.php">← Kembali ke Login</a>
    </div>

  </div>
</div>

</body>
</html>