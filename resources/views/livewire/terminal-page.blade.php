<div class="min-h-screen flex flex-col" x-data="scannerApp()" x-init="initScanner()">
    <!-- Header -->
    <header class="bg-emerald-700 text-white px-4 py-3 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-emerald-200 uppercase tracking-wide">Cook Terminal</p>
                <p class="font-semibold text-sm">{{ $cookName }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-emerald-200">Project</p>
                <p class="font-semibold text-sm truncate max-w-[150px]">{{ $projectName }}</p>
            </div>
        </div>
        <div class="mt-2 bg-emerald-800 rounded-lg px-3 py-1.5 flex items-center justify-between">
            <span class="text-xs text-emerald-300">Fed Today</span>
            <span class="text-xl font-bold">{{ $totalFedToday }}</span>
        </div>
    </header>

    <!-- Alert Zone -->
    <div class="px-4 pt-3">
        @if ($alert)
            <div class="alert-enter rounded-xl p-4 text-center {{ $alert['type'] === 'success' ? 'bg-emerald-100 border-2 border-emerald-500 text-emerald-800' : 'bg-red-100 border-2 border-red-500 text-red-800' }}">
                <div class="flex items-center justify-center gap-2">
                    @if ($alert['type'] === 'success')
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    @else
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 8a4 4 0 100 8 4 4 0 000-8z"/>
                        </svg>
                    @endif
                    <span class="font-semibold text-sm">{{ $alert['message'] }}</span>
                </div>
            </div>
        @endif
    </div>

    <!-- Search Box -->
    <div class="px-4 pt-3">
        <div class="relative">
            <input
                type="text"
                wire:model.live.debounce.200ms="search"
                placeholder="Type shortcode or name..."
                class="w-full px-4 py-3.5 pr-10 text-lg border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white"
                autocomplete="off"
                autocorrect="off"
                autocapitalize="characters"
                x-ref="searchInput"
            >
            @if ($search)
                <button wire:click="$set('search', ''); $set('matches', [])" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            @endif
        </div>
    </div>

    <!-- Matches List -->
    @if (count($matches) > 0)
        <div class="px-4 pt-2 space-y-2">
            @foreach ($matches as $match)
                <div class="bg-white border border-gray-200 rounded-xl p-3 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-gray-900">{{ $match['name'] }}</p>
                        <p class="text-xs text-gray-500">{{ $match['project'] }} &bull; <span class="font-mono font-bold text-emerald-600">{{ $match['shortcode'] }}</span></p>
                    </div>
                    <button
                        wire:click="markFed({{ $match['id'] }})"
                        class="bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold px-4 py-2 rounded-lg text-sm transition-colors"
                    >
                        Mark Fed
                    </button>
                </div>
            @endforeach
        </div>
    @elseif (strlen($search) >= 2 && count($matches) === 0)
        <div class="px-4 pt-2">
            <div class="bg-gray-100 rounded-xl p-4 text-center text-gray-500 text-sm">
                No beneficiaries found matching "{{ $search }}"
            </div>
        </div>
    @endif

    <!-- QR Scanner -->
    <div class="flex-1 px-4 pt-4 pb-4 flex flex-col">
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden flex-1 flex flex-col">
            <div class="bg-gray-800 text-white px-4 py-2 flex items-center justify-between">
                <span class="text-sm font-medium flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                    QR Scanner
                </span>
                <span x-show="scanning" class="text-xs text-emerald-400 flex items-center gap-1">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                    Active
                </span>
            </div>
            <div class="scanner-container flex-1 relative bg-black flex items-center justify-center">
                <div id="qr-reader" class="w-full h-full"></div>
            </div>
        </div>
    </div>

    <!-- Logout -->
    <div class="px-4 pb-4">
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit" class="w-full py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-xl text-sm transition-colors">
                Logout
            </button>
        </form>
    </div>

    <!-- Audio elements -->
    <audio id="success-sound" preload="auto">
        <source src="{{ asset('sounds/success.mp3') }}" type="audio/mpeg">
    </audio>
    <audio id="error-sound" preload="auto">
        <source src="{{ asset('sounds/error.mp3') }}" type="audio/mpeg">
    </audio>

    <script>
        function scannerApp() {
            return {
                scanning: false,
                scanner: null,
                processingLock: false,

                initScanner() {
                    if (typeof Livewire !== 'undefined') {
                        Livewire.on('play-success-sound', () => this.playSound('success'));
                        Livewire.on('play-error-sound', () => this.playSound('error'));
                    }
                    // Start camera immediately on page load
                    this.$nextTick(() => this.startScanner());
                },

                startScanner() {
                    if (this.scanning) return; // already running
                    this.scanning = true;
                    this.scanner = new Html5Qrcode('qr-reader');
                    const config = { fps: 10, qrbox: { width: 250, height: 250 } };

                    this.scanner.start(
                        { facingMode: 'environment' },
                        config,
                        (decodedText) => { this.onScanSuccess(decodedText); },
                        () => {}
                    ).catch(err => {
                        console.error('Scanner error:', err);
                        this.scanning = false;
                    });
                },

                onScanSuccess(decodedText) {
                    if (this.processingLock) return;
                    this.processingLock = true;

                    // Camera stays on — just forward to Livewire
                    @this.processQrToken(decodedText);

                    // Release lock after 2.5 s to prevent the same card
                    // being re-read while still in front of the camera
                    setTimeout(() => { this.processingLock = false; }, 2500);
                },

                playSound(type) {
                    const audio = document.getElementById(type + '-sound');
                    if (audio) {
                        audio.currentTime = 0;
                        audio.play().catch(() => {});
                    }
                }
            }
        }
    </script>
</div>
