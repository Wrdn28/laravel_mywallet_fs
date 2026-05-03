<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <title>{{ $appName }} — Dashboard</title>
    @include('partials.head')
</head>
<body class="overflow-x-hidden">

@include('partials.sidebar', ['activeMenu' => 'beranda'])

{{-- Ambient blobs --}}
<div class="fixed inset-0 pointer-events-none -z-10">
    <div class="absolute top-[5%] left-[25%] w-[40rem] h-[40rem] bg-violet-700/10 rounded-full blur-[120px]"></div>
    <div class="absolute bottom-[5%] right-[5%] w-[30rem] h-[30rem] bg-teal-500/8 rounded-full blur-[100px]"></div>
</div>

<main class="ml-64 min-h-screen main-with-sidebar">

    {{-- Top Bar --}}
    <header class="sticky top-0 z-40 h-16 flex items-center justify-between px-4 lg:px-8
                   app-header">
        {{-- Mobile hamburger --}}
        <button onclick="openSidebar()"
                class="mobile-menu-btn w-9 h-9 rounded-lg items-center justify-center transition-all"
                style="background:var(--bg-input); color:var(--text-3)">
            <span class="ms text-[20px]">menu</span>
        </button>

        <div class="flex-1 lg:flex-none">
            <h2 class="font-bold text-base lg:text-lg" style="color:var(--text-1)">Dashboard Keuangan</h2>
            <p class="text-slate-500 text-xs hidden lg:block">Kelola keuangan Anda dengan mudah</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="toggleTheme()" class="theme-toggle hidden lg:flex" title="Toggle tema">
                <span class="ms text-[18px] theme-icon">light_mode</span>
            </button>

            {{-- ── NOTIFICATION BELL ── --}}
            @php $notifCount = isset($notifications) ? $notifications->count() : 0; @endphp
            <div class="relative" id="notifDropdownWrap">
                <button onclick="toggleNotifPanel()" id="notifBellBtn"
                        class="relative w-9 h-9 rounded-xl flex items-center justify-center transition-all"
                        style="background:var(--bg-input); border:1px solid var(--border); color:var(--text-3)"
                        title="Notifikasi">
                    <span class="ms text-[20px]">notifications</span>
                    @if($notifCount > 0)
                    <span class="absolute -top-1.5 -right-1.5 min-w-[18px] h-[18px] px-1
                                 rounded-full text-[10px] font-black flex items-center justify-center
                                 bg-red-500 leading-none"
                          style="color:#ffffff">
                        {{ $notifCount > 99 ? '99+' : $notifCount }}
                    </span>
                    @endif
                </button>

                {{-- Dropdown panel --}}
                <div id="notifPanel"
                     class="absolute right-0 top-full mt-2 w-80 rounded-2xl overflow-hidden hidden z-[200]"
                     style="background:var(--bg-card); border:1px solid var(--border); box-shadow:var(--shadow-lg)">

                    {{-- Panel header --}}
                    <div class="flex items-center justify-between px-4 py-3"
                         style="border-bottom:1px solid var(--border); background:var(--bg-subtle)">
                        <div class="flex items-center gap-2">
                            <span class="ms text-[18px] text-violet-400">notifications</span>
                            <span class="text-sm font-bold" style="color:var(--text-1)">Notifikasi</span>
                            @if($notifCount > 0)
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-red-500"
                                  style="color:#ffffff">{{ $notifCount }}</span>
                            @endif
                        </div>
                        <button onclick="dismissAllNotif()"
                                class="text-[10px] font-semibold transition-opacity hover:opacity-70"
                                style="color:var(--text-4)">
                            Hapus semua
                        </button>
                    </div>

                    {{-- Notif list --}}
                    <div id="notifList" class="max-h-80 overflow-y-auto">
                        @if($notifCount > 0)
                        @foreach($notifications as $idx => $n)
                        @php
                            $nc = [
                                'danger'  => ['icon'=>'var(--accent-red)',    'bg'=>'rgba(220,38,38,0.07)',  'border'=>'rgba(220,38,38,0.15)'],
                                'warning' => ['icon'=>'var(--accent-yellow)', 'bg'=>'rgba(217,119,6,0.07)',  'border'=>'rgba(217,119,6,0.15)'],
                                'success' => ['icon'=>'var(--accent-teal)',   'bg'=>'rgba(13,148,136,0.07)', 'border'=>'rgba(13,148,136,0.15)'],
                            ][$n['level']] ?? ['icon'=>'var(--accent-violet)', 'bg'=>'var(--bg-subtle)', 'border'=>'var(--border)'];
                        @endphp
                        <div class="notif-item flex items-start gap-3 px-4 py-3 transition-colors"
                             data-idx="{{ $idx }}"
                             style="border-bottom:1px solid var(--divider)"
                             onmouseenter="this.style.background='var(--bg-hover)'"
                             onmouseleave="this.style.background=''">
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5"
                                 style="background:{{ $nc['bg'] }}; border:1px solid {{ $nc['border'] }}">
                                <span class="ms text-[16px]" style="color:{{ $nc['icon'] }}">{{ $n['icon'] }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs leading-relaxed" style="color:var(--text-2)">{{ $n['message'] }}</p>
                                <a href="{{ $n['link'] }}"
                                   class="text-[10px] font-bold mt-1 inline-block transition-opacity hover:opacity-70"
                                   style="color:{{ $nc['icon'] }}">
                                    Lihat detail
                                </a>
                            </div>
                            <button onclick="dismissNotif({{ $idx }}, this)"
                                    class="ms text-[16px] flex-shrink-0 mt-0.5 transition-opacity hover:opacity-60"
                                    style="color:var(--text-4)">close</button>
                        </div>
                        @endforeach
                        @else
                        <div class="flex flex-col items-center justify-center py-10 gap-2">
                            <span class="ms text-[36px]" style="color:var(--text-4)">notifications_none</span>
                            <p class="text-xs" style="color:var(--text-4)">Tidak ada notifikasi</p>
                        </div>
                        @endif
                    </div>

                    {{-- Footer --}}
                    <div class="px-4 py-2.5" style="border-top:1px solid var(--border); background:var(--bg-subtle)">
                        <a href="{{ route('rencana') }}"
                           class="text-xs font-semibold flex items-center gap-1 transition-opacity hover:opacity-70"
                           style="color:var(--accent-violet)">
                            <span class="ms text-[14px]">open_in_new</span>
                            Kelola Anggaran & Target
                        </a>
                    </div>
                </div>
            </div>

            <button onclick="openModal('quickAddModal')"
                    class="flex items-center gap-2 px-4 lg:px-5 py-2 lg:py-2.5 rounded-xl text-xs lg:text-sm font-bold
                           bg-gradient-to-r from-violet-600 to-violet-500 text-white
                           hover:from-violet-500 hover:to-violet-400 transition-all glow-primary active:scale-95">
                <span class="ms text-[16px] lg:text-[18px]">add</span>
                <span class="hidden sm:inline">Tambah Transaksi</span>
            </button>
        </div>
    </header>
    @include('partials.toast')

    {{-- AI Suggestion Bubble --}}
    @include('partials.ai-suggestion')

    <div class="p-4 lg:p-8 space-y-6 lg:space-y-8">

        {{-- ── HERO CARDS ── --}}
        <section class="grid grid-cols-1 sm:grid-cols-3 gap-4 lg:gap-5" id="heroCards">

            {{-- Saldo --}}
            <div class="lg:col-span-1 glass rounded-2xl p-6 relative overflow-hidden glow-primary">
                <div class="absolute -top-16 -right-16 w-48 h-48 bg-violet-600/15 rounded-full blur-[60px]"></div>
                <p class="text-slate-400 text-xs font-semibold tracking-widest uppercase mb-3">Saldo Bersih</p>
                <p class="text-4xl font-black tracking-tight" style="color:var(--text-1)">
                    Rp {{ number_format($saldo, 0, ',', '.') }}
                </p>
                <div class="flex items-center gap-1.5 mt-3 {{ $saldo >= 0 ? 'text-teal-400' : 'text-red-400' }}">
                    <span class="ms text-[16px]">{{ $saldo >= 0 ? 'trending_up' : 'trending_down' }}</span>
                    <span class="text-xs font-semibold">{{ $saldo >= 0 ? 'Keuangan Sehat' : 'Perlu Perhatian' }}</span>
                </div>
            </div>

            {{-- Pemasukan --}}
            <div class="glass glass-hover rounded-2xl p-6 relative overflow-hidden transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-xl bg-teal-500/15 flex items-center justify-center">
                        <span class="ms text-[20px] text-teal-400">arrow_downward</span>
                    </div>
                    <span class="text-teal-400 text-xs font-bold px-2 py-1 bg-teal-500/10 rounded-full">Masuk</span>
                </div>
                <p class="text-slate-400 text-xs font-semibold mb-1">Total Pemasukan</p>
                <p class="text-2xl font-bold" style="color:var(--text-1)">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</p>
            </div>

            {{-- Pengeluaran --}}
            <div class="glass glass-hover rounded-2xl p-6 relative overflow-hidden transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-xl bg-red-500/15 flex items-center justify-center">
                        <span class="ms text-[20px] text-red-400">arrow_upward</span>
                    </div>
                    <span class="text-red-400 text-xs font-bold px-2 py-1 bg-red-500/10 rounded-full">Keluar</span>
                </div>
                <p class="text-slate-400 text-xs font-semibold mb-1">Total Pengeluaran</p>
                <p class="text-2xl font-bold" style="color:var(--text-1)">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
            </div>
        </section>

        {{-- ── TAB SWITCHER ── --}}
        <div class="flex flex-wrap items-center gap-2">
            <div class="glass rounded-2xl p-1.5 flex gap-1">
                @foreach(['semua' => ['label' => 'Semua', 'icon' => 'swap_horiz'], 'pemasukan' => ['label' => 'Pemasukan', 'icon' => 'arrow_downward'], 'pengeluaran' => ['label' => 'Pengeluaran', 'icon' => 'arrow_upward']] as $tab => $cfg)
                <button onclick="switchTransactionTab('{{ $tab }}')"
                        id="tab-{{ $tab }}"
                        data-tab="{{ $tab }}"
                        class="trx-tab-btn flex items-center gap-1 sm:gap-1.5 px-3 sm:px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200
                               text-slate-400 hover:text-white hover:bg-white/5">
                    <span class="ms text-[15px]">{{ $cfg['icon'] }}</span>
                    <span class="hidden xs:inline sm:inline">{{ $cfg['label'] }}</span>
                </button>
                @endforeach
            </div>
            <span id="tabCount"
                  class="text-xs font-semibold text-slate-500 px-3 py-1.5 rounded-full whitespace-nowrap"
                  style="background:var(--bg-subtle)">
                {{ $riwayat->count() }} transaksi
            </span>
        </div>

        {{-- ── FILTER BAR ── --}}
        <div class="glass rounded-2xl px-6 py-4 flex flex-wrap items-center gap-4 relative z-20">
            <span class="text-slate-400 text-xs font-semibold tracking-widest uppercase">Filter</span>
            <div class="flex gap-2 flex-wrap">
                @foreach(['all'=>'Semua','week'=>'1 Minggu','month'=>'1 Bulan','custom'=>'Custom'] as $val=>$label)
                    @php $isActive = request('periode','all') === $val; @endphp
                    <button onclick="applyPeriodFilter('{{ $val }}')"
                            class="px-4 py-1.5 rounded-full text-xs font-semibold transition-all duration-200
                                   {{ $isActive ? 'bg-violet-600 shadow-[0_0_12px_rgba(124,58,237,0.4)]' : '' }}"
                            style="{{ $isActive ? 'color:#ffffff' : 'color:var(--text-3); background:var(--bg-input)' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
            @if(request('periode') === 'custom')
                <div class="flex items-center gap-2 ml-auto">
                    <input type="date" name="start_date" value="{{ request('start_date') }}" onchange="updateCustomFilter()"
                           class="rounded-xl px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-violet-500/40"
                           style="background:var(--bg-input); border:1px solid var(--border); color:var(--text-1)">
                    <span class="text-xs" style="color:var(--text-4)">s/d</span>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" onchange="updateCustomFilter()"
                           class="rounded-xl px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-violet-500/40"
                           style="background:var(--bg-input); border:1px solid var(--border); color:var(--text-1)">
                </div>
            @endif
            {{-- Export --}}
            <div class="ml-auto relative" id="exportDropdown">
                <button onclick="toggleExport()"
                        class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold
                               transition-all"
                        style="color:var(--text-2); background:var(--bg-input); border:1px solid var(--border)">
                    <span class="ms text-[16px]">download</span> Export
                    <span class="ms text-[14px]">expand_more</span>
                </button>
                <div id="exportMenu"
                     class="absolute right-0 top-full mt-2 w-48 rounded-xl overflow-hidden hidden z-[999]"
                     style="background:var(--bg-card); border:1px solid var(--border);
                            box-shadow:var(--shadow-lg)">
                    <a href="{{ route('transaksi.export', ['type' => 'csv']) }}?{{ http_build_query(request()->only(['periode','start_date','end_date'])) }}"
                       class="flex items-center gap-3 px-4 py-3 text-xs font-semibold transition-colors"
                       style="color:var(--text-2)"
                       onmouseenter="this.style.background='var(--bg-hover)'"
                       onmouseleave="this.style.background=''">
                        <span class="ms text-[16px] text-teal-400">table_view</span> Export CSV
                    </a>
                    <a href="{{ route('transaksi.export', ['type' => 'excel']) }}?{{ http_build_query(request()->only(['periode','start_date','end_date'])) }}"
                       class="flex items-center gap-3 px-4 py-3 text-xs font-semibold transition-colors"
                       style="color:var(--text-2); border-top:1px solid var(--border)"
                       onmouseenter="this.style.background='var(--bg-hover)'"
                       onmouseleave="this.style.background=''">
                        <span class="ms text-[16px] text-green-400">grid_on</span> Export Excel
                    </a>
                </div>
            </div>
        </div>

        {{-- ── MAIN GRID ── --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 relative z-10">

            {{-- Transaksi Terbaru --}}
            <div class="xl:col-span-2 glass rounded-2xl flex flex-col">
                <div class="flex items-center justify-between px-6 py-4" style="border-bottom:1px solid var(--border)">
                    <div class="flex items-center gap-2">
                        <span class="ms text-[20px] text-violet-400">receipt_long</span>
                        <h3 class="font-bold" style="color:var(--text-1)">Transaksi Terbaru</h3>
                    </div>
                    <span id="listCount" class="text-xs font-semibold text-slate-400 px-3 py-1 rounded-full"
                          style="background:var(--bg-subtle)">
                        {{ $riwayat->count() }} transaksi
                    </span>
                </div>
                {{-- Scrollable list --}}
                <div class="trx-scroll divide-y" id="transactionList" style="border-color:var(--divider)">
                    @forelse($riwayat as $t)
                        <div class="trx-row px-4 sm:px-6 py-3.5 transition-all duration-200 group"
                             data-tipe="{{ $t->tipe }}"
                             onmouseenter="this.style.background='var(--bg-hover)'"
                             onmouseleave="this.style.background=''">

                            {{-- Layout: icon + info + amount + actions --}}
                            <div class="flex items-center gap-3">

                                {{-- Icon --}}
                                @php
                                    $isTabunganRow = $t->tipe === 'pengeluaran' && !$t->kategori
                                        && $t->deskripsi && str_starts_with($t->deskripsi, 'Tabungan:');
                                @endphp
                                <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                                     style="background:{{ $isTabunganRow ? 'var(--icon-violet-bg)' : ($t->tipe === 'pemasukan' ? 'var(--icon-teal-bg)' : 'var(--icon-red-bg)') }}">
                                    <span class="ms text-[18px] sm:text-[20px]"
                                          style="color:{{ $isTabunganRow ? 'var(--accent-violet)' : ($t->tipe === 'pemasukan' ? 'var(--accent-teal)' : 'var(--accent-red)') }}">
                                        @if($isTabunganRow)
                                            savings
                                        @elseif($t->kategori)
                                            {{ \App\Enums\KategoriTransaksi::icon($t->kategori) }}
                                        @else
                                            {{ $t->tipe === 'pemasukan' ? 'arrow_downward' : 'arrow_upward' }}
                                        @endif
                                    </span>
                                </div>

                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold truncate leading-tight text-sm" style="color:var(--text-1)">
                                        @if($isTabunganRow)
                                            {{ $t->deskripsi }}
                                        @elseif($t->kategori)
                                            {{ \App\Enums\KategoriTransaksi::label($t->kategori) }}
                                        @else
                                            {{ $t->tipe === 'pemasukan' ? 'Pemasukan' : 'Pengeluaran' }}
                                        @endif
                                    </p>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span class="text-xs flex-shrink-0" style="color:var(--text-4)">
                                            {{ $t->tanggal->format('d M Y') }}
                                        </span>
                                        @if($isTabunganRow)
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full"
                                              style="background:var(--icon-violet-bg); color:var(--accent-violet)">
                                            Tabungan
                                        </span>
                                        @elseif($t->deskripsi)
                                        <span class="text-[11px] truncate" style="color:var(--text-4)">
                                            · {{ $t->deskripsi }}
                                        </span>
                                        @else
                                        <span class="text-[11px]" style="color:var(--text-4)">· —</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Amount + Actions --}}
                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    <p class="text-xs sm:text-sm font-bold"
                                       style="color:{{ $isTabunganRow ? 'var(--accent-violet)' : ($t->tipe === 'pemasukan' ? 'var(--accent-teal)' : 'var(--accent-red)') }}">
                                        {{ $t->tipe === 'pemasukan' ? '+' : '-' }}Rp&nbsp;{{ number_format($t->jumlah, 0, ',', '.') }}
                                    </p>
            {{-- Actions: always visible on mobile, hover on desktop --}}
                                    <div class="flex gap-1 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                                        <button onclick="openEditModal({{ $t->toJson() }})"
                                                class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg hover:bg-violet-500/20 hover:text-violet-400 flex items-center justify-center transition-all"
                                                style="background:var(--bg-input); color:var(--text-4)">
                                            <span class="ms text-[14px] sm:text-[16px]">edit</span>
                                        </button>
                                        <button onclick="openDeleteModal({{ $t->id }})"
                                                class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg hover:bg-red-500/20 hover:text-red-400 flex items-center justify-center transition-all"
                                                style="background:var(--bg-input); color:var(--text-4)">
                                            <span class="ms text-[14px] sm:text-[16px]">delete</span>
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @empty
                        <div id="emptyState" class="flex flex-col items-center justify-center py-16 text-slate-500">
                            <span class="ms text-[48px] mb-3 opacity-30">receipt_long</span>
                            <p class="text-sm font-semibold">Belum ada transaksi</p>
                            <button onclick="openModal('quickAddModal')"
                                    class="mt-4 text-xs text-violet-400 hover:text-violet-300 font-semibold transition-colors">
                                + Tambah Transaksi Pertama
                            </button>
                        </div>
                    @endforelse
                </div>
                {{-- Empty state for filtered tab --}}
                <div id="tabEmptyState" class="hidden flex-col items-center justify-center py-16 text-slate-500">                    <span class="ms text-[48px] mb-3 opacity-30">filter_list</span>
                    <p class="text-sm font-semibold" id="tabEmptyMsg">Tidak ada transaksi</p>
                </div>
                {{-- Scroll hint --}}
                @if($riwayat->count() > 8)
                <div class="px-6 py-2.5 flex items-center justify-center gap-1.5" style="border-top:1px solid var(--border)">
                    <span class="ms text-[14px] text-slate-600">expand_more</span>
                    <span class="text-[10px] text-slate-600 font-semibold uppercase tracking-widest">
                        Scroll untuk lihat {{ $riwayat->count() }} transaksi
                    </span>
                </div>
                @endif
            </div>

            {{-- Right Column --}}
            <div class="flex flex-col gap-6">

                {{-- Chart --}}
                <div class="glass rounded-2xl p-6">
                    <div class="flex items-center gap-2 mb-5">
                        <span class="ms text-[20px] text-violet-400">donut_large</span>
                        <h3 class="font-bold" style="color:var(--text-1)">Distribusi</h3>
                    </div>
                    <div class="relative h-44">
                        <canvas id="pieChart"></canvas>
                    </div>
                    <div class="flex gap-4 mt-4 justify-center">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-teal-400"></div>
                            <span class="text-xs text-slate-400">Pemasukan</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-red-400"></div>
                            <span class="text-xs text-slate-400">Pengeluaran</span>
                        </div>
                    </div>
                </div>

                {{-- Stats --}}
                <div class="glass rounded-2xl p-6">
                    <div class="flex items-center gap-2 mb-5">
                        <span class="ms text-[20px] text-violet-400">bar_chart</span>
                        <h3 class="font-bold" style="color:var(--text-1)">Statistik</h3>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        @php
                        $stats = [
                            ['label'=>'Transaksi',    'value'=> $riwayat->count(),                                    'icon'=>'swap_horiz', 'color'=>'text-violet-400'],
                            ['label'=>'Pemasukan',    'value'=>'Rp '.number_format($totalPemasukan,0,',','.'),         'icon'=>'arrow_downward','color'=>'text-teal-400'],
                            ['label'=>'Pengeluaran',  'value'=>'Rp '.number_format($totalPengeluaran,0,',','.'),       'icon'=>'arrow_upward','color'=>'text-red-400'],
                            ['label'=>'Saldo',        'value'=>'Rp '.number_format($saldo,0,',','.'),                  'icon'=>'account_balance_wallet','color'=>$saldo>=0?'text-teal-400':'text-red-400'],
                        ];
                        @endphp
                        @foreach($stats as $s)
                            <div class="rounded-xl p-3" style="background:var(--bg-subtle); border:1px solid var(--border)">
                                <span class="ms text-[18px] {{ $s['color'] }}">{{ $s['icon'] }}</span>
                                <p class="text-sm font-bold mt-1 truncate" style="color:var(--text-1)">{{ $s['value'] }}</p>
                                <p class="text-[10px] font-semibold uppercase tracking-wider mt-0.5" style="color:var(--text-4)">{{ $s['label'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Bar Chart --}}
                <div class="glass rounded-2xl p-6">
                    <div class="flex items-center gap-2 mb-5">
                        <span class="ms text-[20px] text-violet-400">bar_chart</span>
                        <h3 class="font-bold" style="color:var(--text-1)">Perbandingan</h3>
                    </div>
                    <div class="relative h-36">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>

            </div>
        </div>

        {{-- ── ANALISIS PENGELUARAN PER KATEGORI ── --}}
        @php
            use App\Enums\KategoriTransaksi;
            $totalPengeluaranKat = $kategoriStats->sum('total') ?: 1;
            $katColors = ['#f87171','#fb923c','#fbbf24','#a78bfa','#60a5fa','#34d399','#f472b6','#94a3b8'];
        @endphp
        @if($kategoriStats->isNotEmpty())
        <div class="glass rounded-2xl overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4" style="border-bottom:1px solid var(--border)">
                <div class="flex items-center gap-2">
                    <span class="ms text-[20px] text-violet-400">pie_chart</span>
                    <h3 class="font-bold" style="color:var(--text-1)">Analisis Pengeluaran</h3>
                </div>
                <span class="text-xs text-slate-500 px-3 py-1 rounded-full" style="background:var(--bg-subtle)">
                    Total: Rp {{ number_format($totalPengeluaranKat, 0, ',', '.') }}
                </span>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">

                    {{-- Doughnut Chart --}}
                    <div class="flex flex-col items-center">
                        <div class="relative w-52 h-52">
                            <canvas id="userCategoryChart"></canvas>
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                <p class="text-slate-500 text-[10px] font-semibold uppercase tracking-wider">Pengeluaran</p>
                                <p class="font-black text-base leading-tight mt-0.5" style="color:var(--text-1)">
                                    {{ $kategoriStats->count() }} Kategori
                                </p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-x-6 gap-y-1.5 mt-4 w-full max-w-xs">
                            @foreach($kategoriStats->take(8) as $i => $k)
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full flex-shrink-0"
                                     style="background:{{ $katColors[$i % count($katColors)] }}"></div>
                                <span class="text-slate-400 text-[11px] truncate">
                                    {{ KategoriTransaksi::label($k->kategori) }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Persentase List --}}
                    <div class="space-y-3">
                        @foreach($kategoriStats->take(8) as $i => $k)
                        @php
                            $pct  = round(($k->total / $totalPengeluaranKat) * 100, 1);
                            $icon = KategoriTransaksi::icon($k->kategori);
                            $lbl  = KategoriTransaksi::label($k->kategori);
                            $clr  = $katColors[$i % count($katColors)];
                        @endphp
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                                 style="background:{{ $clr }}20">
                                <span class="ms text-[18px]" style="color:{{ $clr }}">{{ $icon }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-semibold truncate" style="color:var(--text-2)">{{ $lbl }}</span>
                                    <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                                        <span class="text-xs font-black" style="color:{{ $clr }}">{{ $pct }}%</span>
                                        <span class="text-slate-500 text-[10px]">
                                            Rp {{ number_format($k->total, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="h-2 rounded-full overflow-hidden" style="background:var(--bg-subtle)">
                                    <div class="h-full rounded-full transition-all duration-700"
                                         style="width:{{ $pct }}%; background:{{ $clr }}"></div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>
</main>

{{-- ══════════════════════════════════════════════════════════
     MODALS
══════════════════════════════════════════════════════════ --}}

{{-- Quick Add --}}
<div id="quickAddModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
    <div class="modal-anim rounded-2xl w-full max-w-md"
         style="background:var(--bg-card); border:1px solid var(--border); box-shadow:var(--shadow-lg)">
        <div class="flex items-center justify-between px-6 py-5" style="border-bottom:1px solid var(--border)">
            <h3 class="font-bold flex items-center gap-2" style="color:var(--text-1)">
                <span class="ms text-[20px] text-violet-400">add_circle</span> Tambah Transaksi
            </h3>
            <button onclick="closeModal('quickAddModal')"
                    class="w-8 h-8 rounded-lg flex items-center justify-center transition-all"
                    style="background:var(--bg-input); color:var(--text-3)"
                    onmouseenter="this.style.color='var(--text-1)'"
                    onmouseleave="this.style.color='var(--text-3)'">
                <span class="ms text-[20px]">close</span>
            </button>
        </div>
        <form method="POST" action="{{ route('transaksi.store') }}" class="p-6 space-y-4"
              style="background:var(--bg-card)">
            @csrf
            @include('partials.transaksi-form', ['mode'=>'add'])
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('quickAddModal')"
                        class="flex-1 py-2.5 rounded-xl text-sm font-semibold transition-all"
                        style="background:var(--bg-input); color:var(--text-3)">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 rounded-xl text-sm font-bold
                               bg-gradient-to-r from-violet-600 to-violet-500 hover:from-violet-500 hover:to-violet-400
                               transition-all glow-primary active:scale-95"
                        style="color:#ffffff">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Edit --}}
<div id="editModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
    <div class="modal-anim rounded-2xl w-full max-w-md"
         style="background:var(--bg-card); border:1px solid var(--border); box-shadow:var(--shadow-lg)">
        <div class="flex items-center justify-between px-6 py-5" style="border-bottom:1px solid var(--border)">
            <h3 class="font-bold flex items-center gap-2" style="color:var(--text-1)">
                <span class="ms text-[20px] text-violet-400">edit</span> Edit Transaksi
            </h3>
            <button onclick="closeModal('editModal')"
                    class="w-8 h-8 rounded-lg flex items-center justify-center transition-all"
                    style="background:var(--bg-input); color:var(--text-3)"
                    onmouseenter="this.style.color='var(--text-1)'"
                    onmouseleave="this.style.color='var(--text-3)'">
                <span class="ms text-[20px]">close</span>
            </button>
        </div>
        <form method="POST" id="editForm" class="p-6 space-y-4"
              style="background:var(--bg-card)">
            @csrf @method('PUT')
            @include('partials.transaksi-form', ['mode'=>'edit'])
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('editModal')"
                        class="flex-1 py-2.5 rounded-xl text-sm font-semibold transition-all"
                        style="background:var(--bg-input); color:var(--text-3)">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 rounded-xl text-sm font-bold
                               bg-gradient-to-r from-violet-600 to-violet-500 hover:from-violet-500 hover:to-violet-400
                               transition-all glow-primary active:scale-95"
                        style="color:#ffffff">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Delete --}}
<div id="deleteModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
    <div class="modal-anim rounded-2xl w-full max-w-sm"
         style="background:var(--bg-card); border:1px solid var(--border); box-shadow:var(--shadow-lg)">
        <div class="p-6 text-center">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4"
                 style="background:var(--icon-red-bg)">
                <span class="ms text-[28px] text-red-400">delete_forever</span>
            </div>
            <h3 class="font-bold text-lg mb-2" style="color:var(--text-1)">Hapus Transaksi?</h3>
            <p class="text-sm" style="color:var(--text-3)">Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <form method="POST" id="deleteForm" class="px-6 pb-6">
            @csrf @method('DELETE')
            <div class="flex gap-3">
                <button type="button" onclick="closeModal('deleteModal')"
                        class="flex-1 py-2.5 rounded-xl text-sm font-semibold transition-all"
                        style="background:var(--bg-input); color:var(--text-3)">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 rounded-xl text-sm font-bold
                               bg-gradient-to-r from-red-600 to-red-500 hover:from-red-500 hover:to-red-400
                               transition-all glow-danger active:scale-95"
                        style="color:#ffffff">
                    Ya, Hapus
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const chartPemasukan   = {{ (int)$chartPemasukan }};
const chartPengeluaran = {{ (int)$chartPengeluaran }};

@if($kategoriStats->isNotEmpty())
@php
    $katColorsJs = ['#f87171','#fb923c','#fbbf24','#a78bfa','#60a5fa','#34d399','#f472b6','#94a3b8'];
    $katTop = $kategoriStats->take(8);
@endphp
const userCatData = {
    labels: @json($katTop->map(fn($k) => \App\Enums\KategoriTransaksi::label($k->kategori))),
    data:   @json($katTop->pluck('total')),
    colors: @json(array_slice($katColorsJs, 0, $katTop->count())),
};
@else
const userCatData = null;
@endif

const kategoriIcons = @json(array_map(fn($v) => $v['icon'], \App\Enums\KategoriTransaksi::all()));

const chartDefaults = {
    plugins: { legend: { display: false } },
    responsive: true,
    maintainAspectRatio: false,
};

document.addEventListener('DOMContentLoaded', () => {
    const c = getChartThemeColors();

    // ── Pie / Doughnut ──
    registerChart(new Chart(document.getElementById('pieChart'), {
        type: 'doughnut',
        data: {
            labels: ['Pemasukan','Pengeluaran'],
            datasets: [{ data: [chartPemasukan, chartPengeluaran],
                backgroundColor: ['rgba(45,212,191,0.8)','rgba(248,113,113,0.8)'],
                borderColor: ['#2dd4bf','#f87171'], borderWidth: 2, hoverOffset: 6 }]
        },
        options: { ...chartDefaults, cutout: '72%',
            plugins: { legend: { display: false }, tooltip: {
                backgroundColor: c.tooltipBg, titleColor: c.tooltipTitle,
                bodyColor: c.tooltipBody, borderColor: c.tooltipBorder, borderWidth: 1,
                callbacks: { label: ctx => ' Rp ' + ctx.parsed.toLocaleString('id-ID') }
            }}
        }
    }));

    // ── Bar ──
    registerChart(new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: ['Periode Ini'],
            datasets: [
                { label:'Pemasukan',   data:[chartPemasukan],   backgroundColor:'rgba(45,212,191,0.7)',  borderRadius:6 },
                { label:'Pengeluaran', data:[chartPengeluaran], backgroundColor:'rgba(248,113,113,0.7)', borderRadius:6 },
            ]
        },
        options: { ...chartDefaults,
            scales: {
                x: { grid: { color: c.gridColor }, ticks: { color: c.tickColor } },
                y: { grid: { color: c.gridColor }, ticks: { color: c.tickColor }, beginAtZero:true }
            },
            plugins: { legend: { display:true, labels:{ color: c.legendColor, boxWidth:10, font:{size:11} } },
                tooltip: { backgroundColor: c.tooltipBg, titleColor: c.tooltipTitle,
                    bodyColor: c.tooltipBody, borderColor: c.tooltipBorder, borderWidth: 1 }
            }
        }
    }));

    // ── User Category Doughnut ──
    if (userCatData) {
        const ucCanvas = document.getElementById('userCategoryChart');
        if (ucCanvas) {
            registerChart(new Chart(ucCanvas, {
                type: 'doughnut',
                data: {
                    labels: userCatData.labels,
                    datasets: [{ data: userCatData.data, backgroundColor: userCatData.colors, borderWidth: 0, hoverOffset: 8 }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false, cutout: '72%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: c.tooltipBg, titleColor: c.tooltipTitle,
                            bodyColor: c.tooltipBody, borderColor: c.tooltipBorder, borderWidth: 1, padding: 10,
                            callbacks: {
                                label: ctx => {
                                    const total = ctx.dataset.data.reduce((a,b) => a+b, 0);
                                    const pct   = ((ctx.parsed / total) * 100).toFixed(1);
                                    return ` ${pct}% — Rp ${ctx.parsed.toLocaleString('id-ID')}`;
                                }
                            }
                        }
                    }
                }
            }));
        }
    }

    // Init tab aktif
    switchTransactionTab('semua');
});

