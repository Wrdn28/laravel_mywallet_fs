{{-- Filter --}}
<div class="glass rounded-2xl px-6 py-4 flex flex-wrap items-center gap-4 mb-4">
    <span class="ms text-[18px] text-violet-400">filter_list</span>
    <form method="GET" class="flex flex-wrap items-center gap-3">
        <input type="hidden" name="tab" value="reports">
        @foreach(['all'=>'Semua','week'=>'1 Minggu','month'=>'1 Bulan','custom'=>'Custom'] as $val=>$label)
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="periode" value="{{ $val }}"
                   {{ request('periode','all') === $val ? 'checked' : '' }}
                   onchange="this.form.submit()"
                   class="accent-violet-500">
            <span class="text-sm font-medium" style="color:var(--text-2)">{{ $label }}</span>
        </label>
        @endforeach
        @if(request('periode') === 'custom')
        <div class="flex items-center gap-2">
            <input type="date" name="start_date" value="{{ request('start_date') }}"
                   class="rounded-xl px-3 py-1.5 text-xs focus:outline-none focus:border-violet-500"
                   style="background:var(--bg-input); border:1px solid var(--border); color:var(--text-1)">
            <span class="text-xs" style="color:var(--text-4)">s/d</span>
            <input type="date" name="end_date" value="{{ request('end_date') }}"
                   class="rounded-xl px-3 py-1.5 text-xs focus:outline-none focus:border-violet-500"
                   style="background:var(--bg-input); border:1px solid var(--border); color:var(--text-1)">
            <button type="submit"
                    class="px-3 py-1.5 rounded-xl text-xs font-bold bg-violet-600 hover:bg-violet-500 transition-all"
                    style="color:#ffffff">
                Terapkan
            </button>
        </div>
        @endif
    </form>
</div>

