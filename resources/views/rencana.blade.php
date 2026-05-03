<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <title>Budget & Goals — {{ $appName }}</title>
    @include('partials.head')
    <style>
    /* glass-card ikut tema via CSS variable */
    .glass-card {
        background: var(--bg-card);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        transition: background 0.25s, border-color 0.25s;
    }
    html:not(.dark) .glass-card {
        background: rgba(255,255,255,0.9);
        border-color: rgba(0,0,0,0.08);
        box-shadow: 0 1px 4px rgba(0,0,0,0.07);
    }
    .goal-achieved {
        background: rgba(45,212,191,0.06);
        border: 1px solid rgba(45,212,191,0.25);
        box-shadow: 0 0 25px rgba(45,212,191,0.08);
    }
    html:not(.dark) .goal-achieved {
        background: rgba(13,148,136,0.06);
        border-color: rgba(13,148,136,0.2);
    }
    .neon-glow-primary  { box-shadow: 0 0 20px rgba(124,58,237,0.3); }
    .neon-glow-secondary{ box-shadow: 0 0 20px rgba(45,212,191,0.25); }
    .progress-glow-violet { box-shadow: 0 0 10px rgba(124,58,237,0.5); }
    .progress-glow-teal   { box-shadow: 0 0 10px rgba(45,212,191,0.5); }
    .progress-glow-red    { box-shadow: 0 0 10px rgba(239,68,68,0.5); }
    .progress-glow-yellow { box-shadow: 0 0 10px rgba(234,179,8,0.5); }
    @keyframes shimmer { 0%,100%{opacity:1} 50%{opacity:0.6} }
    .shimmer { animation: shimmer 2s ease-in-out infinite; }

    /* Progress bar track */
    .progress-track {
        background: var(--bg-input);
    }

    /* Ringkasan card background */
    .ringkasan-card {
        background: var(--bg-input);
        border-left: 4px solid var(--accent-violet);
    }

    /* Over-budget shimmer bar */
    @keyframes shimmer-danger {
        0% { background-position: -200% center; }
        100% { background-position: 200% center; }
    }
    .over-budget-bar {
        background: linear-gradient(90deg, #dc2626, #f87171, #dc2626) !important;
        background-size: 200% auto !important;
        animation: shimmer-danger 1.5s linear infinite !important;
        box-shadow: 0 0 15px rgba(220,38,38,0.6) !important;
    }

    /* Goal achievement celebration */
    @keyframes celebrate-pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(45,212,191,0); }
        50% { box-shadow: 0 0 25px 8px rgba(45,212,191,0.3); }
    }
    .goal-celebrate {
        animation: celebrate-pulse 2s ease-in-out infinite;
    }

    /* Floating particles */
    @keyframes float-up {
        0% { transform: translateY(0) scale(1); opacity: 1; }
        100% { transform: translateY(-40px) scale(0); opacity: 0; }
    }
    .particle {
        position: absolute;
        width: 6px; height: 6px;
        border-radius: 50%;
        background: var(--accent-teal);
        animation: float-up 2s ease-in-out infinite;
        pointer-events: none;
    }
    .particle:nth-child(1) { left: 20%; animation-delay: 0s; }
    .particle:nth-child(2) { left: 50%; animation-delay: 0.5s; background: var(--accent-violet); }
    .particle:nth-child(3) { left: 80%; animation-delay: 1s; }
    </style>
</head>
<body class="overflow-x-hidden font-sans antialiased">

@include('partials.sidebar', ['activeMenu' => 'rencana'])
@include('partials.ai-suggestion')
<div class="fixed inset-0 pointer-events-none -z-10">
    <div class="absolute top-[5%] left-[20%] w-[40rem] h-[40rem] bg-violet-700/8 rounded-full blur-[120px]"></div>
    <div class="absolute bottom-[5%] right-[5%] w-[30rem] h-[30rem] bg-teal-500/6 rounded-full blur-[100px]"></div>
</div>

