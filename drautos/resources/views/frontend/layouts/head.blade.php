<!-- Meta Tag -->
@yield('meta')
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<!-- Title Tag  -->
<title>@yield('title')</title>
<!-- Favicon -->
<link rel="icon" type="image/svg+xml" href="{{asset('frontend/img/logo.svg')}}">
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
<link rel="stylesheet" href="{{asset('frontend/css/style.css')}}?v=3">
<link rel="stylesheet" href="{{asset('frontend/css/responsive.css')}}">

<style>
    /* ===== PREMIUM B2B COMMERCIAL DESIGN TOKENS ===== */
    :root {
        --primary: #083259;       /* Rich Corporate Marine Blue */
        --primary-light: #0d467a; /* Lighter Marine Blue */
        --accent: #fbbf24;        /* Industrial Yellow */
        --accent-alt: #f59e0b;    /* Darker Industrial Yellow */
        --text-main: #334155;     /* Slate text for light backgrounds */
        --text-muted: #8b9cb3;    /* Metallic Silver Blue */
        --bg-soft: #f8fafc;       /* Pearl white / silver background */
        --border-color: #cbd5e1;  /* Standard silver border */
    }

    body {
        font-family: 'Outfit', sans-serif !important;
        color: var(--text-main);
        background-color: #ffffff; /* Clean white background */
        scroll-behavior: smooth;
    }

    h1, h2, h3, h4, h5, h6 {
        font-family: 'Outfit', sans-serif !important;
        font-weight: 700;
        color: var(--primary); /* Navy headings */
    }

    .btn {
        font-family: 'Outfit', sans-serif !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* WhatsApp Floating Button - Corporate Adapted */
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
        animation: pulse-whatsapp 2s infinite;
    }

    .whatsapp-float:hover {
        background-color: #128c7e;
        transform: scale(1.1) translateY(-5px);
        color: #fff;
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

    @keyframes pulse-whatsapp {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.5); }
        70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(37, 211, 102, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(37, 211, 102, 0); }
    }

    /* B2B Transitions */
    .single-product {
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        border-radius: 8px; /* Sharper corporate corners */
        overflow: hidden;
        border: 1px solid var(--border-color);
        background: #ffffff;
    }

    .single-product:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(15, 23, 42, 0.1);
        border-color: var(--primary);
    }
</style>
@stack('styles')
