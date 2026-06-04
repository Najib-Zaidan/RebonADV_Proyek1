<?php
session_start();

if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}

require 'konek.php';
require 'fungsi.php';

// 1. Ambil Data Utama dari Form Modul Ubah
$id_trip       = $_POST['id_trip'];
$id_tujuan     = $_POST['id_tujuan'];
$tgl_berangkat = $_POST['tgl_berangkat'];
$tgl_pulang    = $_POST['tgl_pulang'];
$harga         = $_POST['harga'];
$harga_dp      = $_POST['harga_dp'];
$kuota         = $_POST['kuota'];
$rute          = $_POST['rute'];
$publik        = $_POST['publik'];
$catatan       = $_POST['catatan'];
$deskripsi     = $_POST['deskripsi'];

// ==========================================
// [LOGIKA TAMBAHAN] AMBIL DATA LAMA UNTUK DETEKSI PERUBAHAN
// ==========================================
$list_perubahan = [];

// A. Cek Perubahan Informasi Trip Utama beserta Nama Tujuan Lama
$trip_lama = ambil(kueri("SELECT t.*, tj.tujuan FROM trip t JOIN tujuan tj ON t.id_tujuan = tj.id_tujuan WHERE t.id_trip = $id_trip"));
$nama_trip = $trip_lama['tujuan'] . " (" . $trip_lama['rute'] . ")";

if ($trip_lama['id_tujuan'] != $id_tujuan) {
    $list_perubahan[] = "tujuan";
}
if ($trip_lama['tgl_berangkat'] != $tgl_berangkat || $trip_lama['tgl_pulang'] != $tgl_pulang) {
    $list_perubahan[] = "tanggal pelaksanaan";
}
if ($trip_lama['harga'] != $harga || $trip_lama['harga_dp'] != $harga_dp) {
    $list_perubahan[] = "rincian harga";
}
if ($trip_lama['kuota'] != $kuota) {
    $list_perubahan[] = "kuota trip";
}
if ($trip_lama['rute'] != $rute) {
    $list_perubahan[] = "rute perjalanan";
}
if ($trip_lama['catatan'] != $catatan) {
    $list_perubahan[] = "catatan tambahan";
}

// B. Cek Perubahan Deskripsi Katalog
$katalog_lama = ambil(kueri("SELECT deskripsi FROM katalog WHERE id_trip = $id_trip"));
if ($katalog_lama['deskripsi'] != $deskripsi) {
    $list_perubahan[] = "deskripsi katalog";
}

// C. Cek Perubahan Itinerary
$res_itinerary_lama = kueri("SELECT mulai, selesai, kegiatan FROM itenerary WHERE id_trip = $id_trip");
$itinerary_lama = [];
while ($row = ambil($res_itinerary_lama)) {
    $itinerary_lama[] = $row['mulai'] . "-" . $row['selesai'] . "-" . $row['kegiatan'];
}
$itinerary_baru = [];
if (isset($_POST['mulai']) && is_array($_POST['mulai'])) {
    foreach ($_POST['mulai'] as $key => $val) {
        $itinerary_baru[] = $_POST['mulai'][$key] . "-" . $_POST['selesai'][$key] . "-" . $_POST['kegiatan'][$key];
    }
}
if ($itinerary_lama !== $itinerary_baru) {
    $list_perubahan[] = "jadwal itinerary";
}

// D. Cek Perubahan Meetpoint
$res_mp_lama = kueri("SELECT waktu, kota, daerah FROM meetpoint WHERE id_trip = $id_trip");
$mp_lama = [];
while ($row = ambil($res_mp_lama)) {
    $mp_lama[] = $row['waktu'] . "-" . $row['kota'] . "-" . $row['daerah'];
}
$mp_baru = [];
if (isset($_POST['waktu_mp']) && is_array($_POST['waktu_mp'])) {
    foreach ($_POST['waktu_mp'] as $key => $val) {
        $mp_baru[] = $_POST['waktu_mp'][$key] . "-" . $_POST['kota_mp'][$key] . "-" . $_POST['daerah_mp'][$key];
    }
}
if ($mp_lama !== $mp_baru) {
    $list_perubahan[] = "titik kumpul (meetpoint)";
}

// E. Cek Perubahan Fasilitas
$res_fasil_lama = kueri("SELECT fasilitas, jenis FROM fasilitas WHERE id_trip = $id_trip");
$fasil_lama = [];
while ($row = ambil($res_fasil_lama)) {
    $fasil_lama[] = $row['fasilitas'] . "-" . $row['jenis'];
}
$fasil_baru = [];
if (isset($_POST['fasilitas']) && is_array($_POST['fasilitas'])) {
    foreach ($_POST['fasilitas'] as $key => $val) {
        $fasil_baru[] = $_POST['fasilitas'][$key] . "-" . $_POST['jenis_fasilitas'][$key];
    }
}
if ($fasil_lama !== $fasil_baru) {
    $list_perubahan[] = "fasilitas trip";
}

