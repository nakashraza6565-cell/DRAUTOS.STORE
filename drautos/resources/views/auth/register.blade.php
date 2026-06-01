<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Danyal Autos Co. | Create Account</title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="{{asset('backend/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">

    <style>
        :root {
            --primary:      #083259;
            --primary-light:#0e4a7a;
            --accent:       #facc15;
            --silver:       #a3b1c6;
            --silver-light: #d1dae6;
            --bg-dark:      #06213b;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', 'Montserrat', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: stretch;
            background: var(--bg-dark);
            overflow: hidden;
        }

        /* ===== LEFT PANEL — Brand Side ===== */
        .brand-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 50px;
            position: relative;
            overflow: hidden;
            background: linear-gradient(145deg, #062038 0%, #083259 40%, #0a3f6e 100%);
        }

        /* Diagonal texture grid */
        .brand-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(163,177,198,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(163,177,198,0.04) 1px, transparent 1px);
            background-size: 40px 40px;
            z-index: 0;
        }

        /* Glowing orb top-right */
        .brand-panel::after {
            content: '';
            position: absolute;
            top: -120px; right: -120px;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(250,204,21,0.12) 0%, transparent 70%);
            z-index: 0;
        }

        .brand-panel-inner {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 420px;
        }

        /* DR SVG Logo wrapper */
        .logo-svg-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 18px;
            margin-bottom: 32px;
        }

        .logo-text-block {
            text-align: left;
        }

        .logo-text-block .brand-name {
            font-family: 'Montserrat', sans-serif;
            font-size: 28px;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: 0px;
            line-height: 1;
        }

        .logo-text-block .brand-tagline {
            font-family: 'Montserrat', sans-serif;
            font-size: 10px;
            font-weight: 700;
            color: var(--silver);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-top: 5px;
        }

        .brand-divider {
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, var(--accent), transparent);
            margin: 28px auto;
            border-radius: 2px;
        }

        .brand-headline {
            font-family: 'Montserrat', sans-serif;
            font-size: clamp(1.6rem, 2.5vw, 2.2rem);
            font-weight: 900;
            color: #ffffff;
            line-height: 1.2;
            text-transform: uppercase;
            letter-spacing: -0.5px;
            margin-bottom: 16px;
        }

        .brand-headline span {
            color: var(--accent);
        }

        .brand-sub {
            font-size: 0.95rem;
            color: var(--silver-light);
            line-height: 1.7;
            font-weight: 400;
        }

        .brand-badges {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 36px;
            flex-wrap: wrap;
        }

        .brand-badge {
            display: flex;
            align-items: center;
            gap: 7px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(163,177,198,0.2);
            border-radius: 6px;
            padding: 8px 14px;
            font-size: 12px;
            font-weight: 600;
            color: var(--silver-light);
        }

        .brand-badge i { color: var(--accent); }

        /* ===== RIGHT PANEL — Form Side ===== */
        .form-panel {
            width: 500px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 50px 40px;
            background: #f0f4f8;
            position: relative;
            overflow-y: auto;
        }

        /* Top accent bar */
        .form-panel::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--primary), var(--accent), var(--primary));
        }

        .form-inner {
            width: 100%;
            max-width: 400px;
        }

        .form-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.65rem;
            font-weight: 900;
            color: var(--primary);
            margin-bottom: 6px;
        }

        .form-subtitle {
            font-size: 0.9rem;
            color: #64748b;
            font-weight: 500;
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .form-input {
            width: 100%;
            height: 48px;
            border-radius: 8px;
            border: 1.5px solid #cbd5e1;
            padding: 0 16px 0 40px;
            font-size: 13px;
            font-family: 'Inter', sans-serif;
            color: var(--primary);
            background: #ffffff;
            transition: all 0.25s ease;
            outline: none;
        }

        .form-input::placeholder { color: #94a3b8; }

        .form-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(8, 50, 89, 0.1);
        }

        .input-wrap {
            position: relative;
            margin-bottom: 16px;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--silver);
            font-size: 14px;
            pointer-events: none;
        }

        .input-wrap.is-invalid .form-input {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .invalid-msg {
            font-size: 11px;
            font-weight: 600;
            color: #dc2626;
            margin-top: 4px;
            padding-left: 4px;
        }

        .btn-signin {
            width: 100%;
            height: 52px;
            border-radius: 8px;
            border: none;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: #ffffff;
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 8px 20px rgba(8, 50, 89, 0.35);
            position: relative;
            overflow: hidden;
            margin-top: 24px;
        }

        .btn-signin::after {
            content: '';
            position: absolute;
            top: 0; left: -100%; width: 60%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: left 0.5s ease;
        }

        .btn-signin:hover::after { left: 150%; }
        .btn-signin:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px rgba(8, 50, 89, 0.45);
        }
        .btn-signin:active { transform: translateY(0); }

        .divider-row {
            display: flex;
            align-items: center;
            gap: 14px;
            margin: 20px 0;
            color: #94a3b8;
            font-size: 12px;
            font-weight: 600;
        }
        .divider-row::before, .divider-row::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }

        .back-to-shop {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: #64748b;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            padding: 12px;
            border-radius: 8px;
            border: 1.5px solid #e2e8f0;
            transition: all 0.2s ease;
            background: #fff;
        }
        .back-to-shop:hover {
            border-color: var(--primary);
            color: var(--primary);
            text-decoration: none;
            background: #f8fafc;
        }

        .alert-danger, .pending-alert {
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .alert-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-left: 4px solid #ef4444;
            color: #dc2626;
        }
        .pending-alert {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            border-left: 4px solid #f59e0b;
            color: #92400e;
        }

        .copyright {
            position: absolute;
            bottom: 18px;
            left: 0; right: 0;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            font-weight: 500;
        }

        /* Grid for 2 columns */
        .form-row {
            display: flex;
            gap: 12px;
        }
        .form-col {
            flex: 1;
        }

        @media (max-width: 768px) {
            body { flex-direction: column; overflow: auto; }
            .brand-panel { flex: none; padding: 40px 24px 36px; min-height: auto; }
            .brand-panel::after { display: none; }
            .brand-headline { font-size: 1.4rem; }
            .brand-badges { display: none; }
            .form-panel { width: 100%; padding: 36px 24px 50px; }
            .copyright { position: relative; bottom: auto; margin-top: 20px; }
            .form-row { flex-direction: column; gap: 0; }
        }
    </style>
