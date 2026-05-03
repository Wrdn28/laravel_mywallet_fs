{{-- ── ADD USER ── --}}
<div id="addUserModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
    <div class="modal-anim rounded-2xl w-full max-w-md"
         style="background:var(--bg-card); border:1px solid var(--border); box-shadow:var(--shadow-lg)">
        <div class="flex items-center justify-between px-6 py-5" style="border-bottom:1px solid var(--border)">
            <h3 class="font-bold flex items-center gap-2" style="color:var(--text-1)">
                <span class="ms text-[20px] text-violet-400">person_add</span> Tambah User
            </h3>
            <button onclick="closeModal('addUserModal')"
                    class="w-8 h-8 rounded-lg flex items-center justify-center transition-all"
                    style="background:var(--bg-input); color:var(--text-3)"
                    onmouseenter="this.style.color='var(--text-1)'"
                    onmouseleave="this.style.color='var(--text-3)'">
                <span class="ms text-[20px]">close</span>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.users.add') }}" class="p-6 space-y-4"
              style="background:var(--bg-card)">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider mb-2"
                       style="color:var(--text-3)">Email</label>
                <input type="email" name="email" required placeholder="contoh@email.com"
                       class="w-full rounded-xl px-4 py-3 text-sm focus:outline-none transition-all">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider mb-2"
                       style="color:var(--text-3)">Password</label>
                <input type="password" name="password" required minlength="6" placeholder="Min. 6 karakter"
                       class="w-full rounded-xl px-4 py-3 text-sm focus:outline-none transition-all">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider mb-2"
                       style="color:var(--text-3)">Role</label>
                <div class="grid grid-cols-2 gap-2" id="rolePicker">
                    <button type="button" data-role="user"
                            onclick="selectRole('user')"
                            class="role-btn flex items-center gap-2.5 px-4 py-3 rounded-xl border transition-all"
                            style="background:var(--bg-input); border-color:var(--accent-violet); box-shadow:0 0 0 2px var(--accent-violet)">
                        <span class="ms text-[18px] text-violet-400">person</span>
                        <div class="text-left">
                            <p class="text-sm font-semibold" style="color:var(--text-1)">User</p>
                            <p class="text-[10px]" style="color:var(--text-4)">Akses dashboard</p>
                        </div>
                    </button>
                    <button type="button" data-role="admin"
                            onclick="selectRole('admin')"
                            class="role-btn flex items-center gap-2.5 px-4 py-3 rounded-xl border transition-all"
                            style="background:var(--bg-input); border-color:var(--border)">
                        <span class="ms text-[18px] text-red-400">admin_panel_settings</span>
                        <div class="text-left">
                            <p class="text-sm font-semibold" style="color:var(--text-1)">Admin</p>
                            <p class="text-[10px]" style="color:var(--text-4)">Akses panel admin</p>
                        </div>
                    </button>
                </div>
                <input type="hidden" name="role" id="roleValue" value="user">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('addUserModal')"
                        class="flex-1 py-2.5 rounded-xl text-sm font-semibold transition-all"
                        style="background:var(--bg-input); color:var(--text-3)">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 rounded-xl text-sm font-bold
                               bg-gradient-to-r from-violet-600 to-violet-500 hover:from-violet-500 hover:to-violet-400
                               transition-all active:scale-95"
                        style="color:#ffffff">
                    Tambah
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── RESET PASSWORD ── --}}
<div id="resetPasswordModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
    <div class="modal-anim rounded-2xl w-full max-w-md"
         style="background:var(--bg-card); border:1px solid var(--border); box-shadow:var(--shadow-lg)">
        <div class="flex items-center justify-between px-6 py-5" style="border-bottom:1px solid var(--border)">
            <h3 class="font-bold flex items-center gap-2" style="color:var(--text-1)">
                <span class="ms text-[20px] text-yellow-400">key</span> Reset Password
            </h3>
            <button onclick="closeModal('resetPasswordModal')"
                    class="w-8 h-8 rounded-lg flex items-center justify-center transition-all"
                    style="background:var(--bg-input); color:var(--text-3)"
                    onmouseenter="this.style.color='var(--text-1)'"
                    onmouseleave="this.style.color='var(--text-3)'">
                <span class="ms text-[20px]">close</span>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.users.reset-password') }}" class="p-6 space-y-4"
              style="background:var(--bg-card)">
            @csrf
            <input type="hidden" name="user_id" id="resetUserId">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider mb-2"
                       style="color:var(--text-3)">User</label>
                <input type="text" id="resetUserEmail" readonly
                       class="w-full rounded-xl px-4 py-3 text-sm cursor-not-allowed"
                       style="opacity:0.6">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider mb-2"
                       style="color:var(--text-3)">Password Baru</label>
                <input type="password" name="new_password" id="newPassword" required minlength="6"
                       placeholder="Min. 6 karakter"
                       class="w-full rounded-xl px-4 py-3 text-sm focus:outline-none transition-all">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider mb-2"
                       style="color:var(--text-3)">Konfirmasi Password</label>
                <input type="password" id="confirmPassword" required placeholder="Ulangi password"
                       class="w-full rounded-xl px-4 py-3 text-sm focus:outline-none transition-all">
                <p id="passwordMatchMsg" class="text-xs mt-1.5"></p>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('resetPasswordModal')"
                        class="flex-1 py-2.5 rounded-xl text-sm font-semibold transition-all"
                        style="background:var(--bg-input); color:var(--text-3)">
                    Batal
                </button>
                <button type="submit" id="resetSubmitBtn" disabled
                        class="flex-1 py-2.5 rounded-xl text-sm font-bold
                               bg-gradient-to-r from-yellow-600 to-yellow-500 hover:from-yellow-500 hover:to-yellow-400
                               transition-all active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed"
                        style="color:#ffffff">
                    Reset Password
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── VIEW USER TRANSACTIONS ── --}}
<div id="viewUserModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
    <div class="modal-anim rounded-2xl w-full max-w-2xl max-h-[80vh] flex flex-col"
         style="background:var(--bg-card); border:1px solid var(--border); box-shadow:var(--shadow-lg)">
        <div class="flex items-center justify-between px-6 py-5 flex-shrink-0"
             style="border-bottom:1px solid var(--border)">
            <h3 id="viewUserTitle" class="font-bold flex items-center gap-2" style="color:var(--text-1)">
                <span class="ms text-[20px] text-blue-400">visibility</span> User Transactions
            </h3>
            <button onclick="closeModal('viewUserModal')"
                    class="w-8 h-8 rounded-lg flex items-center justify-center transition-all"
                    style="background:var(--bg-input); color:var(--text-3)"
                    onmouseenter="this.style.color='var(--text-1)'"
                    onmouseleave="this.style.color='var(--text-3)'">
                <span class="ms text-[20px]">close</span>
            </button>
        </div>
        <div id="viewUserContent" class="p-6 overflow-y-auto flex-1"
             style="background:var(--bg-card)">
            <p class="text-sm text-center py-8" style="color:var(--text-4)">Loading...</p>
        </div>
    </div>
</div>

{{-- ── DELETE USER (hidden form) ── --}}
<form id="deleteUserForm" method="POST" style="display:none">
    @csrf @method('DELETE')
</form>

{{-- ── DELETE TRANSACTION (hidden form) ── --}}
<form id="deleteTransactionForm" method="POST" style="display:none">
    @csrf @method('DELETE')
</form>
