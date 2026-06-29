<header class="h-16 flex items-center justify-between px-8 border-b border-white/5 bg-[#0a1128] shrink-0">
    <!-- Left Section -->
    <div class="flex items-center gap-4">
        <h2 class="text-white font-semibold text-lg tracking-wide">
            Interface web de monitoring
        </h2>
    </div>

    <!-- Right Section -->
    <div class="flex items-center gap-4">
        @auth
            <div class="flex items-center gap-3 text-right">
                <div>
                    <p class="text-sm font-semibold text-white">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-gray-400">{{ Auth::user()->email }}</p>
                </div>
                <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-md border-2 border-white/10">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
            </div>
        @endauth
    </div>
</header>