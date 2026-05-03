<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <title>Daftar — DOMPETKU</title>
    @include('partials.head')
</head>
<body class="min-h-screen flex items-center justify-center p-4">

<div class="fixed inset-0 pointer-events-none -z-10">
    <div class="absolute top-[20%] left-[30%] w-[35rem] h-[35rem] bg-violet-700/10 rounded-full blur-[120px] ambient-blob"></div>
    <div class="absolute bottom-[10%] right-[20%] w-[25rem] h-[25rem] bg-teal-500/8 rounded-full blur-[100px] ambient-blob"></div>
</div>

<button onclick="toggleTheme()" class="theme-toggle fixed top-4 right-4 z-50" title="Toggle tema">
    <span class="ms text-[18px] theme-icon">light_mode</span>
</button>

<div class="w-full max-w-sm">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-black bg-gradient-to-br from-violet-400 to-teal-400 bg-clip-text text-transparent">
            DOMPETKU
        </h1>
        <p class="text-slate-500 text-sm mt-1">Buat akun baru</p>
    </div>

    <div class="glass rounded-2xl p-8">
        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf
            @foreach([
                ['name','name','text','Nama Lengkap','Nama Anda'],
                ['email','email','email','Email','email@contoh.com'],
                ['password','password','password','Password','Min. 8 karakter'],
                ['password_confirmation','','password','Konfirmasi Password','Ulangi password'],
            ] as [$field, $errField, $type, $label, $placeholder])
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ $label }}</label>
                <input type="{{ $type }}" name="{{ $field }}"
                       value="{{ $type !== 'password' ? old($field) : '' }}"
                       placeholder="{{ $placeholder }}"
                       {{ in_array($field,['name','email']) ? 'required' : 'required' }}
                       class="w-full bg-white/5 border {{ $errField && $errors->has($errField) ? 'border-red-500/50' : 'border-white/10' }}
                              rounded-xl px-4 py-3 text-white text-sm placeholder:text-slate-600
                              focus:outline-none focus:border-violet-500 transition-all">
                @if($errField)
                    @error($errField)
                        <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                            <span class="ms text-[14px]">error</span> {{ $message }}
                        </p>
                    @enderror
                @endif
            </div>
            @endforeach

            <button type="submit"
                    class="w-full py-3 rounded-xl text-sm font-bold text-white
                           bg-gradient-to-r from-violet-600 to-violet-500
                           hover:from-violet-500 hover:to-violet-400
                           transition-all glow-primary active:scale-[0.98]">
                Daftar
            </button>
        </form>
    </div>

    <p class="text-center text-slate-500 text-sm mt-6">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="text-violet-400 font-semibold hover:text-violet-300 transition-colors">
            Masuk di sini
        </a>
    </p>
</div>
</body>
</html>