// F. Cek Perubahan Gambar (Apakah ada gambar lama dihapus atau ada berkas baru diunggah)
$gambar_tetap_post = isset($_POST['gambar_lama']) ? $_POST['gambar_lama'] : [];
$res_gbr_lama = kueri("SELECT nama_file FROM gambar WHERE id_trip = $id_trip");
$jumlah_gbr_lama = 0;
$ada_gambar_dihapus = false;
while ($row = ambil($res_gbr_lama)) {
    $jumlah_gbr_lama++;
    if (!in_array($row['nama_file'], $gambar_tetap_post)) {
        $ada_gambar_dihapus = true;
    }
}
$ada_gambar_baru = (!empty($_FILES['files']['name'][0]) && $_FILES['files']['name'][0] != "");

if ($ada_gambar_dihapus || $ada_gambar_baru) {
    $list_perubahan[] = "dokumentasi gambar";
}
// ==========================================


// 2. Update Data Utama ke Tabel Trip (Menggunakan id_tujuan terbaru)
$query_update_trip = "UPDATE trip SET 
                        id_tujuan = '$id_tujuan', 
                        tgl_berangkat = '$tgl_berangkat', 
                        tgl_pulang = '$tgl_pulang', 
                        harga = '$harga', 
                        harga_dp = '$harga_dp', 
                        kuota = '$kuota', 
                        rute = '$rute', 
                        publik = '$publik', 
                        catatan = '$catatan' 
                      WHERE id_trip = $id_trip";
kueri($query_update_trip);

// 3. Update Deskripsi ke Tabel Katalog
kueri("UPDATE katalog SET deskripsi = '$deskripsi' WHERE id_trip = $id_trip");

// 4. Sinkronisasi Data Itinerary (Hapus Lama, Masukkan Baru)
kueri("DELETE FROM itenerary WHERE id_trip = $id_trip");
if (isset($_POST['mulai']) && is_array($_POST['mulai'])) {
    foreach ($_POST['mulai'] as $key => $val) {
        $mulai    = $_POST['mulai'][$key];
        $selesai  = $_POST['selesai'][$key];
        $kegiatan = $_POST['kegiatan'][$key];
        kueri("INSERT INTO itenerary (id_trip, mulai, selesai, kegiatan) VALUES ($id_trip, '$mulai', '$selesai', '$kegiatan')");
    }
}

// 5. Sinkronisasi Data Meetpoint (Hapus Lama, Masukkan Baru)
kueri("DELETE FROM meetpoint WHERE id_trip = $id_trip");
if (isset($_POST['waktu_mp']) && is_array($_POST['waktu_mp'])) {
    foreach ($_POST['waktu_mp'] as $key => $val) {
        $waktu  = $_POST['waktu_mp'][$key];
        $kota   = $_POST['kota_mp'][$key];
        $daerah = $_POST['daerah_mp'][$key];
        kueri("INSERT INTO meetpoint (id_trip, waktu, kota, daerah) VALUES ($id_trip, '$waktu', '$kota', '$daerah')");
    }
}

// 6. Sinkronisasi Data Fasilitas (Hapus Lama, Masukkan Baru)
kueri("DELETE FROM fasilitas WHERE id_trip = $id_trip");
if (isset($_POST['fasilitas']) && is_array($_POST['fasilitas'])) {
    foreach ($_POST['fasilitas'] as $key => $val) {
        $fasil = $_POST['fasilitas'][$key];
        $jenis = $_POST['jenis_fasilitas'][$key];
        kueri("INSERT INTO fasilitas (id_trip, fasilitas, jenis) VALUES ($id_trip, '$fasil', '$jenis')");
    }
}

// 7. Sinkronisasi Berkas Gambar Fisik dan Record Database
$gambar_tetap = isset($_POST['gambar_lama']) ? $_POST['gambar_lama'] : [];
$target_dir = "../gambar/upload/";

