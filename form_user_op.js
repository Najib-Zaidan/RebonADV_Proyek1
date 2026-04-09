document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("formPendaftaran");

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    let nama = document.getElementById("nama").value.trim();
    let dd = document.getElementById("dd").value.trim();
    let mm = document.getElementById("mm").value.trim();
    let yyyy = document.getElementById("yyyy").value.trim();
    let alamat = document.getElementById("alamat").value.trim();
    let telepon = document.getElementById("telepon").value.trim();
    let penyakit = document.getElementById("penyakit").value;

    // VALIDASI
    if (!nama || !dd || !mm || !yyyy || !alamat || !telepon || !penyakit) {
      alert("Semua field wajib diisi!");
      return;
    }

    if (isNaN(dd) || isNaN(mm) || isNaN(yyyy)) {
      alert("Tanggal harus angka!");
      return;
    }

    if (dd < 1 || dd > 31 || mm < 1 || mm > 12 || yyyy.length !== 4) {
      alert("Tanggal tidak valid!");
      return;
    }

    if (!/^[0-9]+$/.test(telepon)) {
      alert("Nomor telepon harus angka!");
      return;
    }

    if (telepon.length < 10) {
      alert("Nomor telepon terlalu pendek!");
      return;
    }

    // SUKSES
    alert("Pendaftaran berhasil!");
    form.submit();
  });
});
