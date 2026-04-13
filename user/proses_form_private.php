<?php
require 'fungsi.php';

if (isset($_POST['submit'])){
    $nama =  $_POST['nama'];
    $nohp =  $_POST['nohp'];
    $destinasi = $_POST['destinasi'];
    $tgl_mulai = $_POST['tgl_mulai'];
    $tgl_selesai = $_POST['tgl_selesai'];
    $catatan = $_POST['catatan'];
    $peserta = $_POST['jumlah'];

    kueri("INSERT INTO private (nama, no_hp, tujuan, tgl_berangkat, tgl_pulang, Catatan, jumlah_peserta)
    VALUES ('$nama', '$nohp', '$destinasi', '$tgl_mulai', '$tgl_selesai', '$catatan', $peserta)");
    header("Location: form_member_private.php");

}
?>