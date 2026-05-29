<!-- Meta Tag -->
@yield('meta')
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<!-- Title Tag  -->
<title>@yield('title')</title>
<!-- Favicon -->
<link rel="icon" type="image/png" href="images/favicon.png">
<!-- Web Font -->
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

<!-- StyleSheet -->
<link rel="manifest" href="/manifest.json">
<!-- Bootstrap -->
<link rel="stylesheet" href="{{asset('frontend/css/bootstrap.css')}}">
<!-- Magnific Popup -->
<link rel="stylesheet" href="{{asset('frontend/css/magnific-popup.min.css')}}">
<!-- Font Awesome -->
<link rel="stylesheet" href="{{asset('frontend/css/font-awesome.css')}}">
<!-- Fancybox -->
<link rel="stylesheet" href="{{asset('frontend/css/jquery.fancybox.min.css')}}">
<!-- Themify Icons -->
<link rel="stylesheet" href="{{asset('frontend/css/themify-icons.css')}}">
<!-- Nice Select CSS -->
<link rel="stylesheet" href="{{asset('frontend/css/niceselect.css')}}">
<!-- Animate CSS -->
<link rel="stylesheet" href="{{asset('frontend/css/animate.css')}}">
<!-- Flex Slider CSS -->
<link rel="stylesheet" href="{{asset('frontend/css/flex-slider.min.css')}}">
<!-- Owl Carousel -->
<link rel="stylesheet" href="{{asset('frontend/css/owl-carousel.css')}}">
<!-- Slicknav -->
<link rel="stylesheet" href="{{asset('frontend/css/slicknav.min.css')}}">
<!-- Jquery Ui -->
<link rel="stylesheet" href="{{asset('frontend/css/jquery-ui.css')}}">

<!-- Danyal Autos Co. StyleSheet -->
<link rel="stylesheet" href="{{asset('frontend/css/reset.css')}}">
<link rel="stylesheet" href="{{asset('frontend/css/style.css')}}">
<link rel="stylesheet" href="{{asset('frontend/css/responsive.css')}}">

