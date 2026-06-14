/**
 * DRAUTOS Web Bluetooth & RawBT Thermal Printer Engine
 * ====================================================
 * Direct BLE printing for SpeedX 80mm BLE printers, and RawBT App
 * intent integration for all classic Bluetooth / USB printers.
 *
 * Paper: 80mm | 576 printable dots wide | 203 DPI
 */
(function (window) {
    'use strict';

    // ─── Printer constants ────────────────────────────────────────────────────
    const DOTS_PER_ROW  = 576;   // 80mm printable width at 203 DPI
    const BYTES_PER_ROW = DOTS_PER_ROW / 8; // 72 bytes
    const CHUNK_SIZE    = 100;   // bytes per BLE write (safe for SpeedX)
    const CHUNK_DELAY   = 30;    // ms between chunks (prevents buffer overflow)

    // ─── BLE service/characteristic profiles (SpeedX first, then fallbacks) ──
    const BLE_PROFILES = [
        // Profile A – most common for SpeedX / Chinese OEM 80mm
        { service: '000018f0-0000-1000-8000-00805f9b34fb', char: '00002af1-0000-1000-8000-00805f9b34fb', name: 'SpeedX/Xprinter' },
        // Profile B – HSPOS, MUNBYN, some rebrands
        { service: '0000ff00-0000-1000-8000-00805f9b34fb', char: '0000ff02-0000-1000-8000-00805f9b34fb', name: 'HSPOS/MUNBYN' },
        // Profile C – PT-series clones
        { service: 'e7810a71-73ae-499d-8c15-faa9aef0c3f2', char: 'bef8d6c9-9c21-4c9e-b632-bd58c1009f9f', name: 'PT-series' },
        // Profile D – HM-10 BLE module printers
        { service: '0000ffe0-0000-1000-8000-00805f9b34fb', char: '0000ffe1-0000-1000-8000-00805f9b34fb', name: 'HM-10 Module' },
    ];

    // ─── State ────────────────────────────────────────────────────────────────
    let btDevice         = null;
    let btCharacteristic = null;
    let isPrinting       = false;

    // ─── Helper: sleep ────────────────────────────────────────────────────────
    function sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    // ─── Helper: Convert Uint8Array to Base64 (Safe for large receipts) ───────
    function uint8ArrayToBase64(uint8) {
        let binary = '';
        const len = uint8.byteLength;
        const chunk = 8192;
        if (len < chunk) {
            for (let i = 0; i < len; i++) {
                binary += String.fromCharCode(uint8[i]);
            }
            return window.btoa(binary);
        }
        for (let i = 0; i < len; i += chunk) {
            const sub = uint8.subarray(i, Math.min(i + chunk, len));
            binary += String.fromCharCode.apply(null, sub);
        }
        return window.btoa(binary);
    }

    // ─── UI State Manager ─────────────────────────────────────────────────────
    function setState(state, message) {
        const btn    = document.getElementById('drautos-bt-btn');
        const label  = document.getElementById('drautos-bt-label');
        const icon   = document.getElementById('drautos-bt-icon');
        const dot    = document.getElementById('drautos-bt-dot');
        if (!btn) return;

        btn.disabled = false;
        const states = ['bt-idle', 'bt-scanning', 'bt-printing', 'bt-success', 'bt-error', 'bt-unsupported'];
        states.forEach(s => btn.classList.remove(s));

        const mode = localStorage.getItem('drautos_print_mode') || 'rawbt';

        switch (state) {
            case 'idle':
                btn.classList.add('bt-idle');
                icon.className  = mode === 'rawbt' ? 'fas fa-print' : 'fas fa-bluetooth-b';
                label.textContent = message || (mode === 'rawbt' ? 'Print via RawBT' : 'Bluetooth Print');
                dot.style.background = mode === 'rawbt' ? '#818cf8' : '#94a3b8';
                break;
            case 'scanning':
            case 'connecting':
                btn.classList.add('bt-scanning');
                icon.className  = 'fas fa-spinner fa-spin';
                label.textContent = state === 'scanning' ? 'Scanning for printer...' : 'Connecting...';
                btn.disabled = true;
                dot.style.background = '#f59e0b';
                break;
            case 'printing':
                btn.classList.add('bt-printing');
                icon.className  = 'fas fa-circle-notch fa-spin';
                label.textContent = message || 'Sending to printer...';
                btn.disabled = true;
                dot.style.background = '#6366f1';
                break;
            case 'success':
                btn.classList.add('bt-success');
                icon.className  = 'fas fa-check-circle';
                label.textContent = message || 'Printed successfully!';
                dot.style.background = '#10b981';
                setTimeout(() => {
                    updateUIForCurrentMode();
                }, 3000);
                break;
            case 'connected':
                btn.classList.add('bt-idle');
                icon.className  = 'fas fa-print';
                label.textContent = message || 'Tap to print';
                dot.style.background = '#10b981';
                break;
            case 'error':
                btn.classList.add('bt-error');
                icon.className  = 'fas fa-exclamation-triangle';
                label.textContent = message || 'Failed — tap to retry';
                dot.style.background = '#ef4444';
                break;
            case 'unsupported':
                btn.classList.add('bt-unsupported');
                icon.className  = 'fas fa-times-circle';
                label.textContent = 'Bluetooth not supported on this browser';
                btn.disabled = true;
                dot.style.background = '#ef4444';
                break;
        }
    }

    function updateUIForCurrentMode() {
        const mode = localStorage.getItem('drautos_print_mode') || 'rawbt';
        if (mode === 'rawbt') {
            setState('idle', 'Print via RawBT');
        } else {
            const savedName = localStorage.getItem('drautos_bt_name');
            setState('idle', savedName ? `${savedName} — tap to print` : 'Bluetooth Print');
        }
    }

    // ─── BLE Connection ───────────────────────────────────────────────────────
    function onDisconnected() {
        btCharacteristic = null;
        const name = localStorage.getItem('drautos_bt_name') || 'Printer';
        setState('idle', `${name} — tap to reconnect`);
    }

    async function connectGatt(server) {
        for (const profile of BLE_PROFILES) {
            try {
                const service = await server.getPrimaryService(profile.service);
                const chr     = await service.getCharacteristic(profile.char);
                console.log('[DRAUTOS-BT] Connected using profile:', profile.name);
                return chr;
            } catch (e) {
                // try next profile
            }
        }
        throw new Error('No compatible print service found.\nMake sure your SpeedX printer is ON and in Bluetooth mode.');
    }

    async function ensureConnected() {
        if (btCharacteristic && btDevice && btDevice.gatt.connected) return;

        setState('scanning');
        const optionalServices = BLE_PROFILES.map(p => p.service);

        btDevice = await navigator.bluetooth.requestDevice({
            acceptAllDevices: true,
            optionalServices: optionalServices,
        });

        btDevice.addEventListener('gattserverdisconnected', onDisconnected);

        const printerName = btDevice.name || 'SpeedX Printer';
        localStorage.setItem('drautos_bt_name', printerName);

        setState('connecting', `Connecting to ${printerName}...`);
        const server       = await btDevice.gatt.connect();
        btCharacteristic   = await connectGatt(server);
    }

    // ─── BLE Write (chunked) ──────────────────────────────────────────────────
    async function writeAll(data) {
        const total = data.length;
        for (let offset = 0; offset < total; offset += CHUNK_SIZE) {
            const chunk = data.slice(offset, Math.min(offset + CHUNK_SIZE, total));
            await btCharacteristic.writeValue(chunk);
            await sleep(CHUNK_DELAY);
            const pct = Math.min(100, Math.round(((offset + CHUNK_SIZE) / total) * 100));
            setState('printing', `Sending... ${pct}%`);
        }
    }

    // ─── ESC/POS: Image command builder ──────────────────────────────────────
    function buildRasterCmd(pixels, canvasWidth, canvasHeight) {
        const printWidth  = Math.min(canvasWidth, DOTS_PER_ROW);
        const bytesPerRow = Math.ceil(printWidth / 8);
        const rows        = canvasHeight;

        // GS v 0 header
        const header = new Uint8Array([
            0x1D, 0x76, 0x30, 0x00,       // GS v 0, density=normal
            bytesPerRow & 0xFF,           // xL (bytes per row, low byte)
            (bytesPerRow >> 8) & 0xFF,    // xH (bytes per row, high byte)
            rows & 0xFF,                  // yL (rows, low byte)
            (rows >> 8) & 0xFF,           // yH (rows, high byte)
        ]);

        const imgBytes = new Uint8Array(bytesPerRow * rows);

        for (let y = 0; y < rows; y++) {
            for (let bx = 0; bx < bytesPerRow; bx++) {
                let byte = 0;
                for (let bit = 0; bit < 8; bit++) {
                    const x = bx * 8 + bit;
                    if (x < printWidth) {
                        const idx = (y * canvasWidth + x) * 4;
                        // Luminance (greyscale)
                        const lum = pixels[idx] * 0.299 + pixels[idx + 1] * 0.587 + pixels[idx + 2] * 0.114;
                        // Threshold: dark = print dot (bit=1)
                        if (lum < 160) byte |= (0x80 >> bit);
                    }
                }
                imgBytes[y * bytesPerRow + bx] = byte;
            }
        }

        const cmd = new Uint8Array(header.length + imgBytes.length);
        cmd.set(header, 0);
        cmd.set(imgBytes, header.length);
        return cmd;
    }

    // ─── Main capture + print pipeline ───────────────────────────────────────
    async function captureAndPrint() {
        const panel = document.getElementById('drautos-bt-panel');
        if (panel) panel.style.display = 'none';

        try {
            const mode = localStorage.getItem('drautos_print_mode') || 'rawbt';
            setState('printing', mode === 'rawbt' ? 'Rendering with RawBT...' : 'Capturing receipt...');

            // Calculate scale: target 576px canvas width from current body width
            const bodyWidth = document.body.scrollWidth || document.documentElement.scrollWidth;
            const scale     = DOTS_PER_ROW / bodyWidth;

            const canvas = await html2canvas(document.body, {
                scale           : scale,
                useCORS         : false,   // avoid CORS blocks on QR images
                allowTaint      : true,
                backgroundColor : '#ffffff',
                logging         : false,
                imageTimeout    : 5000,
                windowWidth     : bodyWidth,
                scrollX         : 0,
                scrollY         : 0,
            });

            const ctx     = canvas.getContext('2d');
            const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);

            // Build full ESC/POS job
            const init    = new Uint8Array([0x1B, 0x40]);                      // ESC @ – init
            const raster  = buildRasterCmd(imgData.data, canvas.width, canvas.height);
            const feed    = new Uint8Array([0x0A, 0x0A, 0x0A, 0x0A, 0x0A]);   // 5 line feeds
            const cut     = new Uint8Array([0x1D, 0x56, 0x41, 0x10]);         // GS V – partial cut

            const jobSize = init.length + raster.length + feed.length + cut.length;
            const job     = new Uint8Array(jobSize);
            let off = 0;
            job.set(init,   off); off += init.length;
            job.set(raster, off); off += raster.length;
            job.set(feed,   off); off += feed.length;
            job.set(cut,    off);

            if (mode === 'rawbt') {
                setState('printing', 'Opening RawBT App...');
                const base64 = uint8ArrayToBase64(job);
                window.location.href = "intent:base64," + base64 + "#Intent;scheme=rawbt;package=ru.a402d.rawbtprinter;end;";
                setState('success', 'Sent to RawBT App ✓');
            } else {
                await writeAll(job);
                const name = localStorage.getItem('drautos_bt_name') || 'Printer';
                setState('success', `Printed via ${name} ✓`);
            }

        } finally {
            if (panel) panel.style.display = '';
        }
    }

    // ─── Settings Modal Controllers ───────────────────────────────────────────
    window.drautosOpenSettings = function() {
        const modal = document.getElementById('drautos-bt-settings-modal');
        if (!modal) return;
        modal.style.display = 'flex';

        const hasBt = 'bluetooth' in navigator;
        const bleOpt = document.getElementById('mode-option-ble');
        const bleSub = document.getElementById('ble-subtext');
        
        if (!hasBt) {
            if (bleOpt) {
                bleOpt.style.opacity = '0.5';
                bleOpt.style.cursor = 'not-allowed';
                bleOpt.onclick = null;
            }
            if (bleSub) {
                bleSub.textContent = 'Direct BLE connection (Not supported on this browser/device)';
            }
            // Force setting mode to rawbt if BLE is not supported
            localStorage.setItem('drautos_print_mode', 'rawbt');
        }

        const mode = localStorage.getItem('drautos_print_mode') || 'rawbt';
        window.drautosSetPrintMode(mode, true);
    };

    window.drautosCloseSettings = function() {
        const modal = document.getElementById('drautos-bt-settings-modal');
        if (modal) modal.style.display = 'none';
        updateUIForCurrentMode();
    };

    window.drautosSetPrintMode = function(mode, skipSave = false) {
        if (mode === 'ble' && !('bluetooth' in navigator)) {
            return; // Block selection
        }

        if (!skipSave) {
            localStorage.setItem('drautos_print_mode', mode);
        }

        const rawbtRadio = document.getElementById('mode-rawbt');
        const bleRadio = document.getElementById('mode-ble');
        if (rawbtRadio) rawbtRadio.checked = (mode === 'rawbt');
        if (bleRadio) bleRadio.checked = (mode === 'ble');

        const rawbtCard = document.getElementById('mode-option-rawbt');
        const bleCard = document.getElementById('mode-option-ble');

        if (rawbtCard) {
            if (mode === 'rawbt') {
                rawbtCard.style.border = '2px solid #6366f1';
                rawbtCard.style.background = 'rgba(255, 255, 255, 0.05)';
            } else {
                rawbtCard.style.border = '2px solid rgba(255, 255, 255, 0.05)';
                rawbtCard.style.background = 'rgba(255, 255, 255, 0.02)';
            }
        }

        if (bleCard && ('bluetooth' in navigator)) {
            if (mode === 'ble') {
                bleCard.style.border = '2px solid #6366f1';
                bleCard.style.background = 'rgba(255, 255, 255, 0.05)';
            } else {
                bleCard.style.border = '2px solid rgba(255, 255, 255, 0.05)';
                bleCard.style.background = 'rgba(255, 255, 255, 0.02)';
            }
        }

        const installBtn = document.getElementById('rawbt-install-btn');
        const forgetBtn = document.getElementById('ble-forget-btn');
        if (installBtn) installBtn.style.display = mode === 'rawbt' ? 'flex' : 'none';
        if (forgetBtn) forgetBtn.style.display = mode === 'ble' ? 'flex' : 'none';
    };

    window.drautosForgetBle = function() {
        if (confirm('Forget saved BLE printer and scan for a new one next time?')) {
            localStorage.removeItem('drautos_bt_name');
            btDevice = null;
            btCharacteristic = null;
            window.drautosCloseSettings();
        }
    };

    // ─── Public API ───────────────────────────────────────────────────────────
    window.drautosBTPrint = async function () {
        if (isPrinting) return;
        isPrinting = true;

        try {
            const currentMode = localStorage.getItem('drautos_print_mode') || 'rawbt';
            
            if (currentMode === 'ble') {
                if (!('bluetooth' in navigator)) {
                    setState('unsupported');
                    return;
                }
                await ensureConnected();
            }
            
            await captureAndPrint();
        } catch (err) {
            console.error('[DRAUTOS-BT] Error:', err);
            const msg = err.message || 'Unknown error';
            if (err.name === 'NotFoundError') {
                const name = localStorage.getItem('drautos_bt_name') || '';
                setState('idle', name ? `${name} — tap to print` : 'Bluetooth Print');
            } else {
                setState('error', msg.length > 50 ? msg.substring(0, 50) + '…' : msg);
            }
        } finally {
            isPrinting = false;
        }
    };

    // ─── Init on DOM ready ────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.getElementById('drautos-bt-btn');
        if (!btn) return;

        const currentMode = localStorage.getItem('drautos_print_mode') || 'rawbt';
        if (currentMode === 'ble' && !('bluetooth' in navigator)) {
            localStorage.setItem('drautos_print_mode', 'rawbt');
        }

        updateUIForCurrentMode();
        btn.addEventListener('click', window.drautosBTPrint);
    });

})(window);
