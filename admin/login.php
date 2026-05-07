<?php
require 'konek.php';
session_start();
if($konek){
  if(isset($_SESSION["gagal"])){
    $gagal = $_SESSION["gagal"];
    echo "<srcipt> alert('$gagal'); </srcipt>";
    unset($_SESSION["gagal"]);
  }
  if(isset($_SESSION["login"])){
    header("Location: index.php");
    exit;
  }
}
else{
  header("Location: ../user/home1.php");
}
/* if(!isset($_SESSION["verif"]) || $_SESSION != true){
  session_unset();
  session_destroy();
  header("Location: ../home.html");
  exit;
}
session_unset(); */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin - Rebon Adventure</title>
</head>
<style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
    background-image: url('bg.jpeg'); 
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

body::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: -1;
}

.login-container {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
    width: 100%;
    max-width: 400px;
    color: #fff;
    text-align: center;
}

h2 {
    margin-bottom: 30px;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: #A78BFA;
    font-weight: bold;
}

table {
    width: 100%;
    border-collapse: collapse;
}

tr {
    display: flex;
    flex-direction: column; 
    margin-bottom: 15px;
}

td {
    display: block;
    width: 100%;
    color: #fff;
    padding: 5px 0;
}


@media (max-width: 480px) {
    tr {
        display: flex;
        flex-direction: column;
    }
    .login-container {
        padding: 25px;
    }
}

input[type="text"],
input[type="password"] {
    width: 100%;
    padding: 12px;
    background: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 5px;
    outline: none;
    transition: 0.3s;
}

input:focus {
    box-shadow: 0 0 8px #8B5CF6;
}

button[type="submit"] {
    width: 100%;
    padding: 12px;
    margin-top: 10px;
    border: none;
    border-radius: 5px;
    background-color: #6b3df5;
    color: white;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s ease;
}

button[type="submit"]:hover {
    background-color: #d1d9ff; 
    transform: translateY(-2px);
}

</style>
<body>
  <div class="login-container">
    <h2>Login Admin</h2>
    <form action="proses.php" method="POST">
        <table>
            <tr>
                <td>Username</td>
                <td><input type="text" name="username" required></td>
            </tr>
            <tr>
                <td>Password</td>
                <td><input type="password" name="password" required></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td><button type="submit" name="login">Login</button></td>
            </tr>
        </table>
    </form>
    </div>
</body>
</html>
