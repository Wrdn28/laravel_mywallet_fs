@if($user->transaksi->count())
<div class="overflow-x-auto">
    <table class="w-full">
        <thead>
            <tr style="border-bottom:1px solid var(--border); background:var(--bg-subtle)">
                <th class="text-left px-4 py-3 text-[10px] font-semibold uppercase tracking-widest" style="color:var(--text-3)">Tanggal</th>
                <th class="text-left px-4 py-3 text-[10px] font-semibold uppercase tracking-widest" style="color:var(--text-3)">Deskripsi</th>
                <th class="text-left px-4 py-3 text-[10px] font-semibold uppercase tracking-widest" style="color:var(--text-3)">Tipe</th>
                <th class="text-right px-4 py-3 text-[10px] font-semibold uppercase tracking-widest" style="color:var(--text-3)">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($user->transaksi as $t)
            <tr class="transition-colors"
                style="border-bottom:1px solid var(--divider)"
                onmouseenter="this.style.background='var(--bg-hover)'"
                onmouseleave="this.style.background=''">
                <td class="px-4 py-3 text-sm" style="color:var(--text-3)">{{ $t->tanggal->format('d M Y') }}</td>
                <td class="px-4 py-3 text-sm" style="color:var(--text-1)">{{ $t->deskripsi ?? '—' }}</td>
                <td class="px-4 py-3">
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold
                        {{ $t->tipe === 'pemasukan' ? 'bg-teal-500/15 text-teal-400' : 'bg-red-500/15 text-red-400' }}">
                        {{ ucfirst($t->tipe) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right font-bold text-sm
                           {{ $t->tipe === 'pemasukan' ? 'text-teal-400' : 'text-red-400' }}">
                    {{ $t->tipe === 'pemasukan' ? '+' : '-' }}Rp {{ number_format($t->jumlah, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div class="flex flex-col items-center justify-center py-12">
    <span class="ms text-[48px] mb-3 opacity-30" style="color:var(--text-4)">receipt_long</span>
    <p class="text-sm" style="color:var(--text-4)">User ini belum memiliki transaksi.</p>
</div>
@endif
