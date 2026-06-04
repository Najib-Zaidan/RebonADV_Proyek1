<?php
session_start();

// 1. Proteksi Halaman: Pastikan user sudah login
if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}

require 'konek.php';
require 'fungsi.php';

// 2. Ambil data kiriman dari form dengan aman
$id_private = isset($_POST['id_private']) ? mysqli_real_escape_string($konek, $_POST['id_private']) : '';
$nominal    = isset($_POST['nominal']) ? (int)$_POST['nominal'] : 0;
$catatan    = isset($_POST['catatan']) ? mysqli_real_escape_string($konek, $_POST['catatan']) : '';
$status_bb  = "Belum Diverifikasi"; // Status default setoran bukti bayar baru

// Validasi input dasar jika id_private kosong atau nominal tidak valid
if (empty($id_private) || $nominal <= 0) {
    echo "<script>
            alert('Data pembayaran tidak valid!');
            window.location.href = 'profiluser.php';
          </script>";
    exit;
}

// 3. Manajemen Upload Berkas Bukti Bayar
if (isset($_FILES['bukti_bayar']) && $_FILES['bukti_bayar']['error'] === 0) {
    $nama_asli    = $_FILES['bukti_bayar']['name'];
    $tmp_file     = $_FILES['bukti_bayar']['tmp_name'];
    $ekstensi     = strtolower(pathinfo($nama_asli, PATHINFO_EXTENSION));
    
    // Batasi ekstensi gambar demi keamanan server
    $ekstensi_aman = ['jpg', 'jpeg', 'png', 'webp'];
    
    if (in_array($ekstensi, $ekstensi_aman)) {
        // Standarisasi nama berkas: [TIMESTAMP]_bb_private_[ID_PRIVATE].[EKSTENSI]
        $nama_file = time() . "_bb_private_" . $id_private . "." . $ekstensi;
        $target_dir = "../gambar/payment/";
        $path_tujuan = $target_dir . $nama_file;

        // Pindahkan file dari folder sementara server ke target folder proyek
        if (move_uploaded_file($tmp_file, $path_tujuan)) {
            
            // 4. Masukkan data pembayaran ke tabel payment_private
            $query_insert = "INSERT INTO payment_private (id_private, tgl_bayar, nominal, bukti_bayar, status, catatan) 
                             VALUES ('$id_private', NOW(), '$nominal', '$nama_file', '$status_bb', '$catatan')";
            
            if (kueri($query_insert)) {
                // Alihkan user kembali ke dashboard profile dengan pesan sukses
                echo "<script>
                        alert('Bukti pembayaran berhasil diunggah! Menunggu verifikasi admin.');
                        window.location.href = 'profiluser.php';
                      </script>";
                exit;
            } else {
                // Jika query gagal, hapus file yang telanjur diupload agar tidak menjadi sampah di server
                if (file_exists($path_tujuan)) {
                    unlink($path_tujuan);
                }
                echo "Gagal menyimpan data transaksi ke database: " . mysqli_error($konek);
            }

        } else {
            echo "Gagal memindahkan file gambar ke folder tujuan.";
        }
    } else {
        echo "<script>
                alert('Format berkas tidak didukung! Gunakan format JPG, JPEG, PNG, atau WEBP.');
                window.history.back();
              </script>";
        exit;
    }
} else {
    echo "<script>
            alert('Silakan pilih berkas bukti pembayaran terlebih dahulu!');
            window.history.back();
          </script>";
    exit;
}
?>