document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("formLogin");

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    let username = document.getElementById("username").value.trim();
    let password = document.getElementById("password").value.trim();

    // VALIDASI
    if (username === "" || password === "") {
      alert("Username dan Password wajib diisi!");
      return;
    }

    // LOGIN DUMMY
    if (username === "admin" && password === "12345") {
      alert("Login berhasil!");
      window.location.href = "form_user_op.html";
    } else {
      alert("Username atau Password salah!");
    }
  });
});
