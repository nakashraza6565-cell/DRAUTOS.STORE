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
        background: radial-gradient(ellipse at 50% 60%, #071324 0%, #020817 60%, #000 100%);
        overflow: hidden;
        border-bottom: 2px solid #0f1f3a;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Sci-fi Blueprint Grid Overlay */
    .showroom-grid-overlay {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background-size: 40px 40px;
        background-image:
            linear-gradient(to right, rgba(0, 200, 255, 0.035) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(0, 200, 255, 0.035) 1px, transparent 1px);
        pointer-events: none;
        z-index: 1;
    }

    /* Animated scanning sweep line */
    .scan-sweep {
        position: absolute;
        top: 0; left: 0;
        width: 3px; height: 100%;
        background: linear-gradient(to bottom, transparent, rgba(0,240,255,0.5), transparent);
        box-shadow: 0 0 20px rgba(0,240,255,0.4), 0 0 40px rgba(0,240,255,0.2);
        animation: sweepX 5s ease-in-out infinite;
        pointer-events: none;
        z-index: 3;
    }
    @keyframes sweepX {
        0%   { left: 0%; opacity: 0; }
        10%  { opacity: 1; }
        90%  { opacity: 1; }
        100% { left: 100%; opacity: 0; }
    }

    /* ===== LEFT PANEL: Part Cards ===== */
    .hud-panel {
        position: absolute;
        background: rgba(5, 12, 28, 0.72);
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
        border: 1px solid rgba(0, 200, 255, 0.1);
        padding: 20px 18px;
        border-radius: 14px;
        color: #fff;
        z-index: 5;
        box-shadow: 0 20px 50px rgba(0,0,0,0.6), 0 0 30px rgba(0,200,255,0.04);
        transition: border-color 0.3s ease;
    }

    .hud-left {
        top: 50%;
        transform: translateY(-50%);
        left: 28px;
        width: 230px;
        border-left: 3px solid #06b6d4;
    }

    .hud-right {
        top: 50%;
        transform: translateY(-50%);
        right: 28px;
        width: 270px;
        border-right: 3px solid #f97316;
    }

    .hud-title {
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.18em;
        color: #06b6d4;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
    }

    .hud-right .hud-title { color: #f97316; }

    .blink-dot {
        display: inline-block;
        width: 6px; height: 6px;
        background: #22c55e;
        border-radius: 50%;
        margin-right: 8px;
        animation: hudBlink 1.4s infinite alternate;
        box-shadow: 0 0 6px #22c55e;
    }
    @keyframes hudBlink {
        0% { opacity: 0.2; transform: scale(0.8); }
        100% { opacity: 1; transform: scale(1.2); }
    }

    /* Part Cards */
    .part-card {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        margin-bottom: 8px;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(6,182,212,0.12);
        border-radius: 10px;
        color: #e2e8f0;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        cursor: pointer;
    }
    .part-card:last-child { margin-bottom: 0; }
    .part-card:hover {
        background: rgba(6,182,212,0.1);
        border-color: rgba(6,182,212,0.5);
        transform: translateX(4px);
        box-shadow: 0 8px 25px rgba(6,182,212,0.15);
        color: #fff;
        text-decoration: none;
    }

    .part-card-icon {
        width: 34px; height: 34px;
        background: rgba(6,182,212,0.1);
        border: 1px solid rgba(6,182,212,0.3);
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px;
        color: #06b6d4;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }
    .part-card:hover .part-card-icon {
        background: rgba(6,182,212,0.2);
        color: #fff;
        box-shadow: 0 0 12px rgba(6,182,212,0.4);
    }
    .part-card-body { flex: 1; min-width: 0; }
    .part-card-title {
        font-size: 11px;
        font-weight: 700;
        color: #fff;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .part-card-desc {
        font-size: 9px;
        color: #64748b;
        margin-top: 2px;
        font-family: monospace;
    }
    .part-card-arrow {
        font-size: 10px;
        color: #334155;
        transition: all 0.3s ease;
    }
    .part-card:hover .part-card-arrow { color: #06b6d4; }

    /* ===== CENTER: Holographic Truck Image ===== */
    .showroom-truck-wrap {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2;
        pointer-events: auto;   /* allow drag events through */
        cursor: grab;
        /* 3D perspective origin */
        perspective: 900px;
        perspective-origin: 50% 50%;
    }
    .showroom-truck-wrap:active { cursor: grabbing; }

    /* The rotating 3D stage that holds the image + hotspots */
    .truck-3d-stage {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        transform-style: preserve-3d;
        will-change: transform;
        width: 58%;
        height: 90%;
        user-select: none;
    }

    .showroom-truck-img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        filter: drop-shadow(0 0 30px rgba(0,200,255,0.35)) drop-shadow(0 0 60px rgba(0,100,200,0.2));
        pointer-events: none;
        user-select: none;
        display: block;
    }

    /* Scan label */
    .truck-scan-label {
        position: absolute;
        bottom: 38px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 10px;
        letter-spacing: 0.3em;
        text-transform: uppercase;
        color: rgba(0,200,255,0.45);
        font-family: monospace;
        white-space: nowrap;
        pointer-events: none;
    }

    /* Image-overlaid hotspot rings */
    .img-hotspot {
        position: absolute;
        transform: translate(-50%, -50%);
        z-index: 6;
        pointer-events: auto;
        cursor: pointer;
    }

    .hotspot-ring {
        position: relative;
        width: 30px; height: 30px;
        border-radius: 50%;
        background: rgba(249,115,22,0.12);
        border: 2px solid #f97316;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 0 12px rgba(249,115,22,0.5), inset 0 0 6px rgba(249,115,22,0.2);
        transition: all 0.4s ease;
    }
    .img-hotspot:hover .hotspot-ring {
        background: rgba(249,115,22,0.25);
        box-shadow: 0 0 25px rgba(249,115,22,0.8), inset 0 0 12px rgba(249,115,22,0.4);
        transform: scale(1.2);
    }

    .hotspot-dot {
        width: 7px; height: 7px;
        border-radius: 50%;
        background: #f97316;
        box-shadow: 0 0 8px #f97316;
    }

    .hotspot-pulse {
        position: absolute;
        width: 50px; height: 50px;
        border-radius: 50%;
        border: 2px solid rgba(249,115,22,0.4);
        animation: hotspotPulse 2.2s infinite linear;
        pointer-events: none;
    }
    @keyframes hotspotPulse {
        0%   { transform: scale(0.5); opacity: 1; }
        100% { transform: scale(1.8); opacity: 0; }
    }

    /* ===== RIGHT PANEL: Charts ===== */
    .chart-block {
        margin-bottom: 16px;
    }
    .chart-block:last-child { margin-bottom: 0; }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
    }

    .chart-label {
        font-size: 10px;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .chart-value {
        font-size: 12px;
        font-weight: 800;
        font-family: monospace;
        color: #f97316;
        letter-spacing: 0.05em;
    }

    .hud-chart {
        display: block;
        width: 100%;
        height: 48px;
        border-radius: 6px;
        border: 1px solid rgba(255,255,255,0.04);
        background: rgba(0,0,0,0.3);
    }

    /* HUD Controls Info */
    .hud-controls-info {
        position: absolute;
        bottom: 22px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 5;
        font-size: 10px;
        color: #475569;
        background: rgba(5,12,28,0.6);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        padding: 7px 18px;
        border-radius: 20px;
        border: 1px solid rgba(255,255,255,0.05);
        pointer-events: none;
        white-space: nowrap;
        box-shadow: 0 5px 15px rgba(0,0,0,0.4);
    }

    /* Glassmorphism Side Drawer */
    #parts-side-drawer {
        position: fixed;
        top: 0; right: -460px;
        width: 450px; height: 100vh;
        background: rgba(10,15,30,0.82);
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
        border-left: 1px solid rgba(255,255,255,0.1);
        box-shadow: -20px 0 60px rgba(0,0,0,0.7);
        z-index: 99999;
        transition: all 0.5s cubic-bezier(0.16,1,0.3,1);
        padding: 35px;
        color: #f8fafc;
        display: flex; flex-direction: column;
    }
    #parts-side-drawer.open { right: 0; }

    .drawer-header {
        display: flex; justify-content: space-between; align-items: center;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        padding-bottom: 20px; margin-bottom: 25px;
    }
    .drawer-title { font-size: 1.5rem; font-weight: 800; color: #fff; display: flex; align-items: center; }
    .drawer-title i { color: #f97316; margin-right: 12px; }
    .drawer-close {
        background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
        color: #fff; width: 38px; height: 38px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.3s ease;
    }
    .drawer-close:hover { background: #f97316; border-color: #f97316; transform: rotate(90deg); box-shadow: 0 0 15px rgba(249,115,22,0.5); }
    .drawer-content { flex: 1; overflow-y: auto; padding-right: 8px; }
    .drawer-content::-webkit-scrollbar { width: 6px; }
    .drawer-content::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 3px; }
    .drawer-content::-webkit-scrollbar-thumb:hover { background: #f97316; }

    /* Responsive */
    @media screen and (max-width: 991px) {
        #chassis-3d-showroom { height: 560px; }
        .hud-left { width: 190px; left: 16px; padding: 14px; }
        .hud-right { width: 220px; right: 16px; padding: 14px; }
        .truck-3d-stage { width: 54%; }
    }
    @media screen and (max-width: 767px) {
        #chassis-3d-showroom { height: auto; min-height: 480px; padding: 120px 0 80px; flex-direction: column; cursor: default; }
        .hud-left, .hud-right { position: relative; top: auto !important; left: auto !important; right: auto !important; transform: none !important; width: 92% !important; margin: 8px auto; }
        .showroom-truck-wrap { position: relative; width: 100%; height: 260px; perspective: 600px; }
        .truck-3d-stage { width: 85%; height: 100%; }
        .hud-controls-info { bottom: 10px; font-size: 9px; }
    }
</style>
@stack('styles')
