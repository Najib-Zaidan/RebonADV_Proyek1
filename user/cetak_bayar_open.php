<?php
session_start();
require 'konek.php';
require 'fungsi.php';

$id_akun = $_SESSION['id_akun'];
$id_payment = $_GET['id_payment'];

if(empty($id_payment)){
    echo "<script>alert('ID pembayaran tidak valid');window.close();</script>";
    exit;
}

// Ambil data pembayaran Open Trip
$payment = kueri("
    SELECT 
        p.id_payment,
        p.id_booking,
        p.tgl_bayar,
        p.nominal,
        p.status AS status_bayar,
        p.catatan AS catatan_user,
        b.jumlah_peserta,
        t.tujuan,
        t.tgl_berangkat
    FROM payment_open p
    JOIN booking b ON p.id_booking = b.id_booking
    JOIN trip t ON b.id_trip = t.id_trip
    WHERE p.id_payment = '$id_payment'
    AND b.id_akun = '$id_akun'
");

$p = ambil($payment);

if(!$p){
    echo "<script>alert('Data tidak ditemukan atau akses ditolak');window.close();</script>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Nota_Bayar_OT_<?= $p['id_payment']; ?></title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace; /* Font khas struk belanja */
            color: #333;
            background: #fff;
            margin: 0;
            padding: 20px;
        }

        .struk-box {
            max-width: 400px;
            margin: auto;
            padding: 20px;
            border: 1px dashed #6b3df5;
            border-radius: 4px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            color: #4922c7;
            font-size: 22px;
            letter-spacing: 1px;
        }

        .header p {
            margin: 5px 0 0 0;
            font-size: 12px;
            color: #666;
        }

        .divider {
            border-top: 1px dashed #6b3df5;
            margin: 15px 0;
        }

        .title-nota {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 15px;
            color: #4922c7;
        }

        table {
            width: 100%;
            font-size: 13px;
            border-collapse: collapse;
        }

        td {
            padding: 4px 0;
            vertical-align: top;
        }

        .label {
            width: 40%;
            color: #555;
        }

        .value {
            width: 60%;
            text-align: right;
            font-weight: bold;
        }

        .total-row {
            font-size: 16px;
            font-weight: bold;
            color: #4922c7;
        }

        .catatan-box {
            background: #fdfbff;
            border-left: 3px solid #6b3df5;
            padding: 8px;
            font-size: 12px;
            margin-top: 10px;
            word-break: break-all;
        }

        .footer {
            text-align: center;
            font-size: 11px;
            color: #888;
            margin-top: 25px;
        }

        /* Aturan CSS khusus saat mode cetak kertas aktif */
        @media print {
            body {
                padding: 0;
            }
            .struk-box {
                border: none;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="struk-box">
    <div class="header">
        <h2>REBON ADVENTURE</h2>
        <p>Jl. sukawera No. 15,
        <br>Cirebon, Indonesia</p>
        <p>support@rebonadventure.com</p>
    </div>

    <div class="divider"></div>
    <div class="title-nota">BUKTI PEMBAYARAN (OPEN TRIP)</div>

    <table>
        <tr>
            <td class="label">ID Payment</td>
            <td class="value">#PAY-OT-<?= $p['id_payment']; ?></td>
        </tr>
        <tr>
            <td class="label">ID Booking</td>
            <td class="value">#BK-<?= $p['id_booking']; ?></td>
        </tr>
        <tr>
            <td class="label">Tanggal Bayar</td>
            <td class="value"><?= date('d/m/Y H:i', strtotime($p['tgl_bayar'])); ?></td>
        </tr>
        <tr>
            <td class="label">Tujuan Wisata</td>
            <td class="value"><?= $p['tujuan']; ?></td>
        </tr>
        <tr>
            <td class="label">Tgl Berangkat</td>
            <td class="value"><?= date('d/m/Y', strtotime($p['tgl_berangkat'])); ?></td>
        </tr>
        <tr>
            <td class="label">Peserta</td>
            <td class="value"><?= $p['jumlah_peserta']; ?> Orang</td>
        </tr>
        <tr>
            <td class="label">Status Verif</td>
            <td class="value" style="color: <?= ($p['status_bayar'] == 'Diverifikasi') ? '#00cc66' : (($p['status_bayar'] == 'Ditolak') ? '#ff334b' : '#e67e22'); ?>;">
                <?= strtoupper($p['status_bayar']); ?>
            </td>
        </tr>
        
        <tr>
            <td colspan="2"><div class="divider"></div></td>
        </tr>
        
        <tr class="total-row">
            <td class="label" style="color: #4922c7;">TOTAL BAYAR</td>
            <td class="value">Rp <?= number_format($p['nominal'], 0, ',', '.'); ?></td>
        </tr>
    </table>

    <div class="divider"></div>
    
    <div style="font-size: 12px; color: #555;">Catatan Anda:</div>
    <div class="catatan-box">
        <?= !empty($p['catatan_user']) ? htmlspecialchars($p['catatan_user']) : '-'; ?>
    </div>

    <div class="footer">
        <p>Terima kasih telah bertransaksi.</p>
        <p style="font-size: 9px; margin-top: 5px;"><?= date('Y-m-d H:i:s'); ?></p>
    </div>
</div>

<!-- Script otomatis pemicu pop-up printer/save PDF browser -->
<script>
    window.print();
    // Menutup tab otomatis setelah jendela print ditutup (opsional)
    // window.onafterprint = function() { window.close(); }
</script>

</body>
</html>
