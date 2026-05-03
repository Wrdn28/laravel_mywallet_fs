<header class="sticky top-0 z-40 h-16 flex items-center justify-between px-4 lg:px-8 app-header">
    <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-red-500/15 flex items-center justify-center">
            <span class="ms text-[18px] text-red-400">admin_panel_settings</span>
        </div>
        <div>
            <h1 class="font-bold text-base leading-none" style="color:var(--text-1)">Admin Dashboard</h1>
            <p class="text-[11px] mt-0.5 hidden lg:block" style="color:var(--text-3)">{{ $appName }}</p>
        </div>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="toggleTheme()" class="theme-toggle" title="Toggle tema">
            <span class="ms text-[18px] theme-icon">light_mode</span>
        </button>
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl border"
             style="background:var(--bg-input); border-color:var(--border)">
            <span class="ms text-[16px] text-red-400">shield_person</span>
            <span class="text-xs font-semibold hidden sm:inline" style="color:var(--text-2)">
                {{ Auth::user()->email }}
            </span>
        </div>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit"
                class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold
                       text-red-400 bg-red-500/10 hover:bg-red-500/20 border border-red-500/20
                       transition-all active:scale-95">
                <span class="ms text-[16px]">logout</span>
                <span class="hidden sm:inline">Logout</span>
            </button>
        </form>
    </div>
</header>
