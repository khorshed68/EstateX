<style>
    /* Global cursor hybrid lighting + water wave style */
    .global-water-ripple {
        position: fixed; /* Fixed relative to viewport for perfect scroll resilience */
        border-radius: 50%;
        border: 1.5px solid rgba(52, 211, 153, 0.7); /* Highly visible water wave ripple outline */
        background: radial-gradient(circle, rgba(52, 211, 153, 0.4) 0%, rgba(59, 130, 246, 0.15) 50%, transparent 85%);
        box-shadow: 0 0 10px rgba(52, 211, 153, 0.4), 0 0 20px rgba(59, 130, 246, 0.15);
        pointer-events: none;
        width: 28px;
        height: 28px;
        transform: translate(-50%, -50%) scale(0.5);
        z-index: 999999; /* Draw on top of all UI components */
        animation: lightWaveAnimation 0.55s cubic-bezier(0.1, 0.8, 0.3, 1) forwards;
    }
    @keyframes lightWaveAnimation {
        0% {
            transform: translate(-50%, -50%) scale(0.5);
            opacity: 1;
            filter: blur(0px);
        }
        100% {
            transform: translate(-50%, -50%) scale(2.5);
            opacity: 0;
            filter: blur(1.5px); /* Soft glow fade-out */
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let lastX = 0;
        let lastY = 0;
        const minDistance = 12; // Throttling threshold for a smooth flowing trail

        document.addEventListener('mousemove', function(e) {
            const clientX = e.clientX;
            const clientY = e.clientY;
            
            const dist = Math.hypot(clientX - lastX, clientY - lastY);
            if (dist > minDistance) {
                const ripple = document.createElement('div');
                ripple.className = 'global-water-ripple';
                ripple.style.left = `${clientX}px`;
                ripple.style.top = `${clientY}px`;
                document.body.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 500);
                
                lastX = clientX;
                lastY = clientY;
            }
        });
    });
</script>
