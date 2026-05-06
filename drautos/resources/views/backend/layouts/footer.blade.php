
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
              <button id="ai-chat-close">✕</button>
          </div>

          {{-- Messages Area --}}
          <div id="ai-chat-messages">
              <div class="ai-msg bot">
                  👋 Hello! I'm your AI business assistant.<br><br>
                  You can ask me to:<br>
                  • <b>Update prices</b> — "Set TR Boot price to 450"<br>
                  • <b>Check orders</b> — "Show recent orders"<br>
                  • <b>Check stock</b> — "How much stock of Wheel Boot?"<br>
                  • <b>Download PDF</b> — "Download price list"<br><br>
                  I will always confirm before making any change! 🔒
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
              background: linear-gradient(135deg, #6366f1, #818cf8);
              color: white;
              border: none;
              cursor: pointer;
              box-shadow: 0 8px 25px rgba(99,102,241,0.5);
              display: flex;
              align-items: center;
              justify-content: center;
              z-index: 9999;
              transition: transform 0.3s, box-shadow 0.3s;
              animation: ai-pulse 2.5s infinite;
          }
          #ai-chat-trigger:hover {
              transform: scale(1.1);
              box-shadow: 0 12px 30px rgba(99,102,241,0.7);
              animation: none;
          }
          @keyframes ai-pulse {
              0%   { box-shadow: 0 0 0 0 rgba(99,102,241,0.5); }
              70%  { box-shadow: 0 0 0 12px rgba(99,102,241,0); }
              100% { box-shadow: 0 0 0 0 rgba(99,102,241,0); }
          }
          #ai-chat-window {
              position: fixed;
              bottom: 105px;
              right: 30px;
              width: 370px;
              max-height: 560px;
              background: #fff;
              border-radius: 20px;
              box-shadow: 0 20px 60px rgba(0,0,0,0.2);
              display: none;
              flex-direction: column;
              z-index: 9998;
              overflow: hidden;
              border: 1px solid #e0e7ff;
              animation: chat-slide-in 0.3s ease;
          }
          @keyframes chat-slide-in {
              from { opacity: 0; transform: translateY(20px) scale(0.95); }
              to   { opacity: 1; transform: translateY(0) scale(1); }
          }
          #ai-chat-header {
              background: linear-gradient(135deg, #4f46e5, #6366f1);
              color: white;
              padding: 16px 20px;
              display: flex;
              align-items: center;
              justify-content: space-between;
          }
          #ai-avatar {
              width: 38px;
              height: 38px;
              border-radius: 50%;
              background: rgba(255,255,255,0.2);
              display: flex;
              align-items: center;
              justify-content: center;
              font-weight: 800;
              font-size: 0.85rem;
              margin-right: 12px;
              border: 2px solid rgba(255,255,255,0.3);
          }
          #ai-chat-close {
              background: rgba(255,255,255,0.15);
              border: none;
              color: white;
              width: 30px;
              height: 30px;
              border-radius: 50%;
              cursor: pointer;
              font-size: 0.8rem;
          }
          #ai-chat-messages {
              flex: 1;
              overflow-y: auto;
              padding: 16px;
              display: flex;
              flex-direction: column;
              gap: 10px;
              max-height: 380px;
              background: #f8faff;
          }
          .ai-msg {
              max-width: 85%;
              padding: 10px 14px;
              border-radius: 16px;
              font-size: 0.85rem;
              line-height: 1.5;
              word-wrap: break-word;
          }
          .ai-msg.bot {
              background: white;
              color: #1e293b;
              align-self: flex-start;
              border-bottom-left-radius: 4px;
              box-shadow: 0 2px 8px rgba(0,0,0,0.07);
          }
          .ai-msg.user {
              background: linear-gradient(135deg, #6366f1, #818cf8);
              color: white;
              align-self: flex-end;
              border-bottom-right-radius: 4px;
          }
          .ai-msg.thinking {
              background: #e0e7ff;
              color: #6366f1;
              align-self: flex-start;
              font-style: italic;
          }
          #ai-chat-input-area {
              padding: 12px 16px;
              display: flex;
              gap: 8px;
              border-top: 1px solid #e0e7ff;
              background: white;
          }
          #ai-chat-input {
              flex: 1;
              border: 1.5px solid #e0e7ff;
              border-radius: 12px;
              padding: 10px 14px;
              font-size: 0.85rem;
              outline: none;
              transition: border-color 0.2s;
          }
          #ai-chat-input:focus { border-color: #6366f1; }
          #ai-chat-send {
              width: 42px;
              height: 42px;
              border-radius: 12px;
              background: linear-gradient(135deg, #6366f1, #818cf8);
              border: none;
              color: white;
              cursor: pointer;
              display: flex;
              align-items: center;
              justify-content: center;
              transition: transform 0.2s;
          }
          #ai-chat-send:hover { transform: scale(1.05); }

          /* Mic Button */
          #ai-mic-btn {
              width: 42px;
              height: 42px;
              border-radius: 12px;
              background: #f1f5f9;
              border: 1.5px solid #e0e7ff;
              color: #6366f1;
              cursor: pointer;
              display: flex;
              align-items: center;
              justify-content: center;
              transition: all 0.2s;
              flex-shrink: 0;
          }
          #ai-mic-btn:hover { background: #e0e7ff; }
          #ai-mic-btn.recording {
              background: #fee2e2;
              border-color: #ef4444;
              color: #ef4444;
              animation: mic-pulse 1s infinite;
          }
          @keyframes mic-pulse {
              0%   { box-shadow: 0 0 0 0 rgba(239,68,68,0.4); }
              70%  { box-shadow: 0 0 0 8px rgba(239,68,68,0); }
              100% { box-shadow: 0 0 0 0 rgba(239,68,68,0); }
          }

          @media (max-width: 480px) {
              #ai-chat-window { width: calc(100vw - 20px); right: 10px; bottom: 100px; }
          }
      </style>

      {{-- Script --}}
      <script>
      (function() {
          let pendingAction = null;
          const chatWindow  = document.getElementById('ai-chat-window');
          const trigger     = document.getElementById('ai-chat-trigger');
          const closeBtn    = document.getElementById('ai-chat-close');
          const input       = document.getElementById('ai-chat-input');
          const sendBtn     = document.getElementById('ai-chat-send');
          const messages    = document.getElementById('ai-chat-messages');

          trigger.addEventListener('click', () => {
              chatWindow.style.display = chatWindow.style.display === 'flex' ? 'none' : 'flex';
          });
          closeBtn.addEventListener('click', () => { chatWindow.style.display = 'none'; });

          sendBtn.addEventListener('click', sendMessage);
          input.addEventListener('keydown', (e) => { if (e.key === 'Enter') sendMessage(); });

          function addMessage(text, type) {
              const div = document.createElement('div');
              div.className = 'ai-msg ' + type;
              div.innerHTML = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
              messages.appendChild(div);
              messages.scrollTop = messages.scrollHeight;
              return div;
          }

          function sendMessage() {
              const text = input.value.trim();
              if (!text) return;

              addMessage(text, 'user');
              input.value = '';

              const thinking = addMessage('⏳ Thinking...', 'thinking');

              fetch('/admin/ai-chat', {
                  method: 'POST',
                  headers: {
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                  },
                  body: JSON.stringify({
                      message: text,
                      pending_action: pendingAction
                  })
              })
              .then(res => res.json())
              .then(data => {
                  thinking.remove();
                  addMessage(data.reply, 'bot');

                  if (data.needs_confirm && data.action) {
                      pendingAction = data.action;
                  } else {
                      pendingAction = null;
                  }

                  if (data.redirect) {
                      setTimeout(() => { window.open(data.redirect, '_blank'); }, 800);
                  }
              })
              .catch(() => {
                  thinking.remove();
                  addMessage('❌ Connection error. Please try again.', 'bot');
                  pendingAction = null;
              });
          }

          // ===== Voice / Mic Button =====
          const micBtn = document.getElementById('ai-mic-btn');
          let recognition = null;
          let isRecording = false;

          if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
              const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
              recognition = new SpeechRecognition();
              recognition.continuous = false;
              recognition.interimResults = true;
              // Support both Urdu and English
              recognition.lang = 'ur-PK';

              recognition.onstart = function() {
                  isRecording = true;
                  micBtn.classList.add('recording');
                  input.placeholder = '🔴 Listening... (Urdu / English)';
              };

              recognition.onresult = function(event) {
                  let transcript = '';
                  for (let i = event.resultIndex; i < event.results.length; i++) {
                      transcript += event.results[i][0].transcript;
                  }
                  input.value = transcript;
              };

              recognition.onend = function() {
                  isRecording = false;
                  micBtn.classList.remove('recording');
                  input.placeholder = 'Type or tap 🎤 to speak...';
                  // Auto-send if something was captured
                  if (input.value.trim()) {
                      setTimeout(sendMessage, 400);
                  }
              };

              recognition.onerror = function(e) {
                  isRecording = false;
                  micBtn.classList.remove('recording');
                  input.placeholder = 'Type or tap 🎤 to speak...';
                  if (e.error !== 'no-speech') {
                      addMessage('🎤 Mic error: ' + e.error + '. Try typing instead.', 'bot');
                  }
              };

              micBtn.addEventListener('click', function() {
                  if (isRecording) {
                      recognition.stop();
                  } else {
                      recognition.start();
                  }
              });
          } else {
              // Browser doesn't support voice
              micBtn.title = 'Voice not supported in this browser. Use Chrome.';
              micBtn.addEventListener('click', function() {
                  addMessage('🎤 Voice input requires Google Chrome browser. Please use Chrome on your phone or computer.', 'bot');
              });
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
