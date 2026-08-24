{{-- PWA Install Banner Component --}}
<div id="skoracare-pwa-install-container" style="display: none; position: fixed; bottom: 20px; right: 20px; z-index: 99999; max-width: 360px; font-family: 'Inter', system-ui, sans-serif;">
    <div style="background: rgba(13, 110, 253, 0.95); backdrop-filter: blur(10px); color: #ffffff; border-radius: 16px; padding: 16px 20px; box-shadow: 0 10px 25px rgba(13, 110, 253, 0.35); border: 1px solid rgba(255, 255, 255, 0.2); display: flex; align-items: center; gap: 14px;">
        <img src="{{ asset('icon-192x192.png') }}" alt="Skoracare Icon" style="width: 48px; height: 48px; border-radius: 12px; object-fit: cover; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);">
        <div style="flex: 1;">
            <h6 style="margin: 0; font-size: 15px; font-weight: 700; line-height: 1.2;">Install PMS</h6>
            <p style="margin: 3px 0 0 0; font-size: 12px; opacity: 0.9; line-height: 1.3;">Install for quick access & offline support</p>
        </div>
        <div style="display: flex; gap: 8px; align-items: center;">
            <button id="pwa-install-btn" style="background: #ffffff; color: #0d6efd; border: none; padding: 8px 14px; border-radius: 8px; font-weight: 700; font-size: 13px; cursor: pointer; transition: all 0.2s ease;">
                Install
            </button>
            <button id="pwa-dismiss-btn" style="background: transparent; border: none; color: rgba(255,255,255,0.7); font-size: 18px; cursor: pointer; padding: 0 4px; line-height: 1;" title="Dismiss">&times;</button>
        </div>
    </div>
</div>

<script>
(function() {
    let deferredPrompt = null;
    const container = document.getElementById('skoracare-pwa-install-container');
    const installBtn = document.getElementById('pwa-install-btn');
    const dismissBtn = document.getElementById('pwa-dismiss-btn');

    if (!container || !installBtn) return;

    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        if (!localStorage.getItem('pwa_banner_dismissed')) {
            container.style.display = 'block';
        }
    });

    installBtn.addEventListener('click', async () => {
        if (!deferredPrompt) return;
        container.style.display = 'none';
        deferredPrompt.prompt();
        const { outcome } = await deferredPrompt.userChoice;
        console.log('[Skoracare PWA] Install choice:', outcome);
        deferredPrompt = null;
    });

    if (dismissBtn) {
        dismissBtn.addEventListener('click', () => {
            container.style.display = 'none';
            localStorage.setItem('pwa_banner_dismissed', 'true');
        });
    }
})();
</script>