// ── Tab Switcher ──────────────────────────────────────────────────────────────
let activeTab = 'semua';

function switchTransactionTab(tipe) {
    activeTab = tipe;

    document.querySelectorAll('.trx-tab-btn').forEach(btn => {
        const isActive = btn.dataset.tab === tipe;
        btn.removeAttribute('data-active');
        btn.style.background = '';
        btn.style.color      = '';
        btn.style.boxShadow  = '';
        if (isActive) {
            btn.dataset.active  = '1';
            btn.style.background = 'var(--accent-violet)';
            btn.style.color      = '#fff';
            btn.style.boxShadow  = '0 0 12px rgba(124,58,237,0.4)';
        }
    });

    const rows = document.querySelectorAll('.trx-row');
    let visible = 0;
    rows.forEach(row => {
        const show = tipe === 'semua' || row.dataset.tipe === tipe;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    const countEl    = document.getElementById('listCount');
    const tabCountEl = document.getElementById('tabCount');
    if (countEl)    countEl.textContent    = visible + ' transaksi';
    if (tabCountEl) tabCountEl.textContent = visible + ' transaksi';

    const tabEmpty    = document.getElementById('tabEmptyState');
    const tabEmptyMsg = document.getElementById('tabEmptyMsg');
    if (tabEmpty) {
        const show = visible === 0 && rows.length > 0;
        tabEmpty.classList.toggle('hidden', !show);
        tabEmpty.classList.toggle('flex', show);
        if (show && tabEmptyMsg) {
            tabEmptyMsg.textContent = tipe === 'pemasukan'
                ? 'Tidak ada transaksi pemasukan'
                : 'Tidak ada transaksi pengeluaran';
        }
    }
}

// ── Kategori Icon Update ──────────────────────────────────────────────────────
function updateKategoriIcon(prefix, key) {
    const iconEl = document.getElementById(prefix + 'KategoriIcon');
    if (!iconEl) return;
    if (key && kategoriIcons[key]) {
        iconEl.textContent = kategoriIcons[key];
        iconEl.style.color = 'var(--accent-violet)';
    } else {
        iconEl.textContent = 'label';
        iconEl.style.color = 'var(--text-4)';
    }
}

// ── Auto-fill Deskripsi dari Kategori ────────────────────────────────────────
// Keterangan sepenuhnya opsional, tidak ada auto-fill
function autoFillDeskripsi(prefix, key) {
    // intentionally empty — user mengisi sendiri
}

// preserveValue: true saat edit (jangan reset select)
function filterKategoriOptgroup(prefix, tipe, preserveValue) {
    const optPemasukan   = document.getElementById(prefix + 'OptPemasukan');
    const optPengeluaran = document.getElementById(prefix + 'OptPengeluaran');
    const select         = document.getElementById(prefix + 'Kategori');
    if (!optPemasukan || !optPengeluaran || !select) return;

    optPemasukan.style.display   = tipe === 'pemasukan'   ? '' : 'none';
    optPengeluaran.style.display = tipe === 'pengeluaran' ? '' : 'none';

    if (!preserveValue) {
        select.value = '';
        updateKategoriIcon(prefix, '');
    }
}

// ── Sidebar mobile ────────────────────────────────────────────────────────────
function openSidebar() {
    document.getElementById('sidebar').classList.remove('-translate-x-full');
    document.getElementById('sidebarOverlay').classList.remove('hidden');
}
function closeSidebar() {
    document.getElementById('sidebar').classList.add('-translate-x-full');
    document.getElementById('sidebarOverlay').classList.add('hidden');
}

// ── Reset form tambah ke state awal ──────────────────────────────────────────
function resetAddForm() {
    const m = document.getElementById('quickAddModal');
    if (!m) return;
    setActiveType('quickAddModal', 'pemasukan');
    filterKategoriOptgroup('add', 'pemasukan', false);

    const j = m.querySelector('input[name="jumlah"]');
    const d = m.querySelector('input[name="deskripsi"]');
    const t = m.querySelector('input[name="tanggal"]');
    if (j) j.value = '';
    if (d) d.value = '';
    if (t) t.value = '{{ now()->format("Y-m-d") }}';

    // Reset display jumlah
    const jDisp = document.getElementById('addJumlahDisplay');
    if (jDisp) jDisp.value = '';

    // Reset hidden fields
    const hiddenTipe     = document.getElementById('addTransactionType');
    const hiddenTabungan = document.getElementById('addIsTabungan');
    if (hiddenTipe)     hiddenTipe.value     = 'pemasukan';
    if (hiddenTabungan) hiddenTabungan.value = '0';

    // Tampilkan kategori, sembunyikan tabungan section
    const tabSection = document.getElementById('addTabunganSection');
    const katSection = document.getElementById('addKategoriSection');
    const katSelect  = document.getElementById('addKategori');
    if (tabSection) tabSection.style.display = 'none';
    if (katSection) katSection.style.display = '';
    if (katSelect)  katSelect.required = true;

    // Reset goal
    const goalSel = document.getElementById('addGoalId');
    if (goalSel) goalSel.value = '';
}

// ── Modal helpers ─────────────────────────────────────────────────────────────
function openModal(id) {
    const m = document.getElementById(id);
    if (!m) return;
    m.classList.remove('hidden');
    m.classList.add('flex');
    if (id === 'quickAddModal') resetAddForm();
}
function closeModal(id) {
    const m = document.getElementById(id);
    if (!m) return;
    m.classList.add('hidden');
    m.classList.remove('flex');
}
document.querySelectorAll('[id$="Modal"]').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) closeModal(m.id); });
});

