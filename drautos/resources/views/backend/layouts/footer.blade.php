
      <!-- Footer -->
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; Danyal Autos Co. {{date('Y')}}</span>
          </div>
        </div>
      </footer>
      <!-- End of Footer -->

      @php
          $settings = DB::table('settings')->first();
          $whatsapp_phone = str_replace(['+', ' '], '', $settings->phone ?? '923420867758');
      @endphp

      @if(auth()->check() && auth()->user()->role === 'admin')
      {{-- ===== ADMIN AI CHAT ASSISTANT ===== --}}

      {{-- Floating AI Button --}}
      <button id="ai-chat-trigger" title="AI Assistant">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="28" height="28">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/>
          </svg>
          <svg id="ai-thinking-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="28" height="28" style="display:none;">
              <path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm1 17.93V18a1 1 0 0 0-2 0v1.93A8 8 0 0 1 4.07 13H6a1 1 0 0 0 0-2H4.07A8 8 0 0 1 11 4.07V6a1 1 0 0 0 2 0V4.07A8 8 0 0 1 19.93 11H18a1 1 0 0 0 0 2h1.93A8 8 0 0 1 13 19.93z"/>
          </svg>
      </button>

      {{-- Chat Window --}}
      <div id="ai-chat-window">
          {{-- Header --}}
          <div id="ai-chat-header">
              <div class="d-flex align-items-center">
                  <div id="ai-avatar">AI</div>
                  <div>
                      <div style="font-weight:700; font-size:0.9rem;">Danyal AI Assistant</div>
                      <div style="font-size:0.7rem; color:#a5b4fc;">Always at your service</div>
                  </div>
              </div>
              <div class="d-flex align-items-center gap-2">
                  <button id="ai-chat-resize" title="Maximize/Minimize" style="background:rgba(255,255,255,0.1); border:none; color:white; width:32px; height:32px; border-radius:10px; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                      <svg id="resize-icon-max" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16" height="16">
                          <path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/>
                      </svg>
                      <svg id="resize-icon-min" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16" height="16" style="display:none;">
                          <path d="M5 16h3v3h2v-5H5v2zm3-8H5v2h5V5H8v3zm6 11h2v-3h3v-2h-5v5zm2-11V5h-2v5h5V8h-3z"/>
                      </svg>
                  </button>
                  <button id="ai-chat-close">✕</button>
              </div>
          </div>

          {{-- Messages Area --}}
          <div id="ai-chat-messages">
              <div class="ai-msg bot">
                  👋 Hello {{ auth()->user()->name }}! I'm your **AI Assistant**. I'm connected directly to your store. <br><br>
                  Tap a quick action or ask me anything:
              </div>
              <div id="ai-quick-actions">
                  <button class="ai-action-btn" data-query="Give me a summary of today's sales and pending orders.">📊 Today's Summary</button>
                  <button class="ai-action-btn" data-query="List any products with low stock.">📦 Low Stock</button>
                  <button class="ai-action-btn" data-query="Which customer cheques are pending clearing?">💰 Pending Cheques</button>
                  <button class="ai-action-btn" data-query="Download the latest price list PDF.">📄 Price List PDF</button>
              </div>
          </div>

          {{-- Input Area --}}
          <div id="ai-chat-input-area">
              <button id="ai-mic-btn" title="Speak (Urdu / English)">
                  <svg id="mic-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="18" height="18">
                      <path d="M12 14a3 3 0 0 0 3-3V5a3 3 0 0 0-6 0v6a3 3 0 0 0 3 3zm5-3a5 5 0 0 1-10 0H5a7 7 0 0 0 6 6.93V20H9v2h6v-2h-2v-2.07A7 7 0 0 0 19 11h-2z"/>
                  </svg>
              </button>
              <input type="text" id="ai-chat-input" placeholder="Type or tap 🎤 to speak..." autocomplete="off">
              <button id="ai-chat-send">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20" height="20">
                      <path d="M2 21l21-9L2 3v7l15 2-15 2v7z"/>
                  </svg>
              </button>
          </div>
      </div>

      {{-- Styles --}}
      <style>
          #ai-chat-trigger {
              position: fixed;
              bottom: 30px;
              right: 30px;
              width: 60px;
              height: 60px;
              border-radius: 50%;
              background: linear-gradient(135deg, #4f46e5, #7c3aed);
              color: white;
              border: none;
              cursor: pointer;
              box-shadow: 0 10px 30px rgba(79, 70, 229, 0.4);
              display: flex;
              align-items: center;
              justify-content: center;
              z-index: 9999;
              transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
          }
          #ai-chat-trigger:hover {
              transform: scale(1.1) rotate(5deg);
              box-shadow: 0 15px 40px rgba(79, 70, 229, 0.6);
          }
          #ai-chat-window {
              position: fixed;
              bottom: 105px;
              right: 30px;
              width: 400px;
              max-height: 600px;
              background: #fff;
              border-radius: 24px;
              box-shadow: 0 25px 70px rgba(0,0,0,0.15);
              display: none;
              flex-direction: column;
              z-index: 9998;
              overflow: hidden;
              border: 1px solid #f0f4ff;
              transition: all 0.3s ease;
          }
          #ai-chat-window.large-view {
              width: 850px;
              max-height: 85vh;
              bottom: 105px;
          }
          @media (max-width: 768px) {
              #ai-chat-window {
                  width: calc(100% - 30px) !important;
                  right: 15px !important;
                  bottom: 90px !important;
                  max-height: calc(100vh - 110px) !important;
                  border-radius: 20px;
              }
              #ai-chat-window.large-view {
                  width: calc(100% - 30px) !important;
                  right: 15px !important;
              }
              #ai-chat-trigger {
                  bottom: 20px;
                  right: 20px;
                  width: 55px;
                  height: 55px;
              }
          }
          #ai-chat-header {
              background: #4f46e5;
              color: white;
              padding: 18px 24px;
              display: flex;
              align-items: center;
              justify-content: space-between;
          }
          #ai-avatar {
              width: 42px;
              height: 42px;
              border-radius: 14px;
              background: rgba(255,255,255,0.2);
              display: flex;
              align-items: center;
              justify-content: center;
              font-weight: 800;
              margin-right: 14px;
          }
          #ai-chat-close {
              background: rgba(255,255,255,0.1);
              border: none;
              color: white;
              width: 32px;
              height: 32px;
              border-radius: 10px;
              cursor: pointer;
          }
          #ai-chat-messages {
              flex: 1;
              overflow-y: auto;
              padding: 20px;
              display: flex;
              flex-direction: column;
              gap: 12px;
              background: #fcfdfe;
          }
          .ai-msg {
              max-width: 85%;
              padding: 12px 16px;
              border-radius: 18px;
              font-size: 0.9rem;
              line-height: 1.5;
          }
          .ai-msg.bot {
              background: #f1f5f9;
              color: #1e293b;
              align-self: flex-start;
              border-bottom-left-radius: 4px;
          }
          .ai-msg.user {
              background: #4f46e5;
              color: white;
              align-self: flex-end;
              border-bottom-right-radius: 4px;
          }
          .ai-msg.thinking {
              background: #eef2ff;
              color: #4f46e5;
              align-self: flex-start;
              animation: thinking-pulse 1.5s infinite;
          }
          @keyframes thinking-pulse {
              0% { opacity: 0.5; }
              50% { opacity: 1; }
              100% { opacity: 0.5; }
          }
          #ai-quick-actions {
              display: grid;
              grid-template-columns: 1fr 1fr;
              gap: 8px;
              margin-top: 5px;
          }
          .ai-action-btn {
              background: #fff;
              border: 1px solid #e2e8f0;
              padding: 8px 12px;
              border-radius: 12px;
              font-size: 0.75rem;
              color: #475569;
              cursor: pointer;
              transition: all 0.2s;
              text-align: left;
          }
          .ai-action-btn:hover {
              background: #f1f5f9;
              border-color: #cbd5e1;
              transform: translateY(-2px);
          }
          #ai-chat-input-area {
              padding: 15px 20px;
              display: flex;
              gap: 10px;
              background: white;
              border-top: 1px solid #f1f5f9;
          }
          #ai-chat-input {
              flex: 1;
              border: 1.5px solid #e2e8f0;
              border-radius: 14px;
              padding: 12px 16px;
              font-size: 0.9rem;
              outline: none;
          }
          #ai-chat-input:focus { border-color: #4f46e5; }
          #ai-chat-send {
              width: 48px;
              height: 48px;
              border-radius: 14px;
              background: #4f46e5;
              border: none;
              color: white;
              cursor: pointer;
              display: flex;
              align-items: center;
              justify-content: center;
          }
          #ai-mic-btn {
              width: 48px;
              height: 48px;
              border-radius: 14px;
              background: #f8fafc;
              border: 1.5px solid #e2e8f0;
              color: #64748b;
              cursor: pointer;
              display: flex;
              align-items: center;
              justify-content: center;
          }
          #ai-mic-btn.recording {
              background: #ef4444;
              color: white;
              border-color: #dc2626;
          }
      </style>

      {{-- Script --}}
      <script>
      (function() {
          const chatWindow  = document.getElementById('ai-chat-window');
          const trigger     = document.getElementById('ai-chat-trigger');
          const closeBtn    = document.getElementById('ai-chat-close');
          const resizeBtn   = document.getElementById('ai-chat-resize');
          const input       = document.getElementById('ai-chat-input');
          const sendBtn     = document.getElementById('ai-chat-send');
          const messages    = document.getElementById('ai-chat-messages');
          const quickActions = document.querySelectorAll('.ai-action-btn');
          
          let chatHistory = [];

          trigger.addEventListener('click', () => {
              chatWindow.style.display = chatWindow.style.display === 'flex' ? 'none' : 'flex';
          });
          closeBtn.addEventListener('click', () => { chatWindow.style.display = 'none'; });
          
          resizeBtn.addEventListener('click', () => {
              chatWindow.classList.toggle('large-view');
              const isLarge = chatWindow.classList.contains('large-view');
              document.getElementById('resize-icon-max').style.display = isLarge ? 'none' : 'block';
              document.getElementById('resize-icon-min').style.display = isLarge ? 'block' : 'none';
          });

          sendBtn.addEventListener('click', () => sendMessage());
          input.addEventListener('keydown', (e) => { if (e.key === 'Enter') sendMessage(); });

          quickActions.forEach(btn => {
              btn.addEventListener('click', () => sendMessage(btn.getAttribute('data-query')));
          });

          function addMessage(text, type) {
              const div = document.createElement('div');
              div.className = 'ai-msg ' + type;
              div.innerHTML = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
              messages.appendChild(div);
              messages.scrollTop = messages.scrollHeight;
              return div;
          }

          function sendMessage(overrideText = null) {
              const text = overrideText || input.value.trim();
              if (!text) return;

              addMessage(text, 'user');
              if (!overrideText) input.value = '';

              const thinking = addMessage('🧠 Manager is working...', 'thinking');

              fetch('/admin/ai-chat', {
                  method: 'POST',
                  headers: {
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                  },
                  body: JSON.stringify({
                      message: text,
                      history: chatHistory
                  })
              })
              .then(res => res.json())
              .then(data => {
                  thinking.remove();
                  addMessage(data.reply, 'bot');
                  chatHistory = data.history || chatHistory;

                  if (data.redirect) {
                      setTimeout(() => { window.open(data.redirect, '_blank'); }, 1000);
                  }
              })
              .catch(() => {
                  thinking.remove();
                  addMessage('❌ Brain connection lost. Try again.', 'bot');
              });
          }

          // Voice Logic
          const micBtn = document.getElementById('ai-mic-btn');
          let recognition = null;
          if ('webkitSpeechRecognition' in window) {
              recognition = new webkitSpeechRecognition();
              recognition.lang = 'ur-PK';
              recognition.onstart = () => micBtn.classList.add('recording');
              recognition.onend = () => micBtn.classList.remove('recording');
              recognition.onresult = (e) => {
                  input.value = e.results[0][0].transcript;
                  sendMessage();
              };
              micBtn.onclick = () => recognition.start();
          }
      })();
      </script>
      @endif

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

  <!-- Select2 -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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

        // Auto-expand sidebar if any dropdown menu is clicked while minimized
        $('.sidebar .nav-link[data-toggle="collapse"]').on('click', function() {
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

  <!-- OneSignal Push Notifications Integration -->
  <script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
  <script>
    window.OneSignalDeferred = window.OneSignalDeferred || [];
    OneSignalDeferred.push(function(OneSignal) {
      OneSignal.init({
        appId: "46461a8a-1e8f-4f50-9561-967e52304cba",
        safari_web_id: "web.onesignal.auto.drautos",
        notifyButton: {
          enable: false, // Hidden the floating bell icon
        },
        allowLocalhostAsSecureOrigin: true
      });

      // Handle manual subscribe button click (if it still exists anywhere)
      $('#onesignal-manual-subscribe').on('click', async function(e) {
          e.preventDefault();
          
          if (typeof Notification === 'undefined') {
              alert("Oops! Your current browser or App wrapper DOES NOT support Web Push Notifications. Please open the website in Google Chrome.");
              return;
          }
          
          try {
              if (Notification.permission === 'granted') {
                  alert("Permissions already allowed! Forcing OneSignal to sync your device now...");
                  await OneSignal.User.PushSubscription.optIn();
              } else if (Notification.permission === 'denied') {
                  alert("Your browser is BLOCKING the prompt. Please click the lock icon in the URL bar, go to Site Settings, and change Notifications to Allow.");
              } else {
                  alert("Requesting permission from the browser now...");
                  await OneSignal.Notifications.requestPermission();
              }
              
              if (OneSignal.User.PushSubscription.optedIn) {
                  $('#onesignal-manual-subscribe').hide();
              } else {
                  $(this).find('i').addClass('text-danger');
              }
          } catch (err) {
              console.error(err);
              alert('An error occurred while enabling notifications.');
          }
      });

      // Hide the manual button if already subscribed
      OneSignal.User.PushSubscription.addEventListener("change", function(e) {
          if (OneSignal.User.PushSubscription.optedIn) {
              $('#onesignal-manual-subscribe').hide();
          }
      });
      
      if(OneSignal.User.PushSubscription.optedIn) {
          $('#onesignal-manual-subscribe').hide();
      }
    });
  </script>
