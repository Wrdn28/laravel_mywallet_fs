<script>
// ── Chart Data ──────────────────────────────────────────────────────────────
const userLabels      = @json($userStats->pluck('email'));
const userPemasukan   = @json($userStats->pluck('total_pemasukan'));
const userPengeluaran = @json($userStats->pluck('total_pengeluaran'));
const monthlyLabels   = @json($monthlyStats->pluck('bulan'));
const monthlyIncome   = @json($monthlyStats->pluck('pemasukan'));
const monthlyExpense  = @json($monthlyStats->pluck('pengeluaran'));

document.addEventListener('DOMContentLoaded', () => {
    const c = getChartThemeColors();

    const chartOpts = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { labels: { color: c.legendColor, boxWidth: 10, font: { size: 11 } } },
            tooltip: {
                backgroundColor: c.tooltipBg, titleColor: c.tooltipTitle,
                bodyColor: c.tooltipBody, borderColor: c.tooltipBorder, borderWidth: 1, padding: 10,
            }
        },
        scales: {
            x: { grid: { color: c.gridColor }, ticks: { color: c.tickColor, font: { size: 11 } } },
            y: { grid: { color: c.gridColor }, ticks: { color: c.tickColor, font: { size: 11 } }, beginAtZero: true }
        }
    };

    // User Stats Bar
    registerChart(new Chart(document.getElementById('userStatsChart'), {
        type: 'bar',
        data: {
            labels: userLabels,
            datasets: [
                { label: 'Pemasukan',   data: userPemasukan,   backgroundColor: 'rgba(45,212,191,0.7)',  borderRadius: 6 },
                { label: 'Pengeluaran', data: userPengeluaran, backgroundColor: 'rgba(248,113,113,0.7)', borderRadius: 6 },
            ]
        },
        options: chartOpts
    }));

    // Monthly Trend Line
    const monthlyTrendCanvas = document.getElementById('monthlyTrendChart');
    if (monthlyTrendCanvas) {
        registerChart(new Chart(monthlyTrendCanvas, {
            type: 'line',
            data: {
                labels: monthlyLabels,
                datasets: [
                    { label: 'Pemasukan',   data: monthlyIncome,  borderColor: '#2dd4bf', backgroundColor: 'rgba(45,212,191,0.1)',  tension: 0.4, fill: true, pointBackgroundColor: '#2dd4bf' },
                    { label: 'Pengeluaran', data: monthlyExpense, borderColor: '#f87171', backgroundColor: 'rgba(248,113,113,0.1)', tension: 0.4, fill: true, pointBackgroundColor: '#f87171' },
                ]
            },
            options: chartOpts
        }));
    }

    // Category Doughnut Chart (detail section)
    if (window.categoryChartData) {
        console.log('categoryChartData:', window.categoryChartData);
        console.log('Chart data values:', window.categoryChartData.data);
        const catCanvas = document.getElementById('categoryDoughnutChart');
        if (catCanvas) {
            registerChart(new Chart(catCanvas, {
                type: 'doughnut',
                data: {
                    labels: window.categoryChartData.labels,
                    datasets: [{
                        data: window.categoryChartData.data,
                        backgroundColor: window.categoryChartData.colors,
                        borderWidth: 0,
                        hoverOffset: 10,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: c.tooltipBg, titleColor: c.tooltipTitle,
                            bodyColor: c.tooltipBody, borderColor: c.tooltipBorder,
                            borderWidth: 1, padding: 12,
                            callbacks: { label: ctx => ' Rp ' + ctx.parsed.toLocaleString('id-ID') }
                        }
                    }
                }
            }));
        }

        // Category Bar Chart (horizontal)
        const catBarCanvas = document.getElementById('categoryBarChart');
        if (catBarCanvas) {
            registerChart(new Chart(catBarCanvas, {
                type: 'bar',
                data: {
                    labels: window.categoryChartData.labels,
                    datasets: [{
                        label: 'Pengeluaran',
                        data: window.categoryChartData.data,
                        backgroundColor: window.categoryChartData.colors,
                        borderRadius: 6,
                        borderSkipped: false,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: c.tooltipBg, titleColor: c.tooltipTitle,
                            bodyColor: c.tooltipBody, borderColor: c.tooltipBorder,
                            borderWidth: 1, padding: 12,
                            callbacks: { 
                                    label: function(ctx) {
                                    let value = ctx.raw;

                                    // kalau ternyata object, ambil valuenya
                                    if (typeof value === 'object') {
                                        value = value?.y ?? value?.value ?? 0;
                                    }

                                    return 'Pengeluaran: ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                             }
                        }
                    },
                    scales: {
                        x: {
                            grid: { color: c.gridColor },
                            ticks: {
                                color: c.tickColor, font: { size: 10 },
                                callback: v => v >= 1000000 ? 'Rp '+(v/1000000).toFixed(1)+'jt' : 'Rp '+(v/1000).toFixed(0)+'rb'
                            }
                        },
                        y: {
                            grid: { display: false },
                            ticks: { color: c.legendColor, font: { size: 11 } }
                        }
                    }
                }
            }));
        }
    }

    // Auto-dismiss toast
    const toast = document.getElementById('globalToast');
    if (toast) {
        setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity 0.3s'; setTimeout(() => toast?.remove(), 300); }, 4000);
    }
});

