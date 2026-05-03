<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $appName }} — Maintenance</title>
    @include('partials.head')
</head>
<body class="min-h-screen flex items-center justify-center p-4">

{{-- Ambient blobs --}}
<div class="fixed inset-0 pointer-events-none -z-10">
    <div class="absolute top-[15%] left-[25%] w-[40rem] h-[40rem] bg-violet-700/10 rounded-full blur-[120px]"></div>
    <div class="absolute bottom-[10%] right-[15%] w-[30rem] h-[30rem] bg-yellow-600/8 rounded-full blur-[100px]"></div>
</div>

<div class="text-center max-w-md w-full">

    {{-- Icon --}}
    <div class="relative inline-flex mb-8">
        <div class="w-24 h-24 rounded-3xl bg-yellow-500/15 border border-yellow-500/20
                    flex items-center justify-center mx-auto
                    shadow-[0_0_40px_rgba(234,179,8,0.15)]">
            <span class="ms text-[48px] text-yellow-400">construction</span>
        </div>
        {{-- Spinning ring --}}
        <div class="absolute inset-0 rounded-3xl border-2 border-yellow-500/20 border-t-yellow-400
                    animate-spin" style="animation-duration:3s"></div>
    </div>

    {{-- Text --}}
    <h1 class="text-3xl font-black text-white mb-3 tracking-tight">
        Sedang Maintenance
    </h1>
    <p class="text-slate-400 text-base leading-relaxed mb-8">
        <span class="font-semibold text-white">{{ $appName }}</span> sedang dalam pemeliharaan.<br>
        Kami akan segera kembali. Terima kasih atas kesabaran Anda.
    </p>

    {{-- Status badge --}}
    <div class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full
                bg-yellow-500/10 border border-yellow-500/20 mb-8">
        <span class="w-2 h-2 rounded-full bg-yellow-400 animate-pulse"></span>
        <span class="text-yellow-300 text-sm font-semibold">Maintenance Mode Aktif</span>
    </div>

    {{-- Info card --}}
    <div class="glass rounded-2xl p-6 text-left space-y-3">
        <div class="flex items-center gap-3">
            <span class="ms text-[20px] text-violet-400">schedule</span>
            <div>
                <p class="text-white text-sm font-semibold">Estimasi Selesai</p>
                <p class="text-slate-500 text-xs">Akan diumumkan segera</p>
            </div>
        </div>
        <div class="border-t border-white/5"></div>
        <div class="flex items-center gap-3">
            <span class="ms text-[20px] text-teal-400">support_agent</span>
            <div>
                <p class="text-white text-sm font-semibold">Butuh bantuan?</p>
                <p class="text-slate-500 text-xs">Hubungi administrator sistem</p>
            </div>
        </div>
    </div>

    {{-- Refresh hint --}}
    <p class="text-slate-600 text-xs mt-6">
        Halaman ini akan otomatis refresh setiap 60 detik
    </p>

    {{-- Admin link --}}
    <a href="/admin/login"
       class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 rounded-xl
              text-slate-400 hover:text-white hover:bg-white/10
              border border-white/10 hover:border-white/20
              text-xs font-semibold transition-all duration-200">
        <span class="ms text-[16px]">admin_panel_settings</span>
        Admin? Masuk di sini
    </a>

</div>

<script>
    // Auto refresh setiap 60 detik
    setTimeout(() => window.location.reload(), 60000);
</script>
</body>
</html>
