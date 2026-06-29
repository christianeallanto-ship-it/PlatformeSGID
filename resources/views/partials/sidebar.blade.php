<aside class="w-72 bg-[#0a1128] text-white flex flex-col shrink-0">

    <div class="p-6">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-green-500 rounded flex items-center justify-center">
                <i data-lucide="trash-2" class="text-white"></i>
            </div>
            <span class="font-bold text-xl tracking-tight">BENINCLEAN</span>
        </div>
    </div>

    <nav class="flex-1 px-4 py-4 space-y-1.5 overflow-y-auto custom-scrollbar">

        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-white/10 text-white border-l-4 border-green-500 font-medium' : 'text-gray-400 hover:bg-white/5 hover:text-white transition-colors' }}">
            <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
            <span>Tableau de bord</span>
        </a>

        <a href="{{ route('map') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('map') ? 'bg-white/10 text-white border-l-4 border-green-500 font-medium' : 'text-gray-400 hover:bg-white/5 hover:text-white transition-colors' }}">
            <i data-lucide="map-pin" class="w-5 h-5"></i>
            <span>Carte de monitoring</span>
        </a>

        <a href="{{ route('bins.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('bins.index') ? 'bg-white/10 text-white border-l-4 border-green-500 font-medium' : 'text-gray-400 hover:bg-white/5 hover:text-white transition-colors' }}">
            <i data-lucide="trash" class="w-5 h-5"></i>
            <span>Bacs</span>
        </a>

        <a href="{{ route('alerts.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('alerts.index') ? 'bg-white/10 text-white border-l-4 border-green-500 font-medium' : 'text-gray-400 hover:bg-white/5 hover:text-white transition-colors' }}">
            <i data-lucide="alert-triangle" class="w-5 h-5"></i>
            <span>Alertes</span>
        </a>

        <a href="{{ route('collections.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('collections.index') ? 'bg-white/10 text-white border-l-4 border-green-500 font-medium' : 'text-gray-400 hover:bg-white/5 hover:text-white transition-colors' }}">
            <i data-lucide="truck" class="w-5 h-5"></i>
            <span>Collectes</span>
        </a>

        <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('reports.index') ? 'bg-white/10 text-white border-l-4 border-green-500 font-medium' : 'text-gray-400 hover:bg-white/5 hover:text-white transition-colors' }}">
            <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
            <span>Rapports & Statistiques</span>
        </a>

        <div class="pt-4 pb-1 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Paramétrage</div>

        <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('settings.index') ? 'bg-white/10 text-white border-l-4 border-green-500 font-medium' : 'text-gray-400 hover:bg-white/5 hover:text-white transition-colors' }}">
            <i data-lucide="settings" class="w-5 h-5"></i>
            <span>Paramètres</span>
        </a>

        <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('users.index') ? 'bg-white/10 text-white border-l-4 border-green-500 font-medium' : 'text-gray-400 hover:bg-white/5 hover:text-white transition-colors' }}">
            <i data-lucide="users" class="w-5 h-5"></i>
            <span>Utilisateurs</span>
        </a>

    </nav>

    <!-- Déconnexion -->
    <div class="p-4 border-t border-white/5">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-red-400 hover:bg-red-500/10 rounded-lg transition-colors text-left font-medium">
                <i data-lucide="log-out" class="w-5 h-5"></i>
                <span>Déconnexion</span>
            </button>
        </form>
    </div>

</aside>