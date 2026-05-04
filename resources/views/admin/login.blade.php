<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <title>Admin Login — DOMPETKU</title>
    @include('partials.head')
</head>
<body class="min-h-screen flex items-center justify-center p-4">

<div class="fixed inset-0 pointer-events-none -z-10">
    <div class="absolute top-[20%] left-[30%] w-[35rem] h-[35rem] bg-red-700/8 rounded-full blur-[120px]"></div>
    <div class="absolute bottom-[10%] right-[20%] w-[25rem] h-[25rem] bg-violet-700/8 rounded-full blur-[100px]"></div>
</div>

<div class="w-full max-w-sm">
    <div class="text-center mb-8">
        <div class="w-14 h-14 rounded-2xl bg-red-500/15 border border-red-500/20 flex items-center justify-center mx-auto mb-4">
            <span class="ms text-[28px] text-red-400">admin_panel_settings</span>
        </div>
        <h1 class="text-2xl font-black text-white">Admin Panel</h1>
        <p class="text-slate-500 text-sm mt-1">DOMPETKU — Akses Administrator</p>
    </div>

    <div class="glass rounded-2xl p-8 border border-white/10">
        <form method="POST" action="{{ route('admin.login') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Email Admin</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       placeholder="Email admin"
                       class="w-full bg-white/5 border {{ $errors->has('email') ? 'border-red-500/50' : 'border-white/10' }}
                              rounded-xl px-4 py-3 text-white text-sm placeholder:text-slate-600
                              focus:outline-none focus:border-red-500 transition-all">
                @error('email')
                    <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                        <span class="ms text-[14px]">error</span> {{ $message }}
                    </p>
                @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Password</label>
                <input type="password" name="password" required placeholder="••••••••"
                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm
                              placeholder:text-slate-600 focus:outline-none focus:border-red-500 transition-all">
            </div>
            <button type="submit"
                    class="w-full py-3 rounded-xl text-sm font-bold text-white
                           bg-gradient-to-r from-red-700 to-red-600
                           hover:from-red-600 hover:to-red-500
                           transition-all glow-danger active:scale-[0.98]">
                Masuk sebagai Admin
            </button>
        </form>
    </div>

    <p class="text-center text-slate-500 text-sm mt-6">
        <a href="{{ route('login') }}" class="text-slate-400 hover:text-white transition-colors flex items-center justify-center gap-1">
            <span class="ms text-[16px]">arrow_back</span> Kembali ke login user
        </a>
    </p>

    {{-- Theme toggle pojok kanan bawah --}}
    <div class="fixed bottom-6 right-6">
        <button onclick="toggleTheme()" class="theme-toggle" title="Toggle tema">
            <span class="ms text-[18px] theme-icon">light_mode</span>
        </button>
    </div>
</div>
</body>
</html>
