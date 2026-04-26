
      <!-- Footer -->
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; Dr Auto Parts {{date('Y')}}</span>
          </div>
        </div>
      </footer>
      <!-- End of Footer -->

      @php
          $settings = DB::table('settings')->first();
          $whatsapp_phone = str_replace(['+', ' '], '', $settings->phone ?? '923420867758');
      @endphp
      <!-- WhatsApp Floating Button -->
      <a href="https://wa.me/{{ $whatsapp_phone }}" class="whatsapp-float" target="_blank" style="color: #fff !important;">
          <i class="fab fa-whatsapp"></i>
      </a>

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Logout Modal-->
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <a class="btn btn-primary" href="login.html">Logout</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap core JavaScript-->
  <script src="{{asset('backend/vendor/jquery/jquery.min.js')}}"></script>
  <script src="{{asset('backend/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
  
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Core plugin JavaScript-->
  <script src="{{asset('backend/vendor/jquery-easing/jquery.easing.min.js')}}"></script>

  <!-- Custom scripts for all pages-->
  <script src="{{asset('backend/js/sb-admin-2.min.js')}}"></script>

  <!-- Page level plugins -->
  <script src="{{asset('backend/vendor/chart.js/Chart.min.js')}}"></script>

  <!-- Page level custom scripts -->
  {{-- <script src="{{asset('backend/js/demo/chart-area-demo.js')}}"></script> --}}
  {{-- <script src="{{asset('backend/js/demo/chart-pie-demo.js')}}"></script> --}}

  @stack('scripts')

  <script>
    $(document).ready(function() {
        // Project-wide Ghost Sidebar Logic
        $(".sidebar").addClass("sidebar-ghost-mode");
        // Hover to reveal is removed as per user request

        // Intercept standard Sidebar Toggles to act as 'Full Drawer' toggles
        $('#sidebarToggle, #sidebarToggleTop, #main-sidebar-toggle').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            $(".sidebar").toggleClass("reveal toggled");
            $("body").toggleClass("sidebar-toggled");
        });

        // Auto-expand sidebar if a submenu is clicked while minimized
        $('.sidebar .nav-link.collapsed').on('click', function() {
            if ($('body').hasClass('sidebar-toggled')) {
                $(".sidebar").removeClass("toggled").addClass("reveal");
                $("body").removeClass("sidebar-toggled");
            }
        });

        // Robust Mobile & iPad Swipe Gesture to Open/Close Sidebar
        var touchStartX = 0;
        var touchStartY = 0;
        var touchEndX = 0;
        var touchEndY = 0;

        document.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
            touchStartY = e.changedTouches[0].screenY;
        }, {passive: true});

        document.addEventListener('touchend', function(e) {
            touchEndX = e.changedTouches[0].screenX;
            touchEndY = e.changedTouches[0].screenY;
            handleSwipe();
        }, {passive: true});

        function handleSwipe() {
            var diffX = touchEndX - touchStartX;
            var diffY = Math.abs(touchEndY - touchStartY);
            
            // Ignore if it was mostly a vertical scroll
            if (diffY > 60 || Math.abs(diffX) < 50) return;

            // Swipe Right (Open) - Allowed if started within 100px of left edge (better for iPads with cases)
            if (diffX > 50 && touchStartX < 100) {
                $(".sidebar").addClass("reveal toggled");
                $("body").addClass("sidebar-toggled");
            }
            // Swipe Left (Close)
            if (diffX < -50) {
                $(".sidebar").removeClass("reveal toggled");
                $("body").removeClass("sidebar-toggled");
            }
        }

        setTimeout(function(){
          $('.alert').slideUp();
        },4000);
    });
  </script>
