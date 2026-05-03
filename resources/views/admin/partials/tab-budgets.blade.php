@php use App\Enums\KategoriTransaksi; @endphp

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- ── ANGGARAN SEMUA USER ── --}}
    <div class="glass rounded-2xl overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4" style="border-bottom:1px solid var(--border)">
            <div class="flex items-center gap-2">
                <span class="ms text-[20px] text-violet-400">payments</span>
                <h2 class="font-bold" style="color:var(--text-1)">Anggaran Bulanan</h2>
            </div>
            <div class="flex items-center gap-2">
                @if($budgetsOverLimit > 0)
                <span class="flex items-center gap-1 px-3 py-1 rounded-full bg-red-500/15 text-red-400 text-xs font-bold">
                    <span class="ms text-[14px]">warning</span>
                    {{ $budgetsOverLimit }} melebihi batas
                </span>
                @endif
                <span class="text-xs px-3 py-1 rounded-full"
                      style="background:var(--bg-badge); color:var(--text-3)">
                    {{ $allBudgets->count() }} total
                </span>
            </div>
        </div>

        <div class="max-h-[600px] overflow-y-auto">
            @forelse($allBudgets as $b)
            @php
                $icon = KategoriTransaksi::icon($b->kategori ?? '');
                $lbl  = KategoriTransaksi::label($b->kategori ?? '');
                $clr  = $b->overBudget ? 'var(--accent-red)' : ($b->persen >= 80 ? 'var(--accent-yellow)' : 'var(--accent-violet)');
                $clrHex = $b->overBudget ? '#f87171' : ($b->persen >= 80 ? '#fbbf24' : '#a78bfa');
            @endphp
            <div class="px-6 py-4 transition-colors" style="border-bottom:1px solid var(--divider)"
                 onmouseenter="this.style.background='var(--bg-hover)'"
                 onmouseleave="this.style.background=''">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-violet-500 to-teal-400
                                flex items-center justify-center text-[10px] font-bold flex-shrink-0"
                         style="color:#ffffff">
                        {{ strtoupper(substr($b->user->email ?? '?', 0, 1)) }}
                    </div>
                    <span class="text-xs truncate flex-1" style="color:var(--text-3)">{{ $b->user->email ?? '-' }}</span>
                    <span class="text-[10px] font-bold uppercase tracking-widest"
                          style="color:{{ $clr }}">
                        {{ $b->overBudget ? 'MELEBIHI' : ($b->persen >= 80 ? 'HAMPIR HABIS' : 'NORMAL') }}
                    </span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background: {{ $clrHex }}20">
                        <span class="ms text-[16px]" style="color: {{ $clrHex }}">{{ $icon }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-semibold truncate" style="color:var(--text-1)">{{ $b->nama }}</span>
                            <span class="text-xs font-bold ml-2 flex-shrink-0" style="color: {{ $clrHex }}">
                                {{ $b->persen }}%
                            </span>
                        </div>
                        <div class="h-1.5 rounded-full overflow-hidden" style="background:var(--bg-input)">
                            <div class="h-full rounded-full transition-all duration-500"
                                 style="width: {{ min($b->persen, 100) }}%; background: {{ $clrHex }}"></div>
                        </div>
                        <div class="flex justify-between mt-1">
                            <span class="text-[10px]" style="color:var(--text-4)">
                                Rp {{ number_format($b->aktual, 0, ',', '.') }} terpakai
                            </span>
                            <span class="text-[10px]" style="color:var(--text-4)">
                                dari Rp {{ number_format($b->target, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="px-6 py-16 text-center">
                <span class="ms text-[48px] block mb-3" style="color:var(--text-4)">payments</span>
                <p class="text-sm" style="color:var(--text-4)">Belum ada anggaran yang dibuat user.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ── TARGET TABUNGAN SEMUA USER ── --}}
    <div class="glass rounded-2xl overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4" style="border-bottom:1px solid var(--border)">
            <div class="flex items-center gap-2">
                <span class="ms text-[20px] text-teal-400">flag</span>
                <h2 class="font-bold" style="color:var(--text-1)">Target Tabungan</h2>
            </div>
            <div class="flex items-center gap-2">
                @if($goalsSelesai > 0)
                <span class="flex items-center gap-1 px-3 py-1 rounded-full bg-teal-500/15 text-teal-400 text-xs font-bold">
                    <span class="ms text-[14px]">workspace_premium</span>
                    {{ $goalsSelesai }} tercapai
                </span>
                @endif
                <span class="text-xs px-3 py-1 rounded-full"
                      style="background:var(--bg-badge); color:var(--text-3)">
                    {{ $allGoals->count() }} total
                </span>
            </div>
        </div>

        <div class="max-h-[600px] overflow-y-auto">
            @forelse($allGoals as $g)
            @php
                $selesai = $g->is_selesai;
                $persen  = $g->porsen;
            @endphp
            <div class="px-6 py-4 transition-colors" style="border-bottom:1px solid var(--divider)"
                 onmouseenter="this.style.background='var(--bg-hover)'"
                 onmouseleave="this.style.background=''">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-violet-500 to-teal-400
                                flex items-center justify-center text-[10px] font-bold flex-shrink-0"
                         style="color:#ffffff">
                        {{ strtoupper(substr($g->user->email ?? '?', 0, 1)) }}
                    </div>
                    <span class="text-xs truncate flex-1" style="color:var(--text-3)">{{ $g->user->email ?? '-' }}</span>
                    @if($selesai)
                    <span class="flex items-center gap-1 px-2 py-0.5 rounded-full bg-teal-500/15 text-teal-400 text-[10px] font-bold">
                        <span class="ms text-[12px]" style="font-variation-settings:'FILL' 1">workspace_premium</span>
                        TERCAPAI
                    </span>
                    @elseif($g->deadline && $g->sisa_hari !== null && $g->sisa_hari <= 7)
                    <span class="text-[10px] font-bold text-yellow-400">{{ $g->sisa_hari }} HARI LAGI</span>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background: {{ $g->warna }}20">
                        <span class="ms text-[16px]" style="color: {{ $g->warna }}">{{ $g->icon }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-semibold truncate" style="color:var(--text-1)">{{ $g->nama }}</span>
                            <span class="text-xs font-bold ml-2 flex-shrink-0"
                                  style="color: {{ $g->warna }}">{{ $persen }}%</span>
                        </div>
                        <div class="h-1.5 rounded-full overflow-hidden" style="background:var(--bg-input)">
                            <div class="h-full rounded-full transition-all duration-500"
                                 style="width: {{ $persen }}%; background: {{ $g->warna }}"></div>
                        </div>
                        <div class="flex justify-between mt-1">
                            <span class="text-[10px]" style="color:var(--text-4)">
                                Rp {{ number_format($g->terkumpul, 0, ',', '.') }} terkumpul
                            </span>
                            <span class="text-[10px]" style="color:var(--text-4)">
                                target Rp {{ number_format($g->target, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="px-6 py-16 text-center">
                <span class="ms text-[48px] block mb-3" style="color:var(--text-4)">savings</span>
                <p class="text-sm" style="color:var(--text-4)">Belum ada target tabungan yang dibuat user.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- ── RINGKASAN STATISTIK ── --}}
<div class="glass rounded-2xl p-6 mt-6">
    <div class="flex items-center gap-2 mb-5">
        <span class="ms text-[20px] text-violet-400">insights</span>
        <h3 class="font-bold" style="color:var(--text-1)">Ringkasan Budget & Goals</h3>
    </div>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
        $totalTargetBudget  = $allBudgets->sum('target');
        $totalAktualBudget  = $allBudgets->sum('aktual');
        $totalTargetGoal    = $allGoals->sum('target');
        $totalTerkumpulGoal = $allGoals->sum('terkumpul');
        $budgetRate = $totalTargetBudget > 0 ? round(($totalAktualBudget / $totalTargetBudget) * 100) : 0;
        $goalRate   = $totalTargetGoal > 0 ? round(($totalTerkumpulGoal / $totalTargetGoal) * 100) : 0;
        @endphp
        @php
        $summaryCards = [
            ['label'=>'Total Target Budget', 'value'=>'Rp '.number_format($totalTargetBudget,0,',','.'), 'sub'=>$allBudgets->count().' anggaran aktif', 'color'=>'var(--text-1)'],
            ['label'=>'Total Terpakai',       'value'=>'Rp '.number_format($totalAktualBudget,0,',','.'), 'sub'=>$budgetRate.'% dari total budget', 'color'=>$budgetRate>100?'var(--accent-red)':'var(--text-1)'],
            ['label'=>'Total Target Tabungan','value'=>'Rp '.number_format($totalTargetGoal,0,',','.'),   'sub'=>$allGoals->count().' goals aktif', 'color'=>'var(--text-1)'],
            ['label'=>'Total Terkumpul',      'value'=>'Rp '.number_format($totalTerkumpulGoal,0,',','.'), 'sub'=>$goalRate.'% dari total target', 'color'=>'var(--accent-teal)'],
        ];
        @endphp
        @foreach($summaryCards as $sc)
        <div class="rounded-xl p-4" style="background:var(--bg-subtle); border:1px solid var(--border)">
            <p class="text-[10px] font-semibold uppercase tracking-widest mb-2" style="color:var(--text-3)">
                {{ $sc['label'] }}
            </p>
            <p class="font-bold text-lg" style="color:{{ $sc['color'] }}">{{ $sc['value'] }}</p>
            <p class="text-xs mt-1" style="color:var(--text-4)">{{ $sc['sub'] }}</p>
        </div>
        @endforeach
    </div>
</div>
