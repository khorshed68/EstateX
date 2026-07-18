<style>
    /* Premium Refractive Water Wave Ripple Style */
    .global-water-ripple {
        position: fixed; /* Fixed relative to viewport for perfect scroll resilience */
        border-radius: 50%;
        border: 1px solid rgba(56, 189, 248, 0.35); /* Liquid light teal crest outline */
        background: radial-gradient(circle, rgba(56, 189, 248, 0.08) 0%, rgba(59, 130, 246, 0.02) 60%, transparent 80%);
        box-shadow: 
            inset 0 0 10px rgba(56, 189, 248, 0.15), 
            0 0 15px rgba(59, 130, 246, 0.08);
        
        /* Backdrop refraction: warps & magnifies content beneath the wave */
        backdrop-filter: blur(1px) saturate(110%);
        -webkit-backdrop-filter: blur(1px) saturate(110%);
        
        pointer-events: none;
        width: 32px;
        height: 32px;
        transform: translate(-50%, -50%) scale(0.3);
        z-index: 999999; /* Draw on top of all UI components */
        animation: premiumWaveAnimation 0.65s cubic-bezier(0.1, 0.8, 0.15, 1) forwards;
    }
    
    @keyframes premiumWaveAnimation {
        0% {
            transform: translate(-50%, -50%) scale(0.3);
            opacity: 0.9;
        }
        100% {
            transform: translate(-50%, -50%) scale(3.5);
            opacity: 0;
            border-color: rgba(56, 189, 248, 0); /* Soft border fadeout */
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let lastX = 0;
        let lastY = 0;
        const minDistance = 15; // Smooth flowing trail distance threshold

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
                
                // Remove element after animation completes
                setTimeout(() => {
                    ripple.remove();
                }, 650);
                
                lastX = clientX;
                lastY = clientY;
            }
        });
    });
</script>
