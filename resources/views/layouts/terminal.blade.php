<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Terminal - NMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <style>
        * { -webkit-tap-highlight-color: transparent; }
        body { touch-action: manipulation; overscroll-behavior: none; }
        .scanner-container { position: relative; overflow: hidden; }
        #qr-reader video { border-radius: 0.75rem; }
        #qr-reader__dashboard_section_csr span { display: none !important; }
        #qr-reader__dashboard_section_swaplink { display: none !important; }
        #qr-reader button { display: none !important; }
        .alert-enter { animation: slideIn 0.3s ease-out; }
        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .pulse-success { animation: pulseGreen 0.5s ease-out; }
        @keyframes pulseGreen {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            100% { box-shadow: 0 0 0 20px rgba(16, 185, 129, 0); }
        }
        .pulse-error { animation: pulseRed 0.5s ease-out; }
        @keyframes pulseRed {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            100% { box-shadow: 0 0 0 20px rgba(239, 68, 68, 0); }
        }
    </style>
    @livewireStyles
</head>
<body class="bg-gray-50 min-h-screen">
    {{ $slot }}

    <script>
        document.addEventListener('livewire:init', () => {
            // Auto-reset alert after 2.5 seconds
            Livewire.on('alert-shown', () => {
                setTimeout(() => {
                    $wire.set('alert', null);
                }, 2500);
            });
        });
    </script>
    @livewireScripts
</body>
</html>
