<?php
$konek = mysqli_connect("127.0.0.1", "root", "");
$buat_db = "CREATE DATABASE IF NOT EXISTS rebon_adventure";
mysqli_query($konek, $buat_db);
mysqli_select_db($konek, "rebon_adventure");
$katalog = "CREATE TABLE IF NOT EXISTS katalog (
    id_katalog INT AUTO_INCREMENT PRIMARY KEY,
    id_trip INT,
    deskripsi TEXT NOT NULL,
    FOREIGN KEY (id_trip) REFERENCES trip(id_trip) ON DELETE CASCADE
)";
$trip = "CREATE TABLE IF NOT EXISTS trip (
    id_trip INT AUTO_INCREMENT PRIMARY KEY,
    tujuan VARCHAR(50) NOT NULL,
    tgl_berangkat DATE NOT NULL,
    tgl_pulang DATE NOT NULL,
    harga INT NOT NULL,
    harga_dp INT NOT NULL,
    kuota INT NOT NULL,
    rute VARCHAR(100) NOT NULL,
    publik BOOLEAN NOT NULL DEFAULT FALSE,
    catatan TEXT
)";
$gambar = "CREATE TABLE IF NOT EXISTS gambar (
    id_gambar INT AUTO_INCREMENT PRIMARY KEY,
    id_trip INT,
    nama_file VARCHAR(100) NOT NULL,
    FOREIGN KEY (id_trip) REFERENCES trip(id_trip) ON DELETE CASCADE
)";
$itenerary = "CREATE TABLE IF NOT EXISTS itenerary (
    id_itenerary INT AUTO_INCREMENT PRIMARY KEY,
    id_trip INT,
    mulai TIME NOT NULL,
    selesai TIME NOT NULL,
    kegiatan VARCHAR(50) NOT NULL,
    FOREIGN KEY (id_trip) REFERENCES trip(id_trip) ON DELETE CASCADE
)";
$meetpoint = "CREATE TABLE IF NOT EXISTS meetpoint (
    id_meetoint INT AUTO_INCREMENT PRIMARY KEY,
    id_trip INT,
    waktu TIME NOT NULL,
    kota VARCHAR(50) NOT NULL,
    daerah VARCHAR(50) NOT NULL,
    FOREIGN KEY (id_trip) REFERENCES trip(id_trip) ON DELETE CASCADE
)";
$fasilitas = "CREATE TABLE IF NOT EXISTS fasilitas (
    id_fasilitas INT AUTO_INCREMENT PRIMARY KEY,
    id_trip INT,
    fasilitas VARCHAR(100) NOT NULL,
    jenis ENUM('include', 'exclude') NOT NULL,
    FOREIGN KEY (id_trip) REFERENCES trip(id_trip) ON DELETE CASCADE
)";
$booking = "CREATE TABLE IF NOT EXISTS booking (
    id_booking INT AUTO_INCREMENT PRIMARY KEY,
    id_trip INT,
    id_akun INT,
    jumlah_peserta INT NOT NULL,
    tgl_booking DATETIME NOT NULL DEFAULT NOW(),
    status ENUM('Belum Bayar', 'Bayar non-DP', 'DP', 'Lunas', 'Dibatalkan', 'Refund') NOT NULL DEFAULT 'Belum Bayar',
    FOREIGN KEY (id_akun) REFERENCES akun(id_akun) ON DELETE CASCADE,
    FOREIGN KEY (id_trip) REFERENCES trip(id_trip) ON DELETE CASCADE
)";
$detail = "CREATE TABLE IF NOT EXISTS detail (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_booking INT,
    id_peserta INT,
    FOREIGN KEY (id_booking) REFERENCES booking(id_booking) ON DELETE CASCADE,
    FOREIGN KEY (id_peserta) REFERENCES peserta_open(id_peserta) ON DELETE CASCADE
)";
$peserta_ot = "CREATE TABLE IF NOT EXISTS peserta_open (
    id_peserta INT AUTO_INCREMENT PRIMARY KEY,
    id_akun INT,
    nama VARCHAR(100) NOT NULL,
    no_hp VARCHAR(20) NOT NULL,
    usia INT NOT NULL,
    alamat VARCHAR(100) NOT NULL,
    riwayat VARCHAR(50) NOT NULL,
    FOREIGN KEY (id_akun) REFERENCES akun(id_akun) ON DELETE CASCADE
)";
$payment_ot = "CREATE TABLE IF NOT EXISTS payment_open (
    id_payment INT AUTO_INCREMENT PRIMARY KEY,
    id_booking INT,
    tgl_bayar DATETIME NOT NULL DEFAULT NOW(),
    nominal INT NOT NULL,
    bukti_bayar VARCHAR(100) NOT NULL,
    status ENUM('Belum Diverifikasi', 'Diverifikasi', 'Ditolak') NOT NULL DEFAULT 'Belum Diverifikasi',
    catatan TEXT,
    FOREIGN KEY (id_booking) REFERENCES booking(id_booking) ON DELETE CASCADE
)";
$batal_ot = "CREATE TABLE IF NOT EXISTS batal_open (
    id_batal INT AUTO_INCREMENT PRIMARY KEY,
    id_booking INT,
    status BOOLEAN NOT NULL DEFAULT FALSE,
    tgl_pembatalan DATETIME NOT NULL DEFAULT NOW(),
    alasan TEXT NOT NULL,
    FOREIGN KEY (id_booking) REFERENCES booking(id_booking) ON DELETE CASCADE
)";
$akun = "CREATE TABLE IF NOT EXISTS akun (
    id_akun INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(100) NOT NULL,
    role VARCHAR(10) NOT NULL
)";
$private = "CREATE TABLE IF NOT EXISTS private_trip (
    id_private INT AUTO_INCREMENT PRIMARY KEY,
    id_akun INT,
    nama VARCHAR(100) NOT NULL,
    no_hp VARCHAR(20) NOT NULL,
    tujuan VARCHAR(100) NOT NULL,
    tgl_berangkat DATE NOT NULL,
    tgl_pulang DATE NOT NULL,
    tgl_booking DATETIME NOT NULL DEFAULT NOW(),
    catatan TEXT,
    jumlah_peserta INT NOT NULL,
    harga INT DEFAULT NULL,
    harga_dp INT DEFAULT NULL,
    status_trip ENUM('Belum Disetujui', 'Disetujui', 'Ditolak') NOT NULL DEFAULT 'Belum Disetujui',
    status_bayar ENUM('Belum Bayar', 'DP', 'Lunas', 'Dibatalkan') NOT NULL DEFAULT 'Belum Bayar',
    FOREIGN KEY (id_akun) REFERENCES akun(id_akun) ON DELETE CASCADE
)";
$peserta_pt = "CREATE TABLE IF NOT EXISTS peserta_private (
    id_peserta INT AUTO_INCREMENT PRIMARY KEY,
    id_private INT,
    nama VARCHAR(100) NOT NULL,
    usia INT NOT NULL,
    alamat VARCHAR(100) NOT NULL,
    riwayat VARCHAR(50) NOT NULL,
    FOREIGN KEY (id_private) REFERENCES private_trip(id_private) ON DELETE CASCADE
)";
$payment_pt = "CREATE TABLE IF NOT EXISTS payment_private (
    id_payment INT AUTO_INCREMENT PRIMARY KEY,
    id_private INT,
    tgl_bayar DATETIME NOT NULL DEFAULT NOW(),
    nominal INT NOT NULL,
    bukti_bayar VARCHAR(100) NOT NULL,
    status ENUM('Belum Diverifikasi', 'Diverifikasi', 'Ditolak') NOT NULL DEFAULT 'Belum Diverifikasi',
    catatan TEXT,
    FOREIGN KEY (id_private) REFERENCES private_trip(id_private) ON DELETE CASCADE
)";
$batal_pt = "CREATE TABLE IF NOT EXISTS batal_private (
    id_batal INT AUTO_INCREMENT PRIMARY KEY,
    id_private INT,
    status BOOLEAN NOT NULL DEFAULT FALSE,
    tgl_pembatalan DATETIME NOT NULL DEFAULT NOW(),
    alasan TEXT NOT NULL,
    FOREIGN KEY (id_private) REFERENCES private_trip(id_private) ON DELETE CASCADE
)";


mysqli_query($konek, $trip);
mysqli_query($konek, $katalog);
mysqli_query($konek, $gambar);
mysqli_query($konek, $itenerary);
mysqli_query($konek, $meetpoint);
mysqli_query($konek, $fasilitas);
mysqli_query($konek, $akun);
mysqli_query($konek, $peserta_ot);
mysqli_query($konek, $booking);
mysqli_query($konek, $detail);
mysqli_query($konek, $payment_ot);
mysqli_query($konek, $batal_ot);
mysqli_query($konek, $private);
mysqli_query($konek, $peserta_pt);
mysqli_query($konek, $payment_pt);
mysqli_query($konek, $batal_pt);




$insert_trip = "INSERT INTO trip (tujuan, tgl_berangkat, tgl_pulang, harga, harga_dp, kuota, rute, publik, catatan) VALUES
('Gunung Semeru', '2026-01-15', '2026-01-17', 850000, 300000, 15, 'Cirebon - Tol Trans Jawa - Solo - Surabaya - Malang - Tumpang', TRUE, 'Peserta wajib menyerahkan surat keterangan sehat dari dokter maksimal H-3. Perlengkapan camp seperti tenda dan alat masak sudah disediakan oleh panitia. Meeting point di Stasiun Malang Kota Baru.'),
('Gunung Prau', '2026-02-07', '2026-02-08', 450000, 150000, 20, 'Kuningan - Cirebon - Brebes - Tegal - Pemalang - Wonosobo (Dieng)', TRUE, ''),
('Gunung Gede', '2026-02-20', '2026-02-22', 600000, 200000, 12, 'Majalengka - Tol Cipali - Tol Cipularang - Cianjur - Cibodas', TRUE, 'Pendakian dilakukan melalui jalur Cibodas dan turun via jalur Gunung Putri. Pastikan membawa jaket tebal karena suhu di puncak bisa mencapai 5 derajat celcius. Simaksi sudah termasuk dalam biaya pendaftaran.'),
('Gunung Merbabu', '2026-03-10', '2026-03-12', 750000, 250000, 10, 'Indramayu - Tol Trans Jawa - Semarang - Salatiga - Boyolali (Selo)', FALSE, 'Jalur pendakian yang digunakan adalah via Selo yang terkenal dengan pemandangan sabananya. Disarankan membawa powerbank cadangan karena tidak ada akses listrik di pos bayangan. Pendaftaran akan ditutup segera setelah kuota terpenuhi.'),
('Gunung Lawu', '2026-03-25', '2026-03-25', 350000, 100000, 25, 'Cirebon - Tol Trans Jawa - Solo - Karanganyar - Tawangmangu', TRUE, ''),
('Gunung Papandayan', '2026-04-05', '2026-04-06', 550000, 200000, 18, 'Majalengka - Sumedang - Wado - Garut (Cisurupan)', TRUE, 'Sangat direkomendasikan bagi pendaki pemula atau keluarga yang ingin mencoba camping. Fasilitas sudah termasuk tiket masuk cagar alam dan pemandu lokal yang berpengalaman. Jangan lupa membawa kamera untuk mengabadikan momen di hutan mati.'),
('Gunung Slamet', '2026-04-18', '2026-04-20', 900000, 350000, 8, 'Kuningan - Cirebon - Brebes - Bumiayu - Purbalingga (Bambangan)', TRUE, 'Medan pendakian cukup berat dan menantang sehingga fisik harus benar-benar dalam kondisi prima. Kuota sengaja dibatasi agar koordinasi tim selama di jalur lebih terjaga. Harap membawa jas hujan karena cuaca di lokasi sering berubah mendadak.'),
('Gunung Sindoro', '2026-05-12', '2026-05-12', 400000, 150000, 15, 'Cirebon - Brebes - Tegal - Pemalang - Temanggung (Kledung)', TRUE, ''),
('Gunung Sumbing', '2026-05-28', '2026-05-30', 500000, 200000, 12, 'Indramayu - Cirebon - Tol Trans Jawa - Wonosobo (Garung)', FALSE, 'Trip ini menggunakan sistem portir untuk membawa perlengkapan kelompok sehingga beban tas peserta lebih ringan. Kita akan mengejar momen sunrise di puncak sejati. Dokumentasi foto dan video cinematic sudah termasuk dalam paket ini.'),
('Gunung Cikuray', '2026-06-14', '2026-06-15', 450000, 100000, 20, 'Majalengka - Sumedang - Garut (Pemancar)', TRUE, ''),
('Gunung Arjuno', '2026-01-22', '2026-01-24', 700000, 250000, 10, 'Cirebon - Tol Trans Jawa - Surabaya - Pandaan - Tretes', TRUE, 'Pendakian akan melewati jalur Tretes yang dikenal dengan tanjakan aspalnya yang ikonik. Peserta disarankan membawa trekking pole untuk membantu menjaga keseimbangan selama perjalanan. Biaya sudah termasuk makan selama di gunung sebanyak 5 kali.'),
('Gunung Andong', '2026-02-12', '2026-02-12', 250000, 50000, 30, 'Cirebon - Tol Trans Jawa - Semarang - Magelang (Ngablak)', TRUE, ''),
('Gunung Welirang', '2026-02-26', '2026-02-28', 750000, 300000, 12, 'Indramayu - Tol Trans Jawa - Solo - Surabaya - Tretes', TRUE, 'Kita akan mengeksplorasi kawah belerang yang masih aktif dan melihat aktivitas para penambang lokal. Harap membawa masker cadangan yang tebal untuk mengantisipasi aroma belerang yang menyengat. Trip ini digabung dengan pendakian puncak Arjuno jika waktu memungkinkan.'),
('Gunung Ungaran', '2026-03-05', '2026-03-06', 300000, 100000, 20, 'Cirebon - Tol Trans Jawa - Kendal - Semarang (Bandungan)', TRUE, 'Meeting point berada di Basecamp Mawar pada pagi hari sebelum pendakian dimulai. Jalur pendakian cukup santai dan melewati perkebunan kopi milik warga sekitar. Sangat cocok bagi yang ingin menghabiskan akhir pekan singkat dengan pemandangan kota Semarang.'),
('Gunung Muria', '2026-03-18', '2026-03-18', 200000, 50000, 25, 'Cirebon - Tol Trans Jawa - Semarang - Demak - Kudus (Colo)', TRUE, ''),
('Gunung Buthak', '2026-04-10', '2026-04-12', 500000, 150000, 15, 'Majalengka - Tol Trans Jawa - Solo - Kediri - Blitar - Sirah Kencong', FALSE, 'Area camp berada di sabana luas yang memiliki sumber mata air alami yang sangat jernih. Peserta wajib menjaga kebersihan dan membawa kembali sampah masing-masing ke bawah. Pemandangan City Light kota Malang dari sini adalah salah satu yang terbaik di Jawa Timur.'),
('Gunung Penanggungan', '2026-05-02', '2026-05-03', 350000, 100000, 22, 'Cirebon - Tol Trans Jawa - Surabaya - Mojokerto - Trawas', TRUE, ''),
('Gunung Lemongan', '2026-05-18', '2026-05-19', 450000, 150000, 10, 'Cirebon - Tol Trans Jawa - Probolinggo - Lumajang (Klakah)', TRUE, 'Gunung ini merupakan gunung api yang unik karena dikelilingi oleh banyak ranu atau danau kecil di kaki gunungnya. Kita akan berkemah di dekat area puncak untuk menikmati fenomena samudra awan saat fajar. Pastikan fisik siap menghadapi jalur setapak yang cukup rimbun.'),
('Gunung Raung', '2026-06-05', '2026-06-08', 1500000, 500000, 6, 'Cirebon - Tol Trans Jawa - Surabaya - Jember - Bondowoso (Sumber Wringin)', FALSE, 'Khusus untuk pendaki berpengalaman karena akan melewati jalur ekstrem Jembatan Sirotol Mustaqim. Semua peralatan teknis seperti tali, harness, dan helm sudah disiapkan oleh tim porter profesional. Peserta wajib mengikuti simulasi penggunaan alat sebelum mulai mendaki.'),
('Gunung Galunggung', '2026-06-25', '2026-06-25', 150000, 50000, 40, 'Majalengka - Kawali - Ciamis - Tasikmalaya (Singaparna)', TRUE, '')";

