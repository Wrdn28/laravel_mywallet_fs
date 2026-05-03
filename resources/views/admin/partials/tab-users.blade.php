<div class="glass rounded-2xl overflow-hidden">
    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 px-6 py-4"
         style="border-bottom:1px solid var(--border)">
        <div class="flex items-center gap-2">
            <span class="ms text-[20px] text-violet-400">manage_accounts</span>
            <h2 class="font-bold" style="color:var(--text-1)">User Management</h2>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            {{-- Search --}}
            <form method="GET" class="flex">
                <input type="hidden" name="tab" value="users">
                <div class="flex rounded-xl overflow-hidden" style="border:1px solid var(--border)">
                    <input type="text" name="search_user" value="{{ $searchUser }}"
                           placeholder="Cari email..."
                           class="px-4 py-2 text-sm w-48 transition-all focus:outline-none"
                           style="background:var(--bg-input); color:var(--text-1)">
                    <button type="submit"
                            class="px-3 bg-violet-600 hover:bg-violet-500 transition-colors"
                            style="color:#ffffff">
                        <span class="ms text-[18px]">search</span>
                    </button>
                </div>
            </form>
            {{-- Add User --}}
            <button onclick="openModal('addUserModal')"
                    class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold
                           bg-gradient-to-r from-violet-600 to-violet-500 hover:from-violet-500 hover:to-violet-400
                           transition-all active:scale-95"
                    style="color:#ffffff">
                <span class="ms text-[18px]">person_add</span> Add User
            </button>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr style="border-bottom:1px solid var(--border); background:var(--bg-subtle)">
                    <th class="text-left px-6 py-3 text-[10px] font-semibold uppercase tracking-widest" style="color:var(--text-3)">ID</th>
                    <th class="text-left px-6 py-3 text-[10px] font-semibold uppercase tracking-widest" style="color:var(--text-3)">Email</th>
                    <th class="text-left px-6 py-3 text-[10px] font-semibold uppercase tracking-widest" style="color:var(--text-3)">Bergabung</th>
                    <th class="text-center px-6 py-3 text-[10px] font-semibold uppercase tracking-widest" style="color:var(--text-3)">Transaksi</th>
                    <th class="text-center px-6 py-3 text-[10px] font-semibold uppercase tracking-widest" style="color:var(--text-3)">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr class="transition-colors group"
                    style="border-bottom:1px solid var(--divider)"
                    onmouseenter="this.style.background='var(--bg-hover)'"
                    onmouseleave="this.style.background=''">
                    <td class="px-6 py-4 text-sm" style="color:var(--text-4)">#{{ $user->id }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-violet-500 to-teal-400
                                        flex items-center justify-center text-xs font-bold flex-shrink-0"
                                 style="color:#ffffff">
                                {{ strtoupper(substr($user->email, 0, 1)) }}
                            </div>
                            <span class="text-sm font-semibold" style="color:var(--text-1)">{{ $user->email }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm" style="color:var(--text-3)">{{ $user->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-sm font-bold px-3 py-1 rounded-full"
                              style="background:var(--bg-badge); color:var(--text-1)">
                            {{ $user->transaksi_count }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick="viewUserTransactions({{ $user->id }}, '{{ $user->email }}')"
                                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold
                                           text-blue-400 bg-blue-500/10 hover:bg-blue-500/20 transition-all">
                                <span class="ms text-[14px]">visibility</span> View
                            </button>
                            <button onclick="openResetPasswordModal({{ $user->id }}, '{{ $user->email }}')"
                                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold
                                           text-yellow-400 bg-yellow-500/10 hover:bg-yellow-500/20 transition-all">
                                <span class="ms text-[14px]">key</span> Reset
                            </button>
                            <button onclick="confirmDeleteUser({{ $user->id }})"
                                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold
                                           text-red-400 bg-red-500/10 hover:bg-red-500/20 transition-all">
                                <span class="ms text-[14px]">delete</span> Hapus
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center">
                        <span class="ms text-[48px] block mb-3" style="color:var(--text-4)">person_off</span>
                        <p class="text-sm" style="color:var(--text-4)">
                            {{ $searchUser ? 'User tidak ditemukan.' : 'Belum ada user terdaftar.' }}
                        </p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