{{-- Charts --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
    <div class="glass rounded-2xl p-6">
        <div class="flex items-center gap-2 mb-5">
            <span class="ms text-[20px] text-violet-400">bar_chart</span>
            <h3 class="font-bold" style="color:var(--text-1)">User Transaction Stats</h3>
        </div>
        <div class="relative h-64">
            <canvas id="userStatsChart"></canvas>
        </div>
    </div>
    <div class="glass rounded-2xl p-6">
        <div class="flex items-center gap-2 mb-1">
            <span class="ms text-[20px] text-red-400">category</span>
            <h3 class="font-bold" style="color:var(--text-1)">Top Expense Categories</h3>
        </div>
        <p class="text-xs mb-4 ml-7" style="color:var(--text-4)">Pengeluaran terbesar per kategori</p>
        <div class="relative h-64">
            <canvas id="categoryBarChart"></canvas>
        </div>
    </div>
</div>

{{-- Top Expense Category --}}
@php
    use App\Enums\KategoriTransaksi;
    $topExpenseCategories = \App\Models\Transaksi::where('tipe','pengeluaran')
        ->whereNotNull('kategori')
        ->selectRaw('kategori, SUM(jumlah) as total, COUNT(*) as jumlah_transaksi')
        ->groupBy('kategori')
        ->orderByDesc('total')
        ->limit(8)
        ->get();
    $maxCatTotal = $topExpenseCategories->max('total') ?: 1;
    $chartColors = ['#f87171','#fb923c','#fbbf24','#a78bfa','#60a5fa','#34d399','#f472b6','#94a3b8'];
@endphp

@if($topExpenseCategories->isNotEmpty())
<div class="glass rounded-2xl p-6 mb-5">
    <div class="flex items-center gap-2 mb-6">
        <span class="ms text-[20px] text-red-400">category</span>
        <h3 class="font-bold" style="color:var(--text-1)">Top Expense Categories</h3>
        <span class="text-xs ml-auto" style="color:var(--text-4)">Semua waktu</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">

        {{-- Doughnut Chart --}}
        <div class="flex flex-col items-center">
            <div class="relative w-64 h-64">
                <canvas id="categoryDoughnutChart"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <p class="text-xs font-semibold" style="color:var(--text-3)">Total</p>
                    <p class="font-black text-lg leading-tight" style="color:var(--text-1)">
                        Rp {{ number_format($topExpenseCategories->sum('total'), 0, ',', '.') }}
                    </p>
                    <p class="text-[10px] mt-0.5" style="color:var(--text-4)">
                        {{ $topExpenseCategories->sum('jumlah_transaksi') }} transaksi
                    </p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-x-6 gap-y-2 mt-4 w-full max-w-xs">
                @foreach($topExpenseCategories as $i => $cat)
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full flex-shrink-0"
                         style="background: {{ $chartColors[$i % count($chartColors)] }}"></div>
                    <span class="text-xs truncate" style="color:var(--text-3)">
                        {{ KategoriTransaksi::label($cat->kategori) }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Bar List --}}
        <div class="space-y-3">
            @foreach($topExpenseCategories as $i => $cat)
            @php
                $pct  = round(($cat->total / $maxCatTotal) * 100);
                $icon = KategoriTransaksi::icon($cat->kategori);
                $lbl  = KategoriTransaksi::label($cat->kategori);
                $clr  = $chartColors[$i % count($chartColors)];
            @endphp
            <div>
                <div class="flex items-center gap-3 mb-1.5">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background: {{ $clr }}20">
                        <span class="ms text-[16px]" style="color: {{ $clr }}">{{ $icon }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold truncate" style="color:var(--text-2)">{{ $lbl }}</span>
                            <span class="text-xs font-bold ml-2 flex-shrink-0" style="color: {{ $clr }}">
                                {{ $pct }}%
                            </span>
                        </div>
                        <div class="flex items-center justify-between mt-0.5">
                            <span class="text-[10px]" style="color:var(--text-4)">{{ $cat->jumlah_transaksi }} transaksi</span>
                            <span class="text-[10px] font-semibold" style="color:var(--text-2)">
                                Rp {{ number_format($cat->total, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="h-1.5 rounded-full overflow-hidden ml-11" style="background:var(--bg-input)">
                    <div class="h-full rounded-full transition-all duration-700"
                         style="width: {{ $pct }}%; background: {{ $clr }}"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<script>
window.categoryChartData = {
    labels: @json($topExpenseCategories->map(fn($c) => KategoriTransaksi::label($c->kategori))),
    data:   @json($topExpenseCategories->pluck('total')),
    colors: @json(array_slice($chartColors, 0, $topExpenseCategories->count())),
};
</script>
@else
<div class="glass rounded-2xl p-8 mb-5 text-center">
    <span class="ms text-[48px] block mb-3" style="color:var(--text-4)">category</span>
    <p class="text-sm font-semibold" style="color:var(--text-3)">Belum ada data kategori pengeluaran</p>
    <p class="text-xs mt-1" style="color:var(--text-4)">Minta user untuk mengisi kategori saat mencatat transaksi</p>
</div>
@endif

{{-- Stats Table --}}
<div class="glass rounded-2xl overflow-hidden">
    <div class="flex items-center gap-2 px-6 py-4" style="border-bottom:1px solid var(--border)">
        <span class="ms text-[20px] text-violet-400">table_chart</span>
        <h3 class="font-bold" style="color:var(--text-1)">Detailed User Statistics</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr style="border-bottom:1px solid var(--border); background:var(--bg-subtle)">
                    <th class="text-left px-6 py-3 text-[10px] font-semibold uppercase tracking-widest" style="color:var(--text-3)">User</th>
                    <th class="text-center px-6 py-3 text-[10px] font-semibold uppercase tracking-widest" style="color:var(--text-3)">Transaksi</th>
                    <th class="text-right px-6 py-3 text-[10px] font-semibold uppercase tracking-widest" style="color:var(--text-3)">Pemasukan</th>
                    <th class="text-right px-6 py-3 text-[10px] font-semibold uppercase tracking-widest" style="color:var(--text-3)">Pengeluaran</th>
                    <th class="text-right px-6 py-3 text-[10px] font-semibold uppercase tracking-widest" style="color:var(--text-3)">Saldo</th>
                </tr>
            </thead>
            <tbody>
                @forelse($userStats as $stat)
                @php $saldo = ($stat->total_pemasukan ?? 0) - ($stat->total_pengeluaran ?? 0); @endphp
                <tr class="transition-colors"
                    style="border-bottom:1px solid var(--divider)"
                    onmouseenter="this.style.background='var(--bg-hover)'"
                    onmouseleave="this.style.background=''">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-violet-500 to-teal-400
                                        flex items-center justify-center text-xs font-bold"
                                 style="color:#ffffff">
                                {{ strtoupper(substr($stat->email, 0, 1)) }}
                            </div>
                            <span class="text-sm" style="color:var(--text-1)">{{ $stat->email }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center text-sm font-bold" style="color:var(--text-1)">
                        {{ $stat->total_transactions }}
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-bold text-teal-400">
                        Rp {{ number_format($stat->total_pemasukan ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-bold text-red-400">
                        Rp {{ number_format($stat->total_pengeluaran ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-bold {{ $saldo >= 0 ? 'text-teal-400' : 'text-red-400' }}">
                        Rp {{ number_format($saldo, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center">
                        <span class="ms text-[48px] block mb-3" style="color:var(--text-4)">bar_chart</span>
                        <p class="text-sm" style="color:var(--text-4)">Tidak ada data statistik.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