// ── Tab Switching ────────────────────────────────────────────────────────────
function switchTab(name, btn) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));

    // Reset semua tab button ke inactive state via inline style
    document.querySelectorAll('.tab-btn').forEach(el => {
        el.classList.remove('bg-violet-600', 'shadow-[0_0_15px_rgba(124,58,237,0.3)]');
        el.style.color      = 'var(--text-3)';
        el.style.background = 'var(--bg-input)';
        el.style.border     = '1px solid var(--border)';
    });

    document.getElementById('tab-' + name)?.classList.remove('hidden');

    if (btn) {
        btn.classList.add('bg-violet-600', 'shadow-[0_0_15px_rgba(124,58,237,0.3)]');
        btn.style.color      = '#ffffff';
        btn.style.background = '';
        btn.style.border     = '';
    }

    const url = new URL(window.location);
    url.searchParams.set('tab', name);
    window.history.pushState({}, '', url);
}

// ── Modals ───────────────────────────────────────────────────────────────────
function openModal(id)  { const m = document.getElementById(id); m.classList.remove('hidden'); m.classList.add('flex'); }
function closeModal(id) { const m = document.getElementById(id); m.classList.add('hidden'); m.classList.remove('flex'); }

document.addEventListener('click', e => {
    document.querySelectorAll('[id$="Modal"]').forEach(m => { if (e.target === m) closeModal(m.id); });
});

// ── Role Picker ───────────────────────────────────────────────────────────────
function selectRole(role) {
    document.getElementById('roleValue').value = role;
    document.querySelectorAll('.role-btn').forEach(btn => {
        const isActive = btn.dataset.role === role;
        if (isActive) {
            const color = role === 'admin' ? 'var(--accent-red)' : 'var(--accent-violet)';
            btn.style.borderColor = color;
            btn.style.boxShadow   = '0 0 0 2px ' + color;
            btn.style.background  = role === 'admin' ? 'rgba(220,38,38,0.08)' : 'rgba(124,58,237,0.08)';
        } else {
            btn.style.borderColor = 'var(--border)';
            btn.style.boxShadow   = 'none';
            btn.style.background  = 'var(--bg-input)';
        }
    });
}

// ── Delete User ──────────────────────────────────────────────────────────────
function confirmDeleteUser(userId) {
    if (!confirm('Hapus user ini? Semua transaksinya juga akan dihapus.')) return;
    const form = document.getElementById('deleteUserForm');
    form.action = '{{ url("admin/users") }}/' + userId;
    form.submit();
}

