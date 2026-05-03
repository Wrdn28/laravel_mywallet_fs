<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <title>Pengaturan — {{ $appName }}</title>
    @include('partials.head')
</head>
<body class="overflow-x-hidden">

@include('partials.sidebar', ['activeMenu' => 'pengaturan'])

<div class="fixed inset-0 pointer-events-none -z-10">
    <div class="absolute top-[10%] left-[30%] w-[35rem] h-[35rem] bg-violet-700/8 rounded-full blur-[120px]"></div>
</div>

<main class="ml-64 min-h-screen main-with-sidebar">
    <header class="sticky top-0 z-40 h-16 flex items-center px-4 lg:px-8
                   app-header">
        <button onclick="openSidebar()"
                class="mobile-menu-btn w-9 h-9 rounded-lg bg-white/5 hover:bg-white/10
                       items-center justify-center text-slate-400 hover:text-white transition-all mr-3">
            <span class="ms text-[20px]">menu</span>
        </button>
        <div>
            <h2 class="font-bold text-lg" style="color:var(--text-1)">Pengaturan Akun</h2>
            <p class="text-slate-500 text-xs hidden lg:block">Kelola profil dan keamanan akun Anda</p>
        </div>
    </header>

    @include('partials.toast')

    <div class="p-4 lg:p-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 max-w-4xl">

            {{-- Profile --}}
            <div class="glass rounded-2xl overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4" style="border-bottom:1px solid var(--border)">
                    <span class="ms text-[20px] text-violet-400">manage_accounts</span>
                    <h3 class="font-bold" style="color:var(--text-1)">Profil Saya</h3>
                </div>
                <form method="POST" action="{{ route('pengaturan.profile') }}" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Email</label>
                        <input type="email" name="email" value="{{ $userData->email }}" required
                               class="w-full bg-white/5 border {{ $errors->has('email') ? 'border-red-500/50' : 'border-white/10' }}
                                      rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-violet-500 transition-all">
                        @error('email')
                            <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Bergabung Sejak</label>
                        <input type="text" value="{{ $userData->created_at->format('d M Y') }}" readonly
                               class="w-full rounded-xl px-4 py-3 text-slate-500 text-sm cursor-not-allowed"
                               style="background:var(--bg-subtle); border:1px solid var(--border)">
                    </div>
                    <button type="submit"
                            class="w-full py-3 rounded-xl text-sm font-bold text-white
                                   bg-gradient-to-r from-violet-600 to-violet-500
                                   hover:from-violet-500 hover:to-violet-400 transition-all glow-primary active:scale-[0.98]">
                        Simpan Perubahan
                    </button>
                </form>
            </div>

            {{-- Password --}}
            <div class="glass rounded-2xl overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4" style="border-bottom:1px solid var(--border)">
                    <span class="ms text-[20px] text-violet-400">lock</span>
                    <h3 class="font-bold" style="color:var(--text-1)">Ubah Password</h3>
                </div>
                <form method="POST" action="{{ route('pengaturan.password') }}" class="p-6 space-y-4">
                    @csrf
                    @foreach([
                        ['current_password','Password Saat Ini','Password lama'],
                        ['new_password','Password Baru','Min. 6 karakter'],
                        ['new_password_confirmation','Konfirmasi Password','Ulangi password baru'],
                    ] as [$field, $label, $placeholder])
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ $label }}</label>
                        <input type="password" name="{{ $field }}" placeholder="{{ $placeholder }}" required
                               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm
                                      placeholder:text-slate-600 focus:outline-none focus:border-violet-500 transition-all">
                    </div>
                    @endforeach
                    <button type="submit"
                            class="w-full py-3 rounded-xl text-sm font-bold text-white
                                   bg-gradient-to-r from-violet-600 to-violet-500
                                   hover:from-violet-500 hover:to-violet-400 transition-all glow-primary active:scale-[0.98]">
                        Ubah Password
                    </button>
                </form>
            </div>

            {{-- Account Info --}}
            <div class="glass rounded-2xl overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4" style="border-bottom:1px solid var(--border)">
                    <span class="ms text-[20px] text-violet-400">info</span>
                    <h3 class="font-bold" style="color:var(--text-1)">Informasi Akun</h3>
                </div>
                <div class="p-6 space-y-3">
                    @php
                    $infos = [
                        ['ID Pengguna', '#'.Auth::id(), 'tag'],
                        ['Status Akun', 'Aktif', 'verified'],
                        ['Bergabung', $userData->created_at->format('d F Y'), 'calendar_today'],
                    ];
                    @endphp
                    @foreach($infos as [$label, $value, $icon])
                        <div class="flex items-center justify-between py-3 last:border-0" style="border-bottom:1px solid var(--border)">
                            <div class="flex items-center gap-2 text-slate-400 text-sm">
                                <span class="ms text-[16px]">{{ $icon }}</span>
                                {{ $label }}
                            </div>
                            <span class="text-sm font-semibold
                                         {{ $value === 'Aktif' ? 'text-teal-400 bg-teal-500/10 px-3 py-1 rounded-full text-xs' : '' }}"
                                  style="{{ $value !== 'Aktif' ? 'color:var(--text-1)' : '' }}">
                                {{ $value }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Danger Zone --}}
            <div class="glass rounded-2xl overflow-hidden border border-red-500/20">
                <div class="flex items-center gap-3 px-6 py-4" style="border-bottom:1px solid rgba(220,38,38,0.1)">
                    <span class="ms text-[20px] text-red-400">warning</span>
                    <h3 class="font-bold" style="color:var(--text-1)">Zona Berbahaya</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-start gap-3 p-4 bg-red-500/8 border border-red-500/20 rounded-xl mb-5">
                        <span class="ms text-[18px] text-red-400 flex-shrink-0 mt-0.5">error</span>
                        <p class="text-red-300 text-sm">
                            Menghapus akun akan menghapus semua data transaksi secara permanen dan tidak dapat dikembalikan.
                        </p>
                    </div>
                    <button onclick="document.getElementById('deleteModal').classList.remove('hidden'); document.getElementById('deleteModal').classList.add('flex')"
                            class="w-full py-3 rounded-xl text-sm font-bold text-white
                                   bg-gradient-to-r from-red-700 to-red-600
                                   hover:from-red-600 hover:to-red-500 transition-all glow-danger active:scale-[0.98]">
                        Hapus Akun & Semua Data
                    </button>
                </div>
            </div>

        </div>
    </div>
