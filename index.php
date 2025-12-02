<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KASKU</title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/index.css">
</head>

<body>
    <div class="vh-100 d-flex align-items-center justify-content-center">
        <div class="card login-card shadow-sm border-0">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-3">
                    <img src="img/kas.jpg" alt="Logo KASKU" class="rounded mb-2 logo-img">
                    <h4 class="fw-bold mb-0">Selamat Datang di KASKU</h4>
                    <p class="text-muted small mb-3">Silakan masuk untuk melanjutkan</p>
                </div>

                <div id="alert-container"></div>

                <form action="config/config_login.php" method="POST">
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-person"></i></span>
                            <input type="text" name="username" class="form-control" placeholder="Username" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-login">MASUK</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>