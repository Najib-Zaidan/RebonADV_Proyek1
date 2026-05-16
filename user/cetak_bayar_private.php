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

// Ambil data pembayaran Private Trip
$payment = kueri("
    SELECT 
        p.id_payment,
        p.id_private,
        p.tgl_bayar,
        p.nominal,
        p.status AS status_bayar,
        p.catatan AS catatan_user,
        pt.nama AS nama_pemesan,
        pt.tujuan,
        pt.tgl_berangkat,
        pt.jumlah_peserta
    FROM payment_private p
    JOIN private_trip pt ON p.id_private = pt.id_private
    WHERE p.id_payment = '$id_payment'
    AND pt.id_akun = '$id_akun'
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
    <title>Nota_Bayar_PT_<?= $p['id_payment']; ?></title>
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
            border: 1px dashed #e67e22; /* Border bertema oren asik */
            border-radius: 4px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            color: #d35400; /* Warna oren utama */
            font-size: 22px;
            letter-spacing: 1px;
        }

        .header p {
            margin: 5px 0 0 0;
            font-size: 12px;
            color: #666;
            line-height: 1.4;
        }

        .divider {
            border-top: 1px dashed #e67e22;
            margin: 15px 0;
        }

        .title-nota {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 15px;
            color: #d35400;
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
            color: #d35400;
        }

        .catatan-box {
            background: #fffcf9;
            border-left: 3px solid #e67e22;
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
        <!-- Alamat baru sesuai permintaan -->
        <p>Jl. sukawera No. 15,<br>Cirebon, Indonesia</p>
        <p>support@rebonadventure.com</p>
    </div>

    <div class="divider"></div>
    <div class="title-nota">BUKTI PEMBAYARAN (PRIVATE TRIP)</div>

    <table>
        <tr>
            <td class="label">ID Payment</td>
            <td class="value">#PAY-PT-<?= $p['id_payment']; ?></td>
        </tr>
        <tr>
            <td class="label">ID Private</td>
            <td class="value">#PT-<?= $p['id_private']; ?></td>
        </tr>
        <tr>
            <td class="label">Tanggal Bayar</td>
            <td class="value"><?= date('d/m/Y H:i', strtotime($p['tgl_bayar'])); ?></td>
        </tr>
        <tr>
            <td class="label">Nama Pemesan</td>
            <td class="value"><?= htmlspecialchars($p['nama_pemesan']); ?></td>
        </tr>
        <tr>
            <td class="label">Tujuan Wisata</td>
            <td class="value"><?= htmlspecialchars($p['tujuan']); ?></td>
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
            <td class="value" style="color: <?= ($p['status_bayar'] == 'Diverifikasi') ? '#2ecc71' : (($p['status_bayar'] == 'Ditolak') ? '#e74c3c' : '#e67e22'); ?>;">
                <?= strtoupper($p['status_bayar']); ?>
            </td>
        </tr>
        
        <tr>
            <td colspan="2"><div class="divider"></div></td>
        </tr>
        
        <tr class="total-row">
            <td class="label" style="color: #d35400;">TOTAL BAYAR</td>
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
</script>

</body>
</html>
