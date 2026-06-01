<!-- Floating Chat Widget -->
<style>
    :root {
        --chat-primary: #083259;
        --chat-accent: #facc15;
    }

    #chat-widget-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 9999;
        font-family: 'Inter', sans-serif;
    }

    #chat-widget-toggle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: var(--chat-primary);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        cursor: grab;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        transition: transform 0.2s;
        position: relative;
    }

    #chat-widget-toggle:active {
        cursor: grabbing;
    }

    #chat-widget-toggle:hover {
        transform: scale(1.05);
    }

    #chat-unread-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #ef4444;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 11px;
        font-weight: bold;
        display: none;
        align-items: center;
        justify-content: center;
    }

    #chat-widget-window {
        position: absolute;
        bottom: 75px;
        right: 0;
        width: 350px;
        height: 500px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.2);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        display: none;
        border: 1px solid #e2e8f0;
    }

    #chat-header {
        background: var(--chat-primary);
        color: #fff;
        padding: 15px;
        font-weight: bold;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: grab;
    }
    
    #chat-header:active {
        cursor: grabbing;
    }

    #chat-messages {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .chat-msg {
        max-width: 85%;
        padding: 10px 14px;
        border-radius: 12px;
        font-size: 13px;
        line-height: 1.4;
        position: relative;
        word-wrap: break-word;
    }

    .chat-msg.self {
        align-self: flex-end;
        background: var(--chat-primary);
        color: #fff;
        border-bottom-right-radius: 2px;
    }

    .chat-msg.other {
        align-self: flex-start;
        background: #e2e8f0;
        color: #1e293b;
        border-bottom-left-radius: 2px;
    }

    .chat-msg .sender-name {
        font-size: 10px;
        font-weight: 700;
        margin-bottom: 4px;
        opacity: 0.8;
    }

    .chat-msg.self .sender-name {
        display: none;
    }

    .chat-msg a {
        color: var(--chat-accent);
        text-decoration: underline;
        font-weight: bold;
    }
    
    .chat-msg.other a {
        color: var(--chat-primary);
    }

    #chat-input-area {
        padding: 12px;
        background: #fff;
        border-top: 1px solid #e2e8f0;
        display: flex;
        gap: 10px;
        align-items: flex-end;
    }

    #chat-input {
        flex: 1;
        resize: none;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 10px;
        font-size: 13px;
        height: 40px;
        max-height: 100px;
        outline: none;
        transition: border-color 0.2s;
    }

    #chat-input:focus {
        border-color: var(--chat-primary);
    }

    #chat-input.drag-over {
        border: 2px dashed var(--chat-accent);
        background: #fefce8;
    }
    
    #chat-mic-btn {
        background: none;
        border: none;
        color: #64748b;
        font-size: 18px;
        cursor: pointer;
        padding: 8px;
        transition: color 0.2s;
        height: 40px;
    }

    #chat-mic-btn:hover { color: var(--chat-primary); }
    
    #chat-mic-btn.recording {
        color: #ef4444;
        animation: pulse 1s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }

    #chat-send-btn {
        background: var(--chat-primary);
        color: white;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }

    #chat-send-btn:hover {
        background: #0e4a7a;
    }

    /* Custom Tooltip */
    .seen-by-tooltip {
        position: relative;
        display: inline-block;
    }
    
    .seen-by-tooltip .tooltip-text {
        visibility: hidden;
        width: max-content;
        max-width: 200px;
        background-color: #333;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px 8px;
        position: absolute;
        z-index: 1;
        bottom: 125%; 
        right: 0;
        margin-left: -60px;
        opacity: 0;
        transition: opacity 0.3s;
        font-size: 10px;
        font-weight: normal;
        pointer-events: none;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    
    .seen-by-tooltip .tooltip-text::after {
        content: "";
        position: absolute;
        top: 100%;
        right: 5px;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #333 transparent transparent transparent;
    }
    
    .seen-by-tooltip:hover .tooltip-text, .seen-by-tooltip.active .tooltip-text {
        visibility: visible;
        opacity: 1;
    }
</style>

<div id="chat-widget-container">
    <div id="chat-widget-window">
        <div id="chat-header">
            <div style="display: flex; align-items: center;">
                <span><i class="fas fa-users mr-2"></i> Team Chat</span>
                <div id="team-status-dropdown" style="position: relative; margin-left: 10px;">
                    <i class="fas fa-chevron-down" id="team-dropdown-btn" style="cursor: pointer; font-size: 12px; padding: 5px;" title="View Online Status"></i>
                    <div id="team-dropdown-menu" style="display: none; position: absolute; top: 25px; left: 0; background: white; color: #333; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); width: 220px; padding: 10px; z-index: 1000; font-size: 12px; font-weight: normal; cursor: default;">
                        <!-- Team status list populates here -->
                    </div>
                </div>
            </div>
            <i class="fas fa-times" id="chat-close-btn" style="cursor: pointer; padding: 5px;"></i>
        </div>
        <div id="chat-messages">
            <!-- Messages load here -->
        </div>
        <div id="chat-input-area">
            <textarea id="chat-input" placeholder="Type a message or drop a link..."></textarea>
            <button id="chat-mic-btn" title="Hold/Click to Record Voice Note"><i class="fas fa-microphone"></i></button>
            <button id="chat-send-btn"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>
    
    <div id="chat-widget-toggle">
        <i class="fas fa-comment-dots"></i>
        <div id="chat-unread-badge">0</div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('chat-widget-container');
    const toggleBtn = document.getElementById('chat-widget-toggle');
    const closeBtn = document.getElementById('chat-close-btn');
    const chatHeader = document.getElementById('chat-header');
    const chatWindow = document.getElementById('chat-widget-window');
    const messageContainer = document.getElementById('chat-messages');
    const chatInput = document.getElementById('chat-input');
    const sendBtn = document.getElementById('chat-send-btn');
    const micBtn = document.getElementById('chat-mic-btn');
    const unreadBadge = document.getElementById('chat-unread-badge');

    let isOpen = false;
    let lastMessageId = 0;
    let unreadCount = 0;
    let isInitialLoad = true;
    let teamData = [];
    const currentUserId = {{ Auth::id() ?? 0 }};

    // --- Audio Notification Synthesizer ---
    function playNotificationSound() {
        try {
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioCtx.createOscillator();
            const gainNode = audioCtx.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioCtx.destination);
            
            oscillator.type = 'sine';
            oscillator.frequency.setValueAtTime(880, audioCtx.currentTime); // A5
            oscillator.frequency.exponentialRampToValueAtTime(440, audioCtx.currentTime + 0.1);
            
            gainNode.gain.setValueAtTime(0.5, audioCtx.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.5);
            
            oscillator.start();
            oscillator.stop(audioCtx.currentTime + 0.5);
        } catch(e) {
            console.log("Audio API not supported");
        }
    }

    // --- Drag and Drop Widget Container ---
    let isDragging = false;
    let hasDragged = false;
    let dragStartX, dragStartY, initialX, initialY;

    function startDrag(e) {
        if(e.target === closeBtn) return; // Don't drag if clicking close
        isDragging = true;
        hasDragged = false;
        
        const clientX = e.type.includes('mouse') ? e.clientX : e.touches[0].clientX;
        const clientY = e.type.includes('mouse') ? e.clientY : e.touches[0].clientY;

        const rect = container.getBoundingClientRect();
        initialX = rect.left;
        initialY = rect.top;
        dragStartX = clientX;
        dragStartY = clientY;

        // Switch to left/top positioning relative to viewport
        container.style.right = 'auto';
        container.style.bottom = 'auto';
        container.style.left = initialX + 'px';
        container.style.top = initialY + 'px';
        
        document.addEventListener('mousemove', drag);
        document.addEventListener('mouseup', stopDrag);
        document.addEventListener('touchmove', drag, {passive: false});
        document.addEventListener('touchend', stopDrag);
    }

    function drag(e) {
        if (!isDragging) return;
        hasDragged = true;
        e.preventDefault(); // prevent scroll while dragging

        const clientX = e.type.includes('mouse') ? e.clientX : e.touches[0].clientX;
        const clientY = e.type.includes('mouse') ? e.clientY : e.touches[0].clientY;

        const dx = clientX - dragStartX;
        const dy = clientY - dragStartY;

        // Boundary checks to keep widget on screen
        let newX = initialX + dx;
        let newY = initialY + dy;
        
        const maxX = window.innerWidth - container.offsetWidth;
        const maxY = window.innerHeight - container.offsetHeight;

        newX = Math.max(0, Math.min(newX, maxX));
        newY = Math.max(0, Math.min(newY, maxY));

        container.style.left = newX + 'px';
        container.style.top = newY + 'px';
    }

    function stopDrag() {
        isDragging = false;
        document.removeEventListener('mousemove', drag);
        document.removeEventListener('mouseup', stopDrag);
        document.removeEventListener('touchmove', drag);
        document.removeEventListener('touchend', stopDrag);
    }

    // Bind drag to toggle button and chat header
    toggleBtn.addEventListener('mousedown', startDrag);
    toggleBtn.addEventListener('touchstart', startDrag, {passive: false});
    chatHeader.addEventListener('mousedown', startDrag);
    chatHeader.addEventListener('touchstart', startDrag, {passive: false});

    // Toggle window (only if it wasn't a drag)
    toggleBtn.addEventListener('click', (e) => {
        if (hasDragged) return; 
        isOpen = !isOpen;
        chatWindow.style.display = isOpen ? 'flex' : 'none';
        if (isOpen) {
            unreadCount = 0;
            unreadBadge.style.display = 'none';
            unreadBadge.innerText = '0';
            scrollToBottom();
            chatInput.focus();
            fetchMessages(); // Immediately sync read status and team data
        }
    });

    closeBtn.addEventListener('click', () => {
        isOpen = false;
        chatWindow.style.display = 'none';
        fetchMessages(); // Send closed status immediately
    });

    // --- Team Dropdown Logic ---
    const teamDropdownBtn = document.getElementById('team-dropdown-btn');
    const teamDropdownMenu = document.getElementById('team-dropdown-menu');

    teamDropdownBtn.addEventListener('click', (e) => {
        e.stopPropagation(); // prevent drag start
        teamDropdownMenu.style.display = teamDropdownMenu.style.display === 'none' ? 'block' : 'none';
        renderTeamDropdown();
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('#team-status-dropdown')) {
            teamDropdownMenu.style.display = 'none';
        }
    });
    
    teamDropdownMenu.addEventListener('mousedown', e => e.stopPropagation());
    teamDropdownMenu.addEventListener('touchstart', e => e.stopPropagation());

    function renderTeamDropdown() {
        if (teamData.length === 0) {
            teamDropdownMenu.innerHTML = '<div style="text-align:center; padding: 5px; color: #888;">Loading...</div>';
            return;
        }
        
        let html = '<div style="font-weight: bold; margin-bottom: 8px; border-bottom: 1px solid #eee; padding-bottom: 5px;">Team Status</div>';
        
        teamData.forEach(member => {
            const dotColor = member.is_online ? '#22c55e' : '#cbd5e1';
            html += `
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 4px 0;">
                    <div style="display: flex; align-items: center;">
                        <div style="width: 8px; height: 8px; border-radius: 50%; background-color: ${dotColor}; margin-right: 8px;"></div>
                        <span>${member.name}</span>
                    </div>
                    <span style="color: #64748b; font-size: 10px;">${member.last_active_str}</span>
                </div>
            `;
        });
        
        teamDropdownMenu.innerHTML = html;
    }

    // --- Voice Recording Logic ---
    let mediaRecorder;
    let audioChunks = [];
    let isRecording = false;
    let recordingMimeType = '';

    micBtn.addEventListener('click', async (e) => {
        e.preventDefault();
        if (!isRecording) {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                mediaRecorder = new MediaRecorder(stream);
                
                recordingMimeType = mediaRecorder.mimeType || 'audio/webm';
                
                mediaRecorder.ondataavailable = event => {
                    if (event.data.size > 0) {
                        audioChunks.push(event.data);
                    }
                };
                mediaRecorder.onstop = () => {
                    const audioBlob = new Blob(audioChunks, { type: recordingMimeType });
                    audioChunks = [];
                    sendVoiceNote(audioBlob, recordingMimeType);
                };
                mediaRecorder.start();
                isRecording = true;
                micBtn.classList.add('recording');
                chatInput.placeholder = "Recording voice note... Click mic to stop.";
                chatInput.disabled = true;
            } catch (err) {
                console.error(err);
                alert('Microphone access denied or not available.');
            }
        } else {
            mediaRecorder.stop();
            mediaRecorder.stream.getTracks().forEach(track => track.stop());
            isRecording = false;
            micBtn.classList.remove('recording');
            chatInput.placeholder = "Type a message or drop a link...";
            chatInput.disabled = false;
            chatInput.focus();
        }
    });

    // --- Chat Logic ---
    chatInput.addEventListener('input', function() {
        this.style.height = '40px';
        this.style.height = (this.scrollHeight) + 'px';
    });

    chatInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendTextMessage();
        }
    });

    sendBtn.addEventListener('click', sendTextMessage);

    function fetchMessages() {
        fetch(`{{ route('admin.chat.fetch') }}?last_id=${lastMessageId}&is_open=${isOpen ? '1' : '0'}`)
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    // Update team data
                    if (data.team) {
                        teamData = data.team;
                        if (teamDropdownMenu.style.display === 'block') {
                            renderTeamDropdown();
                        }
                        updateSeenByTooltips();
                    }

                    if (data.messages && data.messages.length > 0) {
                        let shouldScroll = isScrollAtBottom();
                        let playedSound = false;
                    
                        data.messages.forEach(msg => {
                            let isNewMsg = msg.id > lastMessageId;
                            if (isNewMsg) lastMessageId = msg.id;
                            
                            renderMessage(msg);
                            
                            // Handle unread badges and sounds for incoming NEW messages only
                            if (isNewMsg && !isInitialLoad && msg.user_id !== currentUserId) {
                                if (!isOpen) {
                                    unreadCount++;
                                    unreadBadge.style.display = 'flex';
                                    unreadBadge.innerText = unreadCount > 99 ? '99+' : unreadCount;
                                }
                                if (!playedSound) {
                                    playNotificationSound();
                                    playedSound = true; // only ping once per batch
                                }
                            }
                        });

                        if (shouldScroll || data.messages.some(m => m.user_id === currentUserId)) {
                            scrollToBottom();
                        }
                        
                        // Update tooltips immediately for newly rendered messages
                        updateSeenByTooltips();
                    }
                    
                    isInitialLoad = false;
                }
            })
            .catch(err => console.error('Chat poll error', err));
    }

    function updateSeenByTooltips() {
        const infoIcons = document.querySelectorAll('.chat-msg-info');
        infoIcons.forEach(iconContainer => {
            const msgId = parseInt(iconContainer.getAttribute('data-msg-id'));
            const senderId = parseInt(iconContainer.getAttribute('data-sender-id'));
            
            // Find who has seen this message (excluding the sender)
            let seenByNames = [];
            teamData.forEach(member => {
                if (member.id !== senderId && member.last_read_message_id >= msgId) {
                    seenByNames.push(member.name);
                }
            });
            
            const tooltipText = iconContainer.querySelector('.tooltip-text');
            if (seenByNames.length > 0) {
                iconContainer.style.display = 'inline-block';
                if(tooltipText) {
                    tooltipText.innerText = "Seen by: " + seenByNames.join(', ');
                }
            } else {
                iconContainer.style.display = 'none';
            }
        });
    }

    // Toggle tooltips on click for mobile/touch
    document.addEventListener('click', function(e) {
        if (e.target.closest('.seen-by-tooltip')) {
            const tooltip = e.target.closest('.seen-by-tooltip');
            tooltip.classList.toggle('active');
            setTimeout(() => tooltip.classList.remove('active'), 3000);
        } else {
            document.querySelectorAll('.seen-by-tooltip.active').forEach(t => t.classList.remove('active'));
        }
    });

    function sendTextMessage() {
        const text = chatInput.value.trim();
        if(!text) return;

        chatInput.value = '';
        chatInput.style.height = '40px';

        let formData = new FormData();
        formData.append('message', text);
        sendData(formData);
    }

    function sendVoiceNote(blob, mimeType) {
        let formData = new FormData();
        let ext = 'webm';
        if (mimeType) {
            if (mimeType.includes('mp4')) ext = 'mp4';
            else if (mimeType.includes('ogg')) ext = 'ogg';
            else if (mimeType.includes('wav')) ext = 'wav';
            else if (mimeType.includes('mpeg')) ext = 'mp3';
            else if (mimeType.includes('aac')) ext = 'aac';
        }
        
        formData.append('audio', blob, 'voicenote.' + ext);
        sendData(formData);
    }

    function sendData(formData) {
        fetch(`{{ route('admin.chat.send') }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                if (data.message.id > lastMessageId) {
                    lastMessageId = data.message.id;
                    renderMessage(data.message);
                    scrollToBottom();
                }
            } else {
                alert(data.message || 'Error sending message');
            }
        }).catch(err => console.error('Send error', err));
    }

    function renderMessage(msg) {
        if(document.getElementById('msg-'+msg.id)) return;

        let msgContent = '';
        
        if (msg.file_type === 'audio' && msg.file_path) {
            msgContent += `<audio controls src="${msg.file_path}" style="height: 40px; width: 220px; min-width: 220px; margin-bottom: ${msg.message ? '5px' : '0'};"></audio><br>`;
        }
        
        if (msg.message) {
            let formattedMsg = msg.message.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank">$1</a>');
            msgContent += formattedMsg.replace(/\n/g, '<br>');
        }
        
        let infoIcon = `
            <div class="seen-by-tooltip chat-msg-info" data-msg-id="${msg.id}" data-sender-id="${msg.user_id}" style="display: none; cursor: pointer;">
                <i class="fas fa-check-double" style="font-size: 10px; color: #3b82f6; margin-top: 4px;"></i>
                <span class="tooltip-text"></span>
            </div>
        `;

        let msgDiv = document.createElement('div');
        msgDiv.className = `chat-msg ${msg.user_id === currentUserId ? 'self' : 'other'}`;
        msgDiv.id = 'msg-'+msg.id;
        msgDiv.innerHTML = `
            <div class="sender-name">${msg.user ? msg.user.name : 'Unknown'} <span style="opacity:0.5; font-weight:normal; font-size:9px;">${msg.time_str}</span></div>
            <div class="msg-content">${msgContent}</div>
            <div style="text-align: right; line-height: 1;">${infoIcon}</div>
        `;
        messageContainer.appendChild(msgDiv);
    }

    function isScrollAtBottom() {
        return messageContainer.scrollHeight - messageContainer.scrollTop <= messageContainer.clientHeight + 50;
    }

    function scrollToBottom() {
        messageContainer.scrollTop = messageContainer.scrollHeight;
    }

    // --- Drag and Drop Links Logic ---
    chatWindow.addEventListener('dragover', (e) => {
        e.preventDefault();
        chatInput.classList.add('drag-over');
    });

    chatWindow.addEventListener('dragleave', (e) => {
        e.preventDefault();
        chatInput.classList.remove('drag-over');
    });

    chatWindow.addEventListener('drop', (e) => {
        e.preventDefault();
        chatInput.classList.remove('drag-over');
        
        let url = e.dataTransfer.getData('text/plain') || e.dataTransfer.getData('text/uri-list');
        
        if (url) {
            chatInput.value += (chatInput.value ? ' ' : '') + url;
            chatInput.focus();
            chatInput.style.height = (chatInput.scrollHeight) + 'px';
        }
    });

    // Initial load and polling
    fetchMessages();
    setInterval(fetchMessages, 4000);
});
</script>