// ── Edit modal ────────────────────────────────────────────────────────────────
function openEditModal(t) {
    document.getElementById('editForm').action           = '{{ url("transaksi") }}/' + t.id;
    document.getElementById('editId').value              = t.id;
    // Hidden value (raw number)
    document.getElementById('editJumlah').value          = t.jumlah;
    // Display value dengan format ribuan
    const editDisp = document.getElementById('editJumlahDisplay');
    if (editDisp) editDisp.value = parseInt(t.jumlah).toLocaleString('id-ID');
    document.getElementById('editDeskripsi').value       = t.deskripsi || '';
    document.getElementById('editTanggal').value         = t.tanggal;
    document.getElementById('editTransactionType').value = t.tipe;

    setActiveType('editModal', t.tipe);
    filterKategoriOptgroup('edit', t.tipe, true);

    const editSel = document.getElementById('editKategori');
    if (editSel) {
        editSel.value = t.kategori || '';
        updateKategoriIcon('edit', t.kategori || '');
    }

    openModal('editModal');
}

// ── Set active type button ────────────────────────────────────────────────────
function setActiveType(modalId, tipe) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.querySelectorAll('.type-btn').forEach(b => b.classList.remove('active-type'));
    const target = modal.querySelector(`.type-btn[data-value="${tipe}"]`);
    if (target) target.classList.add('active-type');
    const hidden = modal.querySelector('input[name="tipe"]');
    if (hidden) hidden.value = tipe;
}