<main class="ml-64 min-h-screen main-with-sidebar p-4 lg:p-10">

    @include('partials.toast')

    {{-- Header --}}
    <header class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4 mb-8 lg:mb-12">
        <div class="flex items-center gap-3">
            <button onclick="openSidebar()"
                    class="mobile-menu-btn w-9 h-9 rounded-lg items-center justify-center transition-all flex-shrink-0"
                    style="background:var(--bg-input); color:var(--text-3)">
                <span class="ms text-[20px]">menu</span>
            </button>
            <div>
                <h1 class="text-2xl lg:text-3xl font-black mb-1 tracking-tight" style="color:var(--text-1)">Anggaran &amp; Target</h1>
                <p class="text-sm" style="color:var(--text-3)">Alokasi keuangan untuk bulan {{ now()->translatedFormat('F Y') }}</p>
            </div>
        </div>
        <div class="flex gap-3 w-full sm:w-auto">
            <button onclick="openModal('addBudgetModal')"
                class="flex-1 sm:flex-none flex items-center justify-center gap-2 py-2.5 px-4 lg:px-6 rounded-xl text-sm font-bold text-white
                       bg-gradient-to-br from-violet-600 to-teal-500
                       hover:opacity-90 active:scale-95 transition-all neon-glow-primary">
                <span class="ms text-[18px]">add</span> <span class="hidden sm:inline">BUAT</span> ANGGARAN
            </button>
            <button onclick="openModal('addGoalModal')"
                class="flex-1 sm:flex-none flex items-center justify-center gap-2 py-2.5 px-4 lg:px-6 rounded-xl text-sm font-bold
                       text-teal-400 border border-teal-500/30 bg-teal-500/10
                       hover:bg-teal-500/20 active:scale-95 transition-all">
                <span class="ms text-[18px]">flag</span> <span class="hidden sm:inline">BUAT</span> TARGET
            </button>
        </div>
    </header>

    {{-- Bento Grid --}}
    <div class="grid grid-cols-12 gap-6">

        {{-- LEFT: Monthly Budgets --}}
        <div class="col-span-12 lg:col-span-8 space-y-6">
            <div class="flex justify-between items-center px-1">
                <h3 class="font-bold text-xl" style="color:var(--text-1)">Alokasi Bulanan</h3>
                <span class="text-teal-400 text-xs font-bold tracking-widest uppercase">AKTIF</span>
            </div>

            @forelse($budgets as $b)
            @php
                $icon = \App\Enums\KategoriTransaksi::icon($b->kategori ?? '');
                $katLabel = \App\Enums\KategoriTransaksi::label($b->kategori ?? '');
                if ($b->overBudget) {
                    $iconBg = 'bg-red-500/20'; $iconColor = 'text-red-400';
                    $barClass = 'from-red-600 to-red-400'; $barGlow = 'progress-glow-red';
                    $statusColor = 'text-red-400'; $statusText = 'MELEBIHI ANGGARAN';
                    $rightText = '+Rp '.number_format($b->lebih,0,',','.').' KELEBIHAN';
                    $rightColor = 'text-red-400';
                } elseif ($b->persen >= 80) {
                    $iconBg = 'bg-yellow-500/20'; $iconColor = 'text-yellow-400';
                    $barClass = 'from-yellow-600 to-yellow-400'; $barGlow = 'progress-glow-yellow';
                    $statusColor = 'text-yellow-400'; $statusText = $b->persen.'% TERPAKAI';
                    $rightText = 'Rp '.number_format($b->sisa,0,',','.').' TERSISA';
                    $rightColor = '';
                } else {
                    $iconBg = 'bg-violet-500/20'; $iconColor = 'text-violet-400';
                    $barClass = 'from-violet-600 to-violet-400'; $barGlow = 'progress-glow-violet';
                    $statusColor = ''; $statusText = $b->persen.'% TERPAKAI';
                    $rightText = 'Rp '.number_format($b->sisa,0,',','.').' TERSISA';
                    $rightColor = '';
                }
            @endphp
            <div class="glass-card p-6 rounded-xl group">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl {{ $iconBg }} flex items-center justify-center {{ $iconColor }}">
                            <span class="ms text-[24px]">{{ $icon }}</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg" style="color:var(--text-1)">{{ $b->nama }}</h4>
                            <p class="text-sm" style="color:var(--text-4)">{{ $katLabel }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="text-right">
                            <p class="text-xl font-black {{ $b->overBudget ? 'text-red-400' : '' }}" style="{{ !$b->overBudget ? 'color:var(--text-1)' : '' }}">
                                Rp {{ number_format($b->aktual, 0, ',', '.') }}
                            </p>
                            <p class="text-sm" style="color:var(--text-4)">dari Rp {{ number_format($b->target, 0, ',', '.') }}</p>
                        </div>
                        <form method="POST" action="{{ route('rencana.destroy', $b) }}"
                              class="opacity-0 group-hover:opacity-100 transition-opacity mt-1">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Hapus budget ini?')"
                                class="w-7 h-7 rounded-lg hover:bg-red-500/20 hover:text-red-400 flex items-center justify-center transition-all" style="background:var(--bg-input); color:var(--text-4)">
                                <span class="ms text-[14px]">delete</span>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="relative w-full h-3 progress-track rounded-full overflow-hidden mt-4">
                    <div class="absolute top-0 left-0 h-full rounded-full bg-gradient-to-r {{ $barClass }} {{ $barGlow }} {{ $b->overBudget ? 'over-budget-bar' : '' }} transition-all duration-700"
                         style="width: {{ $b->overBudget ? 100 : min($b->persen, 100) }}%"></div>
                </div>
                <div class="flex justify-between mt-2">
                    <span class="text-[10px] font-bold uppercase tracking-widest {{ $statusColor }}"
                          @if(!$statusColor) style="color:var(--text-4)" @endif>{{ $statusText }}</span>
                    <span class="text-[10px] font-bold uppercase tracking-widest {{ $rightColor }}"
                          @if(!$rightColor) style="color:var(--text-4)" @endif>{{ $rightText }}</span>
                </div>
            </div>
            @empty
            <div class="glass-card rounded-xl p-12 text-center">
                <span class="ms text-[48px] block mb-3" style="color:var(--text-4)">account_balance_wallet</span>
                <p class="text-sm font-semibold mb-1" style="color:var(--text-3)">Belum ada budget</p>
                <p class="text-xs mb-4" style="color:var(--text-4)">Buat budget untuk mengontrol pengeluaran per kategori</p>
                <button onclick="openModal('addBudgetModal')"
                    class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-violet-600 to-violet-500 active:scale-95 transition-all">
                    + Buat Budget Pertama
                </button>
            </div>
            @endforelse
        </div>

        {{-- RIGHT: Savings Goals --}}
        <div class="col-span-12 lg:col-span-4 space-y-6">
            <div class="flex justify-between items-center px-1">
                <h3 class="font-bold text-xl" style="color:var(--text-1)">Target Tabungan</h3>
                <span class="text-violet-400 text-xs font-bold tracking-widest uppercase">{{ $goals->count() }} TARGET</span>
            </div>

            @forelse($goals as $g)
            @php $selesai = $g->is_selesai; @endphp

            @if($selesai)
            {{-- ACHIEVED STATE --}}
            <div class="goal-achieved goal-celebrate p-6 rounded-xl relative overflow-hidden">
                {{-- Floating particles --}}
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>

                {{-- Premium badge --}}
                <div class="absolute -top-4 -right-4 bg-teal-400 w-10 h-10 rounded-full
                            flex items-center justify-center shadow-lg rotate-12 neon-glow-secondary"
                     style="color: #0f172a">
                    <span class="ms text-[20px]" style="font-variation-settings:'FILL' 1">workspace_premium</span>
                </div>

                <h4 class="font-bold text-lg mb-4" style="color:var(--text-1)">{{ $g->nama }}</h4>

                <div class="flex items-center gap-4 py-4">
                    {{-- 100% circle --}}
                    <div class="w-16 h-16 rounded-full border-4 border-teal-400/30 flex items-center justify-center relative flex-shrink-0">
                        <div class="absolute inset-0 rounded-full border-4 border-teal-400 shimmer"></div>
                        <span class="text-teal-400 font-black text-sm">100%</span>
                    </div>
                    <div>
                        <p class="font-bold text-lg" style="color:var(--text-1)">Target Tercapai!</p>
                        <p class="text-teal-400/80 text-sm">Rp {{ number_format($g->target, 0, ',', '.') }} terkumpul</p>
                    </div>
                </div>

                <button onclick="openTingkatkanModal({{ $g->id }}, '{{ addslashes($g->nama) }}', {{ $g->target }})"
                    class="w-full mt-2 py-3 rounded-xl text-xs font-bold uppercase tracking-widest
                           text-teal-400 bg-teal-500/10 border border-teal-500/30
                           hover:bg-teal-500/20 transition-colors">
                    <span class="ms text-[16px]">trending_up</span> Tingkatkan Target
                </button>

                <form method="POST" action="{{ route('rencana.destroy', $g) }}" class="mt-2">
                    @csrf @method('DELETE')
                    <button type="submit" onclick="return confirm('Hapus goal ini?')"
                        class="w-full py-2 rounded-xl text-xs font-semibold transition-all"
                        style="background:var(--bg-input); color:var(--text-4); border:1px solid var(--border)">
                        <span class="ms text-[14px]">delete</span> Hapus Goal
                    </button>
                </form>
            </div>

            @else
            {{-- IN PROGRESS STATE --}}
            <div class="glass-card p-6 rounded-xl relative overflow-hidden group">
                {{-- Background icon --}}
                <div class="absolute top-2 right-3 opacity-[0.07] group-hover:opacity-[0.12] transition-opacity pointer-events-none">
                    <span class="ms text-[72px]" style="color:{{ $g->warna }}">{{ $g->icon }}</span>
                </div>

                <div class="relative z-10">
                    @if($g->deadline)
                    <span class="inline-block px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest mb-3"
                          style="background:{{ $g->warna }}20; color:{{ $g->warna }}">
                        {{ $g->sisa_hari }} HARI LAGI
                    </span>
                    @endif

                    <h4 class="font-bold text-xl mb-2" style="color:var(--text-1)">{{ $g->nama }}</h4>

                    <div class="flex items-baseline gap-2 mb-4">
                        <span class="font-black text-3xl tracking-tight" style="color:var(--text-1)">
                            Rp {{ number_format($g->terkumpul, 0, ',', '.') }}
                        </span>
                        <span class="text-sm" style="color:var(--text-4)">/ Rp {{ number_format($g->target, 0, ',', '.') }}</span>
                    </div>

                    <div class="relative w-full h-2 progress-track rounded-full overflow-hidden mb-3">
                        <div class="absolute top-0 left-0 h-full rounded-full transition-all duration-700"
                             style="width:{{ $g->porsen }}%; background:{{ $g->warna }}; box-shadow: 0 0 12px {{ $g->warna }}80"></div>
                    </div>

                    <p class="text-xs mb-4" style="color:var(--text-4)">
                        {{ $g->porsen }}% terkumpul · Sisa Rp {{ number_format($g->sisa, 0, ',', '.') }}
                    </p>

                    <div class="flex gap-2">
                        <button onclick="openAddDana({{ $g->id }}, '{{ $g->nama }}')"
                            class="flex-1 py-2.5 rounded-xl text-xs font-bold uppercase tracking-widest transition-all active:scale-95"
                            style="background:{{ $g->warna }}20; border:1px solid {{ $g->warna }}40; color:{{ $g->warna }}">
                            + TAMBAH DANA
                        </button>
                        <form method="POST" action="{{ route('rencana.destroy', $g) }}"
                              class="opacity-0 group-hover:opacity-100 transition-opacity">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Hapus goal ini?')"
                                class="px-3 py-2.5 rounded-xl hover:bg-red-500/20 hover:text-red-400 transition-all" style="background:var(--bg-input); color:var(--text-4)">
                                <span class="ms text-[16px]">delete</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            @empty
            <div class="glass-card rounded-xl p-12 text-center">
                <span class="ms text-[48px] block mb-3" style="color:var(--text-4)">savings</span>
                <p class="font-semibold mb-1" style="color:var(--text-3)">Belum ada target tabungan</p>
                <p class="text-xs mb-4" style="color:var(--text-4)">Buat target untuk melacak tabungan Anda</p>
                <button onclick="openModal('addGoalModal')"
                    class="px-5 py-2.5 rounded-xl text-sm font-bold bg-gradient-to-r from-teal-600 to-teal-500 active:scale-95 transition-all"
                    style="color:#ffffff">
                    + Buat Goal Pertama
                </button>
            </div>
            @endforelse

        </div>
    </div>

    {{-- Future Projection Banner --}}
    @php
        $budgetRate = $totalBudget > 0 ? round(($totalAktual / $totalBudget) * 100) : 0;
        $goalRate   = $totalGoalTarget > 0 ? round(($totalGoalTerkumpul / $totalGoalTarget) * 100) : 0;
        $savingsThisMonth = max($totalBudget - $totalAktual, 0);
    @endphp
    <section class="mt-10">
        <div class="glass-card p-10 rounded-2xl flex flex-col md:flex-row items-center gap-10 overflow-hidden relative">
            <div class="absolute inset-0 pointer-events-none overflow-hidden">
                <div class="absolute -top-20 -right-20 w-80 h-80 bg-violet-600/10 rounded-full blur-[80px]"></div>
                <div class="absolute -bottom-20 right-40 w-60 h-60 bg-teal-500/8 rounded-full blur-[60px]"></div>
            </div>
            <div class="flex-1 z-10">
                <h3 class="font-black text-2xl mb-3 tracking-tight" style="color:var(--text-1)">Proyeksi Keuangan</h3>
                <p class="text-base mb-8 max-w-xl leading-relaxed" style="color:var(--text-3)">
                    Berdasarkan pola pengeluaran dan tabungan Anda saat ini,
                    @if($goalRate >= 50)
                        Anda sedang dalam jalur yang <span class="text-teal-400 font-bold">sangat baik</span> untuk mencapai semua goals.
                    @else
                        tingkatkan tabungan untuk mencapai goals lebih cepat.
                    @endif
                </p>
                <div class="grid grid-cols-3 gap-8">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest mb-1" style="color:var(--text-4)">BUDGET TERPAKAI</p>
                        <p class="font-black text-2xl" style="color:{{ $budgetRate > 100 ? 'var(--accent-red)' : ($budgetRate > 80 ? 'var(--accent-yellow)' : 'var(--text-1)') }}">
                            {{ $budgetRate }}%
                        </p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest mb-1" style="color:var(--text-4)">PROGRES TARGET</p>
                        <p class="font-black text-2xl" style="color:var(--accent-teal)">{{ $goalRate }}%</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest mb-1" style="color:var(--text-4)">TARGET SELESAI</p>
                        <p class="font-black text-2xl" style="color:var(--accent-yellow)">{{ $goalsSelesai }}/{{ $goals->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="w-full md:w-72 z-10 flex-shrink-0">
                <div class="ringkasan-card rounded-xl p-5">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-violet-400 mb-3">RINGKASAN BULAN INI</p>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span style="color:var(--text-3)">Total Budget</span>
                            <span class="font-bold" style="color:var(--text-1)">Rp {{ number_format($totalBudget, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span style="color:var(--text-3)">Terpakai</span>
                            <span class="font-bold" style="color:{{ $budgetRate > 100 ? 'var(--accent-red)' : 'var(--text-1)' }}">
                                Rp {{ number_format($totalAktual, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="pt-2 flex justify-between text-sm" style="border-top:1px solid var(--border)">
                            <span style="color:var(--text-3)">Sisa Budget</span>
                            <span class="font-bold" style="color:var(--accent-teal)">Rp {{ number_format($savingsThisMonth, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

{{-- ══════════════════════════════════════════════════════════ MODALS ══ --}}

{{-- Add Budget --}}
<div id="addBudgetModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
    <div class="modal-anim rounded-2xl w-full max-w-md"
         style="background:var(--bg-card); border:1px solid var(--border); box-shadow:var(--shadow-lg)">
        <div class="flex items-center justify-between px-6 py-5" style="border-bottom:1px solid var(--border)">
            <h3 class="font-bold flex items-center gap-2" style="color:var(--text-1)">
                <span class="ms text-[20px] text-violet-400">add_chart</span> Buat Anggaran Baru
            </h3>
            <button onclick="closeModal('addBudgetModal')"
                    class="w-8 h-8 rounded-lg flex items-center justify-center transition-all"
                    style="background:var(--bg-input); color:var(--text-3)"
                    onmouseenter="this.style.color='var(--text-1)'"
                    onmouseleave="this.style.color='var(--text-3)'">
                <span class="ms text-[20px]">close</span>
            </button>
        </div>
        <form method="POST" action="{{ route('rencana.budget.store') }}" class="p-6 space-y-4"
              style="background:var(--bg-card)">
            @csrf

            {{-- Warning: kategori sudah ada budget --}}
            <div id="budgetKatWarning" class="hidden items-start gap-3 px-4 py-3 rounded-xl"
                 style="background:rgba(217,119,6,0.1); border:1px solid rgba(217,119,6,0.3)">
                <span class="ms text-[18px] text-yellow-400 flex-shrink-0">warning</span>
                <p class="text-sm text-yellow-400" id="budgetKatWarningText">Kategori ini sudah memiliki budget aktif.</p>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest mb-2" style="color:var(--text-3)">
                    Nama Budget
                </label>
                <input type="text" name="nama" id="budgetNama" placeholder="Contoh: Makan & Minum" required
                       class="w-full rounded-xl px-4 py-3 text-sm focus:outline-none transition-all">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest mb-2" style="color:var(--text-3)">
                    Kategori <span style="color:var(--accent-red)">*</span>
                </label>
                <select name="kategori" id="budgetKategori" required
                        onchange="onBudgetKategoriChange(this)"
                        class="w-full rounded-xl px-4 py-3 text-sm focus:outline-none transition-all cursor-pointer">
                    <option value="">— Pilih Kategori —</option>
                    @foreach(\App\Enums\KategoriTransaksi::PENGELUARAN as $key => $cat)
                    <option value="{{ $key }}"
                            data-has-budget="{{ $budgets->where('kategori', $key)->count() > 0 ? '1' : '0' }}"
                            data-budget-name="{{ $budgets->where('kategori', $key)->first()?->nama ?? '' }}">
                        {{ $cat['label'] }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest mb-2" style="color:var(--text-3)">
                    Target Budget <span style="color:var(--accent-red)">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-0 top-0 bottom-0 w-10 flex items-center justify-center text-xs font-bold rounded-l-xl"
                          style="color:var(--text-3); background:var(--bg-input); border-right:1px solid var(--border)">Rp</span>
                    <input type="text" id="budgetTargetDisplay" placeholder="1.000.000"
                           class="w-full rounded-xl pl-12 pr-4 py-3 text-sm focus:outline-none transition-all">
                    <input type="hidden" name="target" id="budgetTargetValue">
                </div>
                <p class="text-xs mt-1.5" style="color:var(--text-4)">Otomatis tracking dari transaksi bulan ini</p>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('addBudgetModal')"
                        class="flex-1 py-2.5 rounded-xl text-sm font-semibold transition-all"
                        style="background:var(--bg-input); color:var(--text-3)">
                    Batal
                </button>
                <button type="submit" id="budgetSubmitBtn"
                        class="flex-1 py-2.5 rounded-xl text-sm font-bold
                               bg-gradient-to-r from-violet-600 to-violet-500 hover:opacity-90 transition-all active:scale-95"
                        style="color:#ffffff">
                    Buat Budget
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Add Goal --}}
<div id="addGoalModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
    <div class="modal-anim rounded-2xl w-full max-w-md"
         style="background:var(--bg-card); border:1px solid var(--border); box-shadow:var(--shadow-lg)">
        <div class="flex items-center justify-between px-6 py-5" style="border-bottom:1px solid var(--border)">
            <h3 class="font-bold flex items-center gap-2" style="color:var(--text-1)">
                <span class="ms text-[20px] text-teal-400">flag</span> Buat Target Tabungan
            </h3>
            <button onclick="closeModal('addGoalModal')"
                    class="w-8 h-8 rounded-lg flex items-center justify-center transition-all"
                    style="background:var(--bg-input); color:var(--text-3)"
                    onmouseenter="this.style.color='var(--text-1)'"
                    onmouseleave="this.style.color='var(--text-3)'">
                <span class="ms text-[20px]">close</span>
            </button>
        </div>
        <form method="POST" action="{{ route('rencana.goal.store') }}" class="p-6 space-y-4"
              style="background:var(--bg-card)">
            @csrf

            {{-- Validation errors --}}
            @if($errors->any())
            <div class="flex items-start gap-3 px-4 py-3 rounded-xl bg-red-500/10 border border-red-500/20">
                <span class="ms text-[18px] text-red-400 flex-shrink-0">error</span>
                <div class="text-sm text-red-400">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
            @endif

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest mb-2" style="color:var(--text-3)">
                    Nama Goal <span style="color:var(--accent-red)">*</span>
                </label>
                <input type="text" name="nama" placeholder="Contoh: Liburan Bali" required
                       class="w-full rounded-xl px-4 py-3 text-sm focus:outline-none transition-all">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest mb-2" style="color:var(--text-3)">
                        Target <span style="color:var(--accent-red)">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-0 top-0 bottom-0 w-10 flex items-center justify-center text-xs font-bold rounded-l-xl"
                              style="color:var(--text-3); background:var(--bg-input); border-right:1px solid var(--border)">Rp</span>
                        <input type="text" id="goalTargetDisplay" placeholder="5.000.000"
                               class="w-full rounded-xl pl-12 pr-4 py-3 text-sm focus:outline-none transition-all">
                        <input type="hidden" name="target" id="goalTargetValue">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest mb-2" style="color:var(--text-3)">
                        Sudah Terkumpul
                    </label>
                    <div class="relative">
                        <span class="absolute left-0 top-0 bottom-0 w-10 flex items-center justify-center text-xs font-bold rounded-l-xl"
                              style="color:var(--text-3); background:var(--bg-input); border-right:1px solid var(--border)">Rp</span>
                        <input type="text" id="goalTerkumpulDisplay" placeholder="0" value="0"
                               class="w-full rounded-xl pl-12 pr-4 py-3 text-sm focus:outline-none transition-all">
                        <input type="hidden" name="terkumpul" id="goalTerkumpulValue" value="0">
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest mb-2" style="color:var(--text-3)">
                    Deadline <span style="color:var(--text-4)">(Opsional)</span>
                </label>
                <input type="date" name="deadline"
                       class="w-full rounded-xl px-4 py-3 text-sm focus:outline-none transition-all">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest mb-2" style="color:var(--text-3)">
                    Pilih Icon
                </label>
                <div class="grid grid-cols-7 gap-2" id="iconPicker">
                    @foreach(['savings','flight_takeoff','home','directions_car','school','favorite','beach_access','laptop_mac','restaurant','shopping_bag'] as $ic)
                    <button type="button"
                            data-icon="{{ $ic }}"
                            onclick="selectGoalIcon('{{ $ic }}')"
                            class="icon-btn aspect-square rounded-xl border flex items-center justify-center transition-all"
                            style="background:var(--bg-input); border-color:{{ $ic === 'savings' ? 'var(--accent-teal)' : 'var(--border)' }};
                                   {{ $ic === 'savings' ? 'box-shadow:0 0 0 2px var(--accent-teal)' : '' }}">
                        <span class="ms text-[18px]"
                              style="color:{{ $ic === 'savings' ? 'var(--accent-teal)' : 'var(--text-3)' }}">{{ $ic }}</span>
                    </button>
                    @endforeach
                </div>
                <input type="hidden" name="icon" id="goalIconValue" value="savings">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('addGoalModal')"
                        class="flex-1 py-2.5 rounded-xl text-sm font-semibold transition-all"
                        style="background:var(--bg-input); color:var(--text-3)">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 rounded-xl text-sm font-bold
                               bg-gradient-to-r from-teal-600 to-teal-500 hover:opacity-90 transition-all active:scale-95"
                        style="color:#ffffff">
                    Buat Goal
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Add Dana --}}
<div id="addDanaModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
    <div class="modal-anim rounded-2xl w-full max-w-sm"
         style="background:var(--bg-card); border:1px solid var(--border); box-shadow:var(--shadow-lg)">
        <div class="flex items-center justify-between px-6 py-5" style="border-bottom:1px solid var(--border)">
            <h3 class="font-bold flex items-center gap-2" style="color:var(--text-1)">
                <span class="ms text-[20px] text-teal-400">savings</span> Tambah Dana
            </h3>
            <button onclick="closeModal('addDanaModal')"
                    class="w-8 h-8 rounded-lg flex items-center justify-center transition-all"
                    style="background:var(--bg-input); color:var(--text-3)"
                    onmouseenter="this.style.color='var(--text-1)'"
                    onmouseleave="this.style.color='var(--text-3)'">
                <span class="ms text-[20px]">close</span>
            </button>
        </div>
        <form method="POST" id="addDanaForm" class="p-6 space-y-4"
              style="background:var(--bg-card)">
            @csrf
            <p class="text-sm font-semibold" style="color:var(--text-3)" id="addDanaGoalName"></p>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest mb-2" style="color:var(--text-3)">
                    Jumlah <span style="color:var(--accent-red)">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-0 top-0 bottom-0 w-10 flex items-center justify-center text-xs font-bold rounded-l-xl"
                          style="color:var(--text-3); background:var(--bg-input); border-right:1px solid var(--border)">Rp</span>
                    <input type="text" id="danaDisplay" placeholder="100.000"
                           class="w-full rounded-xl pl-12 pr-4 py-3 text-sm focus:outline-none transition-all">
                    <input type="hidden" name="jumlah" id="danaValue">
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('addDanaModal')"
                        class="flex-1 py-2.5 rounded-xl text-sm font-semibold transition-all"
                        style="background:var(--bg-input); color:var(--text-3)">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 rounded-xl text-sm font-bold
                               bg-gradient-to-r from-teal-600 to-teal-500 hover:opacity-90 transition-all active:scale-95"
                        style="color:#ffffff">
                    Tambah Dana
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Tingkatkan Target (setelah goal tercapai) --}}
<div id="tingkatkanModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
    <div class="modal-anim rounded-2xl w-full max-w-sm"
         style="background:var(--bg-card); border:1px solid var(--border); box-shadow:var(--shadow-lg)">
        <div class="flex items-center justify-between px-6 py-5" style="border-bottom:1px solid var(--border)">
            <h3 class="font-bold flex items-center gap-2" style="color:var(--text-1)">
                <span class="ms text-[20px] text-teal-400">trending_up</span> Tingkatkan Target
            </h3>
            <button onclick="closeModal('tingkatkanModal')"
                    class="w-8 h-8 rounded-lg flex items-center justify-center transition-all"
                    style="background:var(--bg-input); color:var(--text-3)"
                    onmouseenter="this.style.color='var(--text-1)'"
                    onmouseleave="this.style.color='var(--text-3)'">
                <span class="ms text-[20px]">close</span>
            </button>
        </div>
        <form method="POST" id="tingkatkanForm" class="p-6 space-y-4"
              style="background:var(--bg-card)">
            @csrf @method('PATCH')
            <p class="text-sm font-semibold" style="color:var(--text-2)" id="tingkatkanGoalName"></p>
            <div class="rounded-xl p-3 text-xs" style="background:var(--bg-subtle); color:var(--text-3)">
                <span class="ms text-[14px] text-teal-400">check_circle</span>
                Target sebelumnya sudah tercapai. Tetapkan target baru yang lebih tinggi.
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest mb-2" style="color:var(--text-3)">
                    Target Baru <span style="color:var(--accent-red)">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-0 top-0 bottom-0 w-10 flex items-center justify-center text-xs font-bold rounded-l-xl"
                          style="color:var(--text-3); background:var(--bg-input); border-right:1px solid var(--border)">Rp</span>
                    <input type="text" id="tingkatkanDisplay" placeholder="0"
                           class="w-full rounded-xl pl-12 pr-4 py-3 text-sm focus:outline-none transition-all">
                    <input type="hidden" name="target" id="tingkatkanValue">
                </div>
                <p class="text-[10px] mt-1.5" style="color:var(--text-4)" id="tingkatkanHint"></p>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('tingkatkanModal')"
                        class="flex-1 py-2.5 rounded-xl text-sm font-semibold transition-all"
                        style="background:var(--bg-input); color:var(--text-3)">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 rounded-xl text-sm font-bold
                               bg-gradient-to-r from-teal-600 to-teal-500 hover:opacity-90 transition-all active:scale-95"
                        style="color:#ffffff">
                    Simpan Target Baru
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// ── Sidebar ───────────────────────────────────────────────────────────────────
function openSidebar() {
    document.getElementById('sidebar').classList.remove('-translate-x-full');
    document.getElementById('sidebarOverlay').classList.remove('hidden');
}
function closeSidebar() {
    document.getElementById('sidebar').classList.add('-translate-x-full');
    document.getElementById('sidebarOverlay').classList.add('hidden');
}

// ── Modal helpers ─────────────────────────────────────────────────────────────
function openModal(id) {
    const m = document.getElementById(id);
    m.classList.remove('hidden');
    m.classList.add('flex');
}
function closeModal(id) {
    const m = document.getElementById(id);
    m.classList.add('hidden');
    m.classList.remove('flex');
}
document.querySelectorAll('[id$="Modal"]').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) closeModal(m.id); });
});

// ── Format ribuan (display) ───────────────────────────────────────────────────
function formatRibuan(val) {
    const num = val.replace(/\D/g, '');
    return num ? parseInt(num).toLocaleString('id-ID') : '';
}

function bindRupiahInput(displayId, hiddenId) {
    const display = document.getElementById(displayId);
    const hidden  = document.getElementById(hiddenId);
    if (!display || !hidden) return;
    display.addEventListener('input', () => {
        const raw = display.value.replace(/\D/g, '');
        display.value = raw ? parseInt(raw).toLocaleString('id-ID') : '';
        hidden.value  = raw || '';
    });
}

document.addEventListener('DOMContentLoaded', () => {
    bindRupiahInput('budgetTargetDisplay', 'budgetTargetValue');
    bindRupiahInput('goalTargetDisplay',   'goalTargetValue');
    bindRupiahInput('goalTerkumpulDisplay','goalTerkumpulValue');
    bindRupiahInput('danaDisplay',         'danaValue');

    // Auto-nama budget dari kategori
    const katSelect = document.getElementById('budgetKategori');
    const namaInput = document.getElementById('budgetNama');
    if (katSelect && namaInput) {
        katSelect.addEventListener('change', () => {
            if (!namaInput.value.trim()) {
                const opt = katSelect.options[katSelect.selectedIndex];
                namaInput.value = opt.text !== '— Pilih Kategori —' ? opt.text : '';
            }
        });
    }
});

// ── Cegah duplikat kategori budget ───────────────────────────────────────────
function onBudgetKategoriChange(select) {
    const opt     = select.options[select.selectedIndex];
    const hasBudget = opt.dataset.hasBudget === '1';
    const warning = document.getElementById('budgetKatWarning');
    const warnTxt = document.getElementById('budgetKatWarningText');
    const submitBtn = document.getElementById('budgetSubmitBtn');

    if (hasBudget) {
        const existingName = opt.dataset.budgetName;
        warnTxt.textContent = 'Kategori ini sudah memiliki budget "' + existingName + '". Hapus dulu sebelum membuat baru.';
        warning.classList.remove('hidden');
        warning.classList.add('flex');
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.4';
        submitBtn.style.cursor  = 'not-allowed';
    } else {
        warning.classList.add('hidden');
        warning.classList.remove('flex');
        submitBtn.disabled = false;
        submitBtn.style.opacity = '';
        submitBtn.style.cursor  = '';
    }

    // Auto-fill nama jika kosong
    const namaInput = document.getElementById('budgetNama');
    if (namaInput && !namaInput.value.trim() && !hasBudget) {
        const opt2 = select.options[select.selectedIndex];
        namaInput.value = opt2.text !== '— Pilih Kategori —' ? opt2.text : '';
    }
}

// ── Tambah Dana — autofocus + nama goal tanpa emoji ───────────────────────────
function openAddDana(goalId, goalName) {
    document.getElementById('addDanaForm').action = '{{ url("rencana") }}/' + goalId + '/add-dana';
    document.getElementById('addDanaGoalName').textContent = 'Target: ' + goalName;

    // Reset display & hidden
    const disp = document.getElementById('danaDisplay');
    const hid  = document.getElementById('danaValue');
    if (disp) disp.value = '';
    if (hid)  hid.value  = '';

    openModal('addDanaModal');

    // Autofocus setelah modal muncul
    setTimeout(() => { disp?.focus(); }, 100);
}

// ── Tingkatkan Target (setelah goal tercapai) ─────────────────────────────────
function openTingkatkanModal(goalId, goalName, currentTarget) {
    document.getElementById('tingkatkanForm').action = '{{ url("rencana") }}/' + goalId + '/tingkatkan';
    document.getElementById('tingkatkanGoalName').textContent = goalName;
    document.getElementById('tingkatkanHint').textContent =
        'Target saat ini: Rp ' + currentTarget.toLocaleString('id-ID') + '. Masukkan nilai lebih tinggi.';

    const disp = document.getElementById('tingkatkanDisplay');
    const hid  = document.getElementById('tingkatkanValue');
    if (disp) disp.value = '';
    if (hid)  hid.value  = '';

    openModal('tingkatkanModal');
    setTimeout(() => disp?.focus(), 100);
}

document.addEventListener('DOMContentLoaded', () => {
    bindRupiahInput('tingkatkanDisplay', 'tingkatkanValue');
});

// ── Goal Icon Picker ──────────────────────────────────────────────────────────
function selectGoalIcon(icon) {
    document.getElementById('goalIconValue').value = icon;
    document.querySelectorAll('.icon-btn').forEach(btn => {
        const isActive = btn.dataset.icon === icon;
        btn.style.borderColor = isActive ? 'var(--accent-teal)' : 'var(--border)';
        btn.style.boxShadow   = isActive ? '0 0 0 2px var(--accent-teal)' : 'none';
        btn.style.background  = isActive ? 'rgba(13,148,136,0.12)' : 'var(--bg-input)';
        const span = btn.querySelector('.ms');
        if (span) span.style.color = isActive ? 'var(--accent-teal)' : 'var(--text-3)';
    });
}

@if($errors->any())
document.addEventListener('DOMContentLoaded', () => openModal('addGoalModal'));
@endif
</script>
</body>
</html>
