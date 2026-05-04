<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <title>Login — DOMPETKU</title>
    @include('partials.head')
</head>
<body class="min-h-screen flex items-center justify-center p-4">

<div class="fixed inset-0 pointer-events-none -z-10">
    <div class="absolute top-[20%] left-[30%] w-[35rem] h-[35rem] bg-violet-700/10 rounded-full blur-[120px] ambient-blob"></div>
    <div class="absolute bottom-[10%] right-[20%] w-[25rem] h-[25rem] bg-teal-500/8 rounded-full blur-[100px] ambient-blob"></div>
</div>

{{-- Theme toggle --}}
<button onclick="toggleTheme()" class="theme-toggle fixed top-4 right-4 z-50" title="Toggle tema">
    <span class="ms text-[18px] theme-icon">light_mode</span>
</button>

<div class="w-full max-w-sm">
    {{-- Logo --}}
    <div class="text-center mb-8">
        <h1 class="text-3xl font-black bg-gradient-to-br from-violet-400 to-teal-400 bg-clip-text text-transparent">
            DOMPETKU
        </h1>
        <p class="text-slate-500 text-sm mt-1">Masuk ke akun Anda</p>
    </div>

    <div class="glass rounded-2xl p-8">
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       placeholder="email@contoh.com"
                       class="w-full bg-white/5 border {{ $errors->has('email') ? 'border-red-500/50' : 'border-white/10' }}
                              rounded-xl px-4 py-3 text-white text-sm placeholder:text-slate-600
                              focus:outline-none focus:border-violet-500 transition-all">
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
                              placeholder:text-slate-600 focus:outline-none focus:border-violet-500 transition-all">
            </div>

            <div class="flex items-center gap-2.5">
                <button type="button" id="rememberBtn"
                        onclick="toggleRemember()"
                        class="w-5 h-5 rounded flex items-center justify-center flex-shrink-0 transition-all"
                        style="background:var(--bg-input); border:1.5px solid var(--border)">
                    <span class="ms text-[14px] hidden" id="rememberCheck" style="color:#ffffff">check</span>
                </button>
                <input type="checkbox" name="remember" id="remember" class="hidden">
                <label for="rememberBtn" onclick="toggleRemember()"
                       class="text-sm cursor-pointer select-none" style="color:var(--text-3)">
                    Ingat saya
                </label>
            </div>

            <button type="submit"
                    class="w-full py-3 rounded-xl text-sm font-bold text-white
                           bg-gradient-to-r from-violet-600 to-violet-500
                           hover:from-violet-500 hover:to-violet-400
                           transition-all glow-primary active:scale-[0.98]">
                Masuk
            </button>
        </form>
    </div>

    <p class="text-center text-slate-500 text-sm mt-6">
        Belum punya akun?
        <a href="{{ route('register') }}" class="text-violet-400 font-semibold hover:text-violet-300 transition-colors">
            Daftar sekarang
        </a>
    </p>
</div>

<script>
function toggleRemember() {
    const cb    = document.getElementById('remember');
    const btn   = document.getElementById('rememberBtn');
    const check = document.getElementById('rememberCheck');
    cb.checked = !cb.checked;
    if (cb.checked) {
        btn.style.background   = 'var(--accent-violet)';
        btn.style.borderColor  = 'var(--accent-violet)';
        check.classList.remove('hidden');
    } else {
        btn.style.background   = 'var(--bg-input)';
        btn.style.borderColor  = 'var(--border)';
        check.classList.add('hidden');
    }
}
</script>
</body>
</html>
