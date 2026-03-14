<?php
session_start();
if(!isset($_SESSION["verif"]) || $_SESSION != true){
  session_unset();
  session_destroy();
  header("Location: home.html");
  exit;
}
echo "berhasil masuk";
session_unset();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin - Rebon Adventure</title>
</head>
<body>
    <h2>Login Admin</h2>
    <form action="proses.php" method="POST">
        <table>
            <tr>
                <td>Username</td>
                <td>:</td>
                <td><input type="text" name="username" required></td>
            </tr>
            <tr>
                <td>Password</td>
                <td>:</td>
                <td><input type="password" name="password" required></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td><button type="submit" name="login">Login</button></td>
            </tr>
        </table>
    </form>
</body>
</html>
