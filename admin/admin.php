<?php
require 'konek.php';
session_start();
if($konek){
  $_SESSION["verif"] = true;
  header("Location: login.php");
  exit;
}
else{
  session_unset();
  session_destroy();
  header("Location: ../home.html");
  exit;
}
?>