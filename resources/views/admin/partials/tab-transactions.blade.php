{{-- Filter --}}
<div class="glass rounded-2xl px-6 py-4 flex flex-wrap items-center gap-4 mb-4">
    <span class="ms text-[18px] text-violet-400">filter_list</span>
    <span class="text-xs font-semibold uppercase tracking-widest" style="color:var(--text-3)">Filter</span>
    <form method="GET" class="flex flex-wrap items-center gap-3">
        <input type="hidden" name="tab" value="transactions">
        @foreach(['all'=>'Semua','week'=>'1 Minggu','month'=>'1 Bulan','custom'=>'Custom'] as $val=>$label)
        @php $isActive = request('periode','all') === $val; @endphp
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="periode" value="{{ $val }}"
                   {{ $isActive ? 'checked' : '' }}
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
    <span class="ml-auto text-xs font-semibold" style="color:var(--text-4)">
        {{ $transactions->count() }} transaksi
    </span>
</div>

{{-- Table --}}
<div class="glass rounded-2xl overflow-hidden">
    <div class="flex items-center gap-2 px-6 py-4" style="border-bottom:1px solid var(--border)">
        <span class="ms text-[20px] text-violet-400">receipt_long</span>
        <h2 class="font-bold" style="color:var(--text-1)">Transaction Management</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr style="border-bottom:1px solid var(--border); background:var(--bg-subtle)">
                    <th class="text-left px-6 py-3 text-[10px] font-semibold uppercase tracking-widest" style="color:var(--text-3)">ID</th>
                    <th class="text-left px-6 py-3 text-[10px] font-semibold uppercase tracking-widest" style="color:var(--text-3)">User</th>
                    <th class="text-left px-6 py-3 text-[10px] font-semibold uppercase tracking-widest" style="color:var(--text-3)">Tanggal</th>
                    <th class="text-left px-6 py-3 text-[10px] font-semibold uppercase tracking-widest" style="color:var(--text-3)">Deskripsi</th>
                    <th class="text-left px-6 py-3 text-[10px] font-semibold uppercase tracking-widest" style="color:var(--text-3)">Tipe</th>
                    <th class="text-right px-6 py-3 text-[10px] font-semibold uppercase tracking-widest" style="color:var(--text-3)">Jumlah</th>
                    <th class="text-center px-6 py-3 text-[10px] font-semibold uppercase tracking-widest" style="color:var(--text-3)">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $t)
                <tr class="transition-colors"
                    style="border-bottom:1px solid var(--divider)"
                    onmouseenter="this.style.background='var(--bg-hover)'"
                    onmouseleave="this.style.background=''">
                    <td class="px-6 py-4 text-sm" style="color:var(--text-4)">#{{ $t->id }}</td>
                    <td class="px-6 py-4 text-sm" style="color:var(--text-2)">{{ $t->user->email ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm" style="color:var(--text-3)">{{ $t->tanggal->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-sm font-medium max-w-[200px] truncate" style="color:var(--text-1)">
                        {{ $t->deskripsi ?? '—' }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold
                            {{ $t->tipe === 'pemasukan'
                                ? 'bg-teal-500/15 text-teal-400'
                                : 'bg-red-500/15 text-red-400' }}">
                            {{ ucfirst($t->tipe) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right font-bold text-sm
                               {{ $t->tipe === 'pemasukan' ? 'text-teal-400' : 'text-red-400' }}">
                        {{ $t->tipe === 'pemasukan' ? '+' : '-' }}Rp {{ number_format($t->jumlah,0,',','.') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button onclick="confirmDeleteTransaction({{ $t->id }})"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold mx-auto
                                       text-red-400 bg-red-500/10 hover:bg-red-500/20 transition-all">
                            <span class="ms text-[14px]">delete</span> Hapus
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center">
                        <span class="ms text-[48px] block mb-3" style="color:var(--text-4)">receipt_long</span>
                        <p class="text-sm" style="color:var(--text-4)">Tidak ada transaksi pada periode ini.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
