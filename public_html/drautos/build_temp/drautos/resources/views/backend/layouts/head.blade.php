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
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
    <!-- Custom styles for this template-->
    <link href="{{asset('backend/css/sb-admin-2.min.css')}}" rel="stylesheet">
    <style>
        :root {
            --primary: #1e293b;
            --primary-dark: #0f172a;
            --accent: #f59e0b;
            --accent-hover: #d97706;
            --success: #22c55e;
            --info: #3b82f6;
            --danger: #ef4444;
            --warning: #f59e0b;
            --bg-body: #f8fafc;
            --card-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --card-shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }
        
        body {
            font-family: 'Outfit', sans-serif !important;
            background-color: var(--bg-body) !important;
        }

        .bg-gradient-primary {
            background-color: var(--primary) !important;
            background-image: linear-gradient(180deg, var(--primary) 10%, var(--primary-dark) 100%) !important;
        }

        .btn-primary {
            background-color: var(--accent) !important;
            border-color: var(--accent) !important;
            color: #1e293b !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            padding: 0.5rem 1.25rem !important;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background-color: var(--accent-hover) !important;
            border-color: var(--accent-hover) !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        .card {
            border: none !important;
            border-radius: 16px !important;
            box-shadow: var(--card-shadow) !important;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            box-shadow: var(--card-shadow-lg) !important;
        }

        .sidebar {
            width: 16rem !important;
            transition: all 0.3s ease;
        }

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
                width: 0 !important;
                overflow-y: auto !important;
                -webkit-overflow-scrolling: touch !important;
                z-index: 1050 !important;
            }
            .sidebar.toggled {
                width: 16rem !important;
                display: flex !important;
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
    </style>
    @stack('styles')
  
</head>
