@php
$cards = [
    ['label'=>'Total Users',       'value'=> $totalUsers,
     'icon'=>'group',              'from'=>'from-violet-600', 'to'=>'to-violet-500'],
    ['label'=>'Total Transaksi',   'value'=> $totalTransactions,
     'icon'=>'swap_horiz',         'from'=>'from-pink-600',   'to'=>'to-pink-500'],
    ['label'=>'Total Pemasukan',   'value'=>'Rp '.number_format($totalPemasukan,0,',','.'),
     'icon'=>'trending_up',        'from'=>'from-teal-600',   'to'=>'to-teal-500'],
    ['label'=>'Total Saldo',       'value'=>'Rp '.number_format($totalSaldo,0,',','.'),
     'icon'=>'account_balance_wallet','from'=>'from-blue-600','to'=>'to-blue-500'],
    ['label'=>'Total Budget',      'value'=> $totalBudgets.' anggaran',
     'icon'=>'payments',           'from'=>'from-orange-600', 'to'=>'to-orange-500'],
    ['label'=>'Budget Melebihi',   'value'=> $budgetsOverLimit.' budget',
     'icon'=>'warning',            'from'=>'from-red-600',    'to'=>'to-red-500'],
    ['label'=>'Total Goals',       'value'=> $totalGoals.' target',
     'icon'=>'flag',               'from'=>'from-yellow-600', 'to'=>'to-yellow-500'],
    ['label'=>'Goals Tercapai',    'value'=> $goalsSelesai.' selesai',
     'icon'=>'workspace_premium',  'from'=>'from-emerald-600','to'=>'to-emerald-500'],
];
@endphp

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    @foreach($cards as $c)
    <div class="glass rounded-2xl p-5 relative overflow-hidden group transition-all duration-300"
         style="border-color:var(--border)">
        {{-- Ambient blob — redup di light mode via CSS --}}
        <div class="absolute -top-6 -right-6 w-24 h-24 bg-gradient-to-br {{ $c['from'] }} {{ $c['to'] }}
                    opacity-10 rounded-full blur-xl group-hover:opacity-20 transition-opacity pointer-events-none"></div>
        {{-- Icon gradient — teks selalu putih karena di atas gradient --}}
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $c['from'] }} {{ $c['to'] }}
                    flex items-center justify-center mb-3 shadow-lg">
            <span class="ms text-[20px]" style="color:#ffffff">{{ $c['icon'] }}</span>
        </div>
        <p class="text-[10px] font-semibold uppercase tracking-widest mb-1" style="color:var(--text-3)">
            {{ $c['label'] }}
        </p>
        <p class="font-bold text-xl leading-tight" style="color:var(--text-1)">
            {{ $c['value'] }}
        </p>
    </div>
    @endforeach
</div>