</head>

<body>

    <!-- ===== LEFT — BRAND PANEL ===== -->
    <div class="brand-panel">
        <div class="brand-panel-inner">

            <!-- DR Logo -->
            <div class="logo-svg-wrap">
                <svg width="90" height="78" viewBox="0 0 75 65" xmlns="http://www.w3.org/2000/svg" style="flex-shrink:0; filter: drop-shadow(0 4px 12px rgba(250,204,21,0.2));">
                    <path d="M 22.74 58.73 L 13.17 58.73 L 13.17 25.29 L 24.94 25.29 L 24.94 48.61 C 28.98 48.47 32.29 46.74 34.87 43.44 C 37.46 40.14 38.75 36.49 38.75 32.5 C 38.75 29.89 38.25 28.01 37.26 26.83 C 36.27 25.66 35.19 24.87 34.02 24.47 C 33.25 24.21 32.51 24.06 31.79 24.03 C 31.08 23.99 30.5 23.97 30.06 23.97 L 10.97 23.92 L 13.72 13.8 L 32.81 13.8 C 39.52 13.83 44.19 15.65 46.83 19.24 C 49.47 22.84 50.79 26.91 50.79 31.45 C 50.79 39.92 47.99 46.59 42.38 51.45 C 36.77 56.3 30.22 58.73 22.74 58.73 " fill="#ffffff"/>
                    <path d="M 36.48 20.54 L 56.18 20.54 C 60.18 20.58 63.08 21.43 64.9 23.11 C 66.72 24.78 67.86 26.64 68.33 28.68 C 68.39 29.08 68.45 29.5 68.5 29.94 C 68.55 30.37 68.58 30.79 68.58 31.19 C 68.58 34.16 67.71 36.67 65.98 38.73 C 64.24 40.79 62.19 42.57 59.83 44.07 C 61.39 47.2 63.18 50.21 65.2 53.11 C 67.22 56 69.38 58.76 71.68 61.39 L 62.73 67.59 C 59.43 63.86 56.49 59.84 53.93 55.53 C 51.36 51.22 49.06 46.79 47.03 42.23 C 47.53 41.83 48.14 41.38 48.88 40.88 C 49.61 40.38 50.36 39.85 51.13 39.28 C 52.46 38.28 53.68 37.19 54.8 36.01 C 55.92 34.83 56.48 33.62 56.48 32.39 C 56.48 31.43 56.13 30.78 55.45 30.44 C 54.77 30.11 54.03 29.93 53.23 29.89 C 52.96 29.86 52.7 29.84 52.45 29.84 C 52.2 29.84 51.96 29.84 51.73 29.84 C 51.66 29.84 51.59 29.84 51.53 29.84 C 51.46 29.84 51.39 29.84 51.33 29.84 L 33.98 29.79 L 36.48 20.54 Z M 46.68 31.04 L 46.68 61.39 L 35.98 61.39 L 35.98 31.04 L 46.68 31.04 Z" fill="#facc15" stroke="#083259" stroke-width="1.5" stroke-linejoin="round"/>
                </svg>
                <div class="logo-text-block">
                    <div class="brand-name">DANYAL AUTOS</div>
                    <div class="brand-tagline">Premium Truck Parts B2B</div>
                </div>
            </div>

            <div class="brand-divider"></div>

            <h2 class="brand-headline">
                CORPORATE<br>
                REGISTRATION <span>|</span> B2B
            </h2>
            <p class="brand-sub">
                Join our enterprise supply chain.<br>
                Unlock wholesale pricing, dedicated procurement agents, and synchronized logistics.
            </p>

            <div class="brand-badges">
                <div class="brand-badge"><i class="fas fa-handshake"></i> Verified Network</div>
                <div class="brand-badge"><i class="fas fa-boxes"></i> Bulk Orders</div>
                <div class="brand-badge"><i class="fas fa-percentage"></i> OEM Discounts</div>
            </div>
        </div>
    </div>

    <!-- ===== RIGHT — FORM PANEL ===== -->
    <div class="form-panel">
        <div class="form-inner">

            <h1 class="form-title">Create Account</h1>
            <p class="form-subtitle">Register your corporate account</p>

            @if(session('pending_approval'))
                <div class="pending-alert">
                    <i class="fas fa-clock mr-2" style="font-size: 16px;"></i>
                    <strong>Request Submitted!</strong><br>
                    Your account is pending admin approval. You will receive a WhatsApp message once active.
                </div>
            @endif
            @if(session('error'))
                <div class="alert-danger"><i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('register.submit') }}">
                @csrf

                {{-- Full Name --}}
                <label class="form-label">Full Name</label>
                <div class="input-wrap {{ $errors->has('name') ? 'is-invalid' : '' }}">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" class="form-input" name="name" value="{{ old('name') }}" placeholder="Enter your full name" required autofocus>
                </div>
                @error('name') <div class="invalid-msg">{{ $message }}</div> @enderror

                {{-- Business Name --}}
                <label class="form-label">Business Name</label>
                <div class="input-wrap {{ $errors->has('business_name') ? 'is-invalid' : '' }}">
                    <i class="fas fa-building input-icon"></i>
                    <input type="text" class="form-input" name="business_name" value="{{ old('business_name') }}" placeholder="Enter your business/company name">
                </div>
                @error('business_name') <div class="invalid-msg">{{ $message }}</div> @enderror

                {{-- Mobile Number --}}
                <label class="form-label">Mobile Number (WhatsApp)</label>
                <div class="input-wrap {{ $errors->has('phone') ? 'is-invalid' : '' }}">
                    <i class="fab fa-whatsapp input-icon"></i>
                    <input type="text" class="form-input" name="phone" value="{{ old('phone') }}" placeholder="e.g. 03001234567" required>
                </div>
                @error('phone') <div class="invalid-msg">{{ $message }}</div> @enderror

                {{-- Email Address --}}
                <label class="form-label">Email Address (Optional)</label>
                <div class="input-wrap {{ $errors->has('email') ? 'is-invalid' : '' }}">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" class="form-input" name="email" value="{{ old('email') }}" placeholder="name@example.com">
                </div>
                @error('email') <div class="invalid-msg">{{ $message }}</div> @enderror

                {{-- Password Row --}}
                <div class="form-row">
                    <div class="form-col">
                        <label class="form-label">Password</label>
                        <div class="input-wrap {{ $errors->has('password') ? 'is-invalid' : '' }}">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" class="form-input" name="password" required>
                        </div>
                        @error('password') <div class="invalid-msg">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-col">
                        <label class="form-label">Confirm</label>
                        <div class="input-wrap">
                            <i class="fas fa-check input-icon"></i>
                            <input type="password" class="form-input" name="password_confirmation" required>
                        </div>
                    </div>
                </div>

                {{-- Register Button --}}
                <button type="submit" class="btn-signin">
                    Register <i class="fas fa-arrow-right"></i>
                </button>
            </form>

            <div class="divider-row">OR</div>

            <a href="{{ route('login') }}" class="back-to-shop">
                <i class="fas fa-sign-in-alt"></i> Already have an account? Sign In
            </a>
        </div>

        <div class="copyright">&copy; {{ date('Y') }} Danyal Autos Co.</div>
    </div>

</body>
</html>
