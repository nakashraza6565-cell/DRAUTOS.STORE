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
            background: #818cf8;
            flex-shrink: 0;
            transition: background 0.3s;
        "></span>

        {{-- Icon --}}
        <i id="drautos-bt-icon" class="fas fa-print" style="font-size: 18px; flex-shrink: 0;"></i>

        {{-- Label --}}
        <span id="drautos-bt-label" style="flex: 1; text-align: left; letter-spacing: 0.3px;">
            Print via RawBT
        </span>

        {{-- Settings / Change printer icon --}}
        <span onclick="event.stopPropagation(); window.drautosOpenSettings();" title="Printer Settings" style="
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
            transition: background 0.2s;
        " onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
            <i class="fas fa-cog"></i>
        </span>
    </button>
</div>

{{-- Modern Settings Modal --}}
<div id="drautos-bt-settings-modal" style="
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    z-index: 10000;
    align-items: center;
    justify-content: center;
    font-family: 'Segoe UI', Arial, sans-serif;
">
    <div style="
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 24px;
        padding: 24px;
        width: 90%;
        max-width: 400px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        animation: drautos-modal-fade-in 0.25s ease-out;
        position: relative;
    ">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0; font-size: 18px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-cog text-primary"></i> Printer Settings
            </h3>
            <span onclick="window.drautosCloseSettings()" style="cursor: pointer; opacity: 0.7; font-size: 24px; line-height: 1; padding: 4px;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'">&times;</span>
        </div>

        <!-- Print Mode Select -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; opacity: 0.6; margin-bottom: 10px; font-weight: 700;">
                Print Method
            </label>
            
            <!-- Option RawBT -->
            <div id="mode-option-rawbt" onclick="window.drautosSetPrintMode('rawbt')" style="
                display: flex;
                align-items: flex-start;
                gap: 12px;
                background: rgba(255,255,255,0.05);
                border: 2px solid #6366f1;
                border-radius: 12px;
                padding: 12px;
                cursor: pointer;
                transition: all 0.2s;
                margin-bottom: 10px;
            ">
                <input type="radio" name="print_mode" id="mode-rawbt" value="rawbt" checked style="margin-top: 4px; pointer-events: none;">
                <div>
                    <strong style="display: block; font-size: 14px;">RawBT App (Recommended)</strong>
                    <span style="display: block; font-size: 11px; opacity: 0.7; margin-top: 3px; line-height: 1.4;">
                        Works with all classic & paired Bluetooth/USB printers. Requires installing 'RawBT' from Play Store.
                    </span>
                </div>
            </div>

            <!-- Option BLE -->
            <div id="mode-option-ble" onclick="window.drautosSetPrintMode('ble')" style="
                display: flex;
                align-items: flex-start;
                gap: 12px;
                background: rgba(255,255,255,0.02);
                border: 2px solid rgba(255,255,255,0.05);
                border-radius: 12px;
                padding: 12px;
                cursor: pointer;
                transition: all 0.2s;
            ">
                <input type="radio" name="print_mode" id="mode-ble" value="ble" style="margin-top: 4px; pointer-events: none;">
                <div>
                    <strong style="display: block; font-size: 14px;">Direct BLE (Web Bluetooth)</strong>
                    <span id="ble-subtext" style="display: block; font-size: 11px; opacity: 0.7; margin-top: 3px; line-height: 1.4;">
                        Direct browser connection. Only works with Bluetooth Low Energy (BLE) compatible printers.
                    </span>
                </div>
            </div>
        </div>

        <!-- Action Section -->
        <div id="settings-action-section" style="margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px;">
            <a href="https://play.google.com/store/apps/details?id=ru.a402d.rawbtprinter" target="_blank" id="rawbt-install-btn" style="
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                background: rgba(255,255,255,0.08);
                color: #fff;
                border: 1px solid rgba(255,255,255,0.1);
                border-radius: 8px;
                padding: 10px;
                font-size: 13px;
                text-decoration: none;
                font-weight: 600;
                transition: background 0.2s;
            " onmouseover="this.style.background='rgba(255,255,255,0.15)'" onmouseout="this.style.background='rgba(255,255,255,0.08)'">
                <i class="fab fa-google-play" style="color: #60a5fa;"></i> Install RawBT from Play Store
            </a>

            <button id="ble-forget-btn" onclick="window.drautosForgetBle()" style="
                display: none;
                width: 100%;
                align-items: center;
                justify-content: center;
                gap: 8px;
                background: #ef4444;
                color: #fff;
                border: none;
                border-radius: 8px;
                padding: 10px;
                font-size: 13px;
                font-weight: 600;
                cursor: pointer;
                transition: background 0.2s;
            " onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">
                <i class="fas fa-trash-alt"></i> Forget Saved BLE Printer
            </button>
        </div>

        <button onclick="window.drautosCloseSettings()" style="
            width: 100%;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 20px;
            box-shadow: 0 4px 15px rgba(99,102,241,0.4);
            transition: all 0.2s;
        " onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 20px rgba(99,102,241,0.6)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 15px rgba(99,102,241,0.4)';">
            Done
        </button>
    </div>
</div>

<style>
    #drautos-bt-btn {
        transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.2s ease !important;
    }
    #drautos-bt-btn:active:not(:disabled) {
        transform: scale(0.97) !important;
    }
    #drautos-bt-btn:disabled {
        opacity: 0.85;
        cursor: not-allowed;
    }

    /* Scanning pulse animation */
    #drautos-bt-btn.bt-scanning {
        animation: drautos-bt-pulse 1.2s ease-in-out infinite;
    }
    @keyframes drautos-bt-pulse {
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

    @keyframes drautos-modal-fade-in {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
</style>
