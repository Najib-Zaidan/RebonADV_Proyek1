<form action="proses_pembayaran.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id_booking" value="<?php echo $_GET['id_booking']; ?>">
    
    <label>Nominal Pembayaran:</label>
    <input type="number" name="nominal" required>
    <hr>
    <label>Bukti Pembayaran:</label>
    <input type="file" name="bukti_bayar" accept="image/*" required>
    <hr>
    <button type="submit">Kirim Pembayaran</button>
</form>