// ── Delete modal ──────────────────────────────────────────────────────────────
function openDeleteModal(id) {
    document.getElementById('deleteForm').action = '{{ url("transaksi") }}/' + id;
    openModal('deleteModal');
}

// ── Type selector (delegated click) ──────────────────────────────────────────
document.addEventListener('click', function(e) {
    const typeBtn = e.target.closest('.type-btn');
    if (!typeBtn) return;
    const modal = typeBtn.closest('[id$="Modal"]');
    if (!modal) return;

    const tipe   = typeBtn.dataset.value;
    const prefix = modal.id === 'quickAddModal' ? 'add' : 'edit';

    setActiveType(modal.id, tipe);

    const isTabungan = tipe === 'tabungan';

    // Untuk tabungan: tipe server = pengeluaran, is_tabungan = 1
    const hiddenTipe     = document.getElementById(prefix + 'TransactionType');
    const hiddenTabungan = document.getElementById(prefix + 'IsTabungan');
    if (hiddenTipe)     hiddenTipe.value     = isTabungan ? 'pengeluaran' : tipe;
    if (hiddenTabungan) hiddenTabungan.value = isTabungan ? '1' : '0';

    // Tampilkan/sembunyikan section
    const tabunganSection = document.getElementById(prefix + 'TabunganSection');
    const kategoriSection = document.getElementById(prefix + 'KategoriSection');
    const kategoriSelect  = document.getElementById(prefix + 'Kategori');

    if (tabunganSection) tabunganSection.style.display = isTabungan ? '' : 'none';
    if (kategoriSection) kategoriSection.style.display = isTabungan ? 'none' : '';

    // Kategori required hanya saat bukan tabungan
    if (kategoriSelect) kategoriSelect.required = !isTabungan;

    if (!isTabungan) {
        filterKategoriOptgroup(prefix, tipe, false);
        const d = document.getElementById(prefix + 'Deskripsi');
        if (d) d.value = '';
        // Reset goal
        const goalSel = document.getElementById(prefix + 'GoalId');
        if (goalSel) goalSel.value = '';
    } else {
        // Saat tabungan: auto-fill deskripsi dari nama goal yang dipilih
        const goalSel = document.getElementById(prefix + 'GoalId');
        if (goalSel) {
            goalSel.onchange = function() {
                const opt = goalSel.options[goalSel.selectedIndex];
                const d   = document.getElementById(prefix + 'Deskripsi');
                const hint = document.getElementById(prefix + 'GoalSisaHint');
                if (d && opt.dataset.nama) d.value = 'Tabungan: ' + opt.dataset.nama;
                if (hint && opt.dataset.sisa) {
                    hint.textContent = 'Sisa target: Rp ' + parseInt(opt.dataset.sisa).toLocaleString('id-ID');
                } else if (hint) {
                    hint.textContent = '';
                }
            };
        }
    }
});

