<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Setting Notifikasi</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      /* Background Gradient Animasi */
      background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
      background-size: 400% 400%;
      animation: gradientBG 15s ease infinite;
    }

    @keyframes gradientBG {
      0% {
        background-position: 0% 50%;
      }

      50% {
        background-position: 100% 50%;
      }

      100% {
        background-position: 0% 50%;
      }
    }

    .login-card {
      position: relative;
      width: 400px;
      padding: 40px;
      /* Efek Glassmorphism */
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 20px;
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
      color: #fff;
    }

    .login-card h2 {
      text-align: center;
      margin-bottom: 30px;
      font-weight: 600;
      letter-spacing: 1px;
    }

    .alert-error {
      background: rgba(255, 75, 75, 0.8);
      color: white;
      padding: 10px;
      border-radius: 8px;
      margin-bottom: 20px;
      text-align: center;
      font-size: 0.9em;
      animation: shake 0.5s;
    }

    @keyframes shake {
      0% {
        transform: translateX(0);
      }

      25% {
        transform: translateX(-5px);
      }

      50% {
        transform: translateX(5px);
      }

      75% {
        transform: translateX(-5px);
      }

      100% {
        transform: translateX(0);
      }
    }

    .input-group {
      position: relative;
      margin-bottom: 25px;
    }

    .input-group input {
      width: 100%;
      padding: 15px 50px 15px 20px;
      background: rgba(255, 255, 255, 0.9);
      border: none;
      outline: none;
      border-radius: 30px;
      font-size: 16px;
      color: #333;
      transition: all 0.3s ease;
    }

    .input-group input:focus {
      background: #fff;
      box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
      transform: scale(1.02);
    }

    .input-group i {
      position: absolute;
      right: 20px;
      top: 50%;
      transform: translateY(-50%);
      color: #777;
      font-size: 1.2em;
    }

    .input-group i.fa-eye,
    .input-group i.fa-eye-slash {
      cursor: pointer;
      transition: color 0.3s;
    }

    .input-group i.fa-eye:hover {
      color: #e73c7e;
    }

    button {
      width: 100%;
      padding: 15px;
      background: #fff;
      color: #333;
      border: none;
      border-radius: 30px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    button:hover {
      background: #e73c7e;
      color: #fff;
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    /* Responsif untuk Mobile */
    @media (max-width: 450px) {
      .login-card {
        width: 90%;
        padding: 30px 20px;
      }
    }
  </style>
</head>

<body>

  <div class="login-card">
    <h2>Selamat Datang</h2>

    <?php if (isset($_GET['error'])): ?>
      <div class="alert-error">
        <i class="fas fa-exclamation-circle"></i> Login gagal! Periksa username/password.
      </div>
    <?php endif; ?>

    <form method="POST" action="auth.php">

      <div class="input-group">
        <input type="text" name="username" placeholder="Username" required autocomplete="off">
        <i class="fas fa-user"></i>
      </div>

      <div class="input-group">
        <input type="password" name="password" id="passwordInput" placeholder="Password" required>
        <i class="fas fa-eye" id="togglePassword"></i>
      </div>

      <button type="submit">LOGIN</button>
    </form>
  </div>

  <script>
    // Script untuk fitur Show/Hide Password
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#passwordInput');

    togglePassword.addEventListener('click', function(e) {
      // Toggle tipe input antara password dan text
      const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
      password.setAttribute('type', type);

      // Ubah ikon mata (slash atau biasa)
      this.classList.toggle('fa-eye-slash');
      this.classList.toggle('fa-eye');
    });
  </script>

</body>

</html>