$insert_katalog = "INSERT INTO katalog (id_trip, deskripsi) VALUES
(1, 'Gunung Semeru merupakan atap tertinggi di Pulau Jawa dengan puncaknya, Mahameru, yang menjulang setinggi 3.676 mdpl. Gunung ini menawarkan pengalaman pendakian yang sangat ikonik, mulai dari padang sabana Oro-oro Ombo yang dipenuhi bunga Verbena hingga keindahan danau Ranu Kumbolo yang melegenda sebagai tempat istirahat para pendaki.

Pendakian ke Semeru memerlukan persiapan fisik dan mental yang kuat karena medan menuju puncak didominasi oleh pasir dan bebatuan labil yang sangat menguras tenaga. Meskipun menantang, panorama yang disuguhkan dari puncaknya, terutama saat kawah Jonggring Saloko mengeluarkan letupan asap berkala, menjadikannya destinasi impian bagi setiap pendaki.'),

(2, 'Gunung Prau yang terletak di kawasan Dataran Tinggi Dieng, Jawa Tengah, dikenal sebagai gunung dengan pemandangan Golden Sunrise terbaik di Asia Tenggara. Puncaknya berupa hamparan bukit teletubbies yang sangat luas, sehingga memudahkan pendaki untuk menemukan spot berkemah yang nyaman tanpa harus berdesakan.

Karena jalur pendakiannya yang relatif singkat dan tidak terlalu curam, Gunung Prau menjadi pilihan utama bagi pendaki pemula maupun wisatawan keluarga. Dari atas sini, pendaki dapat melihat barisan Gunung Sindoro, Sumbing, Merapi, dan Merbabu yang berjejer rapi menghiasi cakrawala saat cuaca cerah.'),

(3, 'Gunung Gede yang berada di Taman Nasional Gunung Gede Pangrango merupakan salah satu destinasi pendakian terpopuler di Jawa Barat karena aksesnya yang dekat dari Jakarta dan Bandung. Gunung ini memiliki ekosistem hutan hujan tropis yang sangat terjaga, di mana pendaki bisa menjumpai berbagai flora dan fauna endemik selama perjalanan.

Salah satu daya tarik utamanya adalah Alun-alun Surya Kencana, sebuah padang edelweiss seluas 50 hektar yang berada di ketinggian. Selain itu, terdapat kawah aktif yang masih mengepulkan uap belerang dan sumber air panas di tengah jalur pendakian yang memberikan sensasi petualangan yang lengkap dan beragam.'),

(4, 'Gunung Merbabu dikenal sebagai salah satu gunung dengan padang sabana terluas dan tercantik di Pulau Jawa, terutama jika didaki melalui jalur Selo. Jalur ini menawarkan pemandangan padang rumput hijau yang bergulung-gulung dengan latar belakang Gunung Merapi yang tampak sangat gagah di sisi selatan.

Pendakian Merbabu memberikan kepuasan visual di setiap posnya, di mana setiap tanjakan akan membuka sudut pandang baru ke arah lembah yang dalam. Puncaknya yang terdiri dari beberapa titik seperti Kenteng Songo dan Triangulasi memberikan keleluasaan bagi pendaki untuk menikmati keindahan alam Jawa Tengah dari ketinggian 3.142 mdpl.'),

(5, 'Gunung Lawu yang berdiri di perbatasan Jawa Tengah dan Jawa Timur memiliki keunikan tersendiri dibandingkan gunung-gunung lainnya di Pulau Jawa karena nilai sejarah dan spiritualnya yang kental. Di sepanjang jalur pendakian, terdapat berbagai situs petilasan dan warung-warung ikonik, termasuk warung tertinggi di Indonesia yang terletak di dekat puncak.

Medan pendakian Lawu cukup bervariasi, mulai dari hutan pinus yang rapat hingga jalur bebatuan yang tertata rapi. Gunung ini juga terkenal dengan keberadaan satwa burung Jalak Lawu yang konon sering menemani pendaki di sepanjang perjalanan, menambah kesan mistis sekaligus bersahabat bagi siapa saja yang datang berkunjung.'),

(6, 'Gunung Papandayan di Garut merupakan laboratorium alam yang sangat menarik karena memiliki kawah-kawah aktif yang luas dan masih mengeluarkan suara gemuruh uap panas. Jalur pendakiannya yang landai dan tertata menjadikannya tempat favorit bagi pendaki pemula yang ingin melihat fenomena geologi secara langsung tanpa harus melakukan pendakian berat.

Di balik kawahnya yang gersang, Papandayan menyimpan keindahan Hutan Mati dengan batang-batang pohon hitam yang eksotis serta padang edelweiss Tegal Alun yang sangat tenang. Perpaduan antara lanskap vulkanik yang keras dan taman bunga yang indah menjadikan Papandayan sebagai salah satu destinasi paling fotogenik di Jawa Barat.'),

(7, 'Gunung Slamet yang memegang predikat sebagai gunung tertinggi di Jawa Tengah sering disebut sebagai Atap Jawa Tengah karena puncaknya yang sangat dominan di wilayah tersebut. Gunung ini memiliki karakter medan yang cukup berat dengan hutan yang lebat dan jalur pendakian yang terus menanjak tanpa banyak bonus jalan landai.

Mendekati puncak, pendaki akan melewati batas vegetasi dan memasuki area pasir bebatuan yang cukup luas. Dari puncak Slamet, pendaki bisa melihat hampir seluruh garis pantai utara dan selatan Jawa Tengah, memberikan sensasi berdiri di titik tertinggi di tengah-tengah pulau Jawa.'),

(8, 'Gunung Sindoro berdiri tegak berhadapan dengan Gunung Sumbing, sering dijuluki sebagai gunung kembar karena bentuknya yang serupa dari kejauhan. Sindoro memiliki kawah yang sangat luas di puncaknya dengan lubang-lubang solfatara yang terus mengepulkan asap, memberikan kesan gunung api aktif yang sangat kuat.

Karakteristik jalur pendakiannya didominasi oleh perkebunan teh dan hutan yang cukup terbuka, sehingga sinar matahari terasa cukup menyengat namun terbayar dengan pemandangan indah di sepanjang jalan. Pada musim kemarau, keindahan bunga edelweiss di area atas menuju puncak menjadi pemandangan yang menyegarkan mata.'),

(9, 'Gunung Sumbing merupakan gunung tertinggi kedua di Jawa Tengah yang menawarkan jalur pendakian cukup menantang dengan elevasi yang tajam. Gunung ini memiliki kawah eksotis yang di dalamnya terdapat sebuah makam keramat serta sabana yang sering disebut sebagai Segoro Wedi.

Lanskap Sumbing sangat dramatis, terutama saat kabut mulai turun menyelimuti sabananya yang luas. Dari sisi puncak, pendaki bisa melihat pemandangan kota Temanggung dan Magelang yang tampak kecil di bawah, serta deretan gunung-gunung besar lainnya di Jawa Tengah yang bersembunyi di balik awan.'),

(10, 'Gunung Cikuray merupakan puncak tertinggi di wilayah Garut yang terkenal dengan jalurnya yang memiliki kemiringan cukup ekstrem dari awal hingga akhir. Gunung ini tidak memiliki banyak sumber air di sepanjang jalur pendakian, sehingga manajemen logistik bagi pendaki sangat diuji di sini.

Meskipun jalurnya sangat melelahkan, Cikuray menyuguhkan fenomena samudra awan yang luar biasa indah di pagi hari. Puncaknya yang kecil biasanya dipadati pendaki yang ingin menyaksikan matahari terbit di atas gumpalan awan yang seolah-olah menutupi seluruh daratan di bawahnya.'),

(11, 'Gunung Arjuno merupakan salah satu gunung tertinggi di Jawa Timur yang menawarkan tantangan fisik luar biasa, terutama jika mendaki melalui jalur Tretes yang terkenal dengan tanjakan aspal dan bebatuan yang konstan. Meskipun jalurnya cukup menguras tenaga, pendaki akan disuguhkan pemandangan hutan pinus yang asri dan udara pegunungan yang sangat bersih sepanjang perjalanan.

Area puncaknya, yang dikenal sebagai Puncak Ogal-Agil, dikelilingi oleh formasi batuan besar yang artistik dan memberikan pandangan 360 derajat ke arah kota Malang, Surabaya, dan deretan gunung di sekitarnya. Arjuno sering kali didaki bersamaan dengan Gunung Welirang karena letaknya yang bersebelahan dalam satu rangkaian pegunungan Malang Raya.'),

(12, 'Gunung Andong di Magelang adalah destinasi favorit bagi pendaki yang ingin menikmati keindahan alam tanpa harus melakukan perjalanan yang ekstrem. Gunung ini memiliki puncak yang memanjang dengan beberapa titik pandang utama, di mana pendaki bisa melihat kemegahan Gunung Merapi, Merbabu, Sindoro, dan Sumbing secara bersamaan dalam satu garis pandang.

Karena aksesnya yang mudah dan waktu tempuh yang singkat, Andong sering dijuluki sebagai gunung wisata. Fasilitas di basecamp dan ketersediaan warung di dekat puncak menjadikan pengalaman berkemah di sini sangat nyaman, bahkan bagi mereka yang baru pertama kali mencoba mendaki gunung.'),

(13, 'Gunung Welirang terkenal dengan aktivitas vulkaniknya yang masih sangat aktif, ditandai dengan kepulan uap belerang yang sangat pekat di sekitar area puncaknya. Pendaki di sini akan mendapatkan pengalaman unik melihat langsung interaksi para penambang belerang tradisional yang mengangkut beban berat menyusuri jalur setapak yang curam setiap harinya.

Pemandangan di Welirang didominasi oleh lanskap batuan vulkanik dan vegetasi khas pegunungan tinggi. Dari kejauhan, kepulan asap putih dari kawahnya menjadi ciri khas yang membedakannya dari Gunung Arjuno, menciptakan suasana petualangan yang eksotis dan sedikit mistis bagi siapa saja yang mengunjunginya.'),

(14, 'Gunung Ungaran di Semarang menawarkan jalur pendakian yang sangat asri, melewati perkebunan kopi yang luas dan hutan hujan tropis yang rimbun. Jalur via Basecamp Mawar merupakan favorit karena lokasinya yang dekat dengan pusat kota namun tetap menyajikan ketenangan alam yang sangat dicari oleh masyarakat perkotaan saat akhir pekan.

Puncak tertinggi Ungaran, yakni Puncak Banteng Raiders, menyajikan panorama laut utara Jawa yang berkilau di kejauhan serta pemandangan Rawa Pening yang memesona. Gunung ini sangat ideal bagi pendaki yang ingin melakukan pendakian santai sambil menikmati kopi lokal langsung di lereng tempat kopi tersebut ditanam.'),

(15, 'Gunung Muria yang terletak di wilayah Kudus merupakan tempat di mana keindahan alam bersatu dengan kekayaan sejarah spiritual Indonesia. Gunung ini tidak hanya menawarkan jalur pendakian yang hijau dan sejuk, tetapi juga merupakan lokasi dari makam Sunan Muria, salah satu tokoh Wali Songo, yang berada di ketinggian lerengnya.

Medan pendakian di Muria relatif moderat dengan banyak pepohonan besar yang memberikan keteduhan sepanjang jalan. Bagi banyak orang, berkunjung ke Muria adalah perjalanan dualitas: sebuah tantangan fisik mendaki puncak-puncaknya seperti Puncak 29, sekaligus sebuah perjalanan batin yang menenangkan melalui suasana religius yang kental.'),

(16, 'Gunung Buthak yang berada di wilayah perbatasan Malang dan Blitar merupakan permata tersembunyi yang memiliki salah satu sabana terluas dan tercantik di Jawa Timur. Setelah melewati jalur hutan yang cukup rapat, pendaki akan tiba di sebuah padang rumput yang sangat luas dengan mata air alami yang jernih, menjadikannya tempat berkemah yang sangat nyaman.

Pemandangan dari puncak Buthak memberikan sudut pandang yang berbeda terhadap kemegahan Gunung Semeru dan Gunung Arjuno di kejauhan. Keheningan sabananya dan keramahtamahan alam di sekitar area perkemahan menjadikan Buthak sebagai destinasi favorit bagi pendaki yang ingin menjauh sejenak dari hiruk pikuk jalur pendakian yang terlalu ramai.'),

(17, 'Gunung Penanggungan sering dijuluki sebagai Gunung Seribu Candi karena banyaknya situs arkeologi berupa petilasan dan candi pemujaan dari masa kerajaan kuno yang tersebar di lerengnya. Secara fisik, bentuk gunung ini sangat unik karena menyerupai miniatur Gunung Semeru, dengan puncak kerucut yang dikelilingi oleh empat bukit pengapit.

Meskipun ketinggiannya tidak mencapai 2.000 mdpl, jalur pendakiannya melalui Tamiajeng cukup menantang dengan elevasi yang konstan. Berdiri di puncak Penanggungan memberikan sensasi seolah-olah berada di tengah-tengah sejarah masa lalu Jawa, sambil menikmati pemandangan kota Surabaya dan sekitarnya dari ketinggian.'),

(18, 'Gunung Lemongan di Lumajang merupakan gunung api yang unik karena memiliki puluhan kerucut vulkanik dan danau vulkanik (ranu) yang tersebar di sekeliling kakinya. Pendakian di sini menawarkan tantangan tersendiri berupa jalur yang dipenuhi oleh vegetasi rimbun dan tanjakan bebatuan lepas menuju puncaknya yang berbatu.

Keistimewaan Lemongan adalah pemandangan matahari terbit yang sangat dramatis dengan latar belakang Gunung Semeru dan Gunung Argopuro. Fenomena samudra awan di sini sangat sering terjadi, menutupi ranu-ranu di bawahnya sehingga menciptakan pemandangan yang seolah-olah berada di atas dunia lain.'),

(19, 'Gunung Raung di Banyuwangi dikenal sebagai gunung dengan jalur pendakian terekstrem di Pulau Jawa karena puncaknya yang berupa punggungan tipis dengan jurang menganga di kedua sisinya. Puncak Sejati Raung hanya bisa dicapai oleh mereka yang memiliki keberanian tinggi dan penguasaan teknik tali-temali (climbing) yang baik.

Kawah Gunung Raung merupakan salah satu kawah terbesar di Indonesia dengan kedalaman yang luar biasa, menciptakan pemandangan yang sangat kolosal dan megah. Pendakian ini bukan sekadar perjalanan fisik, melainkan sebuah ujian nyali dan disiplin yang akan memberikan kepuasan tak tertandingi bagi para petualang sejati.'),

(20, 'Gunung Galunggung di Tasikmalaya menawarkan pesona kawah hijau yang sangat luas hasil dari letusan dahsyatnya di masa lalu. Berbeda dengan gunung lainnya, akses menuju bibir kawah Galunggung dapat dicapai melalui ratusan anak tangga yang tertata rapi, menjadikannya destinasi yang sangat ramah bagi wisatawan umum.

Di dasar kawahnya terdapat danau yang tenang dan area hijau yang luas, memberikan suasana yang sangat damai dan menyejukkan mata. Selain wisata kawah, kawasan Galunggung juga memiliki fasilitas pemandian air panas alami di kaki gunungnya, yang sangat sempurna untuk merelaksasi otot setelah seharian berjalan mengeksplorasi keindahan puncaknya.')";


$insert_fasilitas = "INSERT INTO fasilitas (id_trip, fasilitas, jenis) VALUES
-- Trip ID 1 (Gunung Semeru)
(1, 'Simaksi dan Tiket Masuk TNBTS', 'include'),
(1, 'Tenda Kapasitas 4 (Isi 3 Orang)', 'include'),
(1, 'Makan 7x Selama Pendakian', 'include'),
(1, 'Peralatan Masak & Makan', 'include'),
(1, 'Guide & Porter Logistik', 'include'),
(1, 'Peralatan Pribadi (Carrier, Jaket, dll)', 'exclude'),
(1, 'Porter Pribadi / Porter Barang', 'exclude'),
(1, 'Transportasi dari Kota Asal ke Malang', 'exclude'),
(1, 'Obat-obatan Pribadi', 'exclude'),
(1, 'Tip Guide dan Driver (Sukarela)', 'exclude'),

-- Trip ID 2 (Gunung Prau)
(2, 'Tiket Masuk Wisata Dieng & Prau', 'include'),
(2, 'Homestay 1 Malam di Dieng', 'include'),
(2, 'Makan Sesuai Jadwal (4x)', 'include'),
(2, 'Dokumentasi Foto & Video', 'include'),
(2, 'Pemandu Lokal Berpengalaman', 'include'),
(2, 'Sewa Alat Camp Pribadi (Sleeping Bag/Matras)', 'exclude'),
(2, 'Pengeluaran Pribadi di Warung Dieng', 'exclude'),
(2, 'Asuransi Perjalanan', 'exclude'),
(2, 'Ojek Menuju Basecamp (Opsional)', 'exclude'),
(2, 'Jaket Tebal & Sarung Tangan', 'exclude'),

-- Trip ID 3 (Gunung Gede)
(3, 'Izin Simaksi Jalur Cibodas/Putri', 'include'),
(3, 'Tenda dan Matras Foil', 'include'),
(3, 'Logistik Team (Bahan Makanan)', 'include'),
(3, 'Alat Masak Gas & Kompor', 'include'),
(3, 'P3K Standar Pendakian', 'include'),
(3, 'Peralatan Makan Pribadi (Piring/Gelas)', 'exclude'),
(3, 'Headlamp / Senter & Baterai Cadangan', 'exclude'),
(3, 'Biaya Parkir Kendaraan di Basecamp', 'exclude'),
(3, 'Makan di Luar Program Trip', 'exclude'),
(3, 'Jas Hujan / Raincoat', 'exclude'),

-- Trip ID 4 (Gunung Merbabu)
(4, 'Simaksi Online via Selo', 'include'),
(4, 'Transportasi Lokal (Jeep/Pick Up)', 'include'),
(4, 'Makan Selama di Gunung (4x)', 'include'),
(4, 'Tenda Dome Double Layer', 'include'),
(4, 'Sertifikat Pendakian', 'include'),
(4, 'Sleeping Bag Pribadi', 'exclude'),
(4, 'Kebutuhan Logistik Tambahan (Camilan)', 'exclude'),
(4, 'Powerbank Cadangan', 'exclude'),
(4, 'Transportasi dari Luar Kota ke Meeting Point', 'exclude'),
(4, 'Sewa Trekking Pole', 'exclude'),

-- Trip ID 5 (Gunung Lawu)
(5, 'Retribusi Pendakian & Basecamp', 'include'),
(5, 'Guide Lokal Jalur Cetho/Cemoro Sewu', 'include'),
(5, 'Tenda Kapasitas 4', 'include'),
(5, 'Air Minum Galon untuk Re-fill', 'include'),
(5, 'Kopi & Teh Hangat di Camp', 'include'),
(5, 'Makan di Warung Mbok Yem', 'exclude'),
(5, 'Tipping Guide', 'exclude'),
(5, 'Ojek dari Stasiun ke Basecamp', 'exclude'),
(5, 'Tissue Basah & Kebutuhan Sanitasi', 'exclude'),
(5, 'Sepatu Gunung & Kaos Kaki', 'exclude'),

-- Trip ID 6 (Gunung Papandayan)
(6, 'Tiket Masuk & Simaksi Cagar Alam', 'include'),
(6, 'Tenda Camping di Area Pondok Saladah', 'include'),
(6, 'Makan 3x (Siang, Malam, Sarapan)', 'include'),
(6, 'Local Guide & Dokumentasi Hutan Mati', 'include'),
(6, 'Peralatan Masak Kelompok', 'include'),
(6, 'Tiket Masuk Pemandian Air Panas', 'exclude'),
(6, 'Sewa Masker Gas (Untuk Area Kawah)', 'exclude'),
(6, 'Keperluan Pribadi & Snack', 'exclude'),
(6, 'Transportasi Menuju Basecamp Garut', 'exclude'),
(6, 'Sewa Matras & Sleeping Bag', 'exclude'),

-- Trip ID 7 (Gunung Slamet)
(7, 'Perizinan Simaksi & Asuransi', 'include'),
(7, 'Tenda Kapasitas 4 (Isi 3 Orang)', 'include'),
(7, 'Porter Logistik & Peralatan Masak', 'include'),
(7, 'Makan 5x Selama Pendakian', 'include'),
(7, 'P3K Standar & Oksigen Kaleng', 'include'),
(7, 'Porter Pribadi (Bawa Tas Peserta)', 'exclude'),
(7, 'Peralatan Camping Pribadi', 'exclude'),
(7, 'Makan di Luar Jadwal Trip', 'exclude'),
(7, 'Transportasi dari Kota Asal', 'exclude'),
(7, 'Gaiters & Trekking Pole', 'exclude'),

-- Trip ID 8 (Gunung Sindoro)
(8, 'Simaksi & Retribusi Basecamp', 'include'),
(8, 'Ojek Basecamp (PP/Sesuai Jalur)', 'include'),
(8, 'Makan Selama di Jalur Pendakian', 'include'),
(8, 'Tenda Dome Double Layer', 'include'),
(8, 'Air Mineral & Re-fill Logistik', 'include'),
(8, 'Peralatan Makan & Minum Pribadi', 'exclude'),
(8, 'Senter / Headlamp', 'exclude'),
(8, 'Pengeluaran Pribadi di Basecamp', 'exclude'),
(8, 'Obat Khusus Penyakit Pribadi', 'exclude'),
(8, 'Sleeping Bag & Jaket Windbreaker', 'exclude'),

-- Trip ID 9 (Gunung Sumbing)
(9, 'Simaksi Jalur Garung / Bowongso', 'include'),
(9, 'Sewa Porter Group (Bawa Tenda/Makan)', 'include'),
(9, 'Makan Berat 4x + Welcome Drink', 'include'),
(9, 'Tenda Camping & Matras', 'include'),
(9, 'Dokumentasi Cinematic (Drone/Mirrorless)', 'include'),
(9, 'Tips Porter & Guide', 'exclude'),
(9, 'Baterai Cadangan & Powerbank', 'exclude'),
(9, 'Sewa Sepatu Gunung', 'exclude'),
(9, 'Camilan Tinggi Kalori (Cokelat/Kurma)', 'exclude'),
(9, 'Biaya Parkir & Penitipan Barang', 'exclude'),

-- Trip ID 10 (Gunung Cikuray)
(10, 'Izin Pendakian & Basecamp', 'include'),
(10, 'Tenda Kapasitas 4', 'include'),
(10, 'Makan 3x Selama Program', 'include'),
(10, 'Guide Leader & Navigator', 'include'),
(10, 'Peralatan Masak & Bahan Bakar Gas', 'include'),
(10, 'Air Minum Tambahan (Cikuray Minim Air)', 'exclude'),
(10, 'Jasa Porter Pribadi', 'exclude'),
(10, 'Jas Hujan Sekali Pakai', 'exclude'),
(10, 'Transportasi Menuju Meeting Point', 'exclude'),
(10, 'Alat Makan Pribadi', 'exclude'),

-- Trip ID 11 (Gunung Arjuno)
(11, 'Simaksi & Perizinan Tahura R. Soerjo', 'include'),
(11, 'Makan 6x (Menu Bergizi Selama Camp)', 'include'),
(11, 'Tenda Dome Kapasitas 4', 'include'),
(11, 'Pemandu Jalur Tretes/Purwosari', 'include'),
(11, 'Gas & Peralatan Masak Tim', 'include'),
(11, 'Sewa Trekking Pole (Wajib Karena Jalur Terjal)', 'exclude'),
(11, 'Peralatan Pribadi (SB, Matras, Jaket)', 'exclude'),
(11, 'Ojek dari Meeting Point ke Pos 1', 'exclude'),
(11, 'Snack & Suplemen Pribadi', 'exclude'),
(11, 'Porter Pribadi untuk Tas Carrier', 'exclude'),

-- Trip ID 12 (Gunung Andong)
(12, 'Tiket Retribusi Pendakian', 'include'),
(12, 'Basecamp & Fasilitas Istirahat', 'include'),
(12, 'Guide & Dokumentasi Foto Group', 'include'),
(12, 'Makan 2x (Malam & Sarapan)', 'include'),
(12, 'Air Mineral Re-fill', 'include'),
(12, 'Sewa Alat Camp (Bagi yang Tidak Membawa)', 'exclude'),
(12, 'Transportasi ke Basecamp Magelang', 'exclude'),
(12, 'Jajan di Warung Puncak Andong', 'exclude'),
(12, 'Powerbank & Baterai Cadangan', 'exclude'),
(12, 'Parkir Motor/Mobil di Basecamp', 'exclude'),

-- Trip ID 13 (Gunung Welirang)
(13, 'Izin Pendakian & Asuransi', 'include'),
(13, 'Tenda & Matras Kelompok', 'include'),
(13, 'Logistik Makanan Utama (5x)', 'include'),
(13, 'Masker Standar Pendakian', 'include'),
(13, 'Pemandu Lokal & Porter Logistik', 'include'),
(13, 'Masker Respirator (Saran Untuk Dekat Kawah)', 'exclude'),
(13, 'Tipping Guide & Porter', 'exclude'),
(13, 'Keperluan Sanitasi & Kamar Mandi Basecamp', 'exclude'),
(13, 'Kebutuhan Pribadi Lainnya', 'exclude'),
(13, 'Transportasi dari Kota Asal', 'exclude'),

-- Trip ID 14 (Gunung Ungaran)
(14, 'Simaksi & Tiket Masuk Basecamp Mawar', 'include'),
(14, 'Tenda & Perlengkapan Camp', 'include'),
(14, 'Makan Sesuai Program (3x)', 'include'),
(14, 'Guide & Coffee Break (Kopi Lokal Ungaran)', 'include'),
(14, 'P3K Standar', 'include'),
(14, 'Sewa Sleeping Bag', 'exclude'),
(14, 'Senter / Headlamp Pribadi', 'exclude'),
(14, 'Makan di Luar Paket', 'exclude'),
(14, 'Biaya Parkir Inap Kendaraan', 'exclude'),
(14, 'Baju Ganti & Jaket Tebal', 'exclude'),

-- Trip ID 15 (Gunung Muria)
(15, 'Izin Pendakian Jalur Kedayu/Nataratama', 'include'),
(15, 'Transportasi Lokal Menuju Basecamp', 'include'),
(15, 'Makan Berat 3x Selama Trip', 'include'),
(15, 'Tenda Kapasitas 4 (Double Layer)', 'include'),
(15, 'Guide & Navigator Jalur', 'include'),
(15, 'Biaya Ziarah (Jika Ingin Mampir)', 'exclude'),
(15, 'Sewa Matras & Perlengkapan Camp Pribadi', 'exclude'),
(15, 'Pengeluaran Pribadi & Snack', 'exclude'),
(15, 'Ojek Pangkalan (Opsional)', 'exclude'),
(15, 'Souvenir Khas Gunung Muria', 'exclude'),

-- Trip ID 16 (Gunung Buthak)
(16, 'Simaksi Jalur Sirah Kencong/Panderman', 'include'),
(16, 'Makan 5x Selama Pendakian & Camp', 'include'),
(16, 'Tenda Kapasitas 4 (Double Layer)', 'include'),
(16, 'Porter Logistik & Peralatan Masak Tim', 'include'),
(16, 'Guide Lokal & Dokumentasi Sabana', 'include'),
(16, 'Transportasi dari Meeting Point ke Basecamp', 'exclude'),
(16, 'Peralatan Pribadi (SB, Matras, Jaket)', 'exclude'),
(16, 'Camilan Pribadi & Obat-obatan Khusus', 'exclude'),
(16, 'Sewa Carrier / Tas Gunung', 'exclude'),
(16, 'Tip Sukarela untuk Guide/Porter', 'exclude'),

-- Trip ID 17 (Gunung Penanggungan)
(17, 'Izin Pendakian & Retribusi Desa', 'include'),
(17, 'Tenda & Matras Kelompok', 'include'),
(17, 'Makan 3x (Sesuai Program)', 'include'),
(17, 'Guide Khusus Jalur Sejarah/Candi', 'include'),
(17, 'Air Mineral & Coffee Break di Puncak', 'include'),
(17, 'Tiket Masuk Situs Arkeologi Tambahan', 'exclude'),
(17, 'Biaya Parkir Kendaraan Inap', 'exclude'),
(17, 'Sewa Headlamp / Senter', 'exclude'),
(17, 'Keperluan Sanitasi Pribadi', 'exclude'),
(17, 'Makan di Luar Jadwal Trip', 'exclude'),

-- Trip ID 18 (Gunung Lemongan)
(18, 'Simaksi & Izin Masuk Kawasan Hutan', 'include'),
(18, 'Tenda Camping Kapasitas 4', 'include'),
(18, 'Makan Berat 4x Selama di Gunung', 'include'),
(18, 'Guide Lokal Jalur Klakah', 'include'),
(18, 'Peralatan Masak & Bahan Bakar', 'include'),
(18, 'Ojek Menuju Batas Hutan (Opsional)', 'exclude'),
(18, 'Sewa Sleeping Bag & Matras', 'exclude'),
(18, 'Keperluan Logistik Tambahan', 'exclude'),
(18, 'Transportasi Menuju Lumajang', 'exclude'),
(18, 'Jasa Porter Pribadi', 'exclude'),

-- Trip ID 19 (Gunung Raung)
(19, 'Simaksi & Asuransi Ekstrem', 'include'),
(19, 'Sewa Alat Teknis (Harness, Helmet, Webbing)', 'include'),
(19, 'Makan 9x Selama Ekspedisi Puncak Sejati', 'include'),
(19, 'Porter Logistik & Porter Air', 'include'),
(19, 'Guide Profesional Khusus Panjat Tebing', 'include'),
(19, 'Transportasi dari Kota Asal ke Banyuwangi', 'exclude'),
(19, 'Sewa Sepatu Khusus Trekking (Wajib Sol Bagus)', 'exclude'),
(19, 'Porter Pribadi (Sangat Disarankan)', 'exclude'),
(19, 'Tipping Wajib untuk Guide Teknis', 'exclude'),
(19, 'Logistik Pribadi / Snack Berenergi', 'exclude'),

-- Trip ID 20 (Gunung Galunggung)
(20, 'Tiket Masuk Objek Wisata Galunggung', 'include'),
(20, 'Akses Tangga Kuning & Area Kawah', 'include'),
(20, 'Local Guide / Pemandu Area Kawah', 'include'),
(20, 'Makan 1x (Lunch Box Premium)', 'include'),
(20, 'Air Mineral & Snack Box', 'include'),
(20, 'Tiket Pemandian Air Panas Cipanas', 'exclude'),
(20, 'Sewa Jasa Ojek Wisata ke Bibir Kawah', 'exclude'),
(20, 'Pengeluaran Belanja Souvenir', 'exclude'),
(20, 'Biaya Parkir Kendaraan', 'exclude'),
(20, 'Peralatan Dokumentasi Pribadi', 'exclude')";

$insert_itenerary = "INSERT INTO itenerary (id_trip, mulai, selesai, kegiatan) VALUES
-- Trip ID 1 (Gunung Semeru - 3 Hari: 2026-01-15 s/d 2026-01-17)
(1, '08:15:00', '09:30:00', 'Briefing & Pengecekan Perlengkapan'),
(1, '10:10:00', '13:45:00', 'Trekking Menuju Ranu Kumbolo'),
(1, '14:00:00', '16:20:00', 'Istirahat & Makan Siang di Danau'),
(1, '19:30:00', '21:15:00', 'Makan Malam & Briefing Summit'),
(1, '00:15:00', '05:45:00', 'Summit Attack Mahameru'),
(1, '06:30:00', '08:10:00', 'Foto Bersama di Puncak Mahameru'),
(1, '11:20:00', '14:15:00', 'Perjalanan Turun ke Kalimati'),
(1, '15:30:00', '17:45:00', 'Camping Ceria di Ranu Kumbolo'),
(1, '08:45:00', '12:10:00', 'Perjalanan Kembali ke Basecamp'),
(1, '13:20:00', '15:00:00', 'Sayonara & Drop Stasiun Malang'),

-- Trip ID 2 (Gunung Prau - 2 Hari: 2026-02-07 s/d 2026-02-08)
(2, '09:40:00', '11:15:00', 'Registrasi & Persiapan Pendakian'),
(2, '11:45:00', '15:20:00', 'Trekking Santai ke Puncak Prau'),
(2, '16:05:00', '18:10:00', 'Menikmati Sunset & Bangun Tenda'),
(2, '19:25:00', '20:45:00', 'Makan Malam & Makrab'),
(2, '04:50:00', '06:15:00', 'Golden Sunrise Gunung Prau'),
(2, '07:20:00', '08:40:00', 'Sarapan & Packing Barang'),
(2, '09:15:00', '12:10:00', 'Perjalanan Turun via Patak Banteng'),
(2, '13:05:00', '14:30:00', 'Bersih Diri & Makan Siang Lokal'),
(2, '15:10:00', '16:45:00', 'Wisata Singkat Candi Arjuna'),
(2, '17:15:00', '18:30:00', 'Perjalanan Pulang ke Meeting Point'),

-- Trip ID 3 (Gunung Gede - 3 Hari: 2026-02-20 s/d 2026-02-22)
(3, '21:30:00', '23:45:00', 'Perjalanan dari Jakarta ke Basecamp'),
(3, '07:15:00', '12:40:00', 'Pendakian Menuju Surya Kencana'),
(3, '14:10:00', '16:30:00', 'Mendirikan Tenda di Padang Edelweiss'),
(3, '19:05:00', '21:10:00', 'Makan Malam di Suasana Dingin'),
(3, '05:10:00', '07:20:00', 'Summit ke Puncak Gede'),
(3, '08:30:00', '10:15:00', 'Eksplorasi Kawah & Foto Puncak'),
(3, '12:45:00', '15:20:00', 'Istirahat & Masak Bareng Tim'),
(3, '07:40:00', '13:10:00', 'Perjalanan Turun via Jalur Cibodas'),
(3, '14:30:00', '16:00:00', 'Mandi Air Panas di Jalur Turun'),
(3, '17:10:00', '19:00:00', 'Pembagian Merchandise & Pulang'),

-- Trip ID 4 (Gunung Merbabu - 3 Hari: 2026-03-10 s/d 2026-03-12)
(4, '08:20:00', '10:05:00', 'Kumpul di Basecamp Selo & Packing'),
(4, '10:35:00', '16:15:00', 'Trekking Menuju Sabana 2'),
(4, '17:00:00', '18:40:00', 'Enjoy Sunset & Masak Sore'),
(4, '03:15:00', '05:30:00', 'Summit Attack Puncak Triangulasi'),
(4, '06:20:00', '08:45:00', 'Foto di Puncak Kenteng Songo'),
(4, '10:10:00', '12:20:00', 'Brunch di Area Sabana'),
(4, '14:05:00', '16:50:00', 'Observasi Alam Merbabu'),
(4, '08:15:00', '12:35:00', 'Perjalanan Turun ke Basecamp'),
(4, '13:40:00', '15:10:00', 'Makan Siang & Istirahat Lelah'),
(4, '16:05:00', '17:45:00', 'Evaluasi Trip & Sayonara'),

-- Trip ID 5 (Gunung Lawu - 1 Hari: 2026-03-25 s/d 2026-03-25)
(5, '00:15:00', '00:45:00', 'Persiapan Tektok Malam'),
(5, '01:05:00', '05:20:00', 'Pendakian Malam via Cemoro Sewu'),
(5, '05:45:00', '07:15:00', 'Sunrise di Puncak Hargo Dumilah'),
(5, '07:45:00', '09:10:00', 'Sarapan di Warung Mbok Yem'),
(5, '09:40:00', '11:15:00', 'Eksplorasi Area Sendang Drajat'),
(5, '11:45:00', '14:50:00', 'Perjalanan Turun ke Basecamp'),
(5, '15:15:00', '16:30:00', 'Bersih Diri & Makan Sore'),
(5, '17:05:00', '18:15:00', 'Wisata ke Kebun Teh Kemuning'),
(5, '19:00:00', '20:15:00', 'Belanja Oleh-oleh Khas Lawu'),
(5, '20:45:00', '21:30:00', 'Drop Peserta ke Stasiun Solo'),

-- Trip ID 6 (Gunung Papandayan - 2 Hari: 2026-04-05 s/d 2026-04-06)
(6, '07:45:00', '08:50:00', 'Briefing & Pemanasan di Camp David'),
(6, '09:15:00', '11:40:00', 'Trekking Melewati Area Kawah Aktif'),
(6, '12:05:00', '13:35:00', 'Makan Siang di Area Ghober Hoet'),
(6, '14:10:00', '15:50:00', 'Pemasangan Tenda di Pondok Saladah'),
(6, '16:15:00', '18:25:00', 'Eksplorasi Hutan Mati & Foto Sesi'),
(6, '19:40:00', '21:15:00', 'Makan Malam & Api Unggun Santai'),
(6, '05:15:00', '08:45:00', 'Sunrise Trekking ke Tegal Alun'),
(6, '09:30:00', '11:10:00', 'Sarapan & Operasi Semut (Bersih Sampah)'),
(6, '12:15:00', '14:40:00', 'Perjalanan Turun ke Basecamp'),
(6, '15:20:00', '17:30:00', 'Relaksasi di Kolam Air Panas & Pulang'),

-- Trip ID 7 (Gunung Slamet - 3 Hari: 2026-04-18 s/d 2026-04-20)
(7, '08:10:00', '09:45:00', 'Cek Logistik & Pendaftaran Simaksi'),
(7, '10:15:00', '16:50:00', 'Trekking Berat Menuju Pos 5'),
(7, '17:30:00', '19:10:00', 'Mendirikan Camp & Masak Malam'),
(7, '02:15:00', '06:40:00', 'Summit Attack Atap Jawa Tengah'),
(7, '07:05:00', '09:15:00', 'Menikmati Samudra Awan di Puncak'),
(7, '11:20:00', '14:15:00', 'Perjalanan Turun ke Area Camp'),
(7, '15:40:00', '18:30:00', 'Istirahat Total & Makan Besar'),
(7, '08:15:00', '13:20:00', 'Trekking Turun ke Basecamp Bambangan'),
(7, '14:05:00', '15:45:00', 'Makan Siang Khas Purbalingga'),
(7, '16:30:00', '18:00:00', 'Evaluasi Tim & Sayonara'),

-- Trip ID 8 (Gunung Sindoro - 1 Hari/Tektok: 2026-05-12 s/d 2026-05-12)
(8, '00:20:00', '01:15:00', 'Kumpul di Basecamp Kledung'),
(8, '01:45:00', '03:10:00', 'Naik Ojek Gunung ke Pos 1'),
(8, '03:30:00', '07:45:00', 'Trekking Menuju Puncak Sindoro'),
(8, '08:10:00', '10:20:00', 'Eksplorasi Kawah Segoro Wedi'),
(8, '10:45:00', '11:55:00', 'Brunch Sambil Menikmati View Sumbing'),
(8, '12:30:00', '15:45:00', 'Perjalanan Turun ke Pos 1'),
(8, '16:10:00', '17:25:00', 'Kembali ke Basecamp & Bersih Diri'),
(8, '18:15:00', '19:40:00', 'Wisata Kuliner di Temanggung'),
(8, '20:05:00', '21:15:00', 'Belanja Oleh-oleh Kopi & Tembakau'),
(8, '21:45:00', '22:30:00', 'Drop Off Peserta & Trip Selesai'),

-- Trip ID 9 (Gunung Sumbing - 3 Hari: 2026-05-28 s/d 2026-05-30)
(9, '09:25:00', '11:10:00', 'Persiapan & Packing Barang Porter'),
(9, '11:40:00', '16:25:00', 'Pendakian Menuju Pos 3'),
(9, '17:15:00', '19:30:00', 'Bangun Tenda & Menikmati City Light'),
(9, '03:10:00', '06:15:00', 'Summit Attack Menuju Puncak Sejati'),
(9, '06:45:00', '08:55:00', 'Foto Sesi Cinematic di Kawah Sumbing'),
(9, '10:20:00', '12:45:00', 'Istirahat & Masak Bersama di Camp'),
(9, '14:30:00', '17:10:00', 'Eksplorasi Sabana Sumbing'),
(9, '08:05:00', '12:50:00', 'Perjalanan Turun ke Jalur Garung'),
(9, '13:45:00', '15:15:00', 'Makan Siang & Pembagian Sertifikat'),
(9, '16:10:00', '18:00:00', 'Kepulangan ke Meeting Point Utama'),

-- Trip ID 10 (Gunung Cikuray - 2 Hari: 2026-06-14 s/d 2026-06-15)
(10, '08:40:00', '09:55:00', 'Kumpul di Basecamp Pemancar'),
(10, '10:20:00', '16:15:00', 'Trekking Jalur Akar Cikuray'),
(10, '17:05:00', '19:10:00', 'Mendirikan Tenda di Pos 7'),
(10, '19:45:00', '21:20:00', 'Makan Malam & Briefing Besok Pagi'),
(10, '03:45:00', '05:25:00', 'Summit Menuju Puncak Cikuray'),
(10, '05:45:00', '08:35:00', 'Menanti Samudra Awan Cikuray'),
(10, '09:15:00', '10:45:00', 'Sarapan & Packing Barang Kembali'),
(10, '11:25:00', '15:10:00', 'Trekking Turun dengan Hati-hati'),
(10, '15:50:00', '17:15:00', 'Bersih Diri di Pemandian Terdekat'),
(10, '17:45:00', '19:00:00', 'Pulang Menuju Meeting Point'),

-- Trip ID 11 (Gunung Arjuno - 3 Hari: 2026-01-22 s/d 2026-01-24)
(11, '09:12:00', '10:45:00', 'Registrasi di Izin Pendakian Tretes'),
(11, '11:05:00', '16:30:00', 'Trekking Jalur Aspal Menuju Kop-kopan'),
(11, '17:15:00', '19:20:00', 'Camp Malam Pertama & Masak Bersama'),
(11, '08:40:00', '13:15:00', 'Pendakian Menuju Pondokan'),
(11, '14:25:00', '16:50:00', 'Istirahat & Persiapan Summit Besok'),
(11, '01:35:00', '06:10:00', 'Summit Attack Puncak Ogal-Agil'),
(11, '06:45:00', '08:50:00', 'Foto Sesi di Atas Awan Arjuno'),
(11, '10:30:00', '15:25:00', 'Perjalanan Turun Kembali ke Pondokan'),
(11, '08:15:00', '13:40:00', 'Trekking Turun ke Basecamp'),
(11, '14:20:00', '16:00:00', 'Makan Besar & Penutupan Trip'),

-- Trip ID 12 (Gunung Andong - 1 Hari/Tektok: 2026-02-12 s/d 2026-02-12)
(12, '01:20:00', '02:05:00', 'Kumpul di Basecamp Sawit'),
(12, '02:25:00', '04:40:00', 'Pendakian Santai Menuju Puncak'),
(12, '05:10:00', '07:30:00', 'Menanti Sunrise di Puncak Makam'),
(12, '08:05:00', '09:15:00', 'Sarapan & Ngopi di Warung Puncak'),
(12, '09:40:00', '11:10:00', 'Eksplorasi Puncak Jiwa & Foto Group'),
(12, '11:35:00', '13:20:00', 'Perjalanan Turun ke Basecamp'),
(12, '13:45:00', '15:10:00', 'Makan Siang Bersama di Lereng Gunung'),
(12, '15:40:00', '17:05:00', 'Wisata ke Area Pertanian Magelang'),
(12, '17:35:00', '18:50:00', 'Belanja Oleh-oleh Khas Magelang'),
(12, '19:15:00', '20:30:00', 'Drop Peserta & Selesai'),

-- Trip ID 13 (Gunung Welirang - 3 Hari: 2026-02-26 s/d 2026-02-28)
(13, '08:25:00', '09:40:00', 'Briefing & Doa Bersama di Basecamp'),
(13, '10:15:00', '15:55:00', 'Trekking Menuju Pos Kop-kopan'),
(13, '16:30:00', '18:15:00', 'Pasang Tenda & Masak Sore'),
(13, '02:40:00', '06:50:00', 'Summit Attack Puncak Welirang'),
(13, '07:20:00', '10:15:00', 'Eksplorasi Kawah Belerang Aktif'),
(13, '11:35:00', '14:20:00', 'Perjalanan Turun ke Camp'),
(13, '15:10:00', '17:45:00', 'Istirahat & Dokumentasi Tim'),
(13, '08:50:00', '13:25:00', 'Trekking Turun via Jalur Tretes'),
(13, '14:10:00', '15:50:00', 'Makan Siang & Bersih Diri'),
(13, '16:20:00', '18:00:00', 'Transfer ke Meeting Point Kembali'),

-- Trip ID 14 (Gunung Ungaran - 2 Hari: 2026-03-05 s/d 2026-03-06)
(14, '09:50:00', '11:15:00', 'Kumpul di Basecamp Mawar'),
(14, '11:45:00', '14:20:00', 'Trekking Jalur Hutan Menuju Promasan'),
(14, '14:50:00', '16:40:00', 'Eksplorasi Kebun Teh Promasan'),
(14, '17:15:00', '19:05:00', 'Bangun Tenda & Menikmati Udara Dingin'),
(14, '20:10:00', '21:35:00', 'Makan Malam & Makrab'),
(14, '03:55:00', '05:40:00', 'Summit Menuju Puncak Botak'),
(14, '06:15:00', '08:30:00', 'Coffee Break Kopi Lokal di Puncak'),
(14, '09:45:00', '11:20:00', 'Sarapan & Packing Ulang'),
(14, '12:05:00', '14:55:00', 'Perjalanan Turun ke Basecamp'),
(14, '15:30:00', '17:00:00', 'Pembagian Kenang-kenangan & Pulang'),

-- Trip ID 15 (Gunung Muria - 1 Hari/Tektok: 2026-03-18 s/d 2026-03-18)
(15, '04:15:00', '05:10:00', 'Persiapan Pagi di Basecamp Kedayu'),
(15, '05:35:00', '08:50:00', 'Pendakian Puncak 29 (Songo Likur)'),
(15, '09:15:00', '11:25:00', 'Eksplorasi Puncak & Foto Landscape'),
(15, '11:50:00', '12:45:00', 'Makan Siang Bekal di Puncak'),
(15, '13:10:00', '15:40:00', 'Perjalanan Turun Jalur Religi'),
(15, '16:15:00', '17:40:00', 'Ziarah ke Makam Sunan Muria (Opsional)'),
(15, '18:05:00', '19:15:00', 'Makan Malam Kuliner Parijoto'),
(15, '19:40:00', '20:50:00', 'Istirahat di Area Wisata Muria'),
(15, '21:15:00', '22:30:00', 'Evaluasi Trip & Perpisahan'),
(15, '22:45:00', '23:30:00', 'Drop Off Peserta Selesai'),

-- Trip ID 16 (Gunung Buthak - 3 Hari: 2026-04-10 s/d 2026-04-12)
(16, '08:45:00', '10:10:00', 'Kumpul di Basecamp Sirah Kencong'),
(16, '10:35:00', '16:45:00', 'Trekking Hutan Menuju Sabana Buthak'),
(16, '17:15:00', '19:30:00', 'Mendirikan Camp & Ambil Air di Sendang'),
(16, '19:50:00', '21:15:00', 'Makan Malam & Makrab Tim'),
(16, '04:15:00', '05:50:00', 'Summit Attack Puncak Buthak'),
(16, '06:15:00', '08:40:00', 'Foto Sesi di Atas Samudra Awan'),
(16, '09:30:00', '11:45:00', 'Masak Bareng & Santai di Sabana'),
(16, '13:05:00', '15:20:00', 'Eksplorasi Flora di Sekitar Camp'),
(16, '08:25:00', '13:40:00', 'Perjalanan Turun ke Basecamp'),
(16, '14:15:00', '15:50:00', 'Bersih Diri & Perpisahan Trip'),

-- Trip ID 17 (Gunung Penanggungan - 2 Hari: 2026-05-02 s/d 2026-05-03)
(17, '13:20:00', '14:45:00', 'Registrasi & Briefing via Tamiajeng'),
(17, '15:10:00', '18:35:00', 'Pendakian Menuju Puncak Bayangan'),
(17, '19:05:00', '20:40:00', 'Makan Malam & Menikmati City Light'),
(17, '03:40:00', '05:15:00', 'Summit Attack Menuju Puncak Pawitra'),
(17, '05:45:00', '07:50:00', 'Sunrise & Foto Sesi Puncak'),
(17, '08:30:00', '10:15:00', 'Eksplorasi Situs Purbakala/Candi'),
(17, '10:45:00', '12:10:00', 'Sarapan & Packing Tenda'),
(17, '12:45:00', '15:20:00', 'Perjalanan Turun ke Basecamp'),
(17, '15:50:00', '17:10:00', 'Makan Bakso Khas Jalur Pendakian'),
(17, '17:35:00', '18:45:00', 'Sayonara & Drop Off Peserta'),

-- Trip ID 18 (Gunung Lemongan - 2 Hari: 2026-05-18 s/d 2026-05-19)
(18, '07:15:00', '08:40:00', 'Kumpul di Meeting Point Ranu Klakah'),
(18, '09:05:00', '15:45:00', 'Trekking Menuju Area Watu Gede'),
(18, '16:20:00', '18:15:00', 'Camping & Menikmati Sore di Hutan'),
(18, '19:30:00', '21:05:00', 'Makan Malam Hasil Masakan Tim'),
(18, '02:45:00', '05:30:00', 'Summit Menuju Puncak Lemongan'),
(18, '05:50:00', '08:10:00', 'Sunrise View Gunung Semeru & Argopuro'),
(18, '09:20:00', '11:15:00', 'Brunch & Istirahat di Camp'),
(18, '11:45:00', '15:20:00', 'Trekking Turun Jalur Berpasir'),
(18, '15:50:00', '17:30:00', 'Wisata Kuliner di Pinggir Ranu'),
(18, '18:05:00', '19:20:00', 'Trip Selesai & Kembali ke Kota'),

-- Trip ID 19 (Gunung Raung - 4 Hari: 2026-06-05 s/d 2026-06-08)
(19, '09:15:00', '11:45:00', 'Briefing Teknis & Pengecekan Alat Safety'),
(19, '13:05:00', '17:40:00', 'Pendakian Menuju Camp 2'),
(19, '08:20:00', '15:10:00', 'Trekking Panjang Menuju Camp 7'),
(19, '16:05:00', '18:30:00', 'Istirahat Total & Kalibrasi Alat Climbing'),
(19, '01:45:00', '05:20:00', 'Summit Attack Via Jembatan Sirotol Mustaqim'),
(19, '05:45:00', '09:15:00', 'Eksplorasi Puncak Sejati & Kawah Besar'),
(19, '11:30:00', '16:45:00', 'Perjalanan Turun Kembali ke Camp 7'),
(19, '19:05:00', '21:10:00', 'Makan Malam Perayaan Keberhasilan'),
(19, '07:45:00', '14:20:00', 'Full Trekking Turun ke Basecamp'),
(19, '15:15:00', '17:00:00', 'Pemberian Brevet/Sertifikat Penanjak'),

-- Trip ID 20 (Gunung Galunggung - 1 Hari: 2026-06-25 s/d 2026-06-25)
(20, '07:35:00', '08:15:00', 'Kumpul di Area Parkir Galunggung'),
(20, '08:40:00', '09:50:00', 'Mendaki 625 Anak Tangga Kuning'),
(20, '10:15:00', '12:40:00', 'Eksplorasi Bibir Kawah & Dasar Kawah'),
(20, '13:05:00', '14:20:00', 'Makan Siang Sambil Menikmati View Hijau'),
(20, '14:45:00', '16:15:00', 'Relaksasi di Pemandian Air Panas Alami'),
(20, '16:40:00', '17:50:00', 'Berburu Foto Aesthetic di Hutan Pinus'),
(20, '18:15:00', '19:30:00', 'Makan Malam Kuliner Khas Tasikmalaya'),
(20, '19:50:00', '21:05:00', 'Belanja Kerajinan & Oleh-oleh'),
(20, '21:30:00', '22:15:00', 'Evaluasi Singkat & Perpisahan'),
(20, '22:30:00', '23:00:00', 'Drop Off Peserta Trip Selesai')";

$insert_meetpoint = "INSERT INTO meetpoint (id_trip, waktu, kota, daerah) VALUES
-- Trip ID 1 (Gunung Semeru)
(1, '05:15:00', 'Cirebon', 'Stasiun Cirebon Prujakan'),
(1, '10:30:00', 'Surabaya', 'Terminal Bungurasih'),
(1, '13:45:00', 'Malang', 'Stasiun Malang Kota Baru'),

-- Trip ID 2 (Gunung Prau)
(2, '06:20:00', 'Indramayu', 'Terminal Terisi'),
(2, '09:15:00', 'Pekalongan', 'Terminal Pekalongan'),
(2, '11:40:00', 'Wonosobo', 'Plaza Wonosobo'),

-- Trip ID 3 (Gunung Gede)
(3, '20:10:00', 'Majalengka', 'Bandara Kertajati (BIJB)'),
(3, '23:45:00', 'Bekasi', 'Rest Area KM 19'),
(3, '02:30:00', 'Cianjur', 'Basecamp Cibodas'),

-- Trip ID 4 (Gunung Merbabu)
(4, '05:45:00', 'Kuningan', 'Terminal Kertawangunan'),
(4, '09:20:00', 'Semarang', 'Stasiun Poncol'),
(4, '11:50:00', 'Boyolali', 'Pasar Cepogo'),

-- Trip ID 5 (Gunung Lawu)
(5, '21:30:00', 'Cirebon', 'Terminal Harjamukti'),
(5, '00:45:00', 'Solo', 'Stasiun Solo Balapan'),
(5, '02:15:00', 'Karanganyar', 'Basecamp Cemoro Sewu'),

-- Trip ID 6 (Gunung Papandayan)
(6, '04:20:00', 'Indramayu', 'Alun-Alun Indramayu'),
(6, '07:15:00', 'Bandung', 'Terminal Leuwi Panjang'),
(6, '09:40:00', 'Garut', 'Simpang Lima Tarogong'),

-- Trip ID 7 (Gunung Slamet)
(7, '06:10:00', 'Majalengka', 'Terminal Maja'),
(7, '09:35:00', 'Tegal', 'Stasiun Tegal'),
(7, '11:50:00', 'Purbalingga', 'Basecamp Bambangan'),

-- Trip ID 8 (Gunung Sindoro)
(8, '22:15:00', 'Kuningan', 'Taman Cirendang'),
(8, '01:30:00', 'Magelang', 'Terminal Tidar'),
(8, '03:10:00', 'Temanggung', 'Basecamp Kledung'),

-- Trip ID 9 (Gunung Sumbing)
(9, '06:40:00', 'Cirebon', 'Stasiun Cirebon Kejaksan'),
(9, '10:20:00', 'Kebumen', 'Stasiun Kebumen'),
(9, '12:45:00', 'Wonosobo', 'Basecamp Garung'),

-- Trip ID 10 (Gunung Cikuray)
(10, '05:30:00', 'Majalengka', 'Kadipaten'),
(10, '08:15:00', 'Tasikmalaya', 'Terminal Indihiang'),
(10, '10:40:00', 'Garut', 'Basecamp Pemancar'),

-- Trip ID 11 (Gunung Arjuno)
(11, '06:25:00', 'Indramayu', 'Stasiun Jatibarang'),
(11, '11:40:00', 'Semarang', 'Rest Area KM 429'),
(11, '15:15:00', 'Pasuruan', 'Pasar Wisata Tretes'),

-- Trip ID 12 (Gunung Andong)
(12, '23:10:00', 'Cirebon', 'Taman Sumber'),
(12, '02:45:00', 'Semarang', 'Simpang Lima'),
(12, '04:20:00', 'Magelang', 'Basecamp Sawit'),

-- Trip ID 13 (Gunung Welirang)
(13, '05:40:00', 'Majalengka', 'Bunderan Cigasong'),
(13, '10:15:00', 'Solo', 'Terminal Tirtonadi'),
(13, '14:30:00', 'Sidoarjo', 'Stasiun Sidoarjo'),

-- Trip ID 14 (Gunung Ungaran)
(14, '07:20:00', 'Kuningan', 'Hutan Kota Bagevat'),
(14, '10:50:00', 'Tegal', 'Terminal Pasifik'),
(14, '13:15:00', 'Semarang', 'SPBU Ungaran'),

-- Trip ID 15 (Gunung Muria)
(15, '01:15:00', 'Cirebon', 'Depan CSB Mall'),
(15, '04:40:00', 'Pati', 'Terminal Pati'),
(15, '06:10:00', 'Kudus', 'Basecamp Kedayu'),

-- Trip ID 16 (Gunung Buthak)
(16, '05:55:00', 'Indramayu', 'Sport Center Indramayu'),
(16, '11:20:00', 'Yogyakarta', 'Stasiun Lempuyangan'),
(16, '14:45:00', 'Blitar', 'Stasiun Blitar'),

-- Trip ID 17 (Gunung Penanggungan)
(17, '08:10:00', 'Majalengka', 'Taman Dirgantara'),
(17, '13:40:00', 'Ngawi', 'Terminal Kertonegoro'),
(17, '16:15:00', 'Mojokerto', 'Terminal Kertajaya'),

-- Trip ID 18 (Gunung Lemongan)
(18, '04:35:00', 'Kuningan', 'Alun-Alun Jalaksana'),
(18, '09:50:00', 'Surabaya', 'Stasiun Gubeng'),
(18, '12:20:00', 'Lumajang', 'Ranu Klakah'),

-- Trip ID 19 (Gunung Raung)
(19, '05:10:00', 'Cirebon', 'Stasiun Cirebon Prujakan'),
(19, '13:25:00', 'Probolinggo', 'Stasiun Probolinggo'),
(19, '17:40:00', 'Banyuwangi', 'Stasiun Kalibaru'),

-- Trip ID 20 (Gunung Galunggung)
(20, '05:45:00', 'Indramayu', 'Terminal Karangampel'),
(20, '08:10:00', 'Ciamis', 'Alun-Alun Ciamis'),
(20, '09:55:00', 'Tasikmalaya', 'Pintu Masuk Galunggung')";

$insert_gambar = "INSERT INTO gambar (id_trip, nama_file) VALUES
-- ID 1: Gunung Semeru
(1, 'semeru1.jpg'), (1, 'semeru2.jpg'), (1, 'semeru3.jpg'),
-- ID 2: Gunung Prau
(2, 'prau1.jpg'), (2, 'prau2.jpg'), (2, 'prau3.jpg'),
-- ID 3: Gunung Gede
(3, 'gede1.jpg'), (3, 'gede2.jpg'), (3, 'gede3.jpg'),
-- ID 4: Gunung Merbabu
(4, 'merbabu1.jpg'), (4, 'merbabu2.jpg'), (4, 'merbabu3.jpg'),
-- ID 5: Gunung Lawu
(5, 'lawu1.jpg'), (5, 'lawu2.jpg'), (5, 'lawu3.jpg'),
-- ID 6: Gunung Papandayan
(6, 'papandayan1.jpg'), (6, 'papandayan2.jpg'), (6, 'papandayan3.jpg'),
-- ID 7: Gunung Slamet
(7, 'slamet1.jpg'), (7, 'slamet2.jpg'), (7, 'slamet3.jpg'),
-- ID 8: Gunung Sindoro
(8, 'sindoro1.jpg'), (8, 'sindoro2.jpg'), (8, 'sindoro3.jpg'),
-- ID 9: Gunung Sumbing
(9, 'sumbing1.jpg'), (9, 'sumbing2.jpg'), (9, 'sumbing3.jpg'),
-- ID 10: Gunung Cikuray
(10, 'cikuray1.jpg'), (10, 'cikuray2.jpg'), (10, 'cikuray3.jpg'),
-- ID 11: Gunung Arjuno
(11, 'arjuno1.jpg'), (11, 'arjuno2.jpg'), (11, 'arjuno3.jpg'),
-- ID 12: Gunung Andong
(12, 'andong1.jpg'), (12, 'andong2.jpg'), (12, 'andong3.jpg'),
-- ID 13: Gunung Welirang
(13, 'welirang1.jpg'), (13, 'welirang2.jpg'), (13, 'welirang3.jpg'),
-- ID 14: Gunung Ungaran
(14, 'ungaran1.jpg'), (14, 'ungaran2.jpg'), (14, 'ungaran3.jpg'),
-- ID 15: Gunung Muria
(15, 'muria1.jpg'), (15, 'muria2.jpg'), (15, 'muria3.jpg'),
-- ID 16: Gunung Buthak
(16, 'buthak1.jpg'), (16, 'buthak2.jpg'), (16, 'buthak3.jpg'),
-- ID 17: Gunung Penanggungan
(17, 'penanggungan1.jpg'), (17, 'penanggungan2.jpg'), (17, 'penanggungan3.jpg'),
-- ID 18: Gunung Lemongan
(18, 'lemongan1.jpg'), (18, 'lemongan2.jpg'), (18, 'lemongan3.jpg'),
-- ID 19: Gunung Raung
(19, 'raung1.jpg'), (19, 'raung2.jpg'), (19, 'raung3.jpg'),
-- ID 20: Gunung Galunggung
(20, 'galunggung1.jpg'), (20, 'galunggung2.jpg'), (20, 'galunggung3.jpg')";

/*
$insert_trip = "INSERT INTO trip (tujuan, tgl_berangkat, tgl_pulang, harga, kuota, catatan) VALUES 
('Gunung Ciremai', '2026-04-03', '2026-04-04', 500000, 30, 'Harap Membawa Perlengkapan Tidur'),
('Gunung Slamet', '2026-04-05', '2026-04-05', 350000, 10, 'Harap Membawa Perlengkapan Memasak'),
('Gunung Prau', '2026-04-01', '2026-04-02', 200000, 25, ''),
('Gunung Lawu', '2026-04-10', '2026-04-12', 720000, 15, ''),
('Gunung Merapi', '2026-04-07', '2026-04-07', 400000, 45, 'Dilarang Membuang Sampah di kawah')";

$insert_katalog = "INSERT INTO katalog (id_trip, deskripsi) VALUES 
(1, 'Gunung Ciremai memiliki tinggi lebih dari 3000 Mdpl dan menawarkan pemandangan yang luar biasa ...'),
(2, 'Gunung Slamet merupakan salah satu gunung dengan pemandangan yang asri di Jawa Tengah ...'),
(3, 'Gunung Prau berada di Dieng, Wonosobo, gunung ini memiliki tinggi lebih 2500 Mdpl ...'),
(4, 'Gunung Lawu berada di Jawa Timur dan menjadi gunung paling favorit untuk didaki ...'),
(5, 'Gunung Merapi merupakan gunung yang sudah lama menjadi primadona bagi para pendaki ...')";
*/


$insert_akun = "INSERT INTO akun (username, password, role) VALUES 
('admin', 'admin', 'admin'),
('najip', '123', 'admin'),
('yayat', '123', 'admin'),
('angga', '123', 'admin'),
('radza', '123', 'admin'),
('asep', '12345678', 'user'),
('yanto', '12345678', 'user'),
('wawan', '12345678', 'user'),
('gatot', '12345678', 'user'),
('udin', '12345678', 'user'),
('johan', '12345678', 'user'),
('budi', '12345678', 'user'),
('jono', '12345678', 'user'),
('ahmad', '12345678', 'user'),
('wahyu', '12345678', 'user'),
('rebon', 'rebon', 'admin')";

$insert_peserta_ot = "INSERT INTO peserta_open (id_akun, nama, no_hp, usia, alamat, riwayat) VALUES
-- Akun ID 6
(6, 'Budi Santoso', '081234567801', 24, 'Dsn. Wage, Ds. Kaliwulu, Kec. Plered, Cirebon', ''),
(6, 'Siti Aminah', '081234567802', 22, 'Ds. Maja Selatan, Kec. Maja, Majalengka', 'Asma'),
(6, 'Rian Hidayat', '081234567803', 27, 'Dsn. Puhun, Ds. Jalaksana, Kec. Jalaksana, Kuningan', ''),
(6, 'Dewi Lestari', '081234567804', 25, 'Blok Pilang, Ds. Karanganyar, Kec. Kandanghaur, Indramayu', ''),
(6, 'Fajar Nugraha', '081234567805', 23, 'Jl. Ki Ageng Tapa, Ds. Dawuan, Kec. Tengahtani, Cirebon', 'Alergi Dingin'),
(6, 'Ani Wijaya', '081234567806', 21, 'Dsn. Sukamanah, Kec. Argapura, Majalengka', ''),
(6, 'Hendra Kusuma', '081234567807', 29, 'Ds. Manislor, Kec. Jalaksana, Kuningan', ''),
(6, 'Maya Saputri', '081234567808', 26, 'Ds. Paoman, Kec. Indramayu, Indramayu', 'Anemia'),
(6, 'Eko Prasetyo', '081234567809', 24, 'Ds. Battembat, Kec. Tengah Tani, Cirebon', ''),
(6, 'Rina Marlina', '081234567810', 22, 'Ds. Kawunghilir, Kec. Cigasong, Majalengka', ''),

-- Akun ID 7
(7, 'Andi Setiawan', '081234567811', 30, 'Kp. Bojong, Ds. Ciwidey, Kec. Ciwidey, Bandung', 'Maag Akut'),
(7, 'Lia Permata', '081234567812', 24, 'Dsn. Ganeas, Kec. Ganeas, Sumedang', ''),
(7, 'Rizky Fauzi', '081234567813', 26, 'Kp. Cipanas, Kec. Tarogong Kaler, Garut', ''),
(7, 'Indah Sari', '081234567814', 23, 'Ds. Cisayong, Kec. Cisayong, Tasikmalaya', 'Asma'),
(7, 'Yudi Pratama', '081234567815', 28, 'Ds. Imbanagara, Kec. Ciamis, Ciamis', ''),
(7, 'Fitri Handayani', '081234567816', 25, 'Jl. Setiabudi, Kec. Cidadap, Bandung', ''),
(7, 'Agus Ramadhan', '081234567817', 27, 'Ds. Jatinangor, Kec. Jatinangor, Sumedang', 'Alergi Debu'),
(7, 'Novi Anggraeni', '081234567818', 22, 'Ds. Cangkuang, Kec. Leles, Garut', ''),
(7, 'Diki Wahyudi', '081234567819', 29, 'Ds. Sukaraja, Kec. Rajapolah, Tasikmalaya', ''),
(7, 'Siska Putri', '081234567820', 24, 'Ds. Kawali, Kec. Kawali, Ciamis', ''),

-- Akun ID 8
(8, 'Bambang Heru', '081234567821', 32, 'Jl. Kayu Putih, Kec. Pulo Gadung, Jakarta Timur', 'Hipertensi'),
(8, 'Ratna Dwi', '081234567822', 26, 'Gg. Haji Sairi, Kec. Kebayoran Lama, Jakarta Selatan', ''),
(8, 'Gilang Dirga', '081234567823', 25, 'Perum Harapan Indah, Kec. Tarumajaya, Bekasi', ''),
(8, 'Putri Ayu', '081234567824', 23, 'Ds. Kelapa Dua, Kec. Kelapa Dua, Tangerang', 'Alergi Dingin'),
(8, 'Taufik Hidayat', '081234567825', 28, 'Jl. Margonda, Kec. Beji, Depok', ''),
(8, 'Vina Pandu', '081234567826', 24, 'Kp. Rawabelong, Kec. Kebon Jeruk, Jakarta Barat', ''),
(8, 'Raka Surya', '081234567827', 27, 'Ds. Tambun, Kec. Tambun Selatan, Bekasi', ''),
(8, 'Bella Luna', '081234567828', 22, 'Ds. Karawaci, Kec. Karawaci, Tangerang', 'Anemia'),
(8, 'Adit Moro', '081234567829', 30, 'Ds. Cimanggis, Kec. Cimanggis, Depok', ''),
(8, 'Mega Utami', '081234567830', 25, 'Jl. Pemuda, Kec. Rawamangun, Jakarta Timur', ''),

-- Akun ID 9
(9, 'Joko Susilo', '081234567831', 28, 'Ds. Ngaliyan, Kec. Ngaliyan, Semarang', ''),
(9, 'Niken Ayu', '081234567832', 24, 'Ds. Manahan, Kec. Banjarsari, Solo', 'Maag'),
(9, 'Dani Pedrosa', '081234567833', 26, 'Ds. Caturtunggal, Kec. Depok, Sleman, Yogyakarta', ''),
(9, 'Wulan Guritno', '081234567834', 23, 'Ds. Borobudur, Kec. Borobudur, Magelang', ''),
(9, 'Bagas Kaffa', '081234567835', 22, 'Ds. Selo, Kec. Selo, Boyolali', 'Asma'),
(9, 'Zaskia Adya', '081234567836', 25, 'Jl. Pandanaran, Kec. Semarang Tengah, Semarang', ''),
(9, 'Ferry Salim', '081234567837', 29, 'Ds. Laweyan, Kec. Laweyan, Solo', ''),
(9, 'Luna Maya', '081234567838', 27, 'Ds. Sosromenduran, Kec. Gedongtengen, Yogyakarta', 'Alergi Debu'),
(9, 'Dimas Beck', '081234567839', 24, 'Ds. Muntilan, Kec. Muntilan, Magelang', ''),
(9, 'Chelsea Olivia', '081234567840', 23, 'Ds. Banyudono, Kec. Banyudono, Boyolali', ''),

-- Akun ID 10
(10, 'Irfan Hakim', '081234567841', 31, 'Jl. Dharmahusada, Kec. Gubeng, Surabaya', 'Asam Lambung'),
(10, 'Gracia Indri', '081234567842', 25, 'Ds. Oro-oro Dowo, Kec. Klojen, Malang', ''),
(10, 'Raffi Ahmad', '081234567843', 28, 'Ds. Waru, Kec. Waru, Sidoarjo', ''),
(10, 'Nagita Slavina', '081234567844', 27, 'Ds. Randuagung, Kec. Kebomas, Gresik', 'Alergi Dingin'),
(10, 'Gading Marten', '081234567845', 30, 'Ds. Prigen, Kec. Prigen, Pasuruan', ''),
(10, 'Gisella Anastasia', '081234567846', 26, 'Jl. Mayjen Sungkono, Kec. Dukuh Pakis, Surabaya', ''),
(10, 'Anang Hermansyah', '081234567847', 35, 'Ds. Lowokwaru, Kec. Lowokwaru, Malang', ''),
(10, 'Ashanty Siddik', '081234567848', 32, 'Ds. Sedati, Kec. Sedati, Sidoarjo', 'Anemia'),
(10, 'Aurel Hermansyah', '081234567849', 24, 'Ds. Manyar, Kec. Manyar, Gresik', ''),
(10, 'Azriel Akbar', '081234567850', 22, 'Ds. Pandaan, Kec. Pandaan, Pasuruan', ''),

-- Akun ID 11
(11, 'Aris Setiawan', '081234567901', 26, 'Dsn. Pon, Ds. Caracas, Kec. Cilimus, Kuningan', ''),
(11, 'Lulu Nurhaliza', '081234567902', 23, 'Blok Selasa, Ds. Tukmudal, Kec. Sumber, Cirebon', 'Alergi Debu'),
(11, 'Rendi Pratama', '081234567903', 29, 'Ds. Liangjulang, Kec. Kadipaten, Majalengka', ''),
(11, 'Santi Rahayu', '081234567904', 25, 'Ds. Panyingkiran, Kec. Jatitujuh, Majalengka', ''),
(11, 'Dedi Kurniawan', '081234567905', 30, 'Dsn. Cantel, Ds. Bojonglor, Kec. Jamblang, Cirebon', 'Maag'),
(11, 'Mega Silviani', '081234567906', 22, 'Ds. Cipicung, Kec. Cipicung, Kuningan', ''),
(11, 'Ferry Irawan', '081234567907', 27, 'Ds. Lemahabang, Kec. Lemahabang, Cirebon', ''),
(11, 'Nina Herlina', '081234567908', 24, 'Blok Desa, Ds. Bantarwaru, Kec. Ligung, Majalengka', 'Anemia'),
(11, 'Toto Hermanto', '081234567909', 31, 'Ds. Kenanga, Kec. Sumber, Cirebon', ''),
(11, 'Yani Fitriani', '081234567910', 23, 'Ds. Cibeureum, Kec. Cilimus, Kuningan', ''),

-- Akun ID 12
(12, 'Agus Triyono', '081234567911', 28, 'Ds. Kertajati, Kec. Kertajati, Majalengka', ''),
(12, 'Dina Mariana', '081234567912', 25, 'Ds. Cilayung, Kec. Jatinangor, Sumedang', 'Asma'),
(12, 'Eka Saputra', '081234567913', 22, 'Ds. Wanayasa, Kec. Beber, Cirebon', ''),
(12, 'Rina Astuti', '081234567914', 27, 'Ds. Babakan, Kec. Babakan, Cirebon', ''),
(12, 'Iwan Falsafah', '081234567915', 33, 'Ds. Palimanan Timur, Kec. Palimanan, Cirebon', 'Alergi Dingin'),
(12, 'Sari Wijaya', '081234567916', 24, 'Ds. Panjalin Kidul, Kec. Sumberjaya, Majalengka', ''),
(12, 'Bambang Irawan', '081234567917', 29, 'Ds. Sangkanerang, Kec. Jalaksana, Kuningan', ''),
(12, 'Putri Handayani', '081234567918', 21, 'Ds. Mertapada Kulon, Kec. Astanajapura, Cirebon', ''),
(12, 'Hadi Sucipto', '081234567919', 30, 'Ds. Sindangwangi, Kec. Sindangwangi, Majalengka', 'Maag'),
(12, 'Sri Wahyuni', '081234567920', 26, 'Ds. Linggarjati, Kec. Cilimus, Kuningan', ''),

-- Akun ID 13
(13, 'Mulyadi', '081234567921', 35, 'Ds. Jatibarang Baru, Kec. Jatibarang, Indramayu', 'Asam Urat'),
(13, 'Ratnawati', '081234567922', 28, 'Ds. Karangampel Kidul, Kec. Karangampel, Indramayu', ''),
(13, 'Nanang Kosim', '081234567923', 26, 'Ds. Terusan, Kec. Sindang, Indramayu', ''),
(13, 'Euis Dahlia', '081234567924', 24, 'Ds. Margadadi, Kec. Indramayu, Indramayu', 'Migrain'),
(13, 'Asep Sunandar', '081234567925', 32, 'Ds. Losarang, Kec. Losarang, Indramayu', ''),
(13, 'Cucu Cahyati', '081234567926', 27, 'Ds. Plumbon, Kec. Plumbon, Cirebon', ''),
(13, 'Yayat Ruhiyat', '081234567927', 30, 'Ds. Weru Kidul, Kec. Weru, Cirebon', ''),
(13, 'Tati Hartati', '081234567928', 25, 'Ds. Battembat, Kec. Tengah Tani, Cirebon', 'Alergi Debu'),
(13, 'Dadang Suhendar', '081234567929', 29, 'Ds. Luragunglandeuh, Kec. Luragung, Kuningan', ''),
(13, 'Neng Fitri', '081234567930', 22, 'Ds. Lebakwangi, Kec. Lebakwangi, Kuningan', ''),

-- Akun ID 14
(14, 'Sugeng Prayitno', '081234567931', 34, 'Ds. Gunungjati, Kec. Gunungjati, Cirebon', 'Kolesterol'),
(14, 'Wiwin Winarsih', '081234567932', 26, 'Ds. Mundu Pesisir, Kec. Mundu, Cirebon', ''),
(14, 'Dodo Zakaria', '081234567933', 28, 'Ds. Gebang Mekar, Kec. Gebang, Cirebon', ''),
(14, 'Lilis Karlina', '081234567934', 23, 'Ds. Ciawigebang, Kec. Ciawigebang, Kuningan', 'Asma'),
(14, 'Ujang Saepudin', '081234567935', 31, 'Ds. Kadugede, Kec. Kadugede, Kuningan', ''),
(14, 'Titin Sumarni', '081234567936', 25, 'Ds. Darma, Kec. Darma, Kuningan', ''),
(14, 'Entis Sutisna', '081234567937', 29, 'Ds. Argapura, Kec. Argapura, Majalengka', ''),
(14, 'Kokom Komalasari', '081234567938', 24, 'Ds. Teja, Kec. Rajagaluh, Majalengka', 'Anemia'),
(14, 'Cecep Gorbacep', '081234567939', 27, 'Ds. Sukahaji, Kec. Sukahaji, Majalengka', ''),
(14, 'Eneng Nuraini', '081234567940', 22, 'Ds. Lemahsugih, Kec. Lemahsugih, Majalengka', ''),

-- Akun ID 15
(15, 'Herman Susilo', '081234567941', 32, 'Ds. Arjawinangun, Kec. Arjawinangun, Cirebon', ''),
(15, 'Siti Munawaroh', '081234567942', 26, 'Ds. Susukan, Kec. Susukan, Cirebon', 'Maag'),
(15, 'Rudi Tabuti', '081234567943', 28, 'Ds. Ciwaringin, Kec. Ciwaringin, Cirebon', ''),
(15, 'Ika Kartika', '081234567944', 24, 'Ds. Gempol, Kec. Gempol, Cirebon', ''),
(15, 'Ahmad Subagja', '081234567945', 30, 'Ds. Kapetakan, Kec. Kapetakan, Cirebon', 'Alergi Dingin'),
(15, 'Dewi Persikawati', '081234567946', 25, 'Ds. Suranenggala, Kec. Suranenggala, Cirebon', ''),
(15, 'Maman Suherman', '081234567947', 33, 'Ds. Panguragan, Kec. Panguragan, Cirebon', ''),
(15, 'Yuyun Yuningsih', '081234567948', 27, 'Ds. Klangenan, Kec. Klangenan, Cirebon', 'Anemia'),
(15, 'Udin Seduniawan', '081234567949', 29, 'Ds. Kedawung, Kec. Kedawung, Cirebon', ''),
(15, 'Siska Amelia', '081234567950', 23, 'Ds. Tegalwangi, Kec. Weru, Cirebon', '')";

$insert_booking = "INSERT INTO booking (id_trip, id_akun, jumlah_peserta, tgl_booking, status) VALUES
-- Trip ID 1 (Gunung Semeru)
(1, 7, 2, '2025-12-01 10:15:00', 'Lunas'),
(1, 12, 1, '2025-12-03 14:20:00', 'DP'),
(1, 9, 3, '2025-12-05 09:10:00', 'Belum Bayar'),

-- Trip ID 2 (Gunung Prau)
(2, 6, 4, '2026-01-05 08:30:00', 'Lunas'),
(2, 15, 2, '2026-01-07 19:45:00', 'Bayar non-DP'),
(2, 8, 2, '2026-01-10 11:00:00', 'DP'),

-- Trip ID 3 (Gunung Gede)
(3, 11, 1, '2026-01-15 13:25:00', 'Lunas'),
(3, 13, 2, '2026-01-18 16:40:00', 'Belum Bayar'),
(3, 10, 2, '2026-01-20 10:05:00', 'DP'),

-- Trip ID 4 (Gunung Merbabu)
(4, 14, 3, '2026-02-01 09:50:00', 'Lunas'),
(4, 7, 1, '2026-02-03 21:15:00', 'Bayar non-DP'),
(4, 12, 2, '2026-02-05 14:30:00', 'Dibatalkan'),

-- Trip ID 5 (Gunung Lawu)
(5, 6, 5, '2026-02-15 07:45:00', 'Lunas'),
(5, 9, 2, '2026-02-18 12:20:00', 'DP'),
(5, 15, 1, '2026-02-20 15:55:00', 'Belum Bayar'),

-- Trip ID 6 (Gunung Papandayan)
(6, 10, 2, '2026-03-01 11:30:00', 'Lunas'),
(6, 8, 3, '2026-03-03 10:10:00', 'DP'),
(6, 13, 1, '2026-03-05 16:45:00', 'Belum Bayar'),

-- Trip ID 7 (Gunung Slamet)
(7, 11, 1, '2026-03-15 09:25:00', 'Lunas'),
(7, 14, 2, '2026-03-18 20:35:00', 'DP'),
(7, 6, 1, '2026-03-20 13:50:00', 'Refund'),

-- Trip ID 8 (Gunung Sindoro)
(8, 12, 2, '2026-04-01 14:15:00', 'Lunas'),
(8, 9, 2, '2026-04-05 08:40:00', 'Belum Bayar'),
(8, 15, 3, '2026-04-07 17:20:00', 'DP'),

-- Trip ID 9 (Gunung Sumbing)
(9, 7, 2, '2026-04-15 10:30:00', 'Lunas'),
(9, 10, 1, '2026-04-18 12:05:00', 'DP'),
(9, 13, 2, '2026-04-20 15:45:00', 'Bayar non-DP'),

-- Trip ID 10 (Gunung Cikuray)
(10, 8, 3, '2026-05-01 09:00:00', 'Lunas'),
(10, 11, 2, '2026-05-03 11:55:00', 'Belum Bayar'),
(10, 14, 1, '2026-05-05 14:10:00', 'DP'),

-- Trip ID 11 (Gunung Arjuno)
(11, 14, 2, '2025-12-15 08:45:00', 'Lunas'),
(11, 6, 1, '2025-12-20 14:10:00', 'DP'),
(11, 10, 2, '2025-12-28 10:30:00', 'Belum Bayar'),

-- Trip ID 12 (Gunung Andong)
(12, 13, 4, '2026-01-02 09:20:00', 'Lunas'),
(12, 7, 2, '2026-01-05 16:55:00', 'DP'),
(12, 15, 5, '2026-01-10 11:15:00', 'Belum Bayar'),

-- Trip ID 13 (Gunung Welirang)
(13, 9, 1, '2026-01-12 13:40:00', 'Lunas'),
(13, 12, 2, '2026-01-18 08:05:00', 'Bayar non-DP'),
(13, 8, 1, '2026-01-25 15:20:00', 'Dibatalkan'),

-- Trip ID 14 (Gunung Ungaran)
(14, 11, 3, '2026-02-01 10:10:00', 'Lunas'),
(14, 14, 2, '2026-02-08 19:35:00', 'DP'),
(14, 6, 4, '2026-02-12 14:50:00', 'Belum Bayar'),

-- Trip ID 15 (Gunung Muria)
(15, 10, 2, '2026-02-15 07:55:00', 'Lunas'),
(15, 13, 1, '2026-02-22 12:45:00', 'DP'),
(15, 7, 3, '2026-02-28 16:10:00', 'Belum Bayar'),

-- Trip ID 16 (Gunung Buthak)
(16, 15, 2, '2026-03-05 09:30:00', 'Lunas'),
(16, 12, 2, '2026-03-12 11:20:00', 'DP'),
(16, 9, 1, '2026-03-20 20:15:00', 'Refund'),

-- Trip ID 17 (Gunung Penanggungan)
(17, 8, 3, '2026-04-01 14:40:00', 'Lunas'),
(17, 11, 2, '2026-04-05 10:05:00', 'Bayar non-DP'),
(17, 14, 2, '2026-04-12 13:25:00', 'Belum Bayar'),

-- Trip ID 18 (Gunung Lemongan)
(18, 6, 1, '2026-04-15 08:50:00', 'Lunas'),
(18, 10, 2, '2026-04-22 15:30:00', 'DP'),
(18, 13, 1, '2026-04-28 09:10:00', 'Dibatalkan'),

-- Trip ID 19 (Gunung Raung)
(19, 7, 2, '2026-05-01 11:15:00', 'Lunas'),
(19, 15, 1, '2026-05-10 16:40:00', 'DP'),
(19, 12, 1, '2026-05-18 10:20:00', 'Belum Bayar'),

-- Trip ID 20 (Gunung Galunggung)
(20, 9, 5, '2026-06-01 07:30:00', 'Lunas'),
(20, 8, 3, '2026-06-05 14:55:00', 'DP'),
(20, 11, 2, '2026-06-12 11:00:00', 'Belum Bayar')
";

$insert_detail = "INSERT INTO detail (id_booking, id_peserta) VALUES
(1, 11), (1, 12),
(2, 61),
(3, 31), (3, 32), (3, 33),
(4, 1), (4, 2), (4, 3), (4, 4),
(5, 91), (5, 92),
(6, 21), (6, 22),
(7, 51),
(8, 71), (8, 72),
(9, 41), (9, 42),
(10, 81), (10, 82), (10, 83),
(11, 13),
(12, 62), (12, 63),
(13, 5), (13, 6), (13, 7), (13, 8), (13, 9),
(14, 34), (14, 35),
(15, 93),
(16, 43), (16, 44),
(17, 23), (17, 24), (17, 25),
(18, 73),
(19, 52),
(20, 84), (20, 85),
(21, 10),
(22, 64), (22, 65),
(23, 36), (23, 37),
(24, 94), (24, 95), (24, 96),
(25, 14), (25, 15),
(26, 45),
(27, 74), (27, 75),
(28, 26), (28, 27), (28, 28),
(29, 53), (29, 54),
(30, 86),
(31, 81), (31, 82),
(32, 1),
(33, 41), (33, 42),
(34, 71), (34, 72), (34, 73), (34, 74),
(35, 11), (35, 12),
(36, 91), (36, 92), (36, 93), (36, 94), (36, 95),
(37, 31),
(38, 61), (38, 62),
(39, 21),
(40, 51), (40, 52), (40, 53),
(41, 83), (41, 84),
(42, 2), (42, 3), (42, 4), (42, 5),
(43, 43), (43, 44),
(44, 75),
(45, 13), (45, 14), (45, 15),
(46, 96), (46, 97),
(47, 63), (47, 64),
(48, 32),
(49, 22), (49, 23), (49, 24),
(50, 54), (50, 55),
(51, 85), (51, 86),
(52, 6),
(53, 45), (53, 46),
(54, 76),
(55, 16), (55, 17),
(56, 98),
(57, 65),
(58, 33), (58, 34), (58, 35), (58, 36), (58, 37),
(59, 25), (59, 26), (59, 27),
(60, 56), (60, 57)
";

$insert_payment_ot = "INSERT INTO payment_open (id_booking, tgl_bayar, nominal, bukti_bayar, status, catatan) VALUES
-- Booking ID 1 (Semeru, 2 orang, Total 1.7jt, Status: Lunas)
(1, '2025-12-02 10:14:25', 600000, 'bukti_1_1.jpg', 'Diverifikasi', 'DP dulu ya min'),
(1, '2025-12-15 13:45:10', 1100000, 'bukti_1_2.jpg', 'Diverifikasi', 'Pelunasan atas nama Budi'),

-- Booking ID 2 (Semeru, 1 orang, Total 850rb, Status: DP)
(2, '2025-12-04 09:22:15', 300000, 'bukti_2_1.jpg', 'Diverifikasi', ''),

-- Booking ID 3 (Semeru, 3 orang, Total 2.55jt, Status: Belum Bayar)
(3, '2025-12-06 11:05:40', 2550000, 'bukti_3_1.jpg', 'Belum Diverifikasi', 'Sudah transfer full'),

-- Booking ID 4 (Prau, 4 orang, Total 1.8jt, Status: Lunas)
(4, '2026-01-06 08:12:33', 1800000, 'bukti_4_1.jpg', 'Diverifikasi', 'Bismillah berangkat'),

-- Booking ID 5 (Prau, 2 orang, Total 900rb, Status: Bayar non-DP)
(5, '2026-01-08 14:55:02', 350000, 'bukti_5_1.jpg', 'Diverifikasi', ''),

-- Booking ID 6 (Prau, 2 orang, Total 900rb, Status: DP)
(6, '2026-01-10 19:50:11', 100000, 'bukti_6_1.jpg', 'Ditolak', 'Maaf tadi kurang transfernya'),
(6, '2026-01-11 10:05:44', 300000, 'bukti_6_2.jpg', 'Diverifikasi', 'Ini DP yang benar kak'),

-- Booking ID 7 (Gede, 1 orang, Total 600rb, Status: Lunas)
(7, '2026-01-16 09:18:29', 600000, 'bukti_7_1.jpg', 'Diverifikasi', 'Lunas ya'),

-- Booking ID 8 (Gede, 2 orang, Total 1.2jt, Status: Belum Bayar)
(8, '2026-01-19 15:33:12', 1200000, 'bukti_8_1.jpg', 'Ditolak', 'Sudah bayar lunas min'),

-- Booking ID 9 (Gede, 2 orang, Total 1.2jt, Status: DP)
(9, '2026-01-21 11:42:05', 400000, 'bukti_9_1.jpg', 'Diverifikasi', 'Titip DP buat 2 orang'),

-- Booking ID 10 (Merbabu, 3 orang, Total 2.25jt, Status: Lunas)
(10, '2026-02-02 10:25:18', 750000, 'bukti_10_1.jpg', 'Diverifikasi', 'Angsuran 1'),
(10, '2026-02-05 16:12:55', 500000, 'bukti_10_2.jpg', 'Ditolak', ''),
(10, '2026-02-10 15:08:31', 1500000, 'bukti_10_3.jpg', 'Diverifikasi', 'Sisa pelunasan admin'),

-- Booking ID 11 (Merbabu, 1 orang, Total 750rb, Status: Bayar non-DP)
(11, '2026-02-04 12:44:19', 250000, 'bukti_11_1.jpg', 'Diverifikasi', ''),

-- Booking ID 12 (Merbabu, 2 orang, Total 1.5jt, Status: Dibatalkan)
(12, '2026-02-06 16:21:40', 1500000, 'bukti_12_1.jpg', 'Diverifikasi', 'Lunas semua'),

-- Booking ID 13 (Lawu, 5 orang, Total 1.75jt, Status: Lunas)
(13, '2026-02-16 08:37:12', 1750000, 'bukti_13_1.jpg', 'Diverifikasi', 'Pembayaran rombongan'),

-- Booking ID 14 (Lawu, 2 orang, Total 700rb, Status: DP)
(14, '2026-02-19 14:02:59', 200000, 'bukti_14_1.jpg', 'Diverifikasi', 'DP rek'),

-- Booking ID 15 (Lawu, 1 orang, Total 350rb, Status: Belum Bayar)
(15, '2026-02-21 10:51:22', 350000, 'bukti_15_1.jpg', 'Belum Diverifikasi', 'Cek ya kak'),

-- Booking ID 16 (Papandayan, 2 orang, Total 1.1jt, Status: Lunas)
(16, '2026-03-02 09:18:44', 1100000, 'bukti_16_1.jpg', 'Diverifikasi', 'Lunas bosku'),

-- Booking ID 17 (Papandayan, 3 orang, Total 1.65jt, Status: DP)
(17, '2026-03-04 11:27:01', 600000, 'bukti_17_1.jpg', 'Diverifikasi', 'Booking seat'),

-- Booking ID 18 (Papandayan, 1 orang, Total 550rb, Status: Belum Bayar)
(18, '2026-03-06 15:59:12', 50000, 'bukti_18_1.jpg', 'Ditolak', 'DP dulu ya 50rb'),

-- Booking ID 19 (Slamet, 1 orang, Total 900rb, Status: Lunas)
(19, '2026-03-16 10:14:05', 900000, 'bukti_19_1.jpg', 'Diverifikasi', 'Lunas mantap'),

-- Booking ID 20 (Slamet, 2 orang, Total 1.8jt, Status: DP)
(20, '2026-03-19 12:48:33', 700000, 'bukti_20_1.jpg', 'Diverifikasi', 'Cicil dulu'),

-- Booking ID 21 (Slamet, 1 orang, Total 900rb, Status: Refund)
(21, '2026-03-21 08:11:50', 900000, 'bukti_21_1.jpg', 'Diverifikasi', ''),

-- Booking ID 22 (Sindoro, 2 orang, Total 800rb, Status: Lunas)
(22, '2026-04-02 14:23:11', 400000, 'bukti_22_1.jpg', 'Diverifikasi', 'Setengah dulu'),
(22, '2026-04-10 09:44:50', 400000, 'bukti_22_2.jpg', 'Diverifikasi', 'Sisanya ya min'),

-- Booking ID 23 (Sindoro, 2 orang, Total 800rb, Status: Belum Bayar)
(23, '2026-04-06 10:19:33', 800000, 'bukti_23_1.jpg', 'Belum Diverifikasi', 'Sudah lunas'),

-- Booking ID 24 (Sindoro, 3 orang, Total 1.2jt, Status: DP)
(24, '2026-04-08 17:35:12', 450000, 'bukti_24_1.jpg', 'Diverifikasi', 'DP 3 orang'),

-- Booking ID 25 (Sumbing, 2 orang, Total 1jt, Status: Lunas)
(25, '2026-04-16 11:12:09', 1000000, 'bukti_25_1.jpg', 'Diverifikasi', 'Lunas kak'),

-- Booking ID 26 (Sumbing, 1 orang, Total 500rb, Status: DP)
(26, '2026-04-19 13:54:01', 200000, 'bukti_26_1.jpg', 'Diverifikasi', ''),

-- Booking ID 27 (Sumbing, 2 orang, Total 1jt, Status: Bayar non-DP)
(27, '2026-04-21 16:28:44', 300000, 'bukti_27_1.jpg', 'Diverifikasi', 'Angsuran 1 sumbing'),

-- Booking ID 28 (Cikuray, 3 orang, Total 1.35jt, Status: Lunas)
(28, '2026-05-02 10:09:55', 450000, 'bukti_28_1.jpg', 'Diverifikasi', 'Bayar 1/3'),
(28, '2026-05-10 11:30:22', 450000, 'bukti_28_2.jpg', 'Ditolak', ''),
(28, '2026-05-15 14:52:10', 900000, 'bukti_28_3.jpg', 'Diverifikasi', 'Pelunasan sisa semuanya'),

-- Booking ID 29 (Cikuray, 2 orang, Total 900rb, Status: Belum Bayar)
(29, '2026-05-04 12:11:40', 900000, 'bukti_29_1.jpg', 'Belum Diverifikasi', 'Cek mutasi atas nama Siska'),

-- Booking ID 30 (Cikuray, 1 orang, Total 450rb, Status: DP)
(30, '2026-05-06 15:47:02', 100000, 'bukti_30_1.jpg', 'Diverifikasi', 'DP min'),

-- Booking ID 31 (Arjuno, 2 orang, Total 1.4jt, Status: Lunas) - 3x Bayar
(31, '2025-12-16 10:22:45', 500000, 'bukti_31_1.jpg', 'Diverifikasi', 'DP Arjuno'),
(31, '2025-12-22 14:15:10', 400000, 'bukti_31_2.jpg', 'Diverifikasi', 'Cicilan kedua'),
(31, '2025-12-30 09:12:33', 500000, 'bukti_31_3.jpg', 'Diverifikasi', 'Lunas ya min'),

-- Booking ID 32 (Arjuno, 1 orang, Total 700rb, Status: DP) - 1x Bayar
(32, '2025-12-21 09:44:02', 250000, 'bukti_32_1.jpg', 'Diverifikasi', ''),

-- Booking ID 33 (Arjuno, 2 orang, Total 1.4jt, Status: Belum Bayar) - 2x Bayar (1 Ditolak)
(33, '2025-12-28 11:05:33', 700000, 'bukti_33_1.jpg', 'Ditolak', 'DP dulu'),
(33, '2025-12-29 16:20:11', 1400000, 'bukti_33_2.jpg', 'Belum Diverifikasi', 'Bayar full aja biar cepet'),

-- Booking ID 34 (Andong, 4 orang, Total 1jt, Status: Lunas) - 2x Bayar
(34, '2026-01-03 08:12:19', 200000, 'bukti_34_1.jpg', 'Diverifikasi', 'Booking fee'),
(34, '2026-01-08 14:33:05', 800000, 'bukti_34_2.jpg', 'Diverifikasi', 'Pelunasan rombongan'),

-- Booking ID 35 (Andong, 2 orang, Total 500rb, Status: DP) - 1x Bayar
(35, '2026-01-06 13:55:40', 100000, 'bukti_35_1.jpg', 'Diverifikasi', 'DP tipis kak'),

-- Booking ID 36 (Andong, 5 orang, Total 1.25jt, Status: Belum Bayar) - 1x Bayar (Gagal)
(36, '2026-01-11 19:20:11', 1250000, 'bukti_36_1.jpg', 'Ditolak', 'Bukti transfer editan'),

-- Booking ID 37 (Welirang, 1 orang, Total 750rb, Status: Lunas) - 3x Bayar
(37, '2026-01-13 09:18:29', 300000, 'bukti_37_1.jpg', 'Diverifikasi', 'DP'),
(37, '2026-01-20 15:33:12', 200000, 'bukti_37_2.jpg', 'Diverifikasi', 'Cicil lagi'),
(37, '2026-02-05 10:42:05', 250000, 'bukti_37_3.jpg', 'Diverifikasi', 'Sisanya'),

-- Booking ID 38 (Welirang, 2 orang, Total 1.5jt, Status: Bayar non-DP) - 2x Bayar
(38, '2026-01-19 15:33:12', 300000, 'bukti_38_1.jpg', 'Diverifikasi', ''),
(38, '2026-01-25 11:42:05', 100000, 'bukti_38_2.jpg', 'Diverifikasi', 'Nambah dikit'),

-- Booking ID 39 (Welirang, 1 orang, Total 750rb, Status: Dibatalkan) - 1x Bayar
(39, '2026-01-26 11:42:05', 750000, 'bukti_39_1.jpg', 'Diverifikasi', 'Bayar sebelum cancel'),

-- Booking ID 40 (Ungaran, 3 orang, Total 900rb, Status: Lunas) - 2x Bayar
(40, '2026-02-02 10:25:18', 300000, 'bukti_40_1.jpg', 'Diverifikasi', 'DP awal'),
(40, '2026-02-15 16:12:55', 600000, 'bukti_40_2.jpg', 'Diverifikasi', 'Sisa pelunasan'),

-- Booking ID 41 (Ungaran, 2 orang, Total 600rb, Status: DP) - 2x Bayar (1 Ditolak)
(41, '2026-02-08 12:44:19', 100000, 'bukti_41_1.jpg', 'Ditolak', 'Kurang bayar'),
(41, '2026-02-09 09:21:40', 200000, 'bukti_41_2.jpg', 'Diverifikasi', 'Ini DP pasnya'),

-- Booking ID 42 (Ungaran, 4 orang, Total 1.2jt, Status: Belum Bayar) - 1x Bayar
(42, '2026-02-13 16:21:40', 1200000, 'bukti_42_1.jpg', 'Belum Diverifikasi', ''),

-- Booking ID 43 (Muria, 2 orang, Total 400rb, Status: Lunas) - 2x Bayar
(43, '2026-02-16 08:37:12', 100000, 'bukti_43_1.jpg', 'Diverifikasi', 'DP'),
(43, '2026-02-25 14:02:59', 300000, 'bukti_43_2.jpg', 'Diverifikasi', 'Lunas muria'),

-- Booking ID 44 (Muria, 1 orang, Total 200rb, Status: DP) - 1x Bayar
(44, '2026-02-23 14:02:59', 50000, 'bukti_44_1.jpg', 'Diverifikasi', ''),

-- Booking ID 45 (Muria, 3 orang, Total 600rb, Status: Belum Bayar) - 1x Bayar
(45, '2026-02-28 10:51:22', 100000, 'bukti_45_1.jpg', 'Ditolak', 'Uang kurang'),

-- Booking ID 46 (Buthak, 2 orang, Total 1jt, Status: Lunas) - 3x Bayar
(46, '2026-03-06 09:18:44', 300000, 'bukti_46_1.jpg', 'Diverifikasi', 'DP'),
(46, '2026-03-15 11:27:01', 300000, 'bukti_46_2.jpg', 'Diverifikasi', 'Cicilan 2'),
(46, '2026-03-25 15:59:12', 400000, 'bukti_46_3.jpg', 'Diverifikasi', 'Pelunasan Buthak'),

-- Booking ID 47 (Buthak, 2 orang, Total 1jt, Status: DP) - 1x Bayar
(47, '2026-03-13 11:27:01', 300000, 'bukti_47_1.jpg', 'Diverifikasi', 'Booking seat buthak'),

-- Booking ID 48 (Buthak, 1 orang, Total 500rb, Status: Refund) - 1x Bayar
(48, '2026-03-21 15:59:12', 500000, 'bukti_48_1.jpg', 'Diverifikasi', 'Full payment'),

-- Booking ID 49 (Penanggungan, 3 orang, Total 1.05jt, Status: Lunas) - 2x Bayar
(49, '2026-04-02 10:14:05', 300000, 'bukti_49_1.jpg', 'Diverifikasi', 'DP ber-3'),
(49, '2026-04-10 12:48:33', 750000, 'bukti_49_2.jpg', 'Diverifikasi', 'Lunas penanggungan'),

-- Booking ID 50 (Penanggungan, 2 orang, Total 700rb, Status: Bayar non-DP) - 1x Bayar
(50, '2026-04-06 12:48:33', 150000, 'bukti_50_1.jpg', 'Diverifikasi', ''),

-- Booking ID 51 (Penanggungan, 2 orang, Total 700rb, Status: Belum Bayar) - 2x Bayar (2x Ditolak)
(51, '2026-04-12 08:11:50', 350000, 'bukti_51_1.jpg', 'Ditolak', 'Transfer tidak masuk'),
(51, '2026-04-13 14:23:11', 700000, 'bukti_51_2.jpg', 'Belum Diverifikasi', 'Ulangi transfer full'),

-- Booking ID 52 (Lemongan, 1 orang, Total 450rb, Status: Lunas) - 1x Bayar
(52, '2026-04-16 14:23:11', 450000, 'bukti_52_1.jpg', 'Diverifikasi', 'Lunas ya bos'),

-- Booking ID 53 (Lemongan, 2 orang, Total 900rb, Status: DP) - 2x Bayar
(53, '2026-04-20 09:44:50', 150000, 'bukti_53_1.jpg', 'Diverifikasi', 'Angsuran 1'),
(53, '2026-04-23 16:28:44', 150000, 'bukti_53_2.jpg', 'Diverifikasi', 'DP berdua pas'),

-- Booking ID 54 (Lemongan, 1 orang, Total 450rb, Status: Dibatalkan) - 1x Bayar
(54, '2026-04-29 10:19:33', 150000, 'bukti_54_1.jpg', 'Diverifikasi', 'DP sebelum batal'),

-- Booking ID 55 (Raung, 2 orang, Total 3jt, Status: Lunas) - 3x Bayar
(55, '2026-05-02 17:35:12', 1000000, 'bukti_55_1.jpg', 'Diverifikasi', 'DP Raung'),
(55, '2026-05-15 11:12:09', 1000000, 'bukti_55_2.jpg', 'Diverifikasi', 'Cicilan kedua'),
(55, '2026-05-25 09:54:01', 1000000, 'bukti_55_3.jpg', 'Diverifikasi', 'Lunas Raung'),

-- Booking ID 56 (Raung, 1 orang, Total 1.5jt, Status: DP) - 1x Bayar
(56, '2026-05-11 13:54:01', 500000, 'bukti_56_1.jpg', 'Diverifikasi', ''),

-- Booking ID 57 (Raung, 1 orang, Total 1.5jt, Status: Belum Bayar) - 1x Bayar
(57, '2026-05-19 16:28:44', 1500000, 'bukti_57_1.jpg', 'Ditolak', 'Gambar hitam polos'),

-- Booking ID 58 (Galunggung, 5 orang, Total 750rb, Status: Lunas) - 2x Bayar
(58, '2026-06-02 10:09:55', 250000, 'bukti_58_1.jpg', 'Diverifikasi', 'DP Rombongan'),
(58, '2026-06-15 14:52:10', 500000, 'bukti_58_2.jpg', 'Diverifikasi', 'Lunas Galunggung'),

-- Booking ID 59 (Galunggung, 3 orang, Total 450rb, Status: DP) - 1x Bayar
(59, '2026-06-06 11:30:22', 150000, 'bukti_59_1.jpg', 'Diverifikasi', 'DP dulu min'),

-- Booking ID 60 (Galunggung, 2 orang, Total 300rb, Status: Belum Bayar) - 2x Bayar
(60, '2026-06-12 14:52:10', 100000, 'bukti_60_1.jpg', 'Belum Diverifikasi', ''),
(60, '2026-06-20 09:18:44', 200000, 'bukti_60_2.jpg', 'Belum Diverifikasi', 'Sisanya')
";

$insert_batal_ot = "INSERT INTO batal_open (id_booking, status, tgl_pembatalan, alasan) VALUES
(12, TRUE, '2026-02-07 10:15:22', 'Ada keperluan keluarga mendadak yang tidak bisa ditinggalkan pada tanggal keberangkatan.'),
(21, TRUE, '2026-03-22 14:45:05', 'Kondisi fisik sedang tidak fit setelah cek kesehatan di rumah sakit, disarankan untuk istirahat total.'),
(39, TRUE, '2026-01-27 09:30:12', 'Terjadi bentrok jadwal dengan pekerjaan kantor yang harus diselesaikan segera di akhir bulan.'),
(48, TRUE, '2026-03-23 11:20:44', 'Teman perjalanan satu tim membatalkan ikut, sehingga saya memutuskan untuk ikut batal juga.'),
(54, TRUE, '2026-04-30 16:05:18', 'Dana anggaran perjalanan terpakai untuk keperluan darurat yang lebih mendesak.')
";

$insert_private = "INSERT INTO private_trip (id_akun, nama, no_hp, tujuan, tgl_berangkat, tgl_pulang, tgl_booking, catatan, jumlah_peserta, harga, harga_dp, status_trip, status_bayar) VALUES
(6, 'Andri Hermawan', '081234567890', 'Gunung Prau', '2026-02-10', '2026-02-11', '2026-01-15 09:22:15', 'Kami berencana melakukan sesi foto pre-wedding di puncak, jadi kami membutuhkan bantuan minimal 2 porter tambahan untuk membawa peralatan kamera dan kostum. Kami juga meminta menu makanan yang lebih variatif seperti ayam bakar atau steak gunung saat makan malam di sunrise camp nanti.', 6, 3600000, 1000000, 'Disetujui', 'Lunas'),
(12, 'Siska Amelia', '085712345678', 'Gunung Semeru', '2026-05-20', '2026-05-22', '2026-04-10 14:45:02', 'Peserta terdiri dari rekan kerja kantor yang sebagian besar adalah pendaki pemula, mohon sediakan tim medis cadangan atau minimal pemandu yang memiliki sertifikasi P3K yang kuat. Kami juga meminta tenda kapasitas 4 diisi maksimal 2 orang saja agar istirahat lebih nyaman selama di Kalimati.', 5, 8500000, 2500000, 'Disetujui', 'DP'),
(9, 'Budi Santoso', '081998765432', 'Gunung Gede', '2026-03-05', '2026-03-06', '2026-02-20 10:12:33', '', 8, NULL, NULL, 'Belum Disetujui', 'Belum Bayar'),
(15, 'Dewi Lestari', '082155443322', 'Gunung Merbabu', '2026-06-12', '2026-06-14', '2026-05-01 11:05:40', 'Tolong jemput rombongan kami di Stasiun Solo Jebres pada jam 8 pagi, pastikan armada transportasinya nyaman dan memiliki bagasi luas untuk carrier. Kami juga ingin jalur pendakian via Selo karena ingin mengejar pemandangan sabana yang luas untuk keperluan konten media sosial kami.', 7, 5250000, 1500000, 'Disetujui', 'Lunas'),
(7, 'Fajar Ramadhan', '087766554433', 'Gunung Lawu', '2026-08-17', '2026-08-18', '2026-07-25 08:18:29', 'Kami ingin merayakan HUT RI di puncak Hargo Dumilah dan berencana mengadakan upacara bendera kecil di sana bersama peserta lain. Mohon dibantu untuk koordinasi perizinan khusus dan penyediaan atribut bendera, serta kami ingin makan malam spesial berupa nasi tumpeng mini di area Mbok Yem.', 10, 4500000, 1350000, 'Disetujui', 'Dibatalkan'),
(10, 'Rian Hidayat', '081322114455', 'Gunung Papandayan', '2026-04-02', '2026-04-03', '2026-03-10 19:50:11', 'Ini adalah trip keluarga besar yang menyertakan anak-anak usia sekolah dasar, jadi kami mohon rute pendakian yang paling landai dan aman. Kami sangat tertarik untuk berkemah di area yang dekat dengan fasilitas toilet umum dan ingin mengunjungi area Hutan Mati saat pagi hari sebelum turun kembali.', 5, 3250000, 1000000, 'Disetujui', 'Lunas'),
(14, 'Maya Saputri', '089811223344', 'Gunung Slamet', '2026-09-10', '2026-09-12', '2026-08-15 13:45:10', 'Mengingat medan Gunung Slamet yang cukup berat, kami minta disediakan logistik yang mengandung kalori tinggi dan air minum yang melimpah selama di pos terakhir. Kami juga butuh tenda kualitas terbaik yang tahan badai karena menurut info cuaca bulan September di sana sering terjadi angin kencang.', 9, 10800000, 3000000, 'Disetujui', 'DP'),
(8, 'Eko Prasetyo', '085299008877', 'Gunung Sindoro', '2026-07-04', '2026-07-05', '2026-06-12 11:42:05', '', 6, NULL, NULL, 'Ditolak', 'Belum Bayar'),
(11, 'Nina Marlina', '081122339988', 'Gunung Sumbing', '2026-10-22', '2026-10-24', '2026-09-30 16:21:40', 'Kami memerlukan tim dokumentasi profesional yang bisa mengoperasikan drone dan kamera mirrorless untuk membuat video dokumenter pendek perjalanan kami hingga puncak sejati. Seluruh hasil mentah foto dan video mohon diserahkan langsung kepada kami segera setelah trip selesai di basecamp.', 8, 6800000, 2000000, 'Disetujui', 'Lunas'),
(13, 'Hendra Wijaya', '087855667788', 'Gunung Cikuray', '2026-11-05', '2026-11-06', '2026-10-18 10:25:18', 'Kami akan berangkat dari Jakarta menggunakan bus dan diperkirakan sampai di Terminal Garut tengah malam, mohon disiapkan kendaraan jemputan yang standby. Kami memilih jalur Pemancar dan berharap bisa mendapatkan area camp yang memiliki pemandangan langsung ke arah lautan awan yang ikonik.', 5, NULL, NULL, 'Belum Disetujui', 'Belum Bayar'),
(6, 'Rina Permata', '081244556677', 'Gunung Arjuno', '2026-01-25', '2026-01-27', '2026-01-05 12:44:19', 'Rombongan kami ingin mencoba jalur Tretes yang terkenal dengan tanjakan aspalnya, jadi pastikan fisik semua peserta sudah kami verifikasi sebelumnya. Kami minta disediakan makanan hangat selama di perjalanan seperti sup atau soto, serta kopi hitam berkualitas untuk menemani waktu santai di camp.', 7, 6300000, 2000000, 'Disetujui', 'DP'),
(12, 'Dimas Anggara', '085733445566', 'Gunung Andong', '2026-03-20', '2026-03-20', '2026-03-02 15:33:12', 'Karena ini adalah trip tektok atau perjalanan satu hari tanpa menginap, kami mohon jadwal perjalanan disusun seefisien mungkin agar bisa mengejar momen matahari terbit di puncak. Kami juga butuh sarapan ringan di basecamp sebelum pendakian dimulai dan makan siang prasmanan saat sudah turun.', 10, 2500000, 750000, 'Disetujui', 'Lunas'),
(9, 'Santi Susanti', '081977889900', 'Gunung Welirang', '2026-05-02', '2026-05-04', '2026-04-12 09:18:44', 'Tujuan utama kami adalah mengeksplorasi kawah belerang, jadi mohon sediakan masker gas standar industri yang aman untuk seluruh peserta. Kami juga ingin melihat aktivitas penambang lokal dan jika memungkinkan, mohon pemandu bisa memberikan penjelasan sejarah mengenai area pertambangan belerang tersebut.', 6, 5400000, 1600000, 'Disetujui', 'Dibatalkan'),
(15, 'Taufik Hidayat', '082188990011', 'Gunung Ungaran', '2026-08-05', '2026-08-06', '2026-07-15 11:27:01', '', 8, NULL, NULL, 'Belum Disetujui', 'Belum Bayar'),
(7, 'Indra Kusuma', '087711223355', 'Gunung Muria', '2026-12-10', '2026-12-10', '2026-11-20 15:59:12', 'Kami ingin melakukan wisata religi sekaligus olahraga ringan dengan trekking santai melewati perkebunan kopi di sekitar area Colo. Mohon sediakan pemandu lokal yang paham betul dengan sejarah situs-situs di sana dan pastikan jadwal perjalanan tidak bentrok dengan waktu ibadah para peserta.', 10, 2000000, 600000, 'Disetujui', 'Lunas'),
(10, 'Yanti Fitri', '081355442211', 'Gunung Buthak', '2026-04-20', '2026-04-22', '2026-03-28 10:14:05', 'Kami sangat menantikan untuk berkemah di area sabana Buthak yang luas, jadi tolong pastikan porter membawa peralatan camp yang lengkap termasuk meja dan kursi lipat. Kami juga meminta disediakan air bersih yang cukup banyak untuk keperluan memasak dan mencuci peralatan makan selama 2 malam di sana.', 6, 4200000, 1200000, 'Disetujui', 'DP'),
(14, 'Ahmad Junaidi', '089866778899', 'Gunung Penanggungan', '2026-06-25', '2026-06-26', '2026-06-05 12:48:33', '', 9, 3600000, 1000000, 'Disetujui', 'Lunas'),
(8, 'Deni Sumargo', '085211224466', 'Gunung Lemongan', '2026-10-05', '2026-10-06', '2026-09-12 08:11:50', 'Rencana kami adalah menikmati suasana tenang di pinggir ranu sebelum memulai pendakian ke puncak Lemongan pada dini hari berikutnya. Kami mohon disediakan peralatan memancing sederhana dan bahan barbeque untuk malam hari di pinggir danau agar suasana kekeluargaan tim kami semakin terasa akrab.', 7, NULL, NULL, 'Ditolak', 'Belum Bayar'),
(11, 'Bella Clarisa', '081133445588', 'Gunung Raung', '2026-07-18', '2026-07-21', '2026-06-15 14:23:11', 'Mengingat tingkat kesulitan Raung yang ekstrem, kami mewajibkan adanya pemandu profesional yang memiliki sertifikasi mountaineering dan paham teknis penggunaan tali. Semua peralatan keamanan seperti harness, helm, dan carabiner harus dalam kondisi baru atau sangat layak pakai demi keselamatan nyawa kami.', 5, 12500000, 4000000, 'Disetujui', 'DP'),
(13, 'Gilang Dirga', '087822334455', 'Gunung Galunggung', '2026-09-28', '2026-09-28', '2026-09-02 17:35:12', 'Ini adalah acara karyawisata sekolah menengah, jadi kami membutuhkan jumlah pemandu yang lebih banyak untuk mengawasi 10 siswa yang ikut serta. Mohon edukasi mengenai ekologi gunung berapi juga dimasukkan ke dalam materi pembicaraan pemandu selama perjalanan menuju area kawah Galunggung.', 10, 1500000, 500000, 'Disetujui', 'Lunas');
";

$insert_peserta_pt = "INSERT INTO peserta_private (id_private, nama, usia, alamat, riwayat) VALUES
-- ID Private 1 (6 Peserta)
(1, 'Aris Setiawan', 28, 'Dusun Mawar, Kec. Lowokwaru, Kota Malang', 'Maag'),
(1, 'Bambang Triyono', 27, 'Jl. Melati, Kec. Serpong, Tangerang Selatan', 'Asma'),
(1, 'Catur Nugroho', 26, 'Dusun Krajan, Kec. Ngoro, Kab. Mojokerto', 'Alergi Debu'),
(1, 'Dedi Kurniawan', 29, 'Perum Graha, Kec. Tambun Selatan, Kab. Bekasi', ''),
(1, 'Eka Putri Rahayu', 25, 'Dusun Sono, Kec. Beji, Kota Depok', 'Migran'),
(1, 'Fajar Sidik', 28, 'Kp. Rambutan, Kec. Ciracas, Jakarta Timur', 'Alergi Dingin'),

-- ID Private 2 (5 Peserta)
(2, 'Siska Amelia', 24, 'Dusun Sukasari, Kec. Coblong, Kota Bandung', 'Anemia'),
(2, 'Rina Permadi', 25, 'Kp. Caringin, Kec. Cimahi Tengah, Kota Cimahi', 'Asma'),
(2, 'Heri Sudirjo', 30, 'Dusun Bojong, Kec. Tanjungsari, Kab. Sumedang', 'Hipotermia Ringan'),
(2, 'Andi Wijaya', 26, 'Dusun Lembang, Kec. Lembang, Kab. Bandung Barat', ''),
(2, 'Santi Rahayu', 24, 'Dusun Wanaraja, Kec. Wanaraja, Kab. Garut', 'Maag'),

-- ID Private 3 (8 Peserta)
(3, 'Budi Santoso', 35, 'Dusun Candisari, Kec. Banyumanik, Kota Semarang', 'Maag Akut'),
(3, 'Agus Setiawan', 32, 'Kp. Pajang, Kec. Laweyan, Kota Surakarta', 'Asam Urat'),
(3, 'Larasati Putri', 29, 'Dusun Condongcatur, Kec. Depok, Kab. Sleman', 'Alergi Seafood'),
(3, 'Rahmat Hidayat', 34, 'Dusun Genuk, Kec. Genuk, Kota Semarang', 'Vertigo'),
(3, 'Dewi Sartika', 31, 'Dusun Ampel, Kec. Ampel, Kab. Boyolali', ''),
(3, 'Joko Prasetyo', 33, 'Dusun Sidorejo, Kec. Sidorejo, Kota Salatiga', 'Sinusitis'),
(3, 'Slamet Mulyono', 35, 'Dusun Tembalang, Kec. Tembalang, Kota Semarang', 'Alergi Dingin'),
(3, 'Siti Aminah', 30, 'Dusun Ngaliyan, Kec. Ngaliyan, Kota Semarang', 'Asma'),

-- ID Private 4 (7 Peserta)
(4, 'Yulia Lestari', 27, 'Dusun Gubeng, Kec. Gubeng, Kota Surabaya', 'Anemia'),
(4, 'Bambang Utomo', 28, 'Dusun Waru, Kec. Waru, Kab. Sidoarjo', 'Maag'),
(4, 'Yanto Sudrajat', 30, 'Dusun Blimbing, Kec. Blimbing, Kota Malang', 'Hipotermia'),
(4, 'Indah Permata', 29, 'Dusun Wonokromo, Kec. Wonokromo, Kota Surabaya', ''),
(4, 'Mulyadi', 55, 'Kp. Melayu, Kec. Jatinegara, Jakarta Timur', 'Hipertensi'),
(4, 'Nining Handayani', 50, 'Jl. Sabang, Kec. Menteng, Kota Jakarta Pusat', 'Kolesterol'),
(4, 'Toto Hartono', 32, 'Dusun Manahan, Kec. Banjarsari, Kota Surakarta', 'Asam Lambung'),

-- ID Private 5 (10 Peserta)
(5, 'Dani Ramadhan', 22, 'Dusun Kesambi, Kec. Kesambi, Kota Cirebon', 'Alergi Udara Dingin'),
(5, 'Zaki Mubarok', 21, 'Dusun Jatibarang, Kec. Jatibarang, Kab. Indramayu', 'Maag'),
(5, 'Asep Sunandar', 23, 'Dusun Kadipaten, Kec. Kadipaten, Kab. Majalengka', 'Asma'),
(5, 'Cecep Setiawan', 22, 'Dusun Cilimus, Kec. Cilimus, Kab. Kuningan', ''),
(5, 'Dadang Kusnadi', 25, 'Dusun Soreang, Kec. Soreang, Kab. Bandung', 'Alergi Dingin'),
(5, 'Sutrisno', 24, 'Dusun Palmerah, Kec. Palmerah, Kota Jakarta Barat', 'Migrain'),
(5, 'Ginanjar Saputra', 23, 'Dusun Cipanas, Kec. Cipanas, Kab. Cianjur', 'Asma'),
(5, 'Heri Purnomo', 22, 'Dusun Pedurungan, Kec. Pedurungan, Kota Semarang', 'Alergi Debu'),
(5, 'Iwan Setiawan', 26, 'Dusun Tebet, Kec. Tebet, Kota Jakarta Selatan', 'Maag'),
(5, 'Jajang Mulyana', 24, 'Dusun Ciawi, Kec. Ciawi, Kab. Tasikmalaya', 'Vertigo'),

-- ID Private 6 (5 Peserta)
(6, 'Rian Hidayat', 28, 'Dusun Pasiripis, Kec. Cisurupan, Kab. Garut', 'Maag'),
(6, 'Fatur Rahman', 27, 'Dusun Pananjung, Kec. Tarogong Kaler, Kab. Garut', 'Asma'),
(6, 'Agus Kurnia', 29, 'Kp. Nagrog, Kec. Wanaraja, Kab. Garut', 'Alergi Dingin'),
(6, 'Siti Nuraini', 25, 'Dusun Kadungora, Kec. Kadungora, Kab. Garut', 'Anemia'),
(6, 'Dadan Hamdani', 30, 'Dusun Samarang, Kec. Samarang, Kab. Garut', ''),

-- ID Private 7 (9 Peserta)
(7, 'Maya Saputri', 26, 'Dusun Bambangan, Kec. Karangreja, Kab. Purbalingga', 'Migrain'),
(7, 'Indra Jaya', 27, 'Dusun Serang, Kec. Karangreja, Kab. Purbalingga', 'Alergi Debu'),
(7, 'Putri Handayani', 25, 'Dusun Mrebet, Kec. Mrebet, Kab. Purbalingga', ''),
(7, 'Riko Gunawan', 28, 'Kp. Bobotsari, Kec. Bobotsari, Kab. Purbalingga', 'Asma'),
(7, 'Siska Putri', 24, 'Dusun Bukateja, Kec. Bukateja, Kab. Purbalingga', 'Hipotermia Ringan'),
(7, 'Doni Setiawan', 30, 'Dusun Padamara, Kec. Padamara, Kab. Purbalingga', 'Maag Akut'),
(7, 'Lilis Suryani', 26, 'Dusun Kejobong, Kec. Kejobong, Kab. Purbalingga', 'Sinusitis'),
(7, 'Bambang Prasetyo', 32, 'Dusun Kalimanah, Kec. Kalimanah, Kab. Purbalingga', ''),
(7, 'Taufik Ismail', 29, 'Dusun Kutoarjo, Kec. Kutoarjo, Kab. Purworejo', 'Alergi Dingin'),

-- ID Private 8 (6 Peserta)
(8, 'Eko Prasetyo', 31, 'Dusun Kledung, Kec. Kledung, Kab. Temanggung', 'Asam Lambung'),
(8, 'Wahyu Hidayat', 32, 'Dusun Parakan, Kec. Parakan, Kab. Temanggung', 'Maag Ringan'),
(8, 'Slamet Rahardjo', 35, 'Dusun Ngadirejo, Kec. Ngadirejo, Kab. Temanggung', 'Vertigo'),
(8, 'Yulianto', 30, 'Dusun Bulu, Kec. Bulu, Kab. Temanggung', ''),
(8, 'Nur Hayati', 28, 'Dusun Kandangan, Kec. Kandangan, Kab. Temanggung', 'Anemia'),
(8, 'Anas Setiawan', 29, 'Dusun Tembarak, Kec. Tembarak, Kab. Temanggung', 'Alergi Debu'),

-- ID Private 9 (8 Peserta)
(9, 'Nina Marlina', 27, 'Dusun Garung, Kec. Kalikajar, Kab. Wonosobo', 'Maag'),
(9, 'Aris Munandar', 28, 'Dusun Butuh, Kec. Kalikajar, Kab. Wonosobo', 'Hipotermia'),
(9, 'Mega Utami', 25, 'Dusun Kertek, Kec. Kertek, Kab. Wonosobo', 'Migrain'),
(9, 'Feri Setiawan', 30, 'Dusun Sapuran, Kec. Sapuran, Kab. Wonosobo', 'Alergi Dingin'),
(9, 'Dwi Ariyanto', 29, 'Dusun Leksono, Kec. Leksono, Kab. Wonosobo', ''),
(9, 'Tri Wahyuni', 26, 'Dusun Watumalang, Kec. Watumalang, Kab. Wonosobo', 'Asma'),
(9, 'Catur Putra', 31, 'Dusun Mojotengah, Kec. Mojotengah, Kab. Wonosobo', 'Asam Urat'),
(9, 'Panca Indra', 28, 'Dusun Sukoharjo, Kec. Sukoharjo, Kab. Wonosobo', 'Vertigo'),

-- ID Private 10 (5 Peserta)
(10, 'Hendra Wijaya', 29, 'Dusun Pemancar, Kec. Cilawu, Kab. Garut', 'Kolesterol'),
(10, 'Yudi Pratama', 30, 'Dusun Bayongbong, Kec. Bayongbong, Kab. Garut', 'Maag'),
(10, 'Maman Suherman', 35, 'Dusun Karangpawitan, Kec. Karangpawitan, Kab. Garut', 'Asam Urat'),
(10, 'Ujang Komarudin', 32, 'Dusun Leuwigoong, Kec. Leuwigoong, Kab. Garut', ''),
(10, 'Euis Dahlia', 28, 'Dusun Leles, Kec. Leles, Kab. Garut', 'Anemia'),

-- ID Private 11 (7 Peserta)
(11, 'Rina Permata', 26, 'Dusun Tretes, Kec. Prigen, Kab. Pasuruan', 'Alergi Dingin'),
(11, 'Samsul Arifin', 28, 'Dusun Pandaan, Kec. Pandaan, Kab. Pasuruan', 'Maag'),
(11, 'Lutfi Hakim', 27, 'Dusun Sukorejo, Kec. Sukorejo, Kab. Pasuruan', ''),
(11, 'Beni Irawan', 29, 'Dusun Purwosari, Kec. Purwosari, Kab. Pasuruan', 'Vertigo'),
(11, 'Tita Rosita', 25, 'Dusun Bangil, Kec. Bangil, Kab. Pasuruan', 'Anemia'),
(11, 'Andik Setiawan', 30, 'Dusun Gempol, Kec. Gempol, Kab. Pasuruan', 'Asma'),
(11, 'Fajar Sidik', 24, 'Dusun Ngoro, Kec. Ngoro, Kab. Mojokerto', 'Alergi Debu'),

-- ID Private 12 (10 Peserta)
(12, 'Dimas Anggara', 24, 'Dusun Ngablak, Kec. Ngablak, Kab. Magelang', 'Maag Akut'),
(12, 'Rizky Fauzi', 23, 'Dusun Grabag, Kec. Grabag, Kab. Magelang', 'Sinusitis'),
(12, 'Supardi', 45, 'Dusun Secang, Kec. Secang, Kab. Magelang', 'Asam Urat'),
(12, 'Hadi Pranoto', 46, 'Dusun Tegalrejo, Kec. Tegalrejo, Kab. Magelang', ''),
(12, 'Joko Susilo', 50, 'Dusun Muntilan, Kec. Muntilan, Kab. Magelang', 'Hipertensi'),
(12, 'Nanang Kosim', 47, 'Dusun Salam, Kec. Salam, Kab. Magelang', 'Kolesterol'),
(12, 'Siti Aminah', 55, 'Dusun Borobudur, Kec. Borobudur, Kab. Magelang', 'Diabetes'),
(12, 'Yanto Subagyo', 25, 'Dusun Sawangan, Kec. Sawangan, Kab. Magelang', 'Alergi Dingin'),
(12, 'Aditya Putra', 30, 'Dusun Candimulyo, Kec. Candimulyo, Kab. Magelang', 'Migrain'),
(12, 'Dwi Cahyono', 33, 'Dusun Mertoyudan, Kec. Mertoyudan, Kab. Magelang', 'Hipotermia'),

-- ID Private 13 (6 Peserta)
(13, 'Santi Susanti', 27, 'Dusun Pecalukan, Kec. Prigen, Kab. Pasuruan', 'Anemia'),
(13, 'Eka Megawati', 25, 'Dusun Ledug, Kec. Prigen, Kab. Pasuruan', 'Maag'),
(13, 'Rahmat Hidayat', 28, 'Dusun Lumbang, Kec. Lumbang, Kab. Pasuruan', 'Asma'),
(13, 'Ismail Saleh', 26, 'Dusun Puspo, Kec. Puspo, Kab. Pasuruan', ''),
(13, 'Hafiz Firdaus', 24, 'Dusun Tosari, Kec. Tosari, Kab. Pasuruan', 'Alergi Debu'),
(13, 'Baktiar Muslih', 25, 'Dusun Tutur, Kec. Tutur, Kab. Pasuruan', 'Sinusitis'),

-- ID Private 14 (8 Peserta)
(14, 'Taufik Hidayat', 30, 'Dusun Bandungan, Kec. Bandungan, Kab. Semarang', 'Asam Lambung'),
(14, 'Dwi Kuncoro', 31, 'Dusun Ambarawa, Kec. Ambarawa, Kab. Semarang', 'Migrain'),
(14, 'Agus Santoso', 29, 'Dusun Bawen, Kec. Bawen, Kab. Semarang', 'Vertigo'),
(14, 'Siti Polii', 28, 'Dusun Jambu, Kec. Jambu, Kab. Semarang', 'Alergi Dingin'),
(14, 'Rahayu Putri', 24, 'Dusun Sumowono, Kec. Sumowono, Kab. Semarang', 'Maag'),
(14, 'Jonatan Pratama', 25, 'Dusun Ungaran Barat, Kec. Ungaran Barat, Kab. Semarang', ''),
(14, 'Anthoni Ginting', 25, 'Dusun Ungaran Timur, Kec. Ungaran Timur, Kab. Semarang', 'Anemia'),
(14, 'Kevin Sanjaya', 26, 'Dusun Bergas, Kec. Bergas, Kab. Semarang', 'Asma'),

-- ID Private 15 (10 Peserta)
(15, 'Indra Kusuma', 32, 'Dusun Colo, Kec. Dawe, Kab. Kudus', 'Hipotermia'),
(15, 'Samsul Arif', 33, 'Dusun Gebog, Kec. Gebog, Kab. Kudus', 'Maag Akut'),
(15, 'Bahrul Ulum', 30, 'Dusun Jati, Kec. Jati, Kab. Kudus', 'Asam Urat'),
(15, 'Siti Munawaroh', 28, 'Dusun Mejobo, Kec. Mejobo, Kab. Kudus', 'Sinusitis'),
(15, 'Rahmat Efendi', 31, 'Dusun Undaan, Kec. Undaan, Kab. Kudus', 'Kolesterol'),
(15, 'Nur Azizah', 29, 'Dusun Bae, Kec. Bae, Kab. Kudus', ''),
(15, 'Faisal Basri', 34, 'Dusun Kaliwungu, Kec. Kaliwungu, Kab. Kudus', 'Alergi Debu'),
(15, 'Dewi Sartika', 27, 'Dusun Jekulo, Kec. Jekulo, Kab. Kudus', 'Anemia'),
(15, 'Saiful Anwar', 35, 'Dusun Kota Kudus, Kec. Kota Kudus, Kab. Kudus', 'Asma'),
(15, 'Aldi Taher', 32, 'Dusun Gajah, Kec. Gajah, Kab. Demak', 'Vertigo'),

-- ID Private 16 (6 Peserta)
(16, 'Yanti Fitriani', 28, 'Dusun Sirah Kencong, Kec. Wlingi, Kab. Blitar', 'Anemia'),
(16, 'Jajang Sukirman', 30, 'Dusun Selopuro, Kec. Selopuro, Kab. Blitar', 'Asam Lambung'),
(16, 'Sri Wahyuningsih', 26, 'Dusun Gandusari, Kec. Gandusari, Kab. Blitar', 'Alergi Dingin'),
(16, 'Dedi Mulyono', 32, 'Dusun Talun, Kec. Talun, Kab. Blitar', ''),
(16, 'Susi Susanti', 29, 'Dusun Kanigoro, Kec. Kanigoro, Kab. Blitar', 'Migrain'),
(16, 'Hendra Basuki', 31, 'Dusun Garum, Kec. Garum, Kab. Blitar', 'Maag'),

-- ID Private 17 (9 Peserta)
(17, 'Ahmad Junaidi', 27, 'Dusun Trawas, Kec. Trawas, Kab. Mojokerto', 'Alergi Debu'),
(17, 'Soleh Soleman', 28, 'Dusun Pacet, Kec. Pacet, Kab. Mojokerto', 'Sinusitis'),
(17, 'Samsudin Arif', 35, 'Dusun Gondang, Kec. Gondang, Kab. Mojokerto', 'Asam Urat'),
(17, 'Nurul Hidayah', 24, 'Dusun Jatirejo, Kec. Jatirejo, Kab. Mojokerto', ''),
(17, 'Maimunah Saputri', 22, 'Dusun Trowulan, Kec. Trowulan, Kab. Mojokerto', 'Anemia'),
(17, 'Zulfa Hanum', 23, 'Dusun Puri, Kec. Puri, Kab. Mojokerto', 'Maag'),
(17, 'Abdur Rohim', 29, 'Dusun Dlanggu, Kec. Dlanggu, Kab. Mojokerto', 'Hipotermia Ringan'),
(17, 'Anwar Sadat', 30, 'Dusun Mojoanyar, Kec. Mojoanyar, Kab. Mojokerto', 'Asma'),
(17, 'Soleh Solihun', 32, 'Dusun Bangsal, Kec. Bangsal, Kab. Mojokerto', 'Vertigo'),

-- ID Private 18 (7 Peserta)
(18, 'Deni Setiawan', 34, 'Dusun Klakah, Kec. Klakah, Kab. Lumajang', 'Kolesterol'),
(18, 'Fitriani Allan', 30, 'Dusun Ranuyoso, Kec. Ranuyoso, Kab. Lumajang', 'Maag'),
(18, 'Riko Setiadi', 35, 'Dusun Kedungjajang, Kec. Kedungjajang, Kab. Lumajang', 'Vertigo'),
(18, 'Yayan Ruhiyat', 40, 'Dusun Tempeh, Kec. Tempeh, Kab. Lumajang', 'Asam Urat'),
(18, 'Hendra Kusuma', 38, 'Dusun Pasirian, Kec. Pasirian, Kab. Lumajang', 'Alergi Dingin'),
(18, 'Iwan Setiawan', 36, 'Dusun Senduro, Kec. Senduro, Kab. Lumajang', ''),
(18, 'Siti Aminah', 34, 'Dusun Candipuro, Kec. Candipuro, Kab. Lumajang', 'Anemia'),

-- ID Private 19 (5 Peserta)
(19, 'Bella Clarisa', 23, 'Dusun Sumber Wringin, Kec. Sumber Wringin, Kab. Bondowoso', 'Asma'),
(19, 'Caca Handika', 25, 'Dusun Sukosari, Kec. Sukosari, Kab. Bondowoso', 'Maag'),
(19, 'Ningsih Slamet', 27, 'Dusun Tamanan, Kec. Tamanan, Kab. Bondowoso', 'Alergi Debu'),
(19, 'Raffi Ahmadani', 28, 'Dusun Maesan, Kec. Maesan, Kab. Bondowoso', 'Sinusitis'),
(19, 'Rahmat Hidayat', 22, 'Dusun Prajekan, Kec. Prajekan, Kab. Bondowoso', ''),

-- ID Private 20 (10 Peserta)
(20, 'Gilang Dirgantara', 30, 'Dusun Singaparna, Kec. Singaparna, Kab. Tasikmalaya', 'Asam Lambung'),
(20, 'Aditya Fersa', 28, 'Dusun Cipasung, Kec. Cipasung, Kab. Tasikmalaya', 'Migrain'),
(20, 'Irvan Hakimanto', 35, 'Dusun Manonjaya, Kec. Manonjaya, Kab. Tasikmalaya', 'Asam Urat'),
(20, 'Ramzi Kusuma', 34, 'Dusun Ciawi, Kec. Ciawi, Kab. Tasikmalaya', 'Kolesterol'),
(20, 'Rina Permatasari', 32, 'Dusun Rajapolah, Kec. Rajapolah, Kab. Tasikmalaya', 'Sinusitis'),
(20, 'Jajang Nurjaman', 32, 'Dusun Indihiang, Kec. Indihiang, Kota Tasikmalaya', 'Maag'),
(20, 'Selfi Rahayu', 23, 'Dusun Kawalu, Kec. Kawalu, Kota Tasikmalaya', 'Anemia'),
(20, 'Lesti Listiani', 24, 'Dusun Mangkubumi, Kec. Mangkubumi, Kota Tasikmalaya', 'Alergi Dingin'),
(20, 'Rizky Billah', 26, 'Dusun Tamansari, Kec. Tamansari, Kota Tasikmalaya', ''),
(20, 'Dedi Kurnia', 40, 'Dusun Cibeureum, Kec. Cibeureum, Kota Tasikmalaya', 'Hipertensi')
";

$insert_payment_pt = "INSERT INTO payment_private (id_private, tgl_bayar, nominal, bukti_bayar, status, catatan) VALUES
-- ID Private 1: Status Lunas (Harga 3.600.000). Bayar 2x
(1, '2026-01-16 10:45:00', 1000000, 'private_1_1.jpg', 'Diverifikasi', 'Transfer DP untuk trip Prau sesuai kesepakatan.'),
(1, '2026-02-05 14:20:00', 2600000, 'private_1_2.jpg', 'Diverifikasi', 'Pelunasan sisa pembayaran atas nama Andri Hermawan.'),

-- ID Private 2: Status DP (Harga DP 2.500.000). Bayar 1x
(2, '2026-04-15 09:30:00', 2500000, 'private_2_1.jpg', 'Diverifikasi', 'Pembayaran DP awal agar trip segera diproses admin.'),

-- ID Private 4: Status Lunas (Harga 5.250.000). Bayar 3x
(4, '2026-05-02 08:15:00', 1500000, 'private_4_1.jpg', 'Diverifikasi', 'DP 1 untuk trip Merbabu.'),
(4, '2026-05-20 16:40:00', 2000000, 'private_4_2.jpg', 'Diverifikasi', 'Titip cicilan kedua ya min.'),
(4, '2026-06-05 11:10:00', 1750000, 'private_4_3.jpg', 'Diverifikasi', 'Ini bukti transfer pelunasannya, tolong dicek.'),

-- ID Private 5: Status Dibatalkan (Harga DP 1.350.000). Bayar 1x
(5, '2026-07-28 10:00:00', 1350000, 'private_5_1.jpg', 'Diverifikasi', 'Bayar DP dulu untuk amankan tanggal.'),

-- ID Private 6: Status Lunas (Harga 3.250.000). Bayar 2x
(6, '2026-03-15 13:00:00', 1000000, 'private_6_1.jpg', 'Diverifikasi', 'Izin transfer DP trip Papandayan.'),
(6, '2026-03-25 15:55:00', 2250000, 'private_6_2.jpg', 'Diverifikasi', 'Sisa pelunasannya sudah saya transfer barusan.'),

-- ID Private 7: Status DP (Harga DP 3.000.000). Bayar 2x (1 Belum Diverifikasi)
(7, '2026-08-16 09:12:00', 3000000, 'private_7_1.jpg', 'Diverifikasi', 'Pembayaran DP pertama sesuai invoice.'),
(7, '2026-08-30 14:00:00', 1000000, 'private_7_2.jpg', 'Belum Diverifikasi', 'Tambahan cicilan pembayaran.'),

-- ID Private 9: Status Lunas (Harga 6.800.000). Bayar 3x
(9, '2026-10-02 08:45:00', 2000000, 'private_9_1.jpg', 'Diverifikasi', 'DP awal sesuai instruksi admin.'),
(9, '2026-10-10 12:30:00', 2000000, 'private_9_2.jpg', 'Diverifikasi', 'Cicilan kedua buat trip Sumbing.'),
(9, '2026-10-18 17:15:00', 2800000, 'private_9_3.jpg', 'Diverifikasi', 'Pelunasan akhirnya sudah ya min, terima kasih.'),

-- ID Private 11: Status DP (Harga DP 2.000.000). Bayar 1x
(11, '2026-01-07 10:20:00', 2000000, 'private_11_1.jpg', 'Diverifikasi', 'Transfer DP untuk trip Arjuno via Tretes.'),

-- ID Private 12: Status Lunas (Harga 2.500.000). Bayar 2x
(12, '2026-03-05 08:30:00', 750000, 'private_12_1.jpg', 'Diverifikasi', 'DP awal untuk trip tektok Andong.'),
(12, '2026-03-15 16:45:00', 1750000, 'private_12_2.jpg', 'Diverifikasi', 'Pelunasan sisanya ya admin, tolong dikonfirmasi.'),

-- ID Private 13: Status Dibatalkan (Harga DP 1.600.000). Bayar 1x
(13, '2026-04-15 11:00:00', 1600000, 'private_13_1.jpg', 'Diverifikasi', 'Izin bayar DP untuk amankan slot kawah Welirang.'),

-- ID Private 15: Status Lunas (Harga 2.000.000). Bayar 1x Langsung
(15, '2026-11-25 09:00:00', 2000000, 'private_15_1.jpg', 'Diverifikasi', 'Bayar lunas langsung untuk trip religi Muria.'),

-- ID Private 16: Status DP (Harga DP 1.200.000). Bayar 2x (1 Diverifikasi, 1 Belum)
(16, '2026-03-30 14:20:00', 1200000, 'private_16_1.jpg', 'Diverifikasi', 'Transfer uang muka (DP) trip Buthak.'),
(16, '2026-04-05 10:00:00', 500000, 'private_16_2.jpg', 'Belum Diverifikasi', 'Tambahan sedikit buat bekal porter.'),

-- ID Private 17: Status Lunas (Harga 3.600.000). Bayar 3x (Cicil)
(17, '2026-06-07 08:15:00', 1000000, 'private_17_1.jpg', 'Diverifikasi', 'DP 1 untuk trip Penanggungan.'),
(17, '2026-06-15 13:30:00', 1000000, 'private_17_2.jpg', 'Diverifikasi', 'Cicilan kedua.'),
(17, '2026-06-20 15:00:00', 1600000, 'private_17_3.jpg', 'Diverifikasi', 'Pelunasan trip, mohon diproses simaksinya.'),

-- ID Private 19: Status DP (Harga DP 4.000.000). Bayar 1x
(19, '2026-06-20 09:45:00', 4000000, 'private_19_1.jpg', 'Diverifikasi', 'Pembayaran DP untuk trip Raung, mohon siapkan alat safety terbaik.'),

-- ID Private 20: Status Lunas (Harga 1.500.000). Bayar 2x
(20, '2026-09-05 10:10:00', 500000, 'private_20_1.jpg', 'Diverifikasi', 'Uang muka untuk karyawisata ke Galunggung.'),
(20, '2026-09-15 14:20:00', 1000000, 'private_20_2.jpg', 'Diverifikasi', 'Sisa pelunasannya sudah ditransfer admin.')
";

$insert_batal_pt = "INSERT INTO batal_private (id_private, status, tgl_pembatalan, alasan) VALUES
-- ID Private 5 (Gunung Lawu, Booking: 2026-07-25)
(5, TRUE, '2026-08-05 14:30:00', 'Terdapat kendala mendadak pada jadwal cuti bersama di kantor kami, sehingga sebagian besar peserta tidak bisa ikut berangkat di tanggal tersebut.'),

-- ID Private 13 (Gunung Welirang, Booking: 2026-04-12)
(13, TRUE, '2026-04-20 10:15:22', 'Kondisi kesehatan salah satu anggota keluarga inti pemesan sedang menurun dan memerlukan perawatan intensif, mohon maaf trip harus kami batalkan.')
";

/*
$insert_peserta = "INSERT INTO peserta (id_akun, nama, no_hp, tgl_lahir, alamat, riwayat) VALUES 
(3, 'najib', '0896', '2006-02-12', 'Cirebon Kota', ''),
(5, 'yayat', '0831', '2007-05-01', 'Kecamatan Kroya', ''),
(5, 'angga', '0858', '2004-03-17', 'Desa Bunder', 'Maag'),
(6, 'dai', '0878', '2005-07-21', 'Kecamatan Indramayu', 'Alergi dingin'),
(7, 'aryadi', '0821', '2009-08-17', 'Kabupaten Cirebon', 'Tulang geser')";

$insert_private = "INSERT INTO private (id_akun, nama, no_hp, tujuan, tgl_berangkat, tgl_pulang, catatan, jumlah_peserta) VALUES 
(6, 'gilang', '0896', 'Gunung Prau', '2026-02-12', '2026-02-14', '', 3),
(6, 'rohman', '0831', 'Gunung Slamet', '2027-05-01', '2027-05-02', 'Menggunakan Mobil Toyota Hiace', 2),
(7, 'adinda', '0858', 'Gunung Semeru', '2025-03-17', '2025-03-17', '', 4),
(5, 'sintia', '0878', 'Gunung Gede', '2025-07-21', '2025-07-22', 'Makan di RM Cita Rasa', 2),
(5, 'wildan', '0821', 'Gunung Sumbing', '2026-03-17', '2026-03-17', 'Berangkat via Full Tol', 3)";

$insert_member = "INSERT INTO member (id_private, nama, tgl_lahir, alamat, riwayat) VALUES 
(1, 'najib', '2006-02-12', 'Cirebon Kota', ''),
(2, 'yayat', '2007-05-01', 'Kecamatan Kroya', ''),
(3, 'angga', '2004-03-17', 'Desa Bunder', 'Maag'),
(4, 'dai', '2005-07-21', 'Kecamatan Indramayu', 'Alergi dingin'),
(5, 'aryadi', '2009-08-17', 'Kabupaten Cirebon', 'Tulang geser'),
(1, 'Budi', '1990-05-12', 'Kabupaten Indramayu', ''),
(1, 'Siti', '1985-11-20', 'Kecamatan Sliyeg', 'Alergi debu'),
(2, 'Andi', '1998-02-28', 'Cirebon', 'Pernah operasi'),
(3, 'Dewi', '2001-07-15', 'Kecamatan Jatibarang', ''),
(3, 'Rian', '1992-09-30', 'Kabupaten Majalengka', ''),
(3, 'Eka', '1988-12-05', 'Kecamatan Balongan', 'Asma'),
(4, 'Gita', '1995-03-14', 'Kabupaten Subang', ''),
(5, 'Fajar', '1993-06-22', 'Kecamatan Karangampel', 'Alergi seafood'),
(5, 'Hendra', '1980-01-10', 'Kabupaten Kuningan', 'Hipertensi')";

$insert_gambar = "INSERT INTO gambar (id_trip, nama_file) VALUES 
(1, 'gunung1a'),
(1, 'gunung1b'),
(1, 'gunung1c'),
(2, 'gunung2a'),
(2, 'gunung2b'),
(2, 'gunung2c'),
(3, 'gunung3a'),
(3, 'gunung3b'),
(3, 'gunung3c'),
(4, 'gunung4a'),
(4, 'gunung4b'),
(4, 'gunung4c'),
(5, 'gunung5a'),
(5, 'gunung5b'),
(5, 'gunung5c')";

$insert_itenerary  = "INSERT INTO itenerary (id_trip, mulai, selesai, kegiatan)
VALUES 
(1, '04:00:00', '06:00:00', 'Pendakian Menuju Pos 1'),
(1, '07:00:00', '10:00:00', 'Tracking Jalur Hutan'),
(1, '11:00:00', '13:00:00', 'Istirahat di Pos Bayangan'),
(2, '03:00:00', '05:30:00', 'Summit Attack'),
(2, '06:00:00', '08:00:00', 'Menikmati Matahari Terbit'),
(2, '09:00:00', '12:00:00', 'Perjalanan Turun ke Basecamp'),
(3, '08:00:00', '11:00:00', 'Packing Logistik Puncak'),
(3, '12:00:00', '15:00:00', 'Lintas Punggungan Gunung'),
(3, '16:00:00', '18:00:00', 'Mendirikan Tenda di Sabana'),
(4, '05:00:00', '09:00:00', 'Eksplorasi Kawah Gunung'),
(4, '10:00:00', '12:00:00', 'Masak Logistik di Camp'),
(4, '13:00:00', '16:00:00', 'Navigasi Jalur Vegetasi'),
(5, '07:00:00', '10:00:00', 'Treking Lembah Hijau'),
(5, '11:00:00', '14:00:00', 'Pengamatan Flora Fauna'),
(5, '15:00:00', '17:30:00', 'Bongkar Tenda dan Turun')";

$insert_meetpoint = "INSERT INTO meetpoint (id_trip, waktu, kota, daerah)
VALUES 
(1, '01:15:00', 'Indramayu', 'Terminal Sindang'),
(1, '05:30:00', 'Cirebon', 'Stasiun Kejaksan'),
(1, '09:00:00', 'Majalengka', 'Basecamp Apuy'),
(2, '18:45:00', 'Jakarta', 'Kampung Rambutan'),
(2, '22:15:00', 'Bandung', 'Terminal Leuwi Panjang'),
(2, '02:00:00', 'Garut', 'Basecamp Bambu Runcing'),
(3, '06:30:00', 'Semarang', 'Stasiun Poncol'),
(3, '10:45:00', 'Wonosobo', 'Plaza Wonosobo'),
(3, '14:00:00', 'Wonosobo', 'Basecamp Patak Banteng'),
(4, '08:15:00', 'Surabaya', 'Terminal Purabaya'),
(4, '12:30:00', 'Malang', 'Stasiun Kota Baru'),
(4, '17:45:00', 'Lumajang', 'Pos Ranupani'),
(5, '07:00:00', 'Bekasi', 'Stasiun Bekasi'),
(5, '11:15:00', 'Bogor', 'Terminal Baranangsiang'),
(5, '15:30:00', 'Cianjur', 'Basecamp Gunung Putri')";

$insert_fasilitas = "INSERT INTO fasilitas (id_trip, fasilitas, jenis)
VALUES 
(1, 'Transportasi AC', 'include'),
(1, 'Simaksi & Perizinan', 'include'),
(1, 'Makan Selama Pendakian', 'include'),
(1, 'Peralatan Camping Pribadi', 'exclude'),
(1, 'Porter Pribadi', 'exclude'),
(1, 'Camilan/Snack Pribadi', 'exclude'),

(2, 'Tenda & Alat Masak', 'include'),
(2, 'Pemandu/Guide Gunung', 'include'),
(2, 'Asuransi Pendakian', 'include'),
(2, 'Transportasi ke Basecamp', 'exclude'),
(2, 'Jaket & Sepatu Gunung', 'exclude'),
(2, 'Oleh-oleh', 'exclude'),

(3, 'Dokumentasi Drone', 'include'),
(3, 'Tiket Masuk Wisata', 'include'),
(3, 'P3K & Safety Kit', 'include'),
(3, 'Makan di Luar Program', 'exclude'),
(3, 'Penginapan Hotel', 'exclude'),
(3, 'Tips Guide', 'exclude'),

(4, 'Sewa Jeep 4x4', 'include'),
(4, 'Tiket Masuk Taman Nasional', 'include'),
(4, 'Makan Siang Box', 'include'),
(4, 'Sewa Kuda di Gunung', 'exclude'),
(4, 'Pengeluaran Pribadi', 'exclude'),
(4, 'Jaket Tebal/Winter', 'exclude'),

(5, 'Tenda Kapasitas 4 Orang', 'include'),
(5, 'Matras & Sleeping Bag', 'include'),
(5, 'Logistik Grup', 'include'),
(5, 'Senter/Headlamp', 'exclude'),
(5, 'Obat-obatan Khusus', 'exclude'),
(5, 'Biaya Kamar Mandi Basecamp', 'exclude')";

$insert_booking = "INSERT INTO booking (id_trip, id_peserta, tgl_booking, status)
VALUES 
(1, 3, '2026-04-01 07:00:00', 'Lunas'),
(1, 1, '2026-04-02 09:00:00', 'DP'),
(1, 5, '2026-04-03 07:40:00', 'Belum Bayar'),
(2, 2, '2026-04-01 21:10:00', 'Lunas'),
(2, 4, '2026-04-04 17:50:00', 'Dibatalkan'),
(2, 1, '2026-04-05 07:20:00', 'DP'),
(3, 5, '2026-04-02 20:30:00', 'Lunas'),
(3, 3, '2026-04-03 13:20:00', 'Belum Bayar'),
(3, 2, '2026-04-06 17:10:00', 'DP'),
(4, 4, '2026-04-01 07:50:00', 'Dibatalkan'),
(4, 1, '2026-04-04 23:50:00', 'Lunas'),
(4, 5, '2026-04-05 03:10:00', 'Belum Bayar'),
(5, 2, '2026-04-02 05:00:00', 'DP'),
(5, 4, '2026-04-03 06:00:00', 'Lunas'),
(5, 3, '2026-04-06 10:00:00', 'Dibatalkan')";

$insert_payment = "INSERT INTO payment (id_booking, tgl_bayar, nominal, bukti_bayar, status)
VALUES 
(1, '2026-04-02 09:00:00', 500000, '', 'Diverifikasi'),
(2, '2026-04-03 21:00:00', 250000, '', 'Diverifikasi'),
(3, '2026-04-03 17:00:00', 150000, '', 'Belum Diverifikasi'),
(4, '2026-04-04 13:10:00', 500000, '', 'Diverifikasi'),
(6, '2026-04-05 05:00:00', 350000, '', 'Diverifikasi'),
(7, '2026-04-05 08:30:00', 500000, '', 'Diverifikasi'),
(8, '2026-04-06 11:00:00', 200000, '', 'Belum Diverifikasi'),
(9, '2026-04-06 12:20:00', 300000, '', 'Diverifikasi'),
(11, '2026-04-07 14:00:00', 500000, '', 'Diverifikasi'),
(12, '2026-04-07 16:00:00', 450000, '', 'Diverifikasi'),
(13, '2026-04-08 19:30:00', 250000, '', 'Belum Diverifikasi'),
(14, '2026-04-08 07:20:00', 500000, '', 'Diverifikasi')";
*/



mysqli_query($konek, $insert_trip);
mysqli_query($konek, $insert_katalog);
mysqli_query($konek, $insert_gambar);
mysqli_query($konek, $insert_itenerary);
mysqli_query($konek, $insert_meetpoint);
mysqli_query($konek, $insert_fasilitas);
mysqli_query($konek, $insert_akun);
mysqli_query($konek, $insert_peserta_ot);
mysqli_query($konek, $insert_booking);
mysqli_query($konek, $insert_detail);
mysqli_query($konek, $insert_payment_ot);
mysqli_query($konek, $insert_batal_ot);
mysqli_query($konek, $insert_private);
mysqli_query($konek, $insert_peserta_pt);
mysqli_query($konek, $insert_payment_pt);
mysqli_query($konek, $insert_batal_pt);

echo "DB OK!";

/* For Admin Only 
   DON'T USE THIS
   IF YOU NOT AN ADMIN
   PLEASE READ THE
   NOTE BELLOW !! */
   
mysqli_close($konek);
?>