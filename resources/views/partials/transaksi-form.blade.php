@php
    use App\Enums\KategoriTransaksi;
    $isEdit = ($mode ?? 'add') === 'edit';
    $prefix = $isEdit ? 'edit' : 'add';

    $allKategoriLabels = collect(KategoriTransaksi::PEMASUKAN)
        ->merge(KategoriTransaksi::PENGELUARAN)
        ->mapWithKeys(fn($cat, $key) => [$key => $cat['label']])
        ->all();

    $hasGoals = !$isEdit && isset($activeGoals) && $activeGoals->count() > 0;
@endphp

<input type="hidden" name="id" id="{{ $prefix }}Id">

{{-- ── Jenis Transaksi ── --}}
<div>
    <label class="form-label">Jenis Transaksi</label>
    <div class="grid grid-cols-3 gap-2">
        <button type="button" data-value="pemasukan"
                class="type-btn" id="{{ $prefix }}TypePemasukan">
            <span class="ms text-[18px]">arrow_downward</span>
            <span>Pemasukan</span>
        </button>
        <button type="button" data-value="pengeluaran"
                class="type-btn" id="{{ $prefix }}TypePengeluaran">
            <span class="ms text-[18px]">arrow_upward</span>
            <span>Pengeluaran</span>
        </button>
        <button type="button" data-value="tabungan"
                class="type-btn" id="{{ $prefix }}TypeTabungan">
            <span class="ms text-[18px]">savings</span>
            <span>Tabungan</span>
        </button>
    </div>
    {{-- tipe yang dikirim ke server: tabungan → pengeluaran, goal_id wajib diisi --}}
    <input type="hidden" name="tipe" value="pemasukan" id="{{ $prefix }}TransactionType">
    <input type="hidden" name="is_tabungan" value="0" id="{{ $prefix }}IsTabungan">
</div>

{{-- ── Pilih Target (hanya saat tabungan) ── --}}
@if(!$isEdit)
<div id="{{ $prefix }}TabunganSection" style="display:none">
    <label class="form-label">
        Target Tabungan <span class="form-label-required">*</span>
    </label>
    @if($hasGoals)
    <div class="form-input-wrap">
        <span class="form-icon-left ms text-[18px]" style="color:var(--accent-teal)">savings</span>
        <select name="goal_id" id="{{ $prefix }}GoalId" class="form-select">
            <option value="">— Pilih Target —</option>
            @foreach($activeGoals as $goal)
            @php
                $sisa = $goal->target - $goal->terkumpul;
                $pct  = $goal->target > 0 ? round(($goal->terkumpul / $goal->target) * 100) : 0;
            @endphp
            <option value="{{ $goal->id }}"
                    data-nama="{{ $goal->nama }}"
                    data-sisa="{{ $sisa }}">
                {{ $goal->nama }} — {{ $pct }}% (sisa Rp {{ number_format($sisa, 0, ',', '.') }})
            </option>
            @endforeach
        </select>
        <span class="form-icon-right ms text-[18px]">expand_more</span>
    </div>
    <p class="text-[10px] mt-1.5" style="color:var(--text-4)" id="{{ $prefix }}GoalSisaHint"></p>
    @else
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl"
         style="background:var(--bg-subtle); border:1px solid var(--border)">
        <span class="ms text-[18px]" style="color:var(--text-4)">info</span>
        <div>
            <p class="text-xs font-semibold" style="color:var(--text-2)">Belum ada target tabungan aktif</p>
            <a href="{{ route('rencana') }}" class="text-[10px] font-bold" style="color:var(--accent-violet)">
                Buat target dulu di halaman Budget & Goals
            </a>
        </div>
    </div>
    @endif
</div>
@endif

{{-- ── Kategori (tersembunyi saat tabungan) ── --}}
<div id="{{ $prefix }}KategoriSection">
    <label class="form-label">Kategori <span class="form-label-required">*</span></label>
    <div class="form-input-wrap">
        <span class="form-icon-left ms text-[18px]"
              id="{{ $prefix }}KategoriIcon">label</span>
        <select name="kategori" id="{{ $prefix }}Kategori"
                onchange="updateKategoriIcon('{{ $prefix }}', this.value); autoFillDeskripsi('{{ $prefix }}', this.value)"
                class="form-select">
            <option value="">— Pilih Kategori —</option>
            <optgroup label="Pemasukan" id="{{ $prefix }}OptPemasukan">
                @foreach(KategoriTransaksi::PEMASUKAN as $key => $cat)
                <option value="{{ $key }}">{{ $cat['label'] }}</option>
                @endforeach
            </optgroup>
            <optgroup label="Pengeluaran" id="{{ $prefix }}OptPengeluaran">
                @foreach(KategoriTransaksi::PENGELUARAN as $key => $cat)
                <option value="{{ $key }}">{{ $cat['label'] }}</option>
                @endforeach
            </optgroup>
        </select>
        <span class="form-icon-right ms text-[18px]">expand_more</span>
    </div>
</div>

{{-- ── Jumlah ── --}}
<div>
    <label class="form-label">Jumlah <span class="form-label-required">*</span></label>
    <div class="form-input-wrap">
        <span class="form-prefix">Rp</span>
        <input type="text" id="{{ $prefix }}JumlahDisplay"
               placeholder="0" inputmode="numeric"
               class="form-input form-input-prefixed"
               autocomplete="off">
        <input type="hidden" name="jumlah" id="{{ $prefix }}Jumlah">
    </div>
