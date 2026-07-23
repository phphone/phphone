// public/js/kie.js

document.addEventListener('DOMContentLoaded', () => {
    // INTERCEPTOR UNIVERSAL DE ENLACES
    document.body.addEventListener('click', async (e) => {
        const link = e.target.closest('a');
        
        // Omitir si no es un enlace o si es un enlace externo
        if (!link || link.getAttribute('href').startsWith('http') || link.getAttribute('href').startsWith('//')) {
            return;
        }
        
        const urlDestino = link.getAttribute('href');
        const mainContent = document.getElementById('main-content');
        
        // Si no hay contenedor main-content, dejar que el navegador cambie de página nativamente
        if (!mainContent) {
            return;
        }

        e.preventDefault();

        try {
            const response = await fetch(urlDestino, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            
            if (!response.ok) throw new Error(`Error Phphone: ${response.status}`);
            
            const htmlRecibido = await response.text();
            
            // Inyectar el fragmento dentro del contenedor
            mainContent.innerHTML = htmlRecibido;
            
            // Actualizar el historial de navegación sin recargar
            window.history.pushState({}, '', urlDestino);
        } catch (error) {
            console.error('Phphone Routing Error:', error);
        }
    });
});

// Phphone Framework Namespace
window.Kie = window.Kie || {};

window.Kie.enableDebugMonitor = function() {
    if (document.getElementById('phphone-debug-panel')) return;

    // Inyectar estilos Glassmorphism
    const style = document.createElement('style');
    style.innerHTML = `
        #phphone-debug-panel {
            position: fixed;
            top: 10px;
            right: 10px;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 10px 15px;
            color: #f8fafc;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            font-size: 12px;
            z-index: 999999;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            gap: 5px;
            pointer-events: none;
        }
        .phphone-debug-row {
            display: flex;
            justify-content: space-between;
            gap: 15px;
        }
        .kie-debug-label { color: #94a3b8; font-weight: 600; }
        .kie-debug-val { font-weight: bold; font-variant-numeric: tabular-nums; }
        .kie-val-fps { color: #10b981; }
        .kie-val-ram { color: #3b82f6; }
        .kie-val-cpu { color: #f59e0b; }
    `;
    document.head.appendChild(style);

    // Inyectar Panel
    const panel = document.createElement('div');
    panel.id = 'phphone-debug-panel';
    panel.innerHTML = `
        <div class="phphone-debug-row"><span class="kie-debug-label">FPS</span><span class="kie-debug-val kie-val-fps" id="kie-fps-val">--</span></div>
        <div class="phphone-debug-row"><span class="kie-debug-label">RAM</span><span class="kie-debug-val kie-val-ram" id="kie-ram-val">-- MB</span></div>
        <div class="phphone-debug-row"><span class="kie-debug-label">CPU</span><span class="kie-debug-val kie-val-cpu" id="kie-cpu-val">--</span></div>
    `;
    document.body.appendChild(panel);

    // Calculador de FPS
    let frames = 0;
    let lastTime = performance.now();
    const fpsEl = document.getElementById('kie-fps-val');

    function loop() {
        frames++;
        const now = performance.now();
        if (now >= lastTime + 1000) {
            const fps = Math.round((frames * 1000) / (now - lastTime));
            fpsEl.innerText = fps;
            // Colores dinámicos según rendimiento
            fpsEl.style.color = fps >= 55 ? '#10b981' : (fps >= 30 ? '#f59e0b' : '#ef4444');
            frames = 0;
            lastTime = now;
        }
        requestAnimationFrame(loop);
    }
    requestAnimationFrame(loop);

    // Fetcher de RAM y CPU (PHP Backend)
    const ramEl = document.getElementById('kie-ram-val');
    const cpuEl = document.getElementById('kie-cpu-val');

    setInterval(async () => {
        try {
            // Se usa un action reservado para Phphone
            const res = await fetch('?kie_action=metrics', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) return;
            const data = await res.json();
            if (data.success) {
                ramEl.innerText = data.ram_used + ' MB / ' + data.ram_total + ' MB';
                cpuEl.innerText = data.cpu + ' %';
            }
        } catch (e) {
            // Silencioso para no ensuciar la consola
        }
    }, 1500);
};
