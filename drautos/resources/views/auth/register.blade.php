<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Danyal Autos Co. || Register</title>
    
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
            padding: 40px 20px;
        }

        .register-card {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 500px;
            padding: 40px 30px;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .register-header h1 {
            font-weight: 800;
            color: #0f172a;
            font-size: 1.75rem;
            margin-bottom: 8px;
        }

        .register-header p {
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
            height: 50px !important;
            border: 1.5px solid #e2e8f0 !important;
            padding: 0 20px !important;
            font-weight: 500 !important;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1) !important;
        }

        .btn-register {
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

        .btn-register:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(59, 130, 246, 0.4);
            color: white;
        }

        .invalid-feedback {
            font-weight: 600;
            margin-left: 5px;
            display: block;
        }

        .footer-text {
            text-align: center;
            margin-top: 30px;
            color: #94a3b8;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .pending-alert {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            color: #92400e;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 25px;
            font-size: 0.85rem;
            font-weight: 600;
        }
    </style>
</head>

<body>

    <div class="register-card shadow-lg">
        <div class="register-header">
            <div class="mb-3">
                <i class="fas fa-user-plus fa-3x text-primary"></i>
            </div>
            <h1>Create Account</h1>
            <p>Join the Danyal Autos Co. family</p>
        </div>

        @if(session('pending_approval'))
            <div class="pending-alert animated fadeIn">
                <i class="fas fa-clock mr-2"></i>
                <strong>Request Submitted!</strong><br>
                Your account is pending admin approval. You will receive a WhatsApp message once approved.
            </div>
        @endif

        <form method="POST" action="{{ route('register.submit') }}">
            @csrf
            <div class="form-group mb-3">
                <label>Full Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                       name="name" value="{{ old('name') }}" placeholder="Enter your full name" required autofocus>
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label>Mobile Number (WhatsApp)</label>
                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                       name="phone" value="{{ old('phone') }}" placeholder="e.g. 03001234567" required>
                @error('phone')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label>Email Address (Optional)</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                       name="email" value="{{ old('email') }}" placeholder="name@example.com">
                @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label>Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               name="password" required>
                        @error('password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label>Confirm</label>
                        <input type="password" class="form-control" name="password_confirmation" required>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-register">
                Register <i class="fas fa-check-circle ml-2"></i>
            </button>
        </form>

        <div class="footer-text">
            Already have an account? <a href="{{ route('login') }}" class="font-weight-bold text-primary">Sign In</a>
        </div>
    </div>

</body>
</html>
