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
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        transition: transform 0.2s;
        position: relative;
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
</style>

<div id="chat-widget-container">
    <div id="chat-widget-window">
        <div id="chat-header">
            <span><i class="fas fa-users mr-2"></i> Team Chat</span>
            <i class="fas fa-times" id="chat-close-btn" style="cursor: pointer;"></i>
        </div>
        <div id="chat-messages">
            <!-- Messages load here -->
        </div>
        <div id="chat-input-area">
            <textarea id="chat-input" placeholder="Type a message or drop a link..."></textarea>
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
    const toggleBtn = document.getElementById('chat-widget-toggle');
    const closeBtn = document.getElementById('chat-close-btn');
    const chatWindow = document.getElementById('chat-widget-window');
    const messageContainer = document.getElementById('chat-messages');
    const chatInput = document.getElementById('chat-input');
    const sendBtn = document.getElementById('chat-send-btn');
    const unreadBadge = document.getElementById('chat-unread-badge');

    let isOpen = false;
    let lastMessageId = 0;
    let unreadCount = 0;
    const currentUserId = {{ Auth::id() ?? 0 }};

    // Toggle window
    toggleBtn.addEventListener('click', () => {
        isOpen = !isOpen;
        chatWindow.style.display = isOpen ? 'flex' : 'none';
        if (isOpen) {
            unreadCount = 0;
            unreadBadge.style.display = 'none';
            unreadBadge.innerText = '0';
            scrollToBottom();
            chatInput.focus();
        }
    });

    closeBtn.addEventListener('click', () => {
        isOpen = false;
        chatWindow.style.display = 'none';
    });

    // Auto resize textarea
    chatInput.addEventListener('input', function() {
        this.style.height = '40px';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // Send on Enter (Shift+Enter for new line)
    chatInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    sendBtn.addEventListener('click', sendMessage);

    // Fetch Messages Polling
    function fetchMessages() {
        fetch(`{{ route('admin.chat.fetch') }}?last_id=${lastMessageId}`)
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success' && data.messages.length > 0) {
                    let shouldScroll = isScrollAtBottom();
                    
                    data.messages.forEach(msg => {
                        if (msg.id > lastMessageId) lastMessageId = msg.id;
                        renderMessage(msg);
                        
                        if (!isOpen && msg.user_id !== currentUserId) {
                            unreadCount++;
                            unreadBadge.style.display = 'flex';
                            unreadBadge.innerText = unreadCount;
                        }
                    });

                    // Only force scroll if we were already at bottom or if we sent it
                    if (shouldScroll || data.messages.some(m => m.user_id === currentUserId)) {
                        scrollToBottom();
                    }
                }
            })
            .catch(err => console.error('Chat poll error', err));
    }

    function sendMessage() {
        const text = chatInput.value.trim();
        if(!text) return;

        chatInput.value = '';
        chatInput.style.height = '40px';

        fetch(`{{ route('admin.chat.send') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: text })
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                if (data.message.id > lastMessageId) {
                    lastMessageId = data.message.id;
                    renderMessage(data.message);
                    scrollToBottom();
                }
            }
        });
    }

    function renderMessage(msg) {
        // Prevent duplicates
        if(document.getElementById('msg-'+msg.id)) return;

        const isSelf = msg.user_id === currentUserId;
        const div = document.createElement('div');
        div.className = `chat-msg ${isSelf ? 'self' : 'other'}`;
        div.id = 'msg-'+msg.id;
        
        let userName = msg.user ? msg.user.name : 'Unknown';
        
        // Autolink URLs
        let formattedMsg = msg.message.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank">$1</a>');

        div.innerHTML = `
            <div class="sender-name">${userName}</div>
            <div class="msg-content">${formattedMsg.replace(/\n/g, '<br>')}</div>
        `;
        
        messageContainer.appendChild(div);
    }

    function isScrollAtBottom() {
        return messageContainer.scrollHeight - messageContainer.scrollTop <= messageContainer.clientHeight + 50;
    }

    function scrollToBottom() {
        messageContainer.scrollTop = messageContainer.scrollHeight;
    }

    // --- Drag and Drop Magic ---
    
    // Allow dropping on the whole window
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

    // Start polling every 4 seconds
    fetchMessages();
    setInterval(fetchMessages, 4000);
});
</script>
