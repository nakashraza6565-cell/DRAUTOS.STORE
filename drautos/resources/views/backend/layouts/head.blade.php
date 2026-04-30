<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="danyal auto store">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Danyal Autos | Dashboard</title>
  
    <!-- Custom fonts for this template-->
    <link href="{{asset('backend/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Passion+One:wght@400;700;900&display=swap" rel="stylesheet">
  
    <!-- Custom styles for this template-->
    <link href="{{asset('backend/css/sb-admin-2.min.css')}}" rel="stylesheet">
    <style>
        @font-face {
            font-family: 'RevueCustom';
            src: url("{{ asset('revue/reve.ttf') }}") format("truetype");
        }
        :root {
            --primary: #0c1b3d; /* Packaging Navy Blue */
            --primary-light: #162a55;
            --accent: #f97316; /* Packaging Hazard Orange */
            --accent-gradient: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            --accent-glow: 0 10px 30px -10px rgba(249, 115, 22, 0.5);
            --bg-body: #f1f5f9;
            --sidebar-bg: #0c1b3d;
            --card-shadow: 0 20px 40px -10px rgba(0,0,0,0.04), 0 10px 20px -5px rgba(0,0,0,0.02);
            --radius-xl: 24px;
            --radius-lg: 16px;
            --transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        }
        
        body {
            font-family: 'Outfit', sans-serif !important;
            background-color: var(--bg-body) !important;
            color: #0f172a !important;
            letter-spacing: -0.2px;
            overflow-x: hidden;
        }

        /* Smart Animations & Global Transitions */
        * { transition: background-color 0.3s ease, border-color 0.3s ease, transform 0.3s cubic-bezier(0.165, 0.84, 0.44, 1); }

        .topbar {
            background: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0,0,0,0.05) !important;
            padding: 1rem 1.5rem !important;
            margin: 10px 15px 10px 18.5rem !important; /* Force clear sidebar */
            border-radius: var(--radius-lg);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02) !important;
            z-index: 11000 !important;
        }
        @media (max-width: 768px) {
            .topbar { margin: 10px 15px !important; }
            .topbar, .sidebar { backdrop-filter: none !important; -webkit-backdrop-filter: none !important; }
        }

        .card {
            border: 1px solid rgba(0,0,0,0.03) !important;
            border-radius: var(--radius-xl) !important;
            background: #ffffff !important;
            box-shadow: var(--card-shadow) !important;
            overflow: hidden;
        }
        .card:hover { transform: translateY(-5px); box-shadow: 0 30px 60px -12px rgba(0,0,0,0.08) !important; }

        .card-header {
            background: #fff !important;
            padding: 1.5rem !important;
            border-bottom: 1px solid rgba(0,0,0,0.03) !important;
            position: relative;
        }
        .card-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: repeating-linear-gradient(45deg, var(--accent), var(--accent) 10px, transparent 10px, transparent 20px);
            opacity: 0.15;
        }
        .card-header h6 { 
            color: var(--primary) !important; 
            font-weight: 800 !important; 
            text-transform: uppercase; 
            letter-spacing: 2px; 
            font-size: 0.7rem; 
        }

        .btn-primary, .bg-primary, .badge-primary {
            background: var(--accent-gradient) !important;
            border: none !important;
            color: #fff !important;
            font-weight: 700 !important;
            border-radius: 50px !important;
            padding: 0.7rem 1.8rem !important;
            box-shadow: var(--accent-glow) !important;
        }
        .btn-primary:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 15px 35px -5px rgba(249, 115, 22, 0.4) !important;
        }

        .sidebar {
            background: var(--sidebar-bg) !important;
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-right: 1px solid rgba(255,255,255,0.05) !important;
            width: 17rem !important;
            height: 100vh !important;
            position: fixed !important;
            top: 0;
            left: 0;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            z-index: 10000 !important;
        }
        
        /* Thin scrollbar for sidebar */
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }

        .sidebar-ghost-mode {
            box-shadow: 20px 0 60px rgba(0,0,0,0.15) !important;
        }

        .text-primary { color: var(--accent) !important; }
        
        /* Elite Form Engineering */
        .form-control, select, textarea {
            background: #ffffff !important;
            border: 1px solid rgba(0,0,0,0.08) !important;
            border-radius: 12px !important;
            padding: 0.75rem 1rem !important;
            height: auto !important;
            font-size: 0.9rem !important;
            color: #1e293b !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.01) !important;
            transition: var(--transition) !important;
        }

        .form-control:focus, select:focus, textarea:focus {
            border-color: var(--accent) !important;
            box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.1) !important;
            transform: translateY(-1px);
        }

        label {
            font-weight: 700 !important;
            font-size: 0.75rem !important;
            text-transform: uppercase !important;
            letter-spacing: 1px !important;
            color: #64748b !important;
            margin-bottom: 0.5rem !important;
        }

        /* Select2 Theme Unification */
        .select2-container--default .select2-selection--single {
            border: 1px solid rgba(0,0,0,0.08) !important;
            border-radius: 12px !important;
            height: 45px !important;
            padding: 8px !important;
        }

        /* Modern Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }

        .sidebar-dark .nav-item .nav-link i {
            color: rgba(255, 255, 255, 0.6) !important;
            font-size: 0.95rem;
            width: 1.5rem;
            text-align: center;
        }

        .sidebar-dark .nav-item .nav-link span {
            color: rgba(255, 255, 255, 0.6) !important;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .sidebar-dark .nav-item.active .nav-link i {
            color: #fff !important;
        }

        .sidebar-dark .nav-item.active .nav-link span {
            color: #fff !important;
            font-weight: 700;
        }

        .sidebar-dark .nav-item.active {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            margin: 0 0.75rem;
        }

        /* Global Ghost Sidebar System */
        #sidebar-hover-trigger { position: fixed; left: 0; top: 0; width: 12px; height: 100vh; z-index: 9998; background: transparent; }
        .sidebar-ghost-mode {
            position: fixed !important;
            left: 0; top: 0; height: 100vh; z-index: 9999;
            transform: translateX(-100%);
            transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1) !important;
            box-shadow: 15px 0 30px rgba(0,0,0,0.3);
        }
        .sidebar-ghost-mode.reveal { transform: translateX(0); }
        #wrapper #content-wrapper { margin-left: 0 !important; width: 100% !important; transition: all 0.3s ease; }
        @media (max-width: 768px) {
            .sidebar-ghost-mode { transform: translateX(-100%); }
            .sidebar-ghost-mode.reveal { transform: translateX(0); }
        }
            margin: 0.2rem 0.8rem;
        }

        .sidebar-dark .nav-item.active .nav-link::before {
            content: '';
            position: absolute;
            left: -12px;
            top: 25%;
            height: 50%;
            width: 4px;
            background: var(--accent);
            border-radius: 0 4px 4px 0;
        }

        .sidebar-divider {
            margin: 0.5rem 0 !important;
            opacity: 0.1;
        }

        /* Layout Correction */
        #wrapper {
            display: flex !important;
            width: 100% !important;
            min-height: 100vh !important;
        }

        #content-wrapper {
            flex: 1 !important;
            display: flex !important;
            flex-direction: column !important;
            min-width: 0 !important;
            background-color: var(--bg-body) !important;
        }

        #content {
            flex: 1 0 auto !important;
        }

        .container-fluid {
            width: 100% !important;
            padding-left: 1.5rem !important;
            padding-right: 1.5rem !important;
        }

        footer.sticky-footer {
            margin-top: auto !important;
            background: #fff !important;
            padding: 2rem 0 !important;
            width: 100% !important;
        }

        /* WhatsApp Floating Button */
        .whatsapp-float {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background-color: #25d366;
            color: #fff;
            border-radius: 50px;
            text-align: center;
            font-size: 30px;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.2);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none !important;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .whatsapp-float:hover {
            background-color: #128c7e;
            transform: scale(1.1);
            color: #fff;
        }

        @keyframes pulse-whatsapp {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7); }
            70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(37, 211, 102, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(37, 211, 102, 0); }
        }

        .whatsapp-float {
            animation: pulse-whatsapp 2s infinite;
        }

        /* Modern Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Mobile Sidebar Fixes */
        @media (max-width: 768px) {
            .sidebar {
                width: 17rem !important;
                transform: translateX(-100%) !important;
                transition: transform 0.3s cubic-bezier(0.165, 0.84, 0.44, 1) !important;
                overflow-y: auto !important;
                -webkit-overflow-scrolling: touch !important;
                z-index: 1050 !important;
                display: flex !important;
            }
            .sidebar.toggled, .sidebar.reveal {
                transform: translateX(0) !important;
            }
            /* Prevent accidental closure during scroll */
            .sidebar .nav-item .nav-link {
                pointer-events: auto !important;
            }
            
            /* Global Mobile Paddings */
            .container-fluid {
                padding-left: 0.75rem !important;
                padding-right: 0.75rem !important;
            }
            
            .card-body {
                padding: 1rem !important;
            }
            
            /* Mobile Typography */
            h1.h3 {
                font-size: 1.3rem !important;
            }
            .h2 {
                font-size: 1.5rem !important;
            }
            .h5 {
                font-size: 1.1rem !important;
            }

            /* Fix Tables on Mobile */
            .table-responsive {
                border: 0 !important;
                margin-bottom: 0 !important;
            }
            .table th, .table td {
                padding: 0.5rem !important;
                font-size: 0.85rem !important;
            }
            
            /* Prevent modals from being cut off */
            .modal-dialog {
                margin: 0.5rem !important;
            }
            .modal-body {
                padding: 1rem !important;
            }

            /* Topbar mobile fixes */
            .topbar .nav-item .nav-link {
                padding: 0 0.5rem !important;
            }
            
            /* Cards stack fixes */
            .premium-panel .panel-header {
                padding: 1rem !important;
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 10px;
            }
            .premium-panel .panel-header .btn {
                align-self: flex-start !important;
            }
        }
        
        /* Aggressive Global Notification & Dropdown Z-Index Fix */
        .swal2-container, .swal2-popup, .alert, .toast, .dropdown-menu, .modal, .dropdown-list {
            z-index: 999999 !important;
            overflow: visible !important;
        }
        .modal-backdrop {
            z-index: 999998 !important;
        }
        .topbar, .navbar {
            overflow: visible !important;
        }

        /* Sleek Global Plus Buttons & Input Groups - v1.0.2 */
        .input-group-append .btn { 
            padding: 0 0.5rem !important; 
            font-size: 0.7rem !important; 
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 0 8px 8px 0 !important;
            box-shadow: none !important;
            height: 100% !important;
            border: 1px solid rgba(0,0,0,0.08) !important;
            border-left: none !important;
            background: #f8fafc !important;
            color: var(--primary) !important;
        }
        .input-group-append .btn:hover {
            background: #fff !important;
            color: var(--accent) !important;
        }
        .input-group > .form-control, .input-group > select, .input-group > .select2-container { 
            border-radius: 8px 0 0 8px !important;
            height: 36px !important;
            font-size: 0.85rem !important;
        }
        .input-group-append .btn i {
            font-size: 0.65rem !important;
        }
        
        /* Global Sleek Modals - High End Polish */
        .modal-content {
            border-radius: 12px !important;
            border: none !important;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.3) !important;
            overflow: hidden;
        }
        .modal-header {
            background: #fff !important;
            padding: 1rem 1.5rem !important;
            border-bottom: 1px solid rgba(0,0,0,0.05) !important;
        }
        .modal-header .modal-title {
            color: var(--primary) !important;
            font-weight: 800 !important;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-size: 0.75rem;
        }
        .modal-header .close {
            color: var(--primary) !important;
            padding: 1rem !important;
            margin: -1rem -1rem -1rem auto !important;
        }
        .modal-body {
            padding: 1.5rem !important;
            background: #ffffff;
        }
        .modal-footer {
            background: #f8fafc;
            border-top: 1px solid rgba(0,0,0,0.05) !important;
            padding: 0.75rem 1.5rem !important;
        }
    </style>
    <style>
        /* Admin App Launcher - Modern Enterprise UI */
        #adminAppLauncher {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(12, 27, 61, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            z-index: 100000;
            display: none;
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            overflow-y: auto;
            padding: 5rem 1.5rem 2rem 1.5rem;
        }

        #adminAppLauncher.active {
            display: block;
            opacity: 1;
        }

        .launcher-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            max-width: 500px;
            margin: 0 auto;
        }

        .launcher-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none !important;
            padding: 1.25rem 0.5rem;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .launcher-item:active {
            transform: scale(0.9);
            background: rgba(255, 255, 255, 0.1);
        }

        .launcher-icon {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 0.75rem;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .launcher-label {
            color: #fff;
            font-size: 0.75rem;
            font-weight: 600;
            text-align: center;
            letter-spacing: 0.3px;
        }

        .launcher-section-title {
            color: rgba(255,255,255,0.5);
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin: 2.5rem 0 1rem 0;
            text-align: center;
        }

        .launcher-close {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            z-index: 100001;
            border: 1px solid rgba(255,255,255,0.2);
        }

        /* Mobile Header Refinement */
        @media (max-width: 768px) {
            .topbar {
                margin: 0 !important;
                border-radius: 0 !important;
                padding: 0.75rem 1rem !important;
                height: auto !important;
                position: sticky !important;
                top: 0 !important;
                background: #fff !important;
            }
            .mobile-menu-trigger {
                width: 42px;
                height: 42px;
                border-radius: 12px;
                background: var(--bg-body);
                display: flex;
                align-items: center;
                justify-content: center;
                color: var(--primary);
                font-size: 1.2rem;
                border: none;
                box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            }
            .mobile-search-trigger {
                width: 42px;
                height: 42px;
                border-radius: 12px;
                background: var(--bg-body);
                display: flex;
                align-items: center;
                justify-content: center;
                color: var(--primary);
                font-size: 1.1rem;
                margin-left: 10px;
                border: none;
            }
            .topbar-divider { display: none !important; }
            .nav-item.dropdown { position: static !important; }
            .dropdown-menu { width: 90% !important; margin: 10px 5% !important; border-radius: 20px !important; }
        }
    </style>
    <style>
        /* Universal Mobile Card Table System */
        @media (max-width: 768px) {
            .responsive-table-to-cards { border: 0 !important; }
            .responsive-table-to-cards thead { display: none !important; }
            .responsive-table-to-cards tr {
                display: block !important;
                margin-bottom: 1.5rem !important;
                background: #fff !important;
                border-radius: 20px !important;
                box-shadow: 0 10px 25px rgba(0,0,0,0.05) !important;
                padding: 1rem !important;
                border: 1px solid rgba(0,0,0,0.03) !important;
            }
            .responsive-table-to-cards td {
                display: flex !important;
                justify-content: justify !important;
                align-items: center !important;
                padding: 0.75rem 0 !important;
                border: 0 !important;
                border-bottom: 1px solid rgba(0,0,0,0.02) !important;
                font-size: 0.9rem !important;
                text-align: right !important;
            }
            .responsive-table-to-cards td:last-child { border-bottom: 0 !important; }
            .responsive-table-to-cards td::before {
                content: attr(data-title);
                float: left !important;
                font-weight: 700 !important;
                color: #64748b !important;
                text-transform: uppercase !important;
                font-size: 0.7rem !important;
                letter-spacing: 0.5px !important;
                margin-right: auto !important;
                text-align: left !important;
            }
            
            /* Global Form Mobile Optimization */
            .card-body form .row > [class*="col-"] { margin-bottom: 1rem !important; }
            .topbar { position: sticky !important; top: 0 !important; z-index: 1000 !important; }
        }
    </style>
    <!-- UI Version: 1.0.5 -->
    @stack('styles')
  
</head>
