
	<!-- Start Footer Area -->
	<footer class="footer">
		<!-- Footer Top -->
		<div class="footer-top section">
			<div class="container">
				<div class="row">
					<div class="col-lg-5 col-md-6 col-12">
						<div class="single-footer about">
							<div class="logo mb-3">
								<a href="{{route('home')}}"><h3 class="text-white font-weight-bold">Danyal Autos <span class="text-warning">Co.</span></h3></a>
							</div>
							@php
								$settings=DB::table('settings')->get();
							@endphp
							<p class="text">@foreach($settings as $data) {{$data->short_des}} @endforeach</p>
							<p class="call">Got Question? Call us 24/7<span><a href="tel:123456789">@foreach($settings as $data) {{$data->phone}} @endforeach</a></span></p>
						</div>
						<!-- End Single Widget -->
					</div>
					<div class="col-lg-2 col-md-6 col-12">
						<!-- Single Widget -->
						<div class="single-footer links">
							<h4>Information</h4>
							<ul>
								<li><a href="{{route('about-us')}}">About Us</a></li>
								<li><a href="#">Faq</a></li>
								<li><a href="#">Terms & Conditions</a></li>
								<li><a href="{{route('contact')}}">Contact Us</a></li>
								<li><a href="#">Help</a></li>
							</ul>
						</div>
						<!-- End Single Widget -->
					</div>
					<div class="col-lg-2 col-md-6 col-12">
						<!-- Single Widget -->
						<div class="single-footer links">
							<h4>Customer Service</h4>
							<ul>
								<li><a href="#">Payment Methods</a></li>
								<li><a href="#">Money-back</a></li>
								<li><a href="#">Returns</a></li>
								<li><a href="#">Shipping</a></li>
								<li><a href="#">Privacy Policy</a></li>
							</ul>
						</div>
						<!-- End Single Widget -->
					</div>
					<div class="col-lg-3 col-md-6 col-12">
						<!-- Single Widget -->
						<div class="single-footer social">
							<h4>Get In Tuch</h4>
							<!-- Single Widget -->
							<div class="contact">
								<ul>
									<li>@foreach($settings as $data) {{$data->address}} @endforeach</li>
									<li>@foreach($settings as $data) {{$data->email}} @endforeach</li>
									<li>@foreach($settings as $data) {{$data->phone}} @endforeach</li>
								</ul>
							</div>
							<!-- End Single Widget -->
							<div class="sharethis-inline-follow-buttons"></div>
						</div>
						<!-- End Single Widget -->
					</div>
				</div>
			</div>
		</div>
		<!-- End Footer Top -->
		<div class="copyright">
			<div class="container">
				<div class="inner">
					<div class="row">
						<div class="col-lg-6 col-12">
							<div class="left">
								<p>Copyright © {{date('Y')}} Danyal Autos Co. - All Rights Reserved.</p>
							</div>
						</div>
						
					</div>
				</div>
			</div>
		</div>
	</footer>
	<!-- /End Footer Area -->

    @php
        $settings = DB::table('settings')->first();
        $whatsapp_phone = str_replace(['+', ' '], '', $settings->phone ?? '923420867758');
    @endphp
    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/{{ $whatsapp_phone }}" class="whatsapp-float" target="_blank">
        <i class="fa fa-whatsapp"></i>
    </a>

    <!-- ====== OFFCANVAS CART PANE ====== -->
    <!-- Overlay -->
    <div id="cart-pane-overlay" onclick="closeCartPane()" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.5); z-index:9998; backdrop-filter:blur(3px); opacity:0; transition: opacity 0.35s ease;"></div>

    <!-- Slide-out Cart Sidebar -->
    <div id="cart-pane" style="
        position: fixed;
        top: 0; right: 0;
        width: 380px; max-width: 95vw;
        height: 100vh;
        background: #083259;
        color: #ffffff;
        z-index: 9999;
        transform: translateX(100%);
        transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        display: flex; flex-direction: column;
        box-shadow: -8px 0 40px rgba(0,0,0,0.5);
    ">
        <!-- Pane Header -->
        <div style="background: rgba(0,0,0,0.2); color: #fff; border-bottom: 1px solid rgba(255,255,255,0.1); padding: 18px 20px; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;">
            <div class="d-flex align-items-center">
                <i class="ti-shopping-cart mr-2" style="font-size: 20px;"></i>
                <div>
                    <h6 style="margin:0; font-weight: 800; font-size: 15px;">Your Cart</h6>
                    <small style="color: #cbd5e1; font-size: 11px;" id="cart-pane-item-count">0 items</small>
                </div>
            </div>
            <button onclick="closeCartPane()" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); color: #fff; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <!-- Pane Body (scrollable) -->
        <div id="cart-pane-body" style="flex: 1; overflow-y: auto; padding: 16px; background: #062038;">
            <div class="text-center py-5" style="color: #cbd5e1;">
                <i class="ti-shopping-cart fa-3x mb-3" style="font-size: 48px; display:block;"></i>
                <p style="font-weight: 600;">Your cart is empty.</p>
                <a href="{{ route('product-grids') }}" onclick="closeCartPane()" class="btn btn-sm" style="background: var(--primary); color: #fff; border-radius: 4px; font-weight: 600;">Browse Parts</a>
            </div>
        </div>

        <!-- Pane Footer -->
        <div id="cart-pane-footer" style="background: #083259; border-top: 1px solid rgba(255,255,255,0.1); padding: 16px 20px; flex-shrink: 0;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span style="font-weight: 700; color: #cbd5e1; font-size: 14px;">Total</span>
                <span id="cart-pane-total" style="font-weight: 900; font-size: 1.3rem; color: #facc15;">Rs. 0.00</span>
            </div>
            <div class="d-flex" style="gap: 10px;">
                <a href="{{ route('cart') }}" onclick="closeCartPane()" style="flex: 1; text-align:center; background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.2); padding: 10px; border-radius: 4px; font-weight: 700; font-size: 13px; text-decoration: none; transition: all 0.2s;">
                    <i class="fa fa-shopping-cart mr-1"></i> View Cart
                </a>
                <a href="{{ route('checkout') }}" onclick="closeCartPane()" style="flex: 1; text-align:center; background: var(--accent); color: #000; border: none; padding: 10px; border-radius: 4px; font-weight: 800; font-size: 13px; text-decoration: none; transition: all 0.2s;">
                    Checkout <i class="fa fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Add-to-cart success toast -->
    <div id="cart-toast" style="position:fixed; bottom: 30px; left: 50%; transform: translateX(-50%) translateY(100px); background: #16a34a; color:#fff; padding: 12px 24px; border-radius: 8px; font-weight: 700; font-size: 14px; z-index: 99999; opacity:0; transition: all 0.4s ease; box-shadow: 0 8px 25px rgba(22,163,74,0.35); white-space: nowrap;">
        <i class="fa fa-check-circle mr-2"></i><span id="cart-toast-msg">Added to cart!</span>
    </div>

    <style>
        #cart-pane::-webkit-scrollbar { width: 5px; }
        #cart-pane-body::-webkit-scrollbar { width: 5px; }
        #cart-pane-body::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 3px; }
        .cart-item-row { background: rgba(255,255,255,0.05); color: #fff; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); padding: 12px; margin-bottom: 10px; display: flex; align-items: center; gap: 12px; }
        .cart-item-row h6, .cart-item-row a { color: #fff !important; }
        .cart-item-row img { width: 55px; height: 55px; object-fit: contain; border-radius: 4px; background: #fff; border: 1px solid rgba(255,255,255,0.2); }
        .cart-item-row .price { color: #facc15; font-weight: 700; }
    </style>

    <script>
        // ---- CART PANE OPEN / CLOSE ----
        function openCartPane() {
            var pane = document.getElementById('cart-pane');
            var overlay = document.getElementById('cart-pane-overlay');
            if (!pane) return;
            overlay.style.display = 'block';
            document.body.style.overflow = 'hidden';
            setTimeout(function() { overlay.style.opacity = '1'; pane.style.transform = 'translateX(0)'; }, 10);
            loadCartContents();
        }

        function closeCartPane() {
            var pane = document.getElementById('cart-pane');
            var overlay = document.getElementById('cart-pane-overlay');
            if (!pane) return;
            pane.style.transform = 'translateX(100%)';
            overlay.style.opacity = '0';
            document.body.style.overflow = '';
            setTimeout(function() { overlay.style.display = 'none'; }, 400);
        }

        // ---- LOAD CART CONTENTS ----
        function loadCartContents() {
            fetch('/ajax-get-cart', { headers: { 'Accept': 'application/json' } })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var body = document.getElementById('cart-pane-body');
                var totalEl = document.getElementById('cart-pane-total');
                var countEl = document.getElementById('cart-pane-item-count');
                if (body) body.innerHTML = data.html || '<div class="text-center py-5 text-muted"><i class="ti-shopping-cart" style="font-size:48px;display:block;margin-bottom:12px;"></i><p style="font-weight:600;">Your cart is empty.</p><a href="/product-grids" onclick="closeCartPane()" class="btn btn-sm" style="background:var(--primary);color:#fff;border-radius:4px;font-weight:600;">Browse Parts</a></div>';
                if (totalEl) totalEl.textContent = 'Rs. ' + (data.total || '0.00');
                if (countEl) countEl.textContent = (data.count || 0) + ' item(s)';
                // Update all cart badge counts in header
                document.querySelectorAll('.cart-count-badge, .total-count').forEach(function(el) {
                    el.textContent = data.count || 0;
                });
            })
            .catch(function() {
                var body = document.getElementById('cart-pane-body');
                if (body) body.innerHTML = '<div class="text-center py-4 text-muted"><p>Please <a href="/login">login</a> to view your cart.</p></div>';
            });
        }

        // ---- SHOW TOAST ----
        function showCartToast(msg) {
            var toast = document.getElementById('cart-toast');
            var msgEl = document.getElementById('cart-toast-msg');
            if (!toast) return;
            if (msgEl) msgEl.textContent = msg || 'Added to cart!';
            toast.style.opacity = '1';
            toast.style.transform = 'translateX(-50%) translateY(0)';
            setTimeout(function() {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(-50%) translateY(100px)';
            }, 3000);
        }

        // ---- GLOBAL AJAX ADD TO CART INTERCEPTOR ----
        document.addEventListener('click', function(e) {
            var btn = e.target.closest('.ajax-add-to-cart-btn');
            if (!btn) return;

            e.preventDefault();
            var slug = btn.dataset.slug;
            if (!slug) return;

            var csrf = document.querySelector('meta[name="csrf-token"]');
            var csrfToken = csrf ? csrf.getAttribute('content') : '';

            // Visual feedback
            var origHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
            btn.style.pointerEvents = 'none';

            fetch('/ajax-add-to-cart', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ slug: slug })
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                btn.innerHTML = origHtml;
                btn.style.pointerEvents = '';
                if (data.status === 'success') {
                    showCartToast('✓ Added to cart!');
                    // Update badge
                    document.querySelectorAll('.cart-count-badge, .total-count').forEach(function(el) {
                        el.textContent = data.cart_count;
                    });
                    // Open cart pane after a short delay
                    setTimeout(function() { openCartPane(); }, 300);
                } else {
                    showCartToast(data.message || 'Error adding to cart');
                    var toast = document.getElementById('cart-toast');
                    if (toast) toast.style.background = '#dc2626';
                }
            })
            .catch(function() {
                btn.innerHTML = origHtml;
                btn.style.pointerEvents = '';
                showCartToast('Please login to add to cart');
            });
        });

        // ESC to close
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeCartPane();
        });
    </script>
	<!-- Jquery -->
    <script src="{{asset('frontend/js/jquery.min.js')}}"></script>
    <script src="{{asset('frontend/js/jquery-migrate-3.0.0.js')}}"></script>
	<script src="{{asset('frontend/js/jquery-ui.min.js')}}"></script>
	<!-- Popper JS -->
	<script src="{{asset('frontend/js/popper.min.js')}}"></script>
	<!-- Bootstrap JS -->
	<script src="{{asset('frontend/js/bootstrap.min.js')}}"></script>
	<!-- Color JS -->
	<script src="{{asset('frontend/js/colors.js')}}"></script>
	<!-- Slicknav JS -->
	<script src="{{asset('frontend/js/slicknav.min.js')}}"></script>
	<!-- Owl Carousel JS -->
	<script src="{{asset('frontend/js/owl-carousel.js')}}"></script>
	<!-- Magnific Popup JS -->
	<script src="{{asset('frontend/js/magnific-popup.js')}}"></script>
	<!-- Waypoints JS -->
	<script src="{{asset('frontend/js/waypoints.min.js')}}"></script>
	<!-- Countdown JS -->
	<script src="{{asset('frontend/js/finalcountdown.min.js')}}"></script>
	<!-- Nice Select JS -->
	<script src="{{asset('frontend/js/nicesellect.js')}}"></script>
	<!-- Flex Slider JS -->
	<script src="{{asset('frontend/js/flex-slider.js')}}"></script>
	<!-- ScrollUp JS -->
	<script src="{{asset('frontend/js/scrollup.js')}}"></script>
	<!-- Onepage Nav JS -->
	<script src="{{asset('frontend/js/onepage-nav.min.js')}}"></script>
	{{-- Isotope --}}
	<script src="{{asset('frontend/js/isotope/isotope.pkgd.min.js')}}"></script>
	<!-- Easing JS -->
	<script src="{{asset('frontend/js/easing.js')}}"></script>

	<!-- Active JS -->
	<script src="{{asset('frontend/js/active.js')}}"></script>

	
	<!-- Three.js and GSAP for Interactive 3D Chassis Viewport -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

	@stack('scripts')
	<script>
		setTimeout(function(){
		  $('.alert').slideUp();
		},5000);
		$(function() {
		// ------------------------------------------------------- //
		// Multi Level dropdowns
		// ------------------------------------------------------ //
			$("ul.dropdown-menu [data-toggle='dropdown']").on("click", function(event) {
				event.preventDefault();
				event.stopPropagation();

				$(this).siblings().toggleClass("show");


				if (!$(this).next().hasClass('show')) {
				$(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
				}
				$(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
				$('.dropdown-submenu .show').removeClass("show");
				});

			});
		});
	  </script>
