<?php
$konek = mysqli_connect("127.0.0.1", "root", "");
$drop_db = "DROP DATABASE IF EXIST rebon_adventure";
mysqli_query($konek, $drop_db);
?>