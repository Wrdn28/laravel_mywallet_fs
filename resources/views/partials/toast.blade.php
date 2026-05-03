@if(session('success') || session('error') || session('toast_success') || session('toast_error'))
@php
    $msg  = session('success') ?? session('toast_success') ?? session('error') ?? session('toast_error');
    $type = (session('success') || session('toast_success')) ? 'success' : 'error';
@endphp
<div id="globalToast"
     class="toast-anim fixed top-6 right-6 z-[9999] flex items-center gap-3 px-5 py-4 rounded-2xl glass
            {{ $type === 'success' ? 'border-teal-500/30 shadow-[0_0_20px_rgba(45,212,191,0.15)]' : 'border-red-500/30 shadow-[0_0_20px_rgba(248,113,113,0.15)]' }}
            max-w-sm">
    <span class="ms text-xl {{ $type === 'success' ? 'text-teal-400' : 'text-red-400' }}">
        {{ $type === 'success' ? 'check_circle' : 'error' }}
    </span>
    <p class="text-sm font-semibold text-white flex-1">{{ $msg }}</p>
    <button onclick="this.parentElement.remove()" class="text-slate-500 hover:text-white transition-colors">
        <span class="ms text-[18px]">close</span>
    </button>
</div>
<script>
    setTimeout(() => {
        const t = document.getElementById('globalToast');
        if (t) { t.style.opacity = '0'; t.style.transition = 'opacity 0.3s'; setTimeout(() => t?.remove(), 300); }
    }, 4000);
</script>
@endif
