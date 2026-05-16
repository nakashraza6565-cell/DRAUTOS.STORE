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
            background: #0f172a;
            background-image: 
                radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #f8fafc;
        }

        .login-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
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
            color: #ffffff;
            font-size: 2rem;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .login-header p {
            color: #94a3b8;
            font-weight: 500;
        }

        .icon-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.2) 0%, rgba(139, 92, 246, 0.2) 100%);
            margin-bottom: 20px;
            border: 1px solid rgba(139, 92, 246, 0.3);
            box-shadow: 0 0 20px rgba(139, 92, 246, 0.2);
        }

        .dr-logo {
            font-family: 'Revue', 'Outfit', sans-serif;
            color: #a78bfa;
            font-size: 2.5rem;
            font-weight: normal;
            line-height: 1;
            letter-spacing: 1px;
        }

        .form-group label {
            font-weight: 600;
            font-size: 0.9rem;
            color: #cbd5e1;
            margin-bottom: 8px;
            display: block;
        }

        .form-control {
            background: rgba(15, 23, 42, 0.6) !important;
            border-radius: 12px !important;
            height: 55px !important;
            border: 1px solid rgba(255,255,255,0.1) !important;
            padding: 0 20px !important;
            font-weight: 500 !important;
            color: #ffffff !important;
            transition: all 0.3s ease;
        }

        .form-control::placeholder {
            color: #475569;
        }

        .form-control:focus {
            border-color: #8b5cf6 !important;
            box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.15) !important;
            background: rgba(15, 23, 42, 0.8) !important;
        }

        .btn-login {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            border-radius: 12px;
            height: 55px;
            font-weight: 700;
            font-size: 1.05rem;
            border: none;
            width: 100%;
            margin-top: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px -5px rgba(139, 92, 246, 0.4);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px -5px rgba(139, 92, 246, 0.6);
            color: white;
        }

        .invalid-feedback {
            font-weight: 600;
            margin-left: 5px;
            color: #ef4444;
        }

        .remember-me {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
        }

        .remember-me label {
            margin: 0;
            font-size: 0.9rem;
            color: #cbd5e1;
            font-weight: 500;
        }

        .custom-control-input:checked ~ .custom-control-label::before {
            background-color: #8b5cf6;
            border-color: #8b5cf6;
        }

        .forgot-link {
            font-size: 0.9rem;
            color: #a78bfa;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s;
        }

        .forgot-link:hover {
            color: #c4b5fd;
            text-decoration: none;
        }

        .footer-text {
            text-align: center;
            margin-top: 30px;
            color: #64748b;
            font-size: 0.85rem;
            font-weight: 500;
        }
    </style>
</head>

<body>

    <div class="login-card">
        <div class="login-header">
            <div class="icon-wrapper">
                <span class="dr-logo">DR</span>
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
            &copy; {{ date('Y') }} Danyal Autos Co. All rights reserved.
        </div>
    </div>

</body>
</html>