</div>

{{-- ── Keterangan ── --}}
<div>
    <label class="form-label">
        Keterangan
        <span class="form-label-hint">(opsional)</span>
    </label>
    <div class="form-input-wrap">
        <span class="form-icon-left ms text-[18px]" style="color:var(--text-4)">notes</span>
        <input type="text" name="deskripsi" id="{{ $prefix }}Deskripsi"
               placeholder="Tambah catatan..."
               class="form-input form-input-icon-left">
    </div>
</div>

{{-- ── Tanggal ── --}}
<div>
    <label class="form-label">Tanggal <span class="form-label-required">*</span></label>
    <div class="form-input-wrap">
        <span class="form-icon-left ms text-[18px]" style="color:var(--text-4)">calendar_today</span>
        <input type="date" name="tanggal" id="{{ $prefix }}Tanggal"
               value="{{ now()->format('Y-m-d') }}" required
               class="form-input form-input-icon-left">
    </div>
</div>

{{-- ── Styles ── --}}
<style>
.form-label {
    display: block;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--text-3);
    margin-bottom: 6px;
}
.form-label-hint {
    font-size: 10px;
    font-weight: 400;
    text-transform: none;
    letter-spacing: 0;
    color: var(--text-4);
}
.form-label-required { color: var(--accent-red); font-size: 11px; }
.form-input-wrap { position: relative; }
.form-input {
    width: 100%;
    padding: 11px 14px;
    font-size: 14px;
    border-radius: 12px;
    background: var(--bg-input) !important;
    border: 1.5px solid var(--border) !important;
    color: var(--text-1) !important;
    transition: border-color 0.18s, box-shadow 0.18s;
}
.form-input:focus {
    border-color: var(--accent-violet) !important;
    box-shadow: 0 0 0 3px rgba(124,58,237,0.13) !important;
    outline: none;
}
.form-input::placeholder { color: var(--text-4) !important; }
.form-input-icon-left { padding-left: 40px !important; }
.form-input-prefixed  { padding-left: 44px !important; }
.form-select {
    width: 100%;
    padding: 11px 36px 11px 40px;
    font-size: 14px;
    border-radius: 12px;
    background: var(--bg-input) !important;
    border: 1.5px solid var(--border) !important;
    color: var(--text-1) !important;
    appearance: none;
    cursor: pointer;
    transition: border-color 0.18s, box-shadow 0.18s;
}
.form-select:focus {
    border-color: var(--accent-violet) !important;
    box-shadow: 0 0 0 3px rgba(124,58,237,0.13) !important;
    outline: none;
}
.form-icon-left {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    color: var(--text-4);
    transition: color 0.18s;
    line-height: 1;
}
.form-icon-right {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    color: var(--text-4);
    line-height: 1;
}
.form-prefix {
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 700;
    color: var(--text-3);
    border-right: 1.5px solid var(--border);
    border-radius: 12px 0 0 12px;
    background: var(--bg-input);
    pointer-events: none;
}
.type-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 10px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    border: 1.5px solid var(--border);
    background: var(--bg-input);
    color: var(--text-3);
    cursor: pointer;
    transition: all 0.18s;
    width: 100%;
}
.type-btn:hover { border-color: var(--accent-violet); color: var(--text-1); }
.type-btn.active-type[data-value="pemasukan"] {
    background: var(--icon-teal-bg) !important;
    border-color: var(--accent-teal) !important;
    color: var(--accent-teal) !important;
}
.type-btn.active-type[data-value="pengeluaran"] {
    background: var(--icon-red-bg) !important;
    border-color: var(--accent-red) !important;
    color: var(--accent-red) !important;
}
.type-btn.active-type[data-value="tabungan"] {
    background: var(--icon-violet-bg) !important;
    border-color: var(--accent-violet) !important;
    color: var(--accent-violet) !important;
}
html:not(.dark) input[type="date"]::-webkit-calendar-picker-indicator {
    filter: none;
    opacity: 0.4;
}
</style>

<script>
window._kategoriLabels = window._kategoriLabels || @json($allKategoriLabels);

// Format ribuan untuk input jumlah di form transaksi
(function() {
    const prefix = '{{ $prefix }}';
    const display = document.getElementById(prefix + 'JumlahDisplay');
    const hidden  = document.getElementById(prefix + 'Jumlah');
    if (!display || !hidden) return;

    display.addEventListener('input', function() {
        const raw = this.value.replace(/\D/g, '');
        this.value = raw ? parseInt(raw).toLocaleString('id-ID') : '';
        hidden.value = raw || '';
    });

    // Validasi saat submit — pastikan hidden terisi
    const form = display.closest('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!hidden.value || parseInt(hidden.value) < 1) {
                e.preventDefault();
                display.focus();
                display.style.borderColor = 'var(--accent-red)';
                display.style.boxShadow   = '0 0 0 3px rgba(220,38,38,0.15)';
                setTimeout(() => {
                    display.style.borderColor = '';
                    display.style.boxShadow   = '';
                }, 2000);
            }
        }, { capture: true });
    }
})();
</script>