</main>

{{-- Delete Modal --}}
<div id="deleteModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
    <div class="modal-anim glass rounded-2xl w-full max-w-sm border border-red-500/20">
        <div class="p-6 text-center">
            <div class="w-14 h-14 rounded-2xl bg-red-500/15 flex items-center justify-center mx-auto mb-4">
                <span class="ms text-[28px] text-red-400">delete_forever</span>
            </div>
            <h3 class="font-bold text-lg mb-2" style="color:var(--text-1)">Hapus Akun?</h3>
            <p class="text-slate-400 text-sm">Semua data transaksi akan dihapus permanen.</p>
        </div>
        <form method="POST" action="{{ route('pengaturan.delete') }}" class="px-6 pb-6 space-y-4">
            @csrf @method('DELETE')
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">
                    Ketik <span class="text-red-400">HAPUS</span> untuk konfirmasi
                </label>
                <input type="text" name="confirm_text" placeholder="HAPUS" required
                       class="w-full bg-white/5 border border-red-500/30 rounded-xl px-4 py-3 text-white text-sm
                              placeholder:text-slate-600 focus:outline-none focus:border-red-500 transition-all">
            </div>
            <div class="flex gap-3">
                <button type="button"
                        onclick="document.getElementById('deleteModal').classList.add('hidden'); document.getElementById('deleteModal').classList.remove('flex')"
                        class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-slate-400 bg-white/5 hover:bg-white/10 transition-all">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 rounded-xl text-sm font-bold text-white
                               bg-gradient-to-r from-red-700 to-red-600 hover:from-red-600 hover:to-red-500
                               transition-all glow-danger active:scale-[0.98]">
                    Ya, Hapus
                </button>
            </div>
        </form>
    </div>
</div>
<script>
function openSidebar() {
    document.getElementById('sidebar').classList.remove('-translate-x-full');
    document.getElementById('sidebarOverlay').classList.remove('hidden');
}
function closeSidebar() {
    document.getElementById('sidebar').classList.add('-translate-x-full');
    document.getElementById('sidebarOverlay').classList.add('hidden');
}
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) { this.classList.add('hidden'); this.classList.remove('flex'); }
});
</script>
</body>
</html>
