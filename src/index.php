<?php
// ==============================================================================
// Phphone Hardware Bridge Test
// ==============================================================================

// Enrutador Backend API
$uri = $_SERVER['REQUEST_URI'] ?? '';

if (strpos($uri, 'action=') !== false) {
    header('Content-Type: application/json');

    if (strpos($uri, 'action=toast') !== false) {
        $messages = [
            "¡El hardware es nuestro! 🚀",
            "PHP Real en tu Móvil con Phphone ⚡",
            "Phphone Native Bridge Activo 📱",
            "¡Phphone es el futuro del desarrollo móvil! 🏔️"
        ];
        $randomMsg = $messages[array_rand($messages)];
        \Phphone\Device::toast($randomMsg);
        echo json_encode(['success' => true, 'message' => $randomMsg]);
        return;
    }

    if (strpos($uri, 'action=vibrate') !== false) {
        $patternType = rand(1, 3);
        if ($patternType === 1) {
            \Phphone\Device::vibrate(100);
            usleep(200000); // 200ms
            \Phphone\Device::vibrate(200);
            $rhythmName = "Latido de Corazón ❤️";
        } elseif ($patternType === 2) {
            for ($i = 0; $i < 4; $i++) {
                \Phphone\Device::vibrate(50);
                usleep(100000); // 100ms
            }
            $rhythmName = "Ráfaga Rápida 🔫";
        } else {
            \Phphone\Device::vibrate(1000);
            $rhythmName = "Onda Pesada 🌊";
        }
        echo json_encode(['success' => true, 'rhythm' => $rhythmName]);
        return;
    }

    if (strpos($uri, 'action=gps') !== false) {
        $coords = \Phphone\Device::gps();
        if ($coords) {
            echo json_encode(['success' => true, 'lat' => $coords['lat'], 'lng' => $coords['lng']]);
        } else {
            echo json_encode(['success' => false, 'error' => "Permiso denegado o GPS apagado"]);
        }
        return;
    }

    if (strpos($uri, 'action=camera') !== false) {
        $base64 = \Phphone\Device::camera();
        if ($base64) {
            echo json_encode(['success' => true, 'image' => $base64]);
        } else {
            echo json_encode(['success' => false, 'error' => "Foto cancelada o permiso denegado"]);
        }
        return;
    }

    if (strpos($uri, 'action=notification') !== false) {
        $success = \Phphone\Device::notification("¡Hola desde PHP!", "Esto es una notificación nativa empujada desde Phphone.");
        echo json_encode(['success' => $success]);
        return;
    }

    if (strpos($uri, 'action=biometric') !== false) {
        $success = \Phphone\Device::authenticate("Confirma para acceder a los misiles");
        echo json_encode(['success' => $success]);
        return;
    }

    if (strpos($uri, 'action=pickimage') !== false) {
        $base64 = \Phphone\Device::pickImage();
        if ($base64) {
            echo json_encode(['success' => true, 'image' => $base64]);
        } else {
            echo json_encode(['success' => false, 'error' => "Cancelado o sin permiso"]);
        }
        return;
    }

    if (strpos($uri, 'action=share') !== false) {
        $success = \Phphone\Device::share("¡Estoy desarrollando apps móviles nativas usando PHP puro con Phphone! 🚀📱", "https://phphone.xyz");
        echo json_encode(['success' => $success]);
        return;
    }

    if (strpos($uri, 'action=battery') !== false) {
        $data = \Phphone\Device::battery();
        if ($data) {
            echo json_encode(['success' => true, 'level' => $data['level'], 'isCharging' => $data['isCharging']]);
        } else {
            echo json_encode(['success' => false, 'error' => "Error al leer batería"]);
        }
        return;
    }

    if (strpos($uri, 'action=daemon') !== false) {
        $success = \Phphone\Device::startDaemon('daemon', 60);
        echo json_encode(['success' => $success]);
        return;
    }

    if (strpos($uri, 'action=network') !== false) {
        $status = \Phphone\Device::network();
        echo json_encode(['success' => true, 'status' => $status]);
        return;
    }

    if (strpos($uri, 'action=clipboard') !== false) {
        $action = $_GET['type'] ?? 'read';
        if ($action === 'write') {
            $success = \Phphone\Device::clipboard("Phphone-PROMO-2026");
            echo json_encode(['success' => $success]);
        } else {
            $text = \Phphone\Device::clipboard();
            echo json_encode(['success' => true, 'text' => $text]);
        }
        return;
    }

    if (strpos($uri, 'action=flashlight') !== false) {
        $on = ($_GET['on'] ?? 'true') === 'true';
        $success = \Phphone\Device::flashlight($on);
        echo json_encode(['success' => $success]);
        return;
    }

    if (strpos($uri, 'action=info') !== false) {
        $data = \Phphone\Device::info();
        if ($data) {
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            echo json_encode(['success' => false, 'error' => "Error de info"]);
        }
        return;
    }

    if (strpos($uri, 'action=gyroscope_start') !== false) {
        $success = \Phphone\Device::startGyroscope();
        echo json_encode(['success' => $success]);
        return;
    }

    if (strpos($uri, 'action=gyroscope_stop') !== false) {
        $success = \Phphone\Device::stopGyroscope();
        echo json_encode(['success' => $success]);
        return;
    }

    if (strpos($uri, 'action=gyroscope') !== false && strpos($uri, 'action=gyroscope_') === false) {
        $data = \Phphone\Device::gyroscope();
        if ($data) {
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            echo json_encode(['success' => false, 'error' => "Giroscopio no iniciado o error"]);
        }
        return;
    }

    if (strpos($uri, 'action=contacts') !== false) {
        $data = \Phphone\Device::getContacts();
        if (is_array($data)) {
            echo json_encode(['success' => true, 'contacts' => $data]);
        } else {
            echo json_encode(['success' => false, 'error' => "Permiso denegado o error"]);
        }
        return;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Phphone Hardware Test</title>
    <link rel="stylesheet" href="/css/index.css">
</head>

<body class="index-page">

    <main class="container">
        <div>
            <h1>Phphone Super Control</h1>
            <p class="subtitle">PHP ➔ Phphone Bridge ➔ Hardware</p>
        </div>

        <button class="btn btn-toast" onclick="triggerHardware('toast')">
            💬 Lanzar Toast Random
        </button>

        <button class="btn btn-vibrate" onclick="triggerHardware('vibrate')">
            📳 Vibración Dinámica
        </button>

        <button class="btn btn-gps" onclick="triggerHardware('gps')">
            📍 Obtener Coordenadas GPS
        </button>

        <button class="btn btn-camera" onclick="triggerHardware('camera')">
            📸 Tomar Foto (Cámara)
        </button>

        <button class="btn btn-notification" onclick="triggerHardware('notification')">
            🔔 Lanzar Notificación Push
        </button>

        <!-- Nuevas Funciones -->
        <button class="btn btn-biometric" onclick="triggerHardware('biometric')">
            🔒 Autenticación Geométrica / FaceID
        </button>

        <button class="btn btn-gallery" onclick="triggerHardware('pickimage')">
            🗂️ Seleccionar de Galería
        </button>

        <button class="btn btn-hardware" onclick="triggerHardware('share')">
            📤 Compartir
        </button>

        <button class="btn btn-hardware" onclick="triggerHardware('battery')">
            🔋 Batería
        </button>

        <button class="btn btn-hardware" onclick="triggerHardware('network')">
            📡 Estado de Red
        </button>

        <button class="btn btn-hardware" onclick="triggerHardware('clipboard?type=write')">
            📋 Escribir Portapapeles (Phphone-PROMO)
        </button>

        <button class="btn btn-hardware" onclick="triggerHardware('clipboard?type=read')">
            📋 Leer Portapapeles
        </button>

        <button class="btn btn-hardware" onclick="toggleFlashlight()">
            🔦 Alternar Linterna
        </button>

        <button class="btn btn-hardware" onclick="triggerHardware('info')">
            📱 Info del Dispositivo
        </button>

        <button class="btn btn-hardware" id="btn-gyro" onclick="toggleGyroscope()">
            🧭 Activar Giroscopio (Streaming)
        </button>

        <button class="btn btn-hardware" onclick="triggerHardware('contacts')">
            👥 Ver Agenda (Contactos)
        </button>

        <button class="btn btn-hardware" onclick="triggerHardware('daemon')">
            👻 Iniciar Demonio (Background Task)
        </button>

        <a href="/newgradient.php" class="btn" style="text-decoration: none; justify-content: center; background: rgba(255,255,255,0.1); color: #fff;">
            🎨 Ir a New Gradient (Probar Rutas)
        </a>

        <div class="status-container">
            <p id="status" class="status-text"></p>
            <div id="image-container" style="margin-top: 15px; text-align: center; display: none;">
                <img id="camera-image" src="" alt="Foto" style="max-width: 100%; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
            </div>
            <div id="gyro-box" style="width: 100px; height: 100px; background: linear-gradient(135deg, var(--primary), var(--secondary)); transition: transform 0.1s; margin: 20px auto; display: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.3);"></div>
        </div>
    </main>

    <!-- Modal SweetAlert-like para respuestas rápidas -->
    <div id="phphone-alert-modal" class="custom-modal-backdrop" onclick="closePhphoneAlert(event)">
        <div class="custom-modal-card" onclick="event.stopPropagation()">
            <div id="phphone-alert-icon" class="custom-modal-icon">✨</div>
            <h3 id="phphone-alert-title" class="custom-modal-title">Título</h3>
            <div id="phphone-alert-body" class="custom-modal-body">Contenido del mensaje</div>
            <button class="custom-modal-btn" onclick="closePhphoneAlert()">Aceptar</button>
        </div>
    </div>

    <!-- Modal de Contactos -->
    <div id="contacts-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:9999; flex-direction:column; align-items:center; justify-content:center; padding:20px;">
        <div style="background:var(--bg-color); width:100%; max-width:400px; max-height:80vh; border-radius:15px; border:1px solid rgba(255,255,255,0.1); display:flex; flex-direction:column; overflow:hidden;">
            <div style="padding:15px; border-bottom:1px solid rgba(255,255,255,0.1); display:flex; justify-content:space-between; align-items:center;">
                <h3 style="margin:0; font-size:1.2rem;">Agenda Telefónica</h3>
                <button onclick="document.getElementById('contacts-modal').style.display='none'" style="background:none; border:none; color:var(--text-color); font-size:1.5rem; cursor:pointer;">&times;</button>
            </div>
            <div id="contacts-list" style="overflow-y:auto; padding:15px; display:flex; flex-direction:column; gap:10px;">
                <!-- Contact items here -->
            </div>
        </div>
    </div>

    <script>
        function showPhphoneAlert(title, message, icon = "✨") {
            document.getElementById('phphone-alert-icon').innerText = icon;
            document.getElementById('phphone-alert-title').innerText = title;
            document.getElementById('phphone-alert-body').innerHTML = message;
            document.getElementById('phphone-alert-modal').classList.add('active');
        }

        function closePhphoneAlert() {
            document.getElementById('phphone-alert-modal').classList.remove('active');
        }

        let flashState = false;
        async function toggleFlashlight() {
            flashState = !flashState;
            await triggerHardware(`flashlight&on=${flashState}`);
        }

        let gyroLoopRunning = false;
        async function toggleGyroscope() {
            const btn = document.getElementById('btn-gyro');
            const gyroBox = document.getElementById('gyro-box');
            const statusEl = document.getElementById('status');
            
            if (gyroLoopRunning) {
                gyroLoopRunning = false;
                await fetch('?action=gyroscope_stop');
                btn.innerHTML = '🧭 Activar Giroscopio (Streaming)';
                btn.style.borderLeft = '4px solid #eab308';
                statusEl.innerText = "🧭 Giroscopio apagado.";
            } else {
                gyroLoopRunning = true;
                await fetch('?action=gyroscope_start');
                btn.innerHTML = '🛑 Detener Giroscopio';
                btn.style.borderLeft = '4px solid var(--danger)';
                gyroBox.style.display = 'block';
                statusEl.className = "status-text status-success";
                
                // Loop de alto rendimiento (requestAnimationFrame)
                const fetchGyro = async () => {
                    if (!gyroLoopRunning) return;
                    try {
                        const res = await fetch('?action=gyroscope');
                        const data = await res.json();
                        if (data.success && data.data) {
                            statusEl.innerText = `🧭 X: ${data.data.x.toFixed(2)} | Y: ${data.data.y.toFixed(2)} | Z: ${data.data.z.toFixed(2)}`;
                            const rotateX = data.data.x * 10;
                            const rotateY = data.data.y * 10;
                            gyroBox.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
                        }
                    } catch(e) {}
                    requestAnimationFrame(fetchGyro);
                };
                requestAnimationFrame(fetchGyro);
            }
        }

        async function triggerHardware(actionQuery) {
            const statusEl = document.getElementById('status');
            const imgContainer = document.getElementById('image-container');
            const imgEl = document.getElementById('camera-image');

            // Separar action base de sus parametros
            const action = actionQuery.split('?')[0];

            // Si es un request asíncrono largo, mostrar loading
            if (['gps', 'camera', 'pickimage', 'biometric', 'share'].includes(action)) {
                statusEl.innerText = "⏳ Esperando permisos o respuesta del usuario...";
                statusEl.className = "status-text status-loading";
                if (action !== 'camera') imgContainer.style.display = 'none';
            }

            try {
                const response = await fetch(`?action=${actionQuery}`);
                const textData = await response.text();

                let data;
                try {
                    data = JSON.parse(textData);
                } catch (e) {
                    showPhphoneAlert("Error", "Respuesta no es JSON.<br>" + textData.substring(0, 100), "⚠️");
                    return;
                }

                if (!data.success) {
                    const errMsg = data.error || "Desconocido";
                    statusEl.innerText = "Error: " + errMsg;
                    statusEl.className = "status-text status-error";
                    showPhphoneAlert("Error en Operación", errMsg, "❌");
                    return;
                }

                statusEl.className = "status-text status-success";

                if (action === 'vibrate') {
                    statusEl.innerText = `Ritmo: ${data.rhythm}`;
                    showPhphoneAlert("Vibración Ejecutada", `Patrón enviado:<br><strong>${data.rhythm}</strong>`, "📳");
                } else if (action === 'toast') {
                    statusEl.innerText = "¡Toast enviado al OS!";
                } else if (action === 'gps') {
                    const gpsText = `Latitud: <strong>${data.lat}</strong><br>Longitud: <strong>${data.lng}</strong>`;
                    statusEl.innerText = `📍 Coordenadas: ${data.lat}, ${data.lng}`;
                    showPhphoneAlert("Ubicación GPS Obtenida", gpsText, "📍");
                } else if (action === 'notification') {
                    statusEl.innerText = "🔔 Notificación empujada con éxito.";
                    showPhphoneAlert("Notificación Local", "¡Notificación push emitida exitosamente por el sistema operativo!", "🔔");
                } else if (action === 'camera' || action === 'pickimage') {
                    statusEl.innerText = "📸 ¡Imagen recibida en Base64!";
                    imgEl.src = "data:image/jpeg;base64," + data.image;
                    imgContainer.style.display = 'block';
                    imgContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else if (action === 'biometric') {
                    statusEl.innerText = "🔒 ¡Identidad verificada exitosamente!";
                    showPhphoneAlert("Autenticación Biométrica", "¡Identidad verificada exitosamente con FaceID / TouchID / Huella!", "🛡️");
                } else if (action === 'share') {
                    statusEl.innerText = "📤 Share sheet abierto";
                } else if (action === 'battery') {
                    const battMsg = `Nivel actual: <strong>${data.level}%</strong><br>Estado: <strong>${data.isCharging ? '⚡ Cargando' : '🔋 En batería'}</strong>`;
                    statusEl.innerText = `🔋 Batería: ${data.level}% | Cargando: ${data.isCharging ? 'Sí' : 'No'}`;
                    showPhphoneAlert("Estado de Batería", battMsg, "🔋");
                } else if (action === 'network') {
                    const netMsg = `Conectividad actual: <strong>${data.status.toUpperCase()}</strong>`;
                    statusEl.innerText = `📡 Red: ${data.status.toUpperCase()}`;
                    showPhphoneAlert("Estado de Red", netMsg, "📡");
                } else if (action === 'clipboard') {
                    if (data.text) {
                        statusEl.innerText = `📋 Leído: ${data.text}`;
                        showPhphoneAlert("Portapapeles Leído", `Contenido recuperado:<br><code style="background:rgba(255,255,255,0.1); padding:4px 8px; border-radius:6px;">${data.text}</code>`, "📋");
                    } else {
                        statusEl.innerText = "📋 ¡Texto guardado!";
                        showPhphoneAlert("Portapapeles", "¡Texto copiado exitosamente al portapapeles nativo!", "📋");
                    }
                } else if (action === 'flashlight') {
                    statusEl.innerText = `🔦 Linterna: ${flashState ? 'Encendida' : 'Apagada'}`;
                    showPhphoneAlert("Linterna", `La linterna nativa fue <strong>${flashState ? 'encendida' : 'apagada'}</strong>.`, "🔦");
                } else if (action === 'info') {
                    const infoMsg = `Dispositivo: <strong>${data.data.model}</strong><br>Versión OS: <strong>${data.data.os_version}</strong><br>UUID: <small>${data.data.uuid || 'N/A'}</small>`;
                    statusEl.innerText = `📱 Dispositivo: ${data.data.model} (OS: ${data.data.os_version})`;
                    showPhphoneAlert("Información del Dispositivo", infoMsg, "📱");
                } else if (action === 'daemon') {
                    // Start daemon using the native bridge
                    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
                    if (isIOS) {
                        if (window.webkit?.messageHandlers?.Kie) {
                            window.webkit.messageHandlers.Kie.postMessage({ action: 'startDaemon', taskName: 'daemon', interval: 60 });
                        }
                    } else {
                        if (window.Kie && typeof window.Kie.startDaemon === 'function') {
                            window.Kie.startDaemon(JSON.stringify({ taskName: 'daemon', interval: 60 }));
                        }
                    }
                    statusEl.innerText = "👻 Demonio nativo disparado desde JS a segundo plano.";
                    showPhphoneAlert("Demonio en Segundo Plano", "Servicio en segundo plano programado exitosamente vía BGTaskScheduler.", "👻");
                } else if (action === 'contacts') {
                    statusEl.innerText = `👥 ${data.contacts.length} contactos leídos.`;
                    const modal = document.getElementById('contacts-modal');
                    const list = document.getElementById('contacts-list');
                    list.innerHTML = '';
                    
                    if (data.contacts.length === 0) {
                        list.innerHTML = '<p style="text-align:center; color:#94a3b8;">No hay contactos.</p>';
                    } else {
                        // Guardar datos para Lazy Loading
                        window.currentContacts = data.contacts;
                        window.contactsIndex = 0;
                        const chunkSize = 50;
                        
                        window.loadMoreContacts = function() {
                            if (window.contactsIndex >= window.currentContacts.length) return;
                            
                            let html = '';
                            const end = Math.min(window.contactsIndex + chunkSize, window.currentContacts.length);
                            for (let i = window.contactsIndex; i < end; i++) {
                                const c = window.currentContacts[i];
                                html += `<div style="background:rgba(255,255,255,0.05); padding:10px; border-radius:8px;"><strong>${c.name}</strong><br><span style="color:#94a3b8; font-size:0.9em;">${c.phone}</span></div>`;
                            }
                            list.insertAdjacentHTML('beforeend', html);
                            window.contactsIndex = end;
                        };
                        
                        // Cargar el primer lote
                        window.loadMoreContacts();
                        
                        // Evento de scroll para lazy loading infinito
                        list.onscroll = function() {
                            if (list.scrollTop + list.clientHeight >= list.scrollHeight - 150) {
                                window.loadMoreContacts();
                            }
                        };
                    }
                    modal.style.display = 'flex';
                }
            } catch (err) {
                console.error(err);
                statusEl.innerText = "Error de red local";
                statusEl.className = "status-text status-error";
                showPhphoneAlert("Error de Red", "No se pudo conectar con el puente nativo local.", "⚠️");
            }
        }
    </script>

    <script src="/js/kie.js"></script>
    <script>
        // Enciende el Monitor Profesional de Phphone
        setTimeout(() => {
            window.Kie.enableDebugMonitor();
        }, 500);
    </script>
</body>

</html>