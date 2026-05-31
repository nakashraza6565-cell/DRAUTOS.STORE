@extends('frontend.layouts.master')
@section('title','Danyal Autos Co. || Premium B2B Auto Parts')

@section('main-content')
<!-- Three.js Canvas Container -->
<canvas id="immersive-3d-canvas"></canvas>

<!-- Immersive B2B Styles -->
<style>
    /* Dark Vibe Core */
    body, html {
        background-color: #020b16 !important;
        color: #ffffff;
        overflow-x: hidden;
    }
    
    #immersive-3d-canvas {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: -1;
        pointer-events: none;
        background: radial-gradient(circle at center, #083259 0%, #020b16 100%);
    }

    /* Content Overlay Layer */
    .immersive-content-wrapper {
        position: relative;
        z-index: 10;
    }

    /* Scroll Sections */
    .scroll-section {
        min-height: 120vh; /* Extra height to allow scrolling through animations */
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 100px 0;
        position: relative;
    }

    /* Glassmorphism Panels */
    .glass-panel {
        background: rgba(8, 50, 89, 0.4);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(250, 204, 21, 0.2);
        border-radius: 16px;
        padding: 40px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.5);
        color: #fff;
    }
    
    .glass-panel-light {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* Hero Typography */
    .hero-title {
        font-size: clamp(3rem, 6vw, 6rem);
        font-weight: 900;
        line-height: 1.1;
        letter-spacing: -2px;
        text-transform: uppercase;
        background: linear-gradient(135deg, #ffffff 0%, #cbd5e1 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 20px;
        text-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }
    .hero-title .text-gold {
        background: linear-gradient(135deg, #FACC15 0%, #f59e0b 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .hero-subtitle {
        font-size: 1.3rem;
        color: #94a3b8;
        font-weight: 300;
        max-width: 600px;
        margin-bottom: 40px;
    }

    /* Buttons */
    .btn-vibe {
        background: #FACC15;
        color: #020b16;
        padding: 16px 40px;
        border-radius: 8px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        border: none;
        box-shadow: 0 10px 20px rgba(250, 204, 21, 0.3);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }
    .btn-vibe:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(250, 204, 21, 0.5);
        background: #f59e0b;
        color: #000;
        text-decoration: none;
    }

    /* Brand Cards */
    .vehicle-brand-card {
        background: rgba(8, 50, 89, 0.6) !important;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(250, 204, 21, 0.3) !important;
        box-shadow: 0 10px 20px rgba(0,0,0,0.3) !important;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .vehicle-brand-card:hover {
        transform: translateY(-15px) scale(1.05);
        border-color: #FACC15 !important;
        box-shadow: 0 20px 40px rgba(250, 204, 21, 0.4) !important;
        background: rgba(8, 50, 89, 0.9) !important;
    }

    /* Product Slider Overrides for Dark Mode */
    .b2b-slider-container {
        display: flex; gap: 20px; overflow-x: auto; padding: 20px 0 60px; scroll-behavior: smooth;
        -ms-overflow-style: none; scrollbar-width: none;
    }
    .b2b-slider-container::-webkit-scrollbar { display: none; }
    
    .b2b-slide-card {
        flex: 0 0 300px;
        background: rgba(15, 23, 42, 0.7);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .b2b-slide-card:hover {
        border-color: #FACC15;
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.5), 0 0 20px rgba(250, 204, 21, 0.2);
    }
    .b2b-slide-img-wrap {
        height: 240px; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; position: relative;
    }
    .b2b-slide-img-wrap img { width: 85%; height: 85%; object-fit: contain; transition: transform 0.5s; filter: drop-shadow(0 10px 10px rgba(0,0,0,0.5)); }
    .b2b-slide-card:hover .b2b-slide-img-wrap img { transform: scale(1.1); }
    
    .b2b-slide-title { color: #fff; font-weight: 700; font-size: 1.1rem; margin: 10px 0; }
    .b2b-price { color: #FACC15; font-weight: 800; font-size: 1.3rem; }
    .b2b-add-btn { background: transparent; border: 2px solid #FACC15; color: #FACC15; padding: 6px 16px; border-radius: 6px; font-weight: 700; transition: all 0.3s; }
    .b2b-add-btn:hover { background: #FACC15; color: #000; }
    
    .section-title-glow {
        color: #fff;
        font-size: 3rem;
        font-weight: 900;
        text-transform: uppercase;
        margin-bottom: 20px;
        text-shadow: 0 0 20px rgba(8, 50, 89, 0.8);
    }
    
    /* Scroll Indicator */
    .scroll-indicator {
        position: absolute;
        bottom: 50px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        flex-direction: column;
        align-items: center;
        opacity: 0.7;
        animation: bounce 2s infinite;
    }
    .scroll-indicator span { font-size: 12px; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 10px; color: #FACC15; }
    .scroll-indicator .mouse { width: 30px; height: 50px; border: 2px solid #FACC15; border-radius: 15px; position: relative; }
    .scroll-indicator .wheel { width: 4px; height: 8px; background: #FACC15; border-radius: 2px; position: absolute; top: 10px; left: 50%; transform: translateX(-50%); animation: scroll 2s infinite; }
    
    @keyframes bounce { 0%, 20%, 50%, 80%, 100% { transform: translateY(0) translateX(-50%); } 40% { transform: translateY(-20px) translateX(-50%); } 60% { transform: translateY(-10px) translateX(-50%); } }
    @keyframes scroll { 0% { top: 10px; opacity: 1; } 100% { top: 30px; opacity: 0; } }
</style>

<div class="immersive-content-wrapper">
    
    <!-- Hero Section -->
    <section class="scroll-section" id="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-7">
                    <div class="glass-panel" data-speed="0.8">
                        <div style="display: inline-block; padding: 6px 16px; background: rgba(250, 204, 21, 0.15); border: 1px solid #FACC15; color: #FACC15; border-radius: 30px; font-weight: 700; letter-spacing: 2px; font-size: 12px; margin-bottom: 20px;">
                            NEXT-GEN AUTO PARTS
                        </div>
                        <h1 class="hero-title">
                            HEAVY DUTY <br><span class="text-gold">PRECISION</span>
                        </h1>
                        <p class="hero-subtitle">
                            Experience the future of B2B procurement. Explore our holographic rear axle blueprints and discover industrial-grade components engineered for extreme endurance.
                        </p>
                        <a href="{{route('product-grids')}}" class="btn-vibe">
                            ENTER CATALOG <i class="fa fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="scroll-indicator">
            <span>Scroll to Explode Assembly</span>
            <div class="mouse"><div class="wheel"></div></div>
        </div>
    </section>

    <!-- Vehicle Types Section -->
    <section class="scroll-section" id="brands-section">
        <div class="container">
            <div class="row justify-content-end">
                <div class="col-lg-6">
                    <div class="glass-panel glass-panel-light text-right">
                        <h2 class="section-title-glow">FLEET COMPATIBILITY</h2>
                        <p class="text-light mb-5" style="font-size: 1.2rem;">Select your chassis configuration to source exact OEM & Aftermarket blueprints.</p>
                        
                        <div class="d-flex justify-content-end flex-wrap" style="gap: 15px;">
                            @php
                                $brands = [
                                    ['name' => 'HINO', 'icon' => 'fa-truck'],
                                    ['name' => 'ISUZU', 'icon' => 'fa-truck'],
                                    ['name' => 'NISSAN', 'icon' => 'fa-car'],
                                    ['name' => 'BEDFORD', 'icon' => 'fa-truck'],
                                    ['name' => 'MAZDA', 'icon' => 'fa-car'],
                                    ['name' => 'DAEWOO', 'icon' => 'fa-bus'],
                                    ['name' => 'FAW', 'icon' => 'fa-truck']
                                ];
                            @endphp
                            
                            @foreach($brands as $brand)
                                <a href="{{route('shop.vehicle.brand')}}?vehicle_brand={{$brand['name']}}" class="text-decoration-none vehicle-brand-card" style="flex: 1; min-width: 100px; max-width: 130px; border-radius: 12px; text-align: center; padding: 20px 10px; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 110px;">
                                    <i class="fa {{$brand['icon']}} brand-icon" style="color: #FACC15; font-size: 28px; margin-bottom: 10px;"></i>
                                    <span style="color: #FACC15; font-weight: 900; font-size: 14px; letter-spacing: 1px; font-family: 'Outfit', sans-serif; text-transform: uppercase;">{{$brand['name']}}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Top Selling Products Section -->
    <section class="scroll-section" id="products-section">
        <div class="container-fluid px-5">
            <div class="text-center mb-5">
                <h2 class="section-title-glow text-center">TOP SELLING HARDWARE</h2>
                <p class="text-light" style="font-size: 1.2rem; max-width: 600px; margin: 0 auto;">Interact with our highest-demand parts currently in global inventory.</p>
            </div>
            
            <div style="position: relative; width: 100%;">
                <button class="btn btn-vibe" style="position: absolute; top: 45%; left: 10px; z-index: 20; padding: 15px; border-radius: 50%; width: 50px; height: 50px; justify-content: center;" onclick="scrollSlider(-400)"><i class="fa fa-chevron-left m-0"></i></button>
                <button class="btn btn-vibe" style="position: absolute; top: 45%; right: 10px; z-index: 20; padding: 15px; border-radius: 50%; width: 50px; height: 50px; justify-content: center;" onclick="scrollSlider(400)"><i class="fa fa-chevron-right m-0"></i></button>
                
                <div class="b2b-slider-container" id="trending-slider">
                    @if(isset($product_lists) && count($product_lists) > 0)
                        @foreach($product_lists->take(10) as $product)
                        @php
                            $photos = explode(',',$product->photo);
                            $mainPhoto = $photos[0];
                        @endphp
                        <div class="b2b-slide-card">
                            <div class="b2b-slide-img-wrap">
                                <img src="{{$mainPhoto}}" alt="{{$product->title}}">
                            </div>
                            <div class="p-4">
                                <span style="font-size: 11px; color: #94a3b8; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">{{$product->cat_info['title'] ?? 'Hardware'}}</span>
                                <a href="{{route('product-detail',$product->slug)}}" class="text-decoration-none">
                                    <div class="b2b-slide-title">{{Str::limit($product->title, 40)}}</div>
                                </a>
                                <div class="d-flex justify-content-between align-items-center mt-3 pt-3" style="border-top: 1px solid rgba(255,255,255,0.1);">
                                    @auth
                                        @php
                                            $after_discount = ($product->price - ($product->price*$product->discount)/100);
                                        @endphp
                                        <span class="b2b-price">Rs. {{number_format($after_discount,2)}}</span>
                                        <a href="{{route('add-to-cart',$product->slug)}}" class="b2b-add-btn"><i class="fa fa-cart-plus"></i> ADD</a>
                                    @else
                                        <a href="{{route('login')}}" class="w-100 text-center py-2 text-decoration-none" style="background: rgba(255,255,255,0.1); color: #fff; border-radius: 6px; font-size: 12px; font-weight: 700; text-transform: uppercase;">
                                            <i class="fa fa-lock mr-1"></i> Login for Price
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="w-100 text-center py-5">
                            <h4 class="text-light">Catalog syncing...</h4>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="text-center mt-4">
                <a href="{{route('product-grids')}}" class="btn-vibe" style="background: transparent; color: #FACC15; border: 2px solid #FACC15; box-shadow: none;">
                    VIEW COMPLETE INVENTORY
                </a>
            </div>
        </div>
    </section>
    
    <!-- Wholesale Call to Action -->
    <section class="scroll-section" style="min-height: 80vh;">
        <div class="container text-center">
            <div class="glass-panel" style="max-width: 800px; margin: 0 auto; background: rgba(8, 50, 89, 0.8);">
                <i class="fa fa-globe fa-4x mb-4 text-gold"></i>
                <h2 class="section-title-glow">GLOBAL B2B NETWORK</h2>
                <p class="text-light mb-5" style="font-size: 1.2rem;">Join our enterprise supply chain. Unlock wholesale pricing, dedicated procurement agents, and synchronized logistics.</p>
                @guest
                    <a href="{{route('register')}}" class="btn-vibe px-5"><i class="fa fa-user-plus"></i> REGISTER CORPORATE ACCOUNT</a>
                @else
                    <a href="{{route('contact')}}" class="btn-vibe px-5"><i class="fa fa-envelope"></i> CONTACT SALES</a>
                @endguest
            </div>
        </div>
    </section>

</div>

@endsection

@push('scripts')
<!-- Load Three.js & GSAP -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

<script>
    function scrollSlider(amount) {
        const slider = document.getElementById('trending-slider');
        if (slider) { slider.scrollBy({ left: amount, behavior: 'smooth' }); }
    }

    // Initialize 3D Vibe Experience
    document.addEventListener("DOMContentLoaded", () => {
        gsap.registerPlugin(ScrollTrigger);

        // Setup Three.js Scene
        const canvas = document.getElementById('immersive-3d-canvas');
        const scene = new THREE.Scene();
        // Add subtle fog to blend with the background radial gradient
        scene.fog = new THREE.FogExp2('#020b16', 0.02);

        const camera = new THREE.PerspectiveCamera(45, window.innerWidth / window.innerHeight, 0.1, 1000);
        camera.position.set(0, 5, 25);

        const renderer = new THREE.WebGLRenderer({ canvas: canvas, alpha: true, antialias: true });
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

        // Lighting
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.4);
        scene.add(ambientLight);
        
        const dirLight = new THREE.DirectionalLight(0xFACC15, 1);
        dirLight.position.set(10, 20, 10);
        scene.add(dirLight);

        const blueLight = new THREE.PointLight(0x083259, 2, 50);
        blueLight.position.set(-10, -10, -10);
        scene.add(blueLight);

        // Materials (Holographic Blueprint Style)
        const goldMaterial = new THREE.MeshStandardMaterial({ 
            color: 0xFACC15, metalness: 0.8, roughness: 0.2, wireframe: true 
        });
        const blueMaterial = new THREE.MeshStandardMaterial({ 
            color: 0x0ea5e9, metalness: 0.9, roughness: 0.1, wireframe: true, emissive: 0x083259, emissiveIntensity: 0.5 
        });
        const solidGreyMaterial = new THREE.MeshStandardMaterial({
            color: 0x94a3b8, metalness: 0.5, roughness: 0.5
        });

        // ----------------------------------------------------
        // Build Complex Rear Axle Assembly
        // ----------------------------------------------------
        const axleGroup = new THREE.Group();

        // 1. Central Differential Housing (Sphere + Cylinder)
        const diffGeo = new THREE.SphereGeometry(3, 32, 32);
        const diffMesh = new THREE.Mesh(diffGeo, blueMaterial);
        axleGroup.add(diffMesh);

        // 2. Axle Tubes (Left & Right)
        const tubeGeo = new THREE.CylinderGeometry(1.2, 1.2, 16, 32);
        
        const leftTube = new THREE.Mesh(tubeGeo, solidGreyMaterial);
        leftTube.rotation.z = Math.PI / 2;
        leftTube.position.x = -9;
        axleGroup.add(leftTube);

        const rightTube = new THREE.Mesh(tubeGeo, solidGreyMaterial);
        rightTube.rotation.z = Math.PI / 2;
        rightTube.position.x = 9;
        axleGroup.add(rightTube);

        // 3. Brake Drums/Hubs at the ends
        const drumGeo = new THREE.CylinderGeometry(2.5, 2.5, 2, 32);
        
        const leftDrum = new THREE.Mesh(drumGeo, goldMaterial);
        leftDrum.rotation.z = Math.PI / 2;
        leftDrum.position.x = -17;
        axleGroup.add(leftDrum);

        const rightDrum = new THREE.Mesh(drumGeo, goldMaterial);
        rightDrum.rotation.z = Math.PI / 2;
        rightDrum.position.x = 17;
        axleGroup.add(rightDrum);

        // 4. Pinion Gear & Shaft (Coming out the front)
        const pinionShaftGeo = new THREE.CylinderGeometry(0.8, 0.8, 6, 16);
        const pinionShaft = new THREE.Mesh(pinionShaftGeo, solidGreyMaterial);
        pinionShaft.rotation.x = Math.PI / 2;
        pinionShaft.position.z = 5;
        axleGroup.add(pinionShaft);

        const pinionGearGeo = new THREE.ConeGeometry(2, 3, 16);
        const pinionGear = new THREE.Mesh(pinionGearGeo, goldMaterial);
        pinionGear.rotation.x = -Math.PI / 2;
        pinionGear.position.z = 2;
        axleGroup.add(pinionGear);

        // 5. Brake Pipelines (Curved Tubes over the axle)
        class CustomCurve extends THREE.Curve {
            constructor(scale = 1, flipX = 1) { super(); this.scale = scale; this.flipX = flipX; }
            getPoint(t, optionalTarget = new THREE.Vector3()) {
                const tx = (t * 14 * this.flipX);
                const ty = Math.sin(t * Math.PI) * 2 + 1.5;
                const tz = Math.cos(t * Math.PI * 2) * 1;
                return optionalTarget.set(tx, ty, tz).multiplyScalar(this.scale);
            }
        }
        const pipeGeo1 = new THREE.TubeGeometry(new CustomCurve(1, 1), 64, 0.15, 8, false);
        const pipe1 = new THREE.Mesh(pipeGeo1, goldMaterial);
        axleGroup.add(pipe1);
        
        const pipeGeo2 = new THREE.TubeGeometry(new CustomCurve(1, -1), 64, 0.15, 8, false);
        const pipe2 = new THREE.Mesh(pipeGeo2, goldMaterial);
        axleGroup.add(pipe2);

        // Add assembly to scene
        scene.add(axleGroup);
        
        // Initial Position (Hero)
        axleGroup.position.set(5, 0, 0);
        axleGroup.rotation.y = -Math.PI / 6;
        axleGroup.rotation.x = Math.PI / 12;

        // Store original positions for reassembly
        const parts = [leftDrum, rightDrum, leftTube, rightTube, pinionShaft, pinionGear, pipe1, pipe2, diffMesh];
        parts.forEach(p => {
            p.userData.origX = p.position.x;
            p.userData.origY = p.position.y;
            p.userData.origZ = p.position.z;
        });

        // ----------------------------------------------------
        // GSAP ScrollTrigger Animations (The "Explode" Effect)
        // ----------------------------------------------------
        
        // Continuous slow rotation
        gsap.to(axleGroup.rotation, {
            y: "+=6.28", // Full 360 degree spin
            ease: "none",
            repeat: -1,
            duration: 40
        });

        // 1. Move to left when scrolling to Brands
        gsap.to(axleGroup.position, {
            x: -8,
            z: -5,
            ease: "power2.inOut",
            scrollTrigger: {
                trigger: "#brands-section",
                start: "top bottom",
                end: "center center",
                scrub: 1
            }
        });

        // 2. Explode the Assembly at Products Section
        const tl = gsap.timeline({
            scrollTrigger: {
                trigger: "#products-section",
                start: "top bottom",
                end: "center center",
                scrub: 1.5
            }
        });

        tl.to(axleGroup.position, { x: 0, y: 2, z: 5 }, 0)
          // Pull wheels out
          .to(leftDrum.position, { x: -25 }, 0)
          .to(rightDrum.position, { x: 25 }, 0)
          // Pull axle tubes out
          .to(leftTube.position, { x: -14 }, 0)
          .to(rightTube.position, { x: 14 }, 0)
          // Pull pinion out
          .to(pinionShaft.position, { z: 12 }, 0)
          .to(pinionGear.position, { z: 8 }, 0)
          // Lift pipes
          .to(pipe1.position, { y: 3 }, 0)
          .to(pipe2.position, { y: 3 }, 0)
          // Expand the central sphere
          .to(diffMesh.scale, { x: 1.5, y: 1.5, z: 1.5 }, 0);

        // 3. Reassemble and zoom out at Wholesale Section
        const t2 = gsap.timeline({
            scrollTrigger: {
                trigger: ".scroll-section:last-child",
                start: "top bottom",
                end: "center center",
                scrub: 1.5
            }
        });

        t2.to(axleGroup.position, { y: -5, z: -15 }, 0)
          .to([leftDrum.position, rightDrum.position, leftTube.position, rightTube.position, pinionShaft.position, pinionGear.position, pipe1.position, pipe2.position], { 
              x: (i, t) => t.userData.origX || t.position.x, 
              y: (i, t) => t.userData.origY || t.position.y,
              z: (i, t) => t.userData.origZ || t.position.z 
          }, 0)
          .to(diffMesh.scale, { x: 1, y: 1, z: 1 }, 0);

        // Render Loop
        const clock = new THREE.Clock();
        function animate() {
            requestAnimationFrame(animate);
            // Add subtle floating effect
            const elapsedTime = clock.getElapsedTime();
            axleGroup.position.y += Math.sin(elapsedTime) * 0.005;
            renderer.render(scene, camera);
        }
        animate();

        // Window Resize
        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });
    });
</script>
@endpush
