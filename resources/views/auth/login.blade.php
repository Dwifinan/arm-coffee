<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Arm Coffee</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
            background: #fff;
        }
        .login-container {
            height: 100vh;
            width: 100%;
            display: flex;
        }
        /* Left Side - Image */
        .login-image {
            width: 60%;
            background: url("{{ asset('assets/img/login_bg.png') }}") no-repeat center center/cover;
            position: relative;
            display: flex;
            align-items: flex-end; /* Text at bottom */
            justify-content: flex-start;
            padding: 3rem;
        }
        .login-image::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.7)); /* Gradient Overlay */
        }
        .image-caption {
            position: relative;
            z-index: 2;
            color: #fff;
            max-width: 400px;
        }
        .image-caption h1 {
            font-weight: 700;
            font-size: 3rem;
            margin-bottom: 0.5rem;
            color: #f6c23e; /* Gold accent */
        }
        .image-caption p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* Right Side - Form */
        .login-form {
            width: 40%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
        }
        .form-content {
            width: 100%;
            max-width: 380px;
        }
        .brand-logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: #4e73df;
            margin-bottom: 2rem;
            display: block;
            text-decoration: none;
        }
        .btn-primary {
            background-color: #4e73df;
            border-color: #4e73df;
            padding: 0.75rem;
            font-weight: 600;
            margin-top: 1rem;
        }
        .btn-primary:hover {
            background-color: #224abe;
            border-color: #224abe;
        }
        .form-control {
            padding: 0.75rem;
            border-radius: 0.5rem;
            border: 1px solid #d1d3e2;
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
            border-color: #bac8f3;
        }
        .form-label {
            font-weight: 600;
            color: #5a5c69;
            margin-bottom: 0.5rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }
            .login-image {
                width: 100%;
                height: 35vh; /* Initial height for image */
                padding: 1.5rem;
                justify-content: center;
                align-items: center;
                text-align: center;
            }
            .image-caption h1 {
                font-size: 2rem;
            }
            .login-form {
                width: 100%;
                height: 65vh;
                align-items: flex-start; /* Move form up */
                padding-top: 2rem;
                background: #f8f9fc;
            }
            .form-content {
                background: #fff;
                padding: 2rem;
                border-radius: 1rem;
                box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            }
        }
    </style>
</head>
<body>

    <div class="login-container">
        
        <!-- Left Side -->
        <div class="login-image">
            <div class="image-caption">
                <h1>Arm Coffee</h1>
                <p>Kelola bahan baku, penjualan, dan stok dengan mudah dan efisien.</p>
            </div>
        </div>

        <!-- Right Side -->
        <div class="login-form">
            <div class="form-content">
                <div class="mb-4">
                    <h3 class="fw-bold text-dark">Selamat Datang!</h3>
                    <p class="text-muted">Silakan login untuk mengakses dashboard.</p>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger mb-4 border-0 shadow-sm">
                        <i class="bi bi-exclamation-circle me-2"></i> {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Masukkan username" autocomplete="off" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="position-relative">
                            <input type="password" name="password" id="passwordInput" class="form-control pe-5" placeholder="************" autocomplete="off" required>
                            <span class="position-absolute top-50 end-0 translate-middle-y me-3" id="togglePassword" style="cursor: pointer;">
                                <i class="bi bi-eye text-muted"></i>
                            </span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember">
                            <label class="form-check-label text-muted small" for="remember">Ingat Saya</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 shadow">MASUK</button>
                    
                    <div class="text-center mt-4">
                        <small class="text-muted text-center">&copy; {{ date('Y') }} Arm Coffee System</small>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#passwordInput');

        togglePassword.addEventListener('click', function (e) {
            // toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            // toggle the eye slash icon
            const icon = this.querySelector('i');
            icon.classList.toggle('bi-eye');
            icon.classList.toggle('bi-eye-slash');
        });
    </script>
</body>
</html>
