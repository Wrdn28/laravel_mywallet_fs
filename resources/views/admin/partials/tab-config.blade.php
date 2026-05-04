<div class="max-w-2xl">
    <div class="glass rounded-2xl overflow-hidden">
        <div class="flex items-center gap-2 px-6 py-4" style="border-bottom:1px solid var(--border)">
            <span class="ms text-[20px] text-violet-400">settings</span>
            <h2 class="font-bold" style="color:var(--text-1)">System Configuration</h2>
        </div>

        @if($errors->any())
        <div class="mx-6 mt-4 flex items-center gap-3 px-4 py-3 rounded-xl bg-red-500/10 border border-red-500/20">
            <span class="ms text-[18px] text-red-400">error</span>
            <p class="text-sm text-red-400">{{ $errors->first() }}</p>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.config.update') }}" class="p-6 space-y-5"
              style="background:var(--bg-card)">
            @csrf

            {{-- App Name --}}
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider mb-2"
                       style="color:var(--text-3)">
                    <span class="flex items-center gap-1.5">
                        <span class="ms text-[14px]">title</span> Application Name
                    </span>
                </label>
                <input type="text" name="app_name" id="inputAppName"
                       value="{{ old('app_name', $appName) }}" required
                       class="w-full rounded-xl px-4 py-3 text-sm focus:outline-none transition-all">
                <p class="text-xs mt-1.5" style="color:var(--text-4)">Nama ini tampil di header dan title halaman</p>
            </div>

            {{-- Admin Email --}}
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider mb-2"
                       style="color:var(--text-3)">
                    <span class="flex items-center gap-1.5">
                        <span class="ms text-[14px]">email</span> Admin Email
                    </span>
                </label>
                <input type="email" name="admin_email"
                       value="{{ old('admin_email', $adminEmail) }}" required
                       class="w-full rounded-xl px-4 py-3 text-sm focus:outline-none transition-all">
            </div>

            {{-- System Version (readonly) --}}
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider mb-2"
                       style="color:var(--text-3)">
                    <span class="flex items-center gap-1.5">
                        <span class="ms text-[14px]">code_blocks</span> System Version
                    </span>
                </label>
                <input type="text" value="{{ $systemVersion }}" readonly
                       class="w-full rounded-xl px-4 py-3 text-sm cursor-not-allowed"
                       style="opacity:0.6">
            </div>

            {{-- Maintenance Mode --}}
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider mb-3"
                       style="color:var(--text-3)">
                    <span class="flex items-center gap-1.5">
                        <span class="ms text-[14px]">construction</span> Maintenance Mode
                    </span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer" onclick="toggleMaintenanceUI()">
                    <div class="relative flex-shrink-0">
                        <input type="checkbox" name="maintenance_mode" value="1" id="maintenanceToggle"
                               {{ old('maintenance_mode', $maintenanceMode) === '1' ? 'checked' : '' }}
                               class="sr-only">
                        {{-- Track --}}
                        <div id="maintenanceTrack"
                             class="w-11 h-6 rounded-full transition-colors duration-200"
                             style="background:{{ $maintenanceMode === '1' ? 'var(--accent-violet)' : 'var(--bg-input)' }}">
                        </div>
                        {{-- Thumb --}}
                        <div id="maintenanceThumb"
                             class="absolute top-0.5 h-5 w-5 rounded-full transition-all duration-200"
                             style="background:#ffffff; left:{{ $maintenanceMode === '1' ? '22px' : '2px' }}">
                        </div>
                    </div>
                    <span class="text-sm font-medium" style="color:var(--text-2)">Aktifkan mode maintenance</span>
                </label>
                <p class="text-xs mt-2 ml-14" style="color:var(--text-4)">Saat aktif, user tidak bisa mengakses aplikasi</p>
                <div id="maintenanceWarning"
                     class="{{ $maintenanceMode === '1' ? 'flex' : 'hidden' }} items-center gap-2 mt-3
                             px-4 py-3 rounded-xl bg-yellow-500/10 border border-yellow-500/20">
                    <span class="ms text-[18px] text-yellow-400">warning</span>
                    <p class="text-sm font-semibold text-yellow-400">Mode maintenance sedang AKTIF!</p>
                </div>
            </div>

            <script>
            function toggleMaintenanceUI() {
                const cb      = document.getElementById('maintenanceToggle');
                const track   = document.getElementById('maintenanceTrack');
                const thumb   = document.getElementById('maintenanceThumb');
                const warning = document.getElementById('maintenanceWarning');
                cb.checked = !cb.checked;
                track.style.background = cb.checked ? 'var(--accent-violet)' : 'var(--bg-input)';
                thumb.style.left       = cb.checked ? '22px' : '2px';
                warning.classList.toggle('hidden', !cb.checked);
                warning.classList.toggle('flex',   cb.checked);
            }
            </script>

            {{-- Stats Info --}}
            <div class="grid grid-cols-2 gap-3 pt-2">
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl"
                     style="background:var(--bg-subtle); border:1px solid var(--border)">
                    <span class="ms text-[24px] text-violet-400">group</span>
                    <div>
                        <p class="font-bold text-xl leading-none" style="color:var(--text-1)">{{ $totalUsers }}</p>
                        <p class="text-xs mt-0.5" style="color:var(--text-4)">Total Users</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl"
                     style="background:var(--bg-subtle); border:1px solid var(--border)">
                    <span class="ms text-[24px] text-teal-400">swap_horiz</span>
                    <div>
                        <p class="font-bold text-xl leading-none" style="color:var(--text-1)">{{ $totalTransactions }}</p>
                        <p class="text-xs mt-0.5" style="color:var(--text-4)">Total Transaksi</p>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl text-sm font-bold
                               bg-gradient-to-r from-violet-600 to-violet-500 hover:from-violet-500 hover:to-violet-400
                               transition-all active:scale-[0.98]"
                        style="color:#ffffff">
                    <span class="ms text-[18px]">save</span> Update Configuration
                </button>
                <button type="button" onclick="resetConfigForm()"
                        class="px-5 py-3 rounded-xl text-sm font-semibold transition-all"
                        style="background:var(--bg-input); color:var(--text-3); border:1px solid var(--border)">
                    <span class="ms text-[18px]">undo</span>
                </button>
            </div>
        </form>
    </div>
</div>
