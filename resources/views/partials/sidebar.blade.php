{{-- Sidebar: $appName, $activeMenu --}}
@php $activeMenu = $activeMenu ?? 'beranda'; @endphp

{{-- Mobile overlay --}}
<div id="sidebarOverlay"
     class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 hidden lg:hidden"
     onclick="closeSidebar()"></div>

<aside id="sidebar"
       class="fixed left-0 top-0 h-screen w-64 z-50 flex flex-col
              -translate-x-full lg:translate-x-0 transition-transform duration-300">

    {{-- Logo + close --}}
    <div class="px-6 pt-8 pb-6 flex items-center justify-between"
         style="border-bottom: 1px solid var(--border)">
        <div>
            <h1 class="text-xl font-black bg-gradient-to-br from-violet-500 to-teal-400 bg-clip-text text-transparent tracking-tight">
                {{ $appName ?? 'DOMPETKU' }}
            </h1>
            <p class="text-[10px] font-semibold tracking-[0.2em] uppercase mt-1" style="color:var(--text-3)">
                Money Tracker
            </p>
        </div>
        <button onclick="closeSidebar()"
                class="lg:hidden w-8 h-8 rounded-lg flex items-center justify-center transition-all"
                style="background:var(--bg-input); color:var(--text-3)">
            <span class="ms text-[18px]">close</span>
        </button>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
        @php
        $items = [
            ['key'=>'beranda',    'route'=>'beranda',     'icon'=>'dashboard',              'label'=>'Dashboard'],
            ['key'=>'rencana',    'route'=>'rencana',     'icon'=>'account_balance_wallet', 'label'=>'Anggaran & Target'],
            ['key'=>'pengaturan', 'route'=>'pengaturan',  'icon'=>'manage_accounts',        'label'=>'Pengaturan'],
        ];
        @endphp
        @foreach($items as $item)
            @php $active = $activeMenu === $item['key']; @endphp
            <a href="{{ route($item['route']) }}"
               class="sidebar-link {{ $active ? 'active' : '' }}
                      flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold">
                <span class="ms text-[20px]" style="{{ $active ? 'color:var(--sidebar-active-text)' : '' }}">
                    {{ $item['icon'] }}
                </span>
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>

    {{-- User + Theme toggle + Logout --}}
    <div class="px-3 pb-6 pt-4" style="border-top: 1px solid var(--border)">
        {{-- User info --}}
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl mb-2"
             style="background:var(--bg-input)">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-violet-500 to-teal-400
                        flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                {{ strtoupper(substr(Auth::user()->email, 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-semibold truncate" style="color:var(--text-1)">
                    {{ Auth::user()->email }}
                </p>
                <p class="text-[10px] text-teal-500">Online</p>
            </div>
            {{-- Theme toggle in sidebar --}}
            <button onclick="toggleTheme()" class="theme-toggle flex-shrink-0" title="Toggle tema">
                <span class="ms text-[18px] theme-icon">light_mode</span>
            </button>
        </div>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="sidebar-link w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold
                       hover:text-red-500 hover:bg-red-500/10">
                <span class="ms text-[20px]">logout</span>
                Logout
            </button>
        </form>
    </div>
</aside>