// ── Delete Transaction ───────────────────────────────────────────────────────
function confirmDeleteTransaction(id) {
    if (!confirm('Hapus transaksi ini?')) return;
    const form = document.getElementById('deleteTransactionForm');
    const params = new URLSearchParams(window.location.search);
    params.set('tab', 'transactions');
    form.action = '{{ url("admin/transactions") }}/' + id + '?' + params.toString();
    form.submit();
}

// ── Reset Password Modal ─────────────────────────────────────────────────────
function openResetPasswordModal(userId, email) {
    document.getElementById('resetUserId').value    = userId;
    document.getElementById('resetUserEmail').value = email;
    document.getElementById('newPassword').value    = '';
    document.getElementById('confirmPassword').value = '';
    document.getElementById('passwordMatchMsg').textContent = '';
    document.getElementById('resetSubmitBtn').disabled = true;
    openModal('resetPasswordModal');
}

document.addEventListener('DOMContentLoaded', () => {
    const newPw  = document.getElementById('newPassword');
    const confPw = document.getElementById('confirmPassword');
    const msg    = document.getElementById('passwordMatchMsg');
    const btn    = document.getElementById('resetSubmitBtn');

    function validate() {
        if (!newPw || !confPw) return;
        if (newPw.value.length < 6) {
            msg.textContent = 'Min. 6 karakter'; msg.style.color = '#fbbf24'; btn.disabled = true;
        } else if (newPw.value !== confPw.value) {
            msg.textContent = 'Password tidak cocok'; msg.style.color = '#f87171'; btn.disabled = true;
        } else {
            msg.textContent = 'Password cocok ✓'; msg.style.color = '#34d399'; btn.disabled = false;
        }
    }
    newPw?.addEventListener('input', validate);
    confPw?.addEventListener('input', validate);
});

// ── View User Transactions (AJAX) ────────────────────────────────────────────
function viewUserTransactions(userId, email) {
    document.getElementById('viewUserTitle').innerHTML =
        '<span class="ms text-[20px] text-blue-400">visibility</span> ' + email;
    document.getElementById('viewUserContent').innerHTML =
        '<p class="text-slate-500 text-sm text-center py-8">Loading...</p>';
    openModal('viewUserModal');

    fetch('{{ route("admin.ajax.user-transactions") }}?user_id=' + userId, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.text())
    .then(html => { document.getElementById('viewUserContent').innerHTML = html; })
    .catch(() => { document.getElementById('viewUserContent').innerHTML =
        '<p class="text-red-400 text-sm text-center py-8">Gagal memuat data.</p>'; });
}

// ── Maintenance Toggle ───────────────────────────────────────────────────────
const maintenanceToggle  = document.getElementById('maintenanceToggle');
const maintenanceWarning = document.getElementById('maintenanceWarning');
maintenanceToggle?.addEventListener('change', function () {
    maintenanceWarning?.classList.toggle('hidden', !this.checked);
    maintenanceWarning?.classList.toggle('flex', this.checked);
});

// ── Config Reset ─────────────────────────────────────────────────────────────
const _savedAppName    = @json($appName);
const _savedAdminEmail = @json($adminEmail);
const _savedMaintenance = @json($maintenanceMode === '1');

function resetConfigForm() {
    const appInput   = document.getElementById('inputAppName');
    const emailInput = document.querySelector('input[name="admin_email"]');
    if (appInput)   appInput.value   = _savedAppName;
    if (emailInput) emailInput.value = _savedAdminEmail;
    if (maintenanceToggle) {
        maintenanceToggle.checked = _savedMaintenance;
        maintenanceWarning?.classList.toggle('hidden', !_savedMaintenance);
        maintenanceWarning?.classList.toggle('flex', _savedMaintenance);
    }
}
</script>