<style>
    /* Design Tokens */
    :root {
        --primary: #1e293b;
        --accent: #f59e0b;
        --text-main: #334155;
        --bg-soft: #f8fafc;
    }

    body {
        font-family: 'Outfit', sans-serif !important;
        color: var(--text-main);
        background-color: #fff;
        scroll-behavior: smooth;
    }

    h1, h2, h3, h4, h5, h6 {
        font-family: 'Outfit', sans-serif !important;
        font-weight: 700;
        color: var(--primary);
    }

    .btn {
        font-family: 'Outfit', sans-serif !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Multilevel dropdown */
    .dropdown-submenu {
    position: relative;
    }

    .dropdown-submenu>a:after {
    content: "\f0da";
    float: right;
    border: none;
    font-family: 'FontAwesome';
    }

    .dropdown-submenu>.dropdown-menu {
    top: 0;
    left: 100%;
    margin-top: 0px;
    margin-left: 0px;
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
        box-shadow: 0 10px 25px rgba(37, 211, 102, 0.3);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none !important;
        transition: all 0.3s ease;
    }

    .whatsapp-float:hover {
        background-color: #128c7e;
        transform: scale(1.1) translateY(-5px);
        color: #fff;
    }

    .whatsapp-float i {
        margin-top: 1px;
    }

    @media screen and (max-width: 767px) {
        .whatsapp-float {
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            font-size: 25px;
        }
    }

    /* Pulse Animation */
    @keyframes pulse-whatsapp {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7); }
        70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(37, 211, 102, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(37, 211, 102, 0); }
    }

    .whatsapp-float {
        animation: pulse-whatsapp 2s infinite;
    }

    /* Premium Transitions */
    .single-product {
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #f1f5f9;
    }

    .single-product:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        border-color: var(--accent);
    }

    /* 3D WebGL Showroom & Hotspot CSS */
    #chassis-3d-showroom {
        position: relative;
        width: 100%;
        height: 650px;
        background: radial-gradient(circle at 50% 50%, #0b1528 0%, #020617 100%);
        overflow: hidden;
        border-bottom: 2px solid #1e293b;
    }

    #chassis-canvas {
        width: 100%;
        height: 100%;
        display: block;
        cursor: grab;
        position: absolute;
        top: 0;
        left: 0;
        z-index: 2;
    }

    #chassis-canvas:active {
        cursor: grabbing;
    }

    /* Sci-fi Blueprint Grid Overlay */
    .showroom-grid-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-size: 30px 30px;
        background-image: 
            linear-gradient(to right, rgba(99, 102, 241, 0.05) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(99, 102, 241, 0.05) 1px, transparent 1px);
        pointer-events: none;
        z-index: 1;
    }

    /* Tech Hotspots positioned responsively via coordinates */
    .tech-hotspot {
        position: absolute;
        transform: translate(-50%, -50%);
        cursor: pointer;
        z-index: 10;
        pointer-events: auto;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .tech-hotspot:hover {
        transform: translate(-50%, -50%) scale(1.2);
    }

    .hotspot-ring {
        position: relative;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(6, 182, 212, 0.15);
        border: 2px solid #06b6d4;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0 15px rgba(6, 182, 212, 0.5), inset 0 0 8px rgba(6, 182, 212, 0.3);
        transition: all 0.4s ease;
    }

    .tech-hotspot:hover .hotspot-ring {
        background: rgba(249, 115, 22, 0.25);
        border-color: #f97316;
        box-shadow: 0 0 25px rgba(249, 115, 22, 0.8), inset 0 0 12px rgba(249, 115, 22, 0.5);
    }

    .hotspot-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #fff;
        box-shadow: 0 0 10px #fff;
        transition: all 0.3s ease;
    }

    .tech-hotspot:hover .hotspot-dot {
        background: #f97316;
        box-shadow: 0 0 12px #f97316;
    }

    .hotspot-pulse {
        position: absolute;
        width: 52px;
        height: 52px;
        border-radius: 50%;
        border: 2px solid rgba(6, 182, 212, 0.4);
        animation: hotspotPulse 2.5s infinite linear;
        pointer-events: none;
    }

    .tech-hotspot:hover .hotspot-pulse {
        border-color: rgba(249, 115, 22, 0.5);
    }

    @keyframes hotspotPulse {
        0% { transform: scale(0.5); opacity: 1; }
        100% { transform: scale(1.6); opacity: 0; }
    }

    /* Hotspot Tooltip / Label */
    .hotspot-tooltip {
        position: absolute;
        bottom: 45px;
        background: rgba(10, 15, 30, 0.85);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(6, 182, 212, 0.4);
        padding: 10px 16px;
        border-radius: 10px;
        white-space: nowrap;
        opacity: 0;
        visibility: hidden;
        transform: translateY(10px);
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        color: #fff;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.6), 0 0 15px rgba(6, 182, 212, 0.1);
        pointer-events: none;
        display: flex;
        flex-direction: column;
    }

    .tech-hotspot:hover .hotspot-tooltip {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
        border-color: rgba(249, 115, 22, 0.5);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.6), 0 0 20px rgba(249, 115, 22, 0.15);
    }

    .tooltip-title {
        font-size: 13px;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #06b6d4;
        transition: color 0.3s ease;
    }

    .tech-hotspot:hover .tooltip-title {
        color: #f97316;
    }

    .tooltip-desc {
        font-size: 11px;
        color: #94a3b8;
        margin-top: 3px;
        font-family: monospace;
    }

    /* Glassmorphism Side Drawer */
    #parts-side-drawer {
        position: fixed;
        top: 0;
        right: -460px;
        width: 450px;
        height: 100vh;
        background: rgba(10, 15, 30, 0.82);
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
        border-left: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: -20px 0 60px rgba(0, 0, 0, 0.7);
        z-index: 99999;
        transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        padding: 35px;
        color: #f8fafc;
        display: flex;
        flex-direction: column;
    }

    #parts-side-drawer.open {
        right: 0;
    }

    .drawer-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding-bottom: 20px;
        margin-bottom: 25px;
    }

    .drawer-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #fff;
        display: flex;
        align-items: center;
    }

    .drawer-title i {
        color: #f97316;
        margin-right: 12px;
    }

    .drawer-close {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #fff;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .drawer-close:hover {
        background: #f97316;
        border-color: #f97316;
        transform: rotate(90deg);
        box-shadow: 0 0 15px rgba(249, 115, 22, 0.5);
    }

    .drawer-content {
        flex: 1;
        overflow-y: auto;
        padding-right: 8px;
    }

    .drawer-content::-webkit-scrollbar {
        width: 6px;
    }

    .drawer-content::-webkit-scrollbar-track {
        background: rgba(255,255,255,0.02);
        border-radius: 3px;
    }

    .drawer-content::-webkit-scrollbar-thumb {
        background: rgba(255,255,255,0.15);
        border-radius: 3px;
    }

    .drawer-content::-webkit-scrollbar-thumb:hover {
        background: #f97316;
    }

    /* WebGL Showroom Floating HUD Panels */
    .hud-panel {
        position: absolute;
        background: rgba(10, 15, 30, 0.65);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.06);
        padding: 20px;
        border-radius: 16px;
        color: #fff;
        z-index: 5;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5), 0 0 20px rgba(6, 182, 212, 0.05);
        pointer-events: auto;
        transition: all 0.3s ease;
    }

    .hud-panel:hover {
        border-color: rgba(255, 255, 255, 0.12);
        background: rgba(10, 15, 30, 0.75);
    }

    .hud-left {
        top: 30px;
        left: 30px;
        width: 240px;
        border-left: 4px solid #06b6d4;
    }

    .hud-right {
        top: 30px;
        right: 30px;
        width: 260px;
        border-right: 4px solid #f97316;
    }

    .hud-title {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.15em;
        color: #06b6d4;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
    }

    .hud-right .hud-title {
        color: #f97316;
        justify-content: flex-end;
    }

    .hud-title .blink-dot {
        width: 6px;
        height: 6px;
        background-color: #22c55e;
        border-radius: 50%;
        margin-right: 8px;
        animation: hudBlink 1.5s infinite alternate;
        box-shadow: 0 0 8px #22c55e;
    }

    .hud-right .hud-title .blink-dot {
        margin-right: 0;
        margin-left: 8px;
        order: 2;
    }

    @keyframes hudBlink {
        0% { opacity: 0.3; transform: scale(0.9); }
        100% { opacity: 1; transform: scale(1.1); }
    }

    .hud-metric {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        font-size: 12px;
        border-bottom: 1px dashed rgba(255,255,255,0.05);
        padding-bottom: 8px;
    }

    .hud-metric:last-child {
        margin-bottom: 0;
        border-bottom: none;
        padding-bottom: 0;
    }

    .hud-label {
        color: #94a3b8;
    }

    .hud-value {
        font-family: monospace;
        font-weight: bold;
        color: #fff;
        letter-spacing: 0.05em;
    }

    .hud-value.text-success {
        color: #22c55e !important;
        text-shadow: 0 0 8px rgba(34, 197, 150, 0.4);
    }

    /* Telemetry Sparklines Styles */
    .telemetry-sparkline {
        width: 100%;
        height: 32px;
        margin-top: 6px;
        background: rgba(0, 0, 0, 0.25);
        border-radius: 4px;
        border: 1px solid rgba(255, 255, 255, 0.03);
    }

    /* Tech HUD overlay controls info */
    .hud-controls-info {
        position: absolute;
        bottom: 30px;
        left: 30px;
        z-index: 5;
        font-size: 11px;
        color: #94a3b8;
        background: rgba(10, 15, 30, 0.6);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        padding: 8px 16px;
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.06);
        pointer-events: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }

    /* Responsive Viewport Optimizations */
    @media screen and (max-width: 991px) {
        #chassis-3d-showroom {
            height: 520px;
        }
        .hud-left {
            top: 20px;
            left: 20px;
            width: 200px;
            padding: 12px;
        }
        .hud-right {
            top: 20px;
            right: 20px;
            width: 220px;
            padding: 12px;
        }
        .hud-controls-info {
            bottom: 20px;
            left: 20px;
            font-size: 10px;
            padding: 6px 12px;
        }
        .telemetry-sparkline {
            height: 24px;
        }
    }

    @media screen and (max-width: 767px) {
        #chassis-3d-showroom {
            height: 700px; /* Taller viewport to stack panels neatly */
        }
        .hud-panel {
            position: relative;
            top: 15px !important;
            left: 5% !important;
            right: auto !important;
            width: 90% !important;
            margin-bottom: 12px;
            border-left: none !important;
            border-right: none !important;
            border-top: 3px solid #06b6d4 !important;
        }
        .hud-right {
            border-top: 3px solid #f97316 !important;
        }
        .hud-right .hud-title {
            justify-content: flex-start;
        }
        .hud-right .hud-title .blink-dot {
            margin-right: 8px;
            margin-left: 0;
            order: 0;
        }
        .hud-controls-info {
            bottom: 15px;
            left: 5%;
            width: 90%;
            text-align: center;
        }
    }
</style>
@stack('styles')
