<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Danyal Autos Co. || Login</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{asset('backend/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('backend/css/sb-admin-2.min.css')}}" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 450px;
            padding: 40px 30px;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-header h1 {
            font-weight: 800;
            color: #0f172a;
            font-size: 1.75rem;
            margin-bottom: 8px;
        }

        .login-header p {
            color: #64748b;
            font-weight: 500;
        }

        .form-group label {
            font-weight: 700;
            font-size: 0.85rem;
            color: #475569;
            margin-bottom: 8px;
            display: block;
        }

        .form-control {
            border-radius: 12px !important;
            height: 55px !important;
            border: 1.5px solid #e2e8f0 !important;
            padding: 0 20px !important;
            font-weight: 500 !important;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1) !important;
        }

        .btn-login {
            background: #3b82f6;
            color: white;
            border-radius: 12px;
            height: 55px;
            font-weight: 700;
            font-size: 1rem;
            border: none;
            width: 100%;
            margin-top: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3);
        }

        .btn-login:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(59, 130, 246, 0.4);
            color: white;
        }

        .invalid-feedback {
            font-weight: 600;
            margin-left: 5px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
        }

        .remember-me label {
            margin: 0;
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 600;
        }

        .forgot-link {
            font-size: 0.85rem;
            color: #3b82f6;
            font-weight: 700;
            text-decoration: none;
        }

        .footer-text {
            text-align: center;
            margin-top: 30px;
            color: #94a3b8;
            font-size: 0.85rem;
            font-weight: 500;
        }
    </style>
</head>

<body>

    <div class="login-card shadow-lg">
        <div class="login-header">
            <div class="mb-4">
                <i class="fas fa-tools fa-3x text-primary"></i>
            </div>
            <h1>Danyal Autos</h1>
            <p>Customer Access Portal</p>
        </div>
        
        @include('backend.layouts.notification')

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group mb-4">
                <label>Email or Mobile Number</label>
                <input type="text" class="form-control @error('email') is-invalid @enderror" 
                       name="email" value="{{ old('email') }}" placeholder="Email or 03XXXXXXXXX" required autofocus>
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label>Password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                       name="password" placeholder="Enter your password" required>
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="remember-me">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="custom-control-label" for="remember">Remember Me</label>
                </div>
                @if (Route::has('password.request'))
                    <a class="forgot-link" href="{{ route('password.request') }}">Forgot?</a>
                @endif
            </div>

            <button type="submit" class="btn btn-login">
                Sign In <i class="fas fa-arrow-right ml-2"></i>
            </button>
        </form>

        <div class="footer-text">
            &copy; {{ date('Y') }} Danyal Autos Co.. All rights reserved.
        </div>
    </div>

</body>
</html>

