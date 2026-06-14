{{--
    Reusable Bluetooth Print Button Partial
    Include on any thermal print page with:  @include('backend.partials.bluetooth-print-btn')
    Requires:  html2canvas CDN + bluetooth-print.js  (loaded in page)
--}}
<div id="drautos-bt-panel" style="
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;
    font-family: 'Segoe UI', Arial, sans-serif;
">
    <button id="drautos-bt-btn" class="bt-idle" onclick="window.drautosBTPrint()" style="
        display: flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: #fff;
        border: none;
        border-radius: 50px;
        padding: 14px 24px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 8px 32px rgba(0,0,0,0.35), 0 2px 8px rgba(0,0,0,0.2);
        min-width: 220px;
        transition: all 0.2s ease;
        white-space: nowrap;
        -webkit-tap-highlight-color: transparent;
        user-select: none;
    ">
        {{-- Status dot --}}
        <span id="drautos-bt-dot" style="
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #94a3b8;
            flex-shrink: 0;
            transition: background 0.3s;
        "></span>

        {{-- Icon --}}
        <i id="drautos-bt-icon" class="fas fa-bluetooth-b" style="font-size: 18px; flex-shrink: 0;"></i>

        {{-- Label --}}
        <span id="drautos-bt-label" style="flex: 1; text-align: left; letter-spacing: 0.3px;">
            Bluetooth Print
        </span>

        {{-- Change printer icon --}}
        <span onclick="event.stopPropagation(); window.drautosChangePrinter();" title="Change printer" style="
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            cursor: pointer;
            font-size: 12px;
        ">
            <i class="fas fa-cog"></i>
        </span>
    </button>
</div>

<style>
    #drautos-bt-btn {
        transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.2s ease !important;
    }
    #drautos-bt-btn:active:not(:disabled) {
        transform: translateX(-50%) scale(0.97) !important;
        /* Override parent transform via wrapper */
    }
    #drautos-bt-btn:disabled {
        opacity: 0.85;
        cursor: not-allowed;
    }

    /* Scanning pulse animation */
    #drautos-bt-btn.bt-scanning {
        animation: bt-pulse 1.2s ease-in-out infinite;
    }
    @keyframes bt-pulse {
        0%,100% { box-shadow: 0 8px 32px rgba(0,0,0,0.35); }
        50%      { box-shadow: 0 8px 32px rgba(99,102,241,0.5), 0 0 0 6px rgba(99,102,241,0.15); }
    }

    /* Printing - indigo accent */
    #drautos-bt-btn.bt-printing {
        background: linear-gradient(135deg, #312e81 0%, #1e1b4b 100%) !important;
    }

    /* Success - green */
    #drautos-bt-btn.bt-success {
        background: linear-gradient(135deg, #065f46 0%, #064e3b 100%) !important;
    }

    /* Error - red */
    #drautos-bt-btn.bt-error {
        background: linear-gradient(135deg, #7f1d1d 0%, #450a0a 100%) !important;
    }
</style>

<script>
    // Allow user to change/forget printer
    window.drautosChangePrinter = function() {
        if(confirm('Forget saved printer and scan for a new one?')) {
            localStorage.removeItem('drautos_bt_name');
            // Force new device selection on next print
            window.btDevice = null;
            window.btCharacteristic = null;
            document.getElementById('drautos-bt-label').textContent = 'Bluetooth Print';
            document.getElementById('drautos-bt-dot').style.background = '#94a3b8';
        }
    };
</script>