// Ambil semua daftar file lama yang tersimpan di database sebelum dihapus
$res_db_gambar = kueri("SELECT nama_file FROM gambar WHERE id_trip = $id_trip");
while ($row = ambil($res_db_gambar)) {
    // Jika file fisik di folder server tidak ada di daftar gambar_tetap form, hapus file fisiknya
    if (!in_array($row['nama_file'], $gambar_tetap)) {
        $file_path = $target_dir . $row['nama_file'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
}

// Hapus semua relasi gambar lama untuk trip ini di database
kueri("DELETE FROM gambar WHERE id_trip = $id_trip");

// Masukkan kembali record gambar lama yang dipertahaman admin ke database
if (!empty($gambar_tetap)) {
    foreach ($gambar_tetap as $nama_lama) {
        kueri("INSERT INTO gambar (id_trip, nama_file) VALUES ($id_trip, '$nama_lama')");
    }
}

// Proses unggah gambar baru (jika ada file baru yang dimasukkan)
if (!empty($_FILES['files']['name'][0])) {
    $ekstensi_aman = ['jpg', 'jpeg', 'png', 'webp'];

    foreach ($_FILES['files']['name'] as $key => $val) {
        if ($_FILES['files']['name'][$key] != "") {
            $nama_asli = $_FILES['files']['name'][$key];
            $tmp_name  = $_FILES['files']['tmp_name'][$key];
            $ekstensi  = strtolower(pathinfo($nama_asli, PATHINFO_EXTENSION));

            if (in_array($ekstensi, $ekstensi_aman)) {
                $nama_file_baru = time() . "_" . $key . "." . $ekstensi;
                $path_tujuan    = $target_dir . $nama_file_baru;

                if (move_uploaded_file($tmp_name, $path_tujuan)) {
                    kueri("INSERT INTO gambar (id_trip, nama_file) VALUES ($id_trip, '$nama_file_baru')");
                }
            }
        }
    }
}


// =========================================================================
// [LOGIKA TAMBAHAN KUSTOM] SINKRONISASI ULANG STATUS PEMBAYARAN BOOKING USER
// =========================================================================
// Logika ini dipicu jika komponen harga atau harga_dp terdeteksi berubah
if (in_array("rincian harga", $list_perubahan)) {
    
    // Ambil semua data booking aktif untuk trip ini (Abaikan data Dibatalkan/Refund)
    $res_all_booking = kueri("SELECT id_booking, jumlah_peserta, status FROM booking WHERE id_trip = $id_trip AND status NOT IN ('Dibatalkan', 'Refund')");
    
    while ($row_booking = ambil($res_all_booking)) {
        $id_booking     = $row_booking['id_booking'];
        $jumlah_peserta = $row_booking['jumlah_peserta'];
        
        // Ambil akumulasi nominal pembayaran yang valid (telah diverifikasi admin)
        $res_payment = kueri("SELECT SUM(nominal) as total_bayar FROM payment_open WHERE id_booking = $id_booking AND status = 'Diverifikasi'");
        $data_payment = ambil($res_payment);
        $total_bayar  = isset($data_payment['total_bayar']) ? (int)$data_payment['total_bayar'] : 0;
        
        // Hitung ambang batas nominal tagihan baru sesuai variabel input terbaru
        $total_harga_baru = (int)$harga * $jumlah_peserta;
        $total_dp_baru    = (int)$harga_dp * $jumlah_peserta;
        
        // Komparasi kecukupan dana untuk memetakan ENUM status baru
        if ($total_bayar >= $total_harga_baru) {
            $status_baru = 'Lunas';
        } elseif ($total_bayar >= $total_dp_baru) {
            $status_baru = 'DP';
        } elseif ($total_bayar > 0) {
            $status_baru = 'Bayar non-DP';
        } else {
            $status_baru = 'Belum Bayar';
        }
        
        // Eksekusi update data jika status komparasi berbeda dari data lawas database
        if ($row_booking['status'] != $status_baru) {
            kueri("UPDATE booking SET status = '$status_baru' WHERE id_booking = $id_booking");
        }
    }
}
// =========================================================================


// ==========================================
// [LOGIKA TAMBAHAN] PROSES GENERATE & KIRIM NOTIFIKASI KE USER
// ==========================================
// Menentukan pesan notifikasi berdasarkan item yang diubah
if (empty($list_perubahan)) {
    $pesan_notif = "Admin telah memperbarui rincian berkas internal pada trip $nama_trip.";
} else {
    // Menggabungkan item menjadi teks terpisah koma, contoh: "fasilitas trip, jadwal itinerary"
    $item_diubah = implode(', ', $list_perubahan);
    $pesan_notif = "Admin telah mengubah komponen [$item_diubah] pada trip $nama_trip.";
    
    // Memberikan catatan tambahan khusus jika harga berubah agar user memeriksa tagihannya kembali
    if (in_array("rincian harga", $list_perubahan)) {
        $pesan_notif .= " Mohon periksa kembali riwayat tagihan Anda karena terjadi penyesuaian status pembayaran.";
    } else {
        $pesan_notif .= " Silakan periksa kembali detail pesanan Anda.";
    }
}

// Ambil semua daftar id_akun unik yang telah membooking trip bersangkutan (Kecuali status dibatalkan)
$res_pemboking = kueri("SELECT DISTINCT id_akun FROM booking WHERE id_trip = $id_trip AND status != 'Dibatalkan'");

while ($user = ambil($res_pemboking)) {
    $id_user_penerima = $user['id_akun'];
    kueri("INSERT INTO notif (pesan, waktu, dibaca, id_akun) VALUES ('$pesan_notif', NOW(), 0, $id_user_penerima)");
}
// ==========================================


// Mengalihkan halaman kembali ke index admin
header("Location: index.php");
exit;
?>