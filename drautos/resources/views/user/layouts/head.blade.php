<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Danyal Autos Co. || DASHBOARD</title>
  
    <!-- Custom fonts for this template-->
    <link href="{{asset('backend/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
    <!-- Custom styles for this template-->
    <link href="{{asset('backend/css/sb-admin-2.min.css')}}" rel="stylesheet">
    
    <style>
        :root {
            --primary: #0f172a;
            --accent: #3b82f6;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --bg-light: #f8fafc;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            --border-radius: 16px;
        }

        body {
            font-family: 'Outfit', sans-serif !important;
            background-color: var(--bg-light) !important;
            color: #334155;
        }

        /* Mobile First Layout Adjustments */
        #content-wrapper {
            background-color: transparent !important;
        }

        .container-fluid {
            padding: 1.25rem !important;
        }

        /* Premium Cards */
        .card {
            border: none !important;
            border-radius: var(--border-radius) !important;
            box-shadow: var(--card-shadow) !important;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .card-header {
            background: #fff !important;
            border-bottom: 1px solid rgba(0,0,0,0.05) !important;
            padding: 1.25rem !important;
        }

        /* Mobile Bottom Navigation */
        .mobile-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fff;
            height: 70px;
            z-index: 9999;
            box-shadow: 0 -4px 20px rgba(0,0,0,0.08);
            border-top: 1px solid rgba(0,0,0,0.05);
            border-radius: 24px 24px 0 0;
            padding: 0 1rem;
        }

        @media (max-width: 768px) {
            .mobile-nav {
                display: flex;
                justify-content: space-around;
                align-items: center;
            }
            #wrapper #content-wrapper {
                margin-bottom: 70px; /* Space for bottom nav */
            }
            .sidebar {
                display: none !important;
            }
            .topbar {
                display: none !important;
            }
            .container-fluid {
                padding-top: 1rem !important;
            }
        }

        .nav-item-mobile {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #94a3b8;
            text-decoration: none !important;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .nav-item-mobile.active {
            color: var(--accent);
        }

        .nav-item-mobile i {
            font-size: 1.4rem;
            margin-bottom: 4px;
        }

        /* Stats & Widgets */
        .stat-card {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .stat-label {
            font-size: 0.8rem;
            color: #64748b;
            font-weight: 500;
        }

        .stat-value {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--primary);
        }

        /* Button Refinement */
        .btn-premium {
            background: var(--primary);
            color: white;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
            color: white;
        }

        /* Order List Mobile Optimization */
        .mobile-order-card {
            background: #fff;
            padding: 1rem;
            border-radius: 16px;
            margin-bottom: 1rem;
            border: 1px solid rgba(0,0,0,0.05);
        }
    </style>
    @stack('styles')
  
</head>
