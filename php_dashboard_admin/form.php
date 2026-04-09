<?php
session_start();

// CEK LOGIN
if (!isset($_SESSION['username'])) {
    header("Location: ../login_user.html");
    exit;
}
?>

<h3>Halo, <?php echo $_SESSION['username']; ?> 👋</h3>

<a href="logout.php">Logout</a>

<form action="proses_daftar.php" method="POST">
  <input type="text" name="nama" placeholder="Nama">

  <input type="text" name="dd" placeholder="DD">
  <input type="text" name="mm" placeholder="MM">
  <input type="text" name="yyyy" placeholder="YYYY">

  <input type="text" name="alamat" placeholder="Alamat">
  <input type="text" name="telepon" placeholder="Telepon">

  <select name="penyakit">
    <option value="">Penyakit</option>
    <option>Ada</option>
    <option>Tidak Ada</option>
  </select>

  <input type="text" name="detail" placeholder="Detail">

  <button type="submit">Kirim</button>
</form>