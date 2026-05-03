@php $periode = request('periode', 'all'); @endphp
<div class="filter-options">
    <label class="filter-option">
        <input type="radio" name="periode" value="all"    {{ $periode === 'all'    ? 'checked' : '' }} onchange="this.form.submit()">
        <span class="filter-label">Semua</span>
    </label>
    <label class="filter-option">
        <input type="radio" name="periode" value="week"   {{ $periode === 'week'   ? 'checked' : '' }} onchange="this.form.submit()">
        <span class="filter-label">1 Minggu</span>
    </label>
    <label class="filter-option">
        <input type="radio" name="periode" value="month"  {{ $periode === 'month'  ? 'checked' : '' }} onchange="this.form.submit()">
        <span class="filter-label">1 Bulan</span>
    </label>
    <label class="filter-option">
        <input type="radio" name="periode" value="custom" {{ $periode === 'custom' ? 'checked' : '' }} onchange="toggleCustomDate(this)">
        <span class="filter-label">Custom</span>
    </label>
</div>
<div class="custom-date" id="customDateBox" style="{{ $periode === 'custom' ? 'display:flex' : 'display:none' }}">
    <input type="date" name="start_date" value="{{ request('start_date') }}">
    <span>s/d</span>
    <input type="date" name="end_date" value="{{ request('end_date') }}">
    <button type="submit" class="btn-filter"><i class="fas fa-check"></i> Terapkan</button>
</div>
