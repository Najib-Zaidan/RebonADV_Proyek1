<?php
include "fungsi.php";

$id = $_GET['id'];

if (isset($_POST['setujui_p'])) {
    // 1. Update status batal
    kueri("UPDATE batal_private SET status = 1 WHERE id_batal = '$id'");
    
    // 2. Update status bayar di private_trip
    $batal = ambil(kueri("SELECT id_private FROM batal_private WHERE id_batal = '$id'"));
    $id_pri = $batal['id_private'];
    kueri("UPDATE private_trip SET status_bayar = 'Dibatalkan' WHERE id_private = '$id_pri'");

    echo "<script>alert('Pembatalan Private Trip Berhasil!'); window.location='index.php?menu=pembatalan&tab=private';</script>";
}

$data = ambil(kueri("SELECT bp.*, pt.tujuan, pt.nama AS penanggung_jawab, a.username 
                     FROM batal_private bp 
                     JOIN private_trip pt ON bp.id_private = pt.id_private 
                     JOIN akun a ON pt.id_akun = a.id_akun 
                     WHERE bp.id_batal = '$id'"));
?>

<div style="padding: 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <h2 style="color: #321180;">Detail Pembatalan Private Trip</h2>
    
    <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 10px 20px rgba(0,0,0,0.1); border-left: 8px solid #321180;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 10px; color: #666; width: 200px;">Penanggung Jawab</td>
                <td style="padding: 10px; font-weight: bold; color: #321180;">: <?php echo $data['penanggung_jawab']; ?> (@<?php echo $data['username']; ?>)</td>
            </tr>
            <tr>
                <td style="padding: 10px; color: #666;">Tujuan</td>
                <td style="padding: 10px;">: <?php echo $data['tujuan']; ?></td>
            </tr>
            <tr>
                <td style="padding: 10px; color: #666;">Alasan Pembatalan</td>
                <td style="padding: 10px; background: #f0f0ff; border-radius: 8px; color: #444; line-height: 1.6;">
                    <?php echo $data['alasan']; ?>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px; color: #666;">Diajukan Pada</td>
                <td style="padding: 10px;">: <?php echo date('l, d M Y - H:i', strtotime($data['tgl_pembatalan'])); ?></td>
            </tr>
        </table>

        <div style="margin-top: 30px; display: flex; gap: 10px;">
            <a href="index.php?menu=pembatalan&tab=private" style="padding: 12px 20px; background: #eee; color: #333; border-radius: 8px; text-decoration: none;">Batal</a>
            
            <?php if(!$data['status']): ?>
                <form method="POST">
                    <button type="submit" name="setujui_p" onclick="return confirm('Konfirmasi pembatalan private trip ini?')" 
                            style="padding: 12px 25px; background: #6b3df5; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">
                        Konfirmasi Pembatalan
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>