// ── Period filter ─────────────────────────────────────────────────────────────
function applyPeriodFilter(p) {
    const url = new URL(window.location);
    url.searchParams.set('periode', p);
    if (p !== 'custom') { url.searchParams.delete('start_date'); url.searchParams.delete('end_date'); }
    window.location.href = url.toString();
}
function updateCustomFilter() {
    const s = document.querySelector('input[name="start_date"]')?.value;
    const e = document.querySelector('input[name="end_date"]')?.value;
    if (s && e) {
        const url = new URL(window.location);
        url.searchParams.set('periode','custom');
        url.searchParams.set('start_date',s);
        url.searchParams.set('end_date',e);
        window.location.href = url.toString();
    }
}

// ── Notification Bell ────────────────────────────────────────────────────────
function toggleNotifPanel() {
    const panel = document.getElementById('notifPanel');
    panel.classList.toggle('hidden');
}

// Tutup saat klik di luar
document.addEventListener('click', function(e) {
    const wrap = document.getElementById('notifDropdownWrap');
    if (wrap && !wrap.contains(e.target)) {
        document.getElementById('notifPanel')?.classList.add('hidden');
    }
});

function dismissNotif(idx, btn) {
    const item = btn.closest('.notif-item');
    if (item) item.remove();
    updateNotifBadge();
}

