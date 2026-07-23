// public/js/app.js

// Función para calcular la luminancia relativa de un color HEX
function getLuminance(hex) {
    hex = hex.replace('#', '');
    if (hex.length === 3) {
        hex = hex.split('').map(char => char + char).join('');
    }

    const r = parseInt(hex.substring(0, 2), 16);
    const g = parseInt(hex.substring(2, 4), 16);
    const b = parseInt(hex.substring(4, 6), 16);

    // Formula estándar simplificada de luminancia (YIQ)
    // Devuelve un valor entre 0 (muy oscuro) y 255 (muy brillante)
    return (r * 0.299 + g * 0.587 + b * 0.114);
}

document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('generate-btn');
    const bg = document.getElementById('gradient-bg');
    const card = document.querySelector('.glass-card');

    // Validación defensiva contra nulos
    if (btn && bg && card) {
        btn.addEventListener('click', async () => {
            try {
                const response = await fetch('?action=generate-gradient', {
                    method: 'POST'
                });
                const data = await response.json();

                if (data.success) {
                    const grad = data.gradient;
                    const colorsStr = grad.colors.join(', ');

                    // 1. Aplicar el nuevo gradiente al fondo
                    bg.style.background = `linear-gradient(${grad.angle}deg, ${colorsStr})`;

                    // 2. Calcular la luminancia promedio de los colores generados
                    let totalLuminance = 0;
                    grad.colors.forEach(color => {
                        totalLuminance += getLuminance(color);
                    });
                    const averageLuminance = totalLuminance / grad.colors.length;

                    // 3. Evaluar el contraste dinámico
                    // Si el fondo es claro (luminancia > 128), cambiamos la tarjeta al tema oscuro
                    if (averageLuminance > 128) {
                        card.classList.add('theme-light-bg');
                    } else {
                        card.classList.remove('theme-light-bg');
                    }
                } else {
                    alert("Error en PHP: " + (data.error || "Desconocido"));
                }
            } catch (error) {
                alert('Fallo la conexión JS a PHP: ' + error.message);
                console.error('Error generating gradient:', error);
            }
        });
    }
});
