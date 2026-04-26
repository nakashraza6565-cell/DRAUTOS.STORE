
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
        // Mobile Swipe Support
        let touchstartX = 0;
        let touchendX = 0;
        
        function checkDirection() {
            if (touchendX < touchstartX - 80) { // Swipe Left (Close)
                if (!$('.sidebar').hasClass('hidden')) {
                    $('.sidebar').addClass('hidden');
                    $('#wrapper').addClass('sidebar-hidden');
                    localStorage.setItem('sidebar_state', 'hidden');
                }
            }
            if (touchendX > touchstartX + 80) { // Swipe Right (Open)
                if ($('.sidebar').hasClass('hidden')) {
                    $('.sidebar').removeClass('hidden');
                    $('#wrapper').removeClass('sidebar-hidden');
                    localStorage.setItem('sidebar_state', 'visible');
                }
            }
        }

        document.addEventListener('touchstart', e => {
            touchstartX = e.changedTouches[0].screenX;
        }, {passive: true});

        document.addEventListener('touchend', e => {
            touchendX = e.changedTouches[0].screenX;
            checkDirection();
        }, {passive: true});

        // Initial mobile check
        if ($(window).width() < 768) {
            $(".sidebar").addClass("hidden");
            $("#wrapper").addClass("sidebar-hidden");
        }

        // Full Drawer Toggle Logic
        $('#sidebarToggle, #sidebarToggleTop').on('click', function(e) {
            e.preventDefault();
            $(".sidebar").toggleClass("hidden");
            $("#wrapper").toggleClass("sidebar-hidden");
        });
        setTimeout(function(){
          $('.alert').slideUp();
        },4000);
    });
  </script>
