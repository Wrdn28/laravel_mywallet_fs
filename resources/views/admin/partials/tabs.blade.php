@php
$tabList = [
    ['key'=>'users',        'icon'=>'manage_accounts',       'label'=>'Manage Users'],
    ['key'=>'transactions', 'icon'=>'receipt_long',          'label'=>'Transaksi'],
    ['key'=>'budgets',      'icon'=>'account_balance_wallet','label'=>'Budget & Goals'],
    ['key'=>'reports',      'icon'=>'bar_chart',             'label'=>'Laporan'],
    ['key'=>'config',       'icon'=>'settings',              'label'=>'Konfigurasi'],
];
@endphp

<div class="flex gap-2 flex-wrap">
    @foreach($tabList as $tab)
    @php $isActive = $activeTab === $tab['key']; @endphp
    <button onclick="switchTab('{{ $tab['key'] }}', this)"
            data-tab="{{ $tab['key'] }}"
            class="tab-btn flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200
                   {{ $isActive ? 'bg-violet-600 shadow-[0_0_15px_rgba(124,58,237,0.3)]' : '' }}"
            style="{{ $isActive
                ? 'color:#ffffff'
                : 'color:var(--text-3); background:var(--bg-input); border:1px solid var(--border)' }}">
        <span class="ms text-[18px]">{{ $tab['icon'] }}</span>
        {{ $tab['label'] }}
    </button>
    @endforeach
</div>