function dismissAllNotif() {
    document.querySelectorAll('.notif-item').forEach(el => el.remove());
    updateNotifBadge();
    // Tampilkan empty state
    const list = document.getElementById('notifList');
    if (list && list.children.length === 0) {
        list.innerHTML = `
            <div class="flex flex-col items-center justify-center py-10 gap-2">
                <span class="ms text-[36px]" style="color:var(--text-4)">notifications_none</span>
                <p class="text-xs" style="color:var(--text-4)">Tidak ada notifikasi</p>
            </div>`;
    }
}

function updateNotifBadge() {
    const count = document.querySelectorAll('.notif-item').length;
    const badge = document.querySelector('#notifBellBtn span[class*="bg-red-500"]');
    const headerBadge = document.querySelector('#notifPanel span[class*="bg-red-500"]');

    if (count === 0) {
        badge?.remove();
        headerBadge?.remove();
    } else {
        const label = count > 99 ? '99+' : count;
        if (badge) badge.textContent = label;
        if (headerBadge) headerBadge.textContent = label;
    }
}

// ── Export dropdown ───────────────────────────────────────────────────────────
function toggleExport() {
    document.getElementById('exportMenu').classList.toggle('hidden');
}
document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('exportDropdown');
    if (dropdown && !dropdown.contains(e.target)) {
        document.getElementById('exportMenu')?.classList.add('hidden');
    }
});
</script>
</body>
</html